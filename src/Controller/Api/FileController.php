<?php

namespace App\Controller\Api;

use App\Entity\DsaFormFilled;
use App\Entity\File as FileEntity;
use Exception;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FileController extends MyRestController {

    /**
     * Returns a filled PDF.
     * @FOSRest\Get(path="/api/get-filled-pdf")
     */
    public function getFilledPDF(Request $request) {
        try {
            $fileId = $request->get('file');
            $userInfo = $this->getRequestUser($request);
            $user = $userInfo['user'];
            if ($userInfo['code'] !== 'success' || !$user->isDO()) {
                return new JsonResponse(['code' => 'error', 'msg' => 'Invalid parameters', 'data' => []], Response::HTTP_OK);
            }

            $filledForm = $this->getDoctrine()->getManager()->getRepository(DsaFormFilled::class)->find($fileId);
            if (!$filledForm) {
                return new JsonResponse(['code' => 'error', 'msg' => 'File not found', 'data' => null], Response::HTTP_OK);
            }

            $formFiller = $filledForm->getUser();

            if (!($formFiller === $user || ($formFiller->getUniversity() === $user->getUniversity() && $user->isDO()))) {
                return new JsonResponse(['code' => 'error', 'msg' => 'Access denied', 'data' => null], Response::HTTP_OK);
            }
            
            return $this->returnFileStream($this->getDSAFilledFormsDir(), $filledForm->getFilename());

            /*$filename = $filledForm->getFilename();
            $file = new File($this->getDSAFilledFormsDir() . $filename);
            return $this->file($file, $filename);*/
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => []], Response::HTTP_OK);
        }
    }
    
    /**
     * Returns a file.
     * @FOSRest\Get(path="/api/download-attached-file")
     */
    public function getAttachedFile(Request $request) {
        try {
            $fileId = $request->get('file');
            $userInfo = $this->getRequestUser($request);
            $user = $userInfo['user'];
            if ($userInfo['code'] !== 'success' || !$user->isStudent()) {
                return new JsonResponse(['code' => 'error', 'msg' => 'Invalid parameters', 'data' => []], Response::HTTP_OK);
            }
            $file = $this->getDoctrine()->getManager()->getRepository(FileEntity::class)->findOneBy(['id' => $fileId, 'user' => $user]);
            if (!$file) {
                return new JsonResponse(['code' => 'error', 'msg' => 'File not found', 'data' => null], Response::HTTP_OK);
            }
            return $this->returnFileStream($this->getAttachedFilesDir(), $file->getNew_filename());
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => []], Response::HTTP_OK);
        }
    }
    
    /**
     * Returns a file.
     * @FOSRest\Post(path="/api/delete-attached-file")
     */
    public function deleteAttachedFile(Request $request) {
        try {
            $fileId = $request->get('file');
            $userInfo = $this->getRequestUser($request);
            $user = $userInfo['user'];
            $targetType = $request->get('target_type');
            if ($userInfo['code'] !== 'success' || !$user->isStudent()) {
                return new JsonResponse(['code' => 'error', 'msg' => 'Invalid parameters', 'data' => []], Response::HTTP_OK);
            }
            $file = $this->getDoctrine()->getManager()->getRepository(FileEntity::class)->findOneBy(['id' => $fileId, 'user' => $user]);
            if (!$file) {
                return new JsonResponse(['code' => 'error', 'msg' => 'Invalid file', 'data' => null], Response::HTTP_OK);
            }
            $this->getEntityManager()->remove($file);
            $this->getEntityManager()->flush();
            $filePath = $this->getAttachedFilesDir() . $file->getNew_filename();
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $data = $this->getFiles($user, $targetType);
            return new JsonResponse(['code' => 'success', 'msg' => 'File removed', 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => []], Response::HTTP_OK);
        }
    }

    private function returnFileStream($path, $filename) {
        return $this->file(new File($path . $filename), $filename);
    }

    private function getFiles($user, $targetType) {
        $files = $this->getDoctrine()->getManager()->getRepository(FileEntity::class)->findBy(['user' => $user, 'ac' => ($targetType === 'dsa' ? null : $user->getAC())]);
        $res = [];
        foreach ($files as $file) {
            $res[] = [
                'id' => $file->getId(),
                'name' => $file->getName(),
                'description' => $file->getDescription(),
                'date' => date_format($file->getDate(), 'Y-m-d H:i'),
            ];
        }
        return $res;
    }

    /**
     * Returns a filled PDF.
     * @FOSRest\Get(path="/api/get-attached-files")
     */
    public function getAttachedFiles(Request $request) {
        try {
            $userInfo = $this->getRequestUser($request);
            $user = $userInfo['user'];
            $targetType = $request->get('target_type');
            if ($userInfo['code'] !== 'success' || !$user->isStudent() || !$targetType) {
                return new JsonResponse(['code' => 'error', 'msg' => 'Invalid parameters', 'data' => []], Response::HTTP_OK);
            }
            $data = $this->getFiles($user, $targetType);
            return new JsonResponse(['code' => 'success', 'msg' => 'Files fetched', 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => []], Response::HTTP_OK);
        }
    }

    /**
     * Registers with AC
     * @FOSRest\Post(path="/api/attach-file")
     */
    public function attachFile(Request $request) {
        try {
            $user = $this->getRequestUser($request);
            if ($user['code'] !== 'success') {
                return new JsonResponse(['code' => 'error', 'msg' => 'Invalid user', 'data' => []], Response::HTTP_OK);
            }
            $user = $user['user'];
            $sentFile = $request->files->get('content');
            $targetType = $request->get('target_type');
            $name = $request->get('name');
            $description = $request->get('description');

            if (!$sentFile || !$targetType || !$name) {
                return new JsonResponse(['code' => 'error', 'msg' => 'Missing parameters', 'data' => []], Response::HTTP_OK);
            }

            $newFilename = $user->getId() . '-' . time() . '.' . $sentFile->getClientOriginalExtension();
            $sentFile->move($this->getAttachedFilesDir(), $newFilename);

            $file = new FileEntity();
            $file->setName($name);
            $file->setDescription($description);
            $file->setOriginal_filename($sentFile->getClientOriginalName());
            $file->setNew_filename($newFilename);
            $file->setDate(date_create(date('Y-m-d H:i')));
            $file->setUser($user);
            if ($targetType === 'ac') {
                $file->setAc($user->getAC());
            }
            $this->getEntityManager()->persist($file);
            $headline = date('Y/m/d H:i:s', time());
            //$this->createNotification("New file attached", 'Your form has been submitted to ' . $ac->getName() . '. You can check its content <a href="/#/' . $myAcForm . '">here</a>.', $headline, $user, 1, 2);
            $this->createNotification("New file attached", "You have attached a new file to your $targetType.", $headline, $user, 1, 2);
            $this->getEntityManager()->flush();
            $data = $this->getFiles($user, $targetType);
            return new JsonResponse(['code' => 'success', 'msg' => 'File attached', 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => []], Response::HTTP_OK);
        }
    }

}
