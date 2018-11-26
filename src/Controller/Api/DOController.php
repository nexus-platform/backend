<?php

namespace App\Controller\Api;

use App\Entity\AppSettings;
use App\Entity\DsaForm;
use App\Entity\DsaFormFilled;
use App\Entity\QrCode as QrCode2;
use App\Entity\University;
use App\Entity\UniversityDsaForm;
use App\Entity\User;
use App\Utils\StaticMembers;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use mikehaertl\pdftk\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use setasign\Fpdi\TcpdfFpdi;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Brand controller.
 *
 * @Route("/")
 */
class DOController extends MyRestController {

    /**
     * Retrieves an university.
     * @FOSRest\Get(path="/api/get-university")
     */
    public function getUniversityAction(Request $request) {
        try {
            $token = $request->get('university_token', null);
            //$entityManager = $this->getDoctrine()->getManager();
            $item = $this->getDoctrine()->getManager()->getRepository(University::class)->findOneBy(['token' => $token]);

            if ($item) {
                $data = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                    'country' => $item->getCountry()->getName()
                ];
                $code = 'success';
                $msg = '';
            } else {
                $data = null;
                $code = 'error';
                $msg = 'Invalid university identifier';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Retrieves the list of DSA forms.
     * @FOSRest\Get(path="/api/get-dsa-forms")
     */
    public function getDsaFormsAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            if ($payload) {
                $data = $this->getDsaForms($this->getDoctrine()->getManager(), $payload);
                $code = 'success';
                $msg = "List of available DSA forms";
            } else {
                $data = null;
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    private function getDsaForms(ObjectManager $entityManager, $payload) {
        $formsDir = $this->container->getParameter('kernel.project_dir') . '/public/dsa_forms/';
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($payload->user_id);
        $res = [];

        if ($user->isDO()) {
            $items = $this->getDoctrine()->getManager()->getRepository(DsaForm::class)->findBy(['active' => 1]);
            foreach ($items as $dsaForm) {
                $res[] = [
                    'id' => $dsaForm->getId(),
                    'name' => $dsaForm->getName(),
                    'code' => $dsaForm->getCode(),
                    'active' => $dsaForm->getActive(),
                    'file_status' => file_exists($formsDir . $dsaForm->getBase()),
                ];
            }
        } else {
            $univ = $user->getUniversity();
            $univForms = $univ->getUniv_dsa_form();
            foreach ($univForms as $univForm) {
                if ($univForm->getActive()) {
                    $dsaForm = $univForm->getDsa_form();
                    $res[] = [
                        'id' => $dsaForm->getId(),
                        'name' => $dsaForm->getName(),
                        'code' => $dsaForm->getCode(),
                        'active' => $dsaForm->getActive(),
                        'route' => '/dsa/' . $univ->getToken() . '/dsa-forms/' . $univForm->getDsa_form_slug(),
                        'file_status' => file_exists($formsDir . $dsaForm->getBase()),
                    ];
                }
            }
        }
        return $res;
    }

    /**
     * Retrieves the Institute info
     * @FOSRest\Get(path="/api/get-my-institute-info")
     */
    public function getMyInstituteInfoAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            if ($payload) {
                //$entityManager = $this->getDoctrine()->getManager();
                $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($payload->user_id);
                $univ = $user->getUniversity();
                $univForms = $univ->getUniv_dsa_form();
                $forms = [];
                foreach ($univForms as $form) {
                    $forms[] = [
                        'id' => $form->getId(),
                        'name' => $form->getDsa_form()->getName(),
                        'active' => $form->getActive(),
                        'slug' => $form->getDsa_form_slug()
                    ];
                }
                $data = [
                    'univ_slug' => $univ->getToken(),
                    'forms' => $forms,
                ];
                $code = 'success';
                $msg = $univ->getName();
            } else {
                $data = null;
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Retrieves the list of inputs for a form.
     * @FOSRest\Get(path="/api/get-pdf-content")
     */
    public function getPdfContentAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $data = null;
            $unfinishedForms = [];
            $code = 'error';

            if ($payload) {
                //$entityManager = $this->getDoctrine()->getManager();
                $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($payload->user_id);
                $univSlug = $request->get('univ_slug');
                $univFromSlug = $this->getDoctrine()->getManager()->getRepository(University::class)->findOneBy(['token' => $univSlug]);
                $univFromUser = $user->getUniversity();
                $formSlug = $request->get('form_slug');
                $univForm = $this->getDoctrine()->getManager()->getRepository(UniversityDsaForm::class)->findOneBy(['university' => $univFromUser, 'dsa_form_slug' => $formSlug]);

                if ($univFromSlug === $univFromUser && $univForm) {
                    $dsaForm = $univForm->getDsa_form();
                    $formsDir = $this->container->getParameter('kernel.project_dir') . '/public/dsa_forms/';
                    $formPath = $formsDir . $dsaForm->getBase();

                    $formId = $request->get('entity_id', null);
                    $data = $dsaForm->getContent();

                    if ($formId != '0') {
                        $filledForm = $this->getDoctrine()->getManager()->getRepository(DsaFormFilled::class)->find($formId);
                        if ($filledForm) {
                            $filledFormUser = $filledForm->getUser();
                            if (($user === $filledFormUser && ($filledForm->getStatus() == 0 || $filledForm->getStatus() == 2)) || ($user->isDO() && $user->getUniversity() === $filledFormUser->getUniversity())) {
                                $filledData = $filledForm->getContent();
                                $comments = $filledForm->getComments();
                                $signatures = $filledForm->getSignatures();
                                $dataCount = count($data);
                                for ($i = 0; $i < $dataCount; $i++) {
                                    $components = $data[$i]['components'];
                                    $componentsCount = count($data[$i]['components']);
                                    for ($j = 0; $j < $componentsCount; $j++) {
                                        $colsCount = count($data[$i]['components'][$j]);
                                        for ($k = 0; $k < $colsCount; $k++) {
                                            $col = $data[$i]['components'][$j][$k];
                                            if ($col['content_type'] === 'input') {
                                                $name = $col['input']['name'];
                                                if ($filledForm->getStatus() == 1 || $filledForm->getStatus() == 2 || $user !== $filledFormUser) {
                                                    $data[$i]['components'][$j][$k]['input']['disabled'] = true;
                                                    $data[$i]['components'][$j][$k]['input']['read_only'] = true;
                                                }
                                                if (isset($filledData[$name])) {
                                                    $data[$i]['components'][$j][$k]['input']['value'] = $filledData[$name];
                                                }
                                                if (isset($comments[$name])) {
                                                    if ($user === $filledFormUser) {
                                                        $data[$i]['components'][$j][$k]['input']['disabled'] = false;
                                                        $data[$i]['components'][$j][$k]['input']['read_only'] = false;
                                                    }
                                                    $data[$i]['components'][$j][$k]['input']['comments'] = $comments[$name];
                                                }
                                                if (isset($signatures[$name])) {
                                                    $data[$i]['components'][$j][$k]['input']['value'] = $signatures[$name]['value'];
                                                }
                                                //Input group
                                            } else if ($col['content_type'] === 'input_group') {
                                                $inputGroupName = $col['name'];
                                                if (isset($comments[$inputGroupName])) {
                                                    if ($user === $filledFormUser) {
                                                        $data[$i]['components'][$j][$k]['comments'] = $comments[$inputGroupName];
                                                    }
                                                }

                                                $rowsCount = 0;
                                                $rows = [];

                                                if (isset($filledData[$inputGroupName])) {
                                                    $rowsCount = $filledData[$inputGroupName];
                                                    $models = $col['model'];
                                                    for ($l = 1; $l <= $rowsCount; $l++) {
                                                        $newRow = [];
                                                        foreach ($models as $model) {
                                                            $newName = $model['input']['name'] .= " $l";
                                                            $model['input']['name'] = $newName;
                                                            if ($filledForm->getStatus() == 1 || $filledForm->getStatus() == 2 || $user !== $filledFormUser) {
                                                                $model['input']['disabled'] = true;
                                                                $model['input']['read_only'] = true;
                                                            }
                                                            if (isset($filledData[$newName])) {
                                                                $model['input']['value'] = $filledData[$newName];
                                                            }
                                                            if (isset($signatures[$newName])) {
                                                                $model['input']['value'] = $signatures[$newName]['value'];
                                                            }
                                                            $newRow[] = $model;
                                                        }
                                                        $rows[] = $newRow;
                                                    }
                                                }
                                                $data[$i]['components'][$j][$k]['rows'] = $rows;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $unfinishedForms = $this->getEntityManager()->getRepository(DsaFormFilled::class)->getUnfinishedForms($user, $dsaForm);
                        $auxArr = [];
                        foreach ($unfinishedForms as $unfinishedForm) {
                            $auxContent = $unfinishedForm->getContent();
                            $auxArr[] = [
                                'id' => $unfinishedForm->getId(),
                                'date' => date('Y-m-d H:i', $unfinishedForm->getCreated_at()),
                                'progress' => round($auxContent['filled_inputs'] * 100 / $auxContent['total_inputs'], 2) . '% (' . $auxContent['filled_inputs'] . ' filled inputs of ' . $auxContent['total_inputs'] . ')',
                                'route' => '/dsa/' . $univFromSlug->getToken() . '/dsa-forms/' . $dsaForm->getCode() . '/' . $unfinishedForm->getId(),
                            ];
                        }
                        $unfinishedForms = $auxArr;
                    }
                    $code = 'success';
                    $msg = $dsaForm->getName();
                    $formSlug = $dsaForm->getCode();
                }
            } else {
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'pdf_code' => $formSlug, 'data' => $data, 'unfinished_forms' => $unfinishedForms], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Retrieves the content of a partially filled form.
     * @FOSRest\Get(path="/api/get-my-dsa-form-content")
     */
    public function getMyDsaFormContentAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $formId = $request->get('pdf_code', null);
            $payload = $this->decodeJWT($jwt);
            $data = null;

            if ($payload) {
                //$entityManager = $this->getDoctrine()->getManager();
                $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($payload->user_id);
                $filledForm = $this->getDoctrine()->getManager()->getRepository(DsaFormFilled::class)->findOneBy(['id' => $formId, 'user' => $user]);
                if (!$filledForm) {
                    $filledForm = new DsaFormFilled();
                }
                $item = $filledForm->getDsaForm();

                if ($item) {
                    $data = $item->getContent();
                    $dataCount = count($data);
                    $filledData = $filledForm->getContent();

                    for ($i = 0; $i < $dataCount; $i++) {
                        $components = $data[$i]['components'];
                        $componentsCount = count($data[$i]['components']);
                        for ($j = 0; $j < $componentsCount; $j++) {
                            $colsCount = count($data[$i]['components'][$j]);
                            for ($k = 0; $k < $colsCount; $k++) {
                                $col = $data[$i]['components'][$j][$k];
                                if ($col['content_type'] === 'input') {
                                    $name = $col['input']['name'];
                                    if (isset($filledData[$name])) {
                                        $data[$i]['components'][$j][$k]['input']['value'] = $filledData[$name];
                                    }
                                }
                            }
                        }
                    }
                    $code = 'success';
                    $msg = $item->getName();
                } else {
                    $code = 'error';
                    $msg = 'Invalid form identifier supplied';
                }
            } else {
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Fills a form with received data.
     * @FOSRest\Post(path="/api/fill-pdf-form")
     */
    public function fillPdfFormAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $data = $request->get('data');
            $signaturesInfo = $request->get('signaturesInfo');

            if ($data || $signaturesInfo) {
                $payload = $this->decodeJWT($jwt);
                if ($payload) {
                    //$entityManager = $this->getDoctrine()->getManager();
                    $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($payload->user_id);
                    $univFromSlug = $this->getDoctrine()->getManager()->getRepository(University::class)->findOneBy(['token' => $request->get('univ_slug')]);
                    $univFromUser = $user->getUniversity();
                    $univForm = $this->getDoctrine()->getManager()->getRepository(UniversityDsaForm::class)->findOneBy(['university' => $univFromUser, 'dsa_form_slug' => $request->get('form_slug')]);

                    if ($univFromSlug === $univFromUser && $univForm) {
                        $item = $univForm->getDsa_form();
                        if ($data['id'] === 0) {
                            $filledForm = new DsaFormFilled();
                        } else {
                            $filledForm = $this->getDoctrine()->getManager()->getRepository(DsaFormFilled::class)->findOneBy(['id' => $data['id'], 'user' => $user]);
                        }

                        if ($filledForm) {
                            $cleanData = $data;
                            unset($cleanData['id']);
                            unset($cleanData['full_submit']);
                            $now = time();
                            $filledForm->setContent($cleanData);
                            $filledForm->setDsaForm($item);
                            $filledForm->setUser($user);
                            $filledForm->setCreated_at($now);
                            $filledForm->setSignatures($signaturesInfo);
                            $filledForm->setStatus($data['full_submit'] ? 1 : 0);
                            $this->getDoctrine()->getManager()->persist($filledForm);
                            $this->getDoctrine()->getManager()->flush();
                            $code = 'success';

                            if ($data['full_submit']) {
                                $disabOfficers = StaticMembers::executeRawSQL($entityManager, "SELECT * FROM `user` where `university_id` = " . $univFromUser->getId() . " and json_contains(roles, json_array('do')) = 1");
                                $headline = date('Y/m/d H:i:s', $now);
                                $route = 'dsa/' . $univFromUser->getToken() . '/dsa-forms/' . $univForm->getDsa_form_slug() . '/' . $filledForm->getId();
                                $myFormsRoute = 'dsa/' . $univFromUser->getToken() . '/my-dsa-forms/index';
                                $this->createNotification('You have submitted a new DSA Form', 'Your "' . $item->getName() . '" has been submitted. You can check its status <a href="/#/' . $myFormsRoute . '">here</a>.', $headline, $user, 1, 2);
                                foreach ($disabOfficers as $do) {
                                    $doEntity = $this->getDoctrine()->getManager()->getRepository(User::class)->find($do['id']);
                                    $this->createNotification('New DSA Form submitted by ' . $user->__toString(), 'A new "' . $item->getName() . '" has been submitted. You can review it <a href="/#/' . $route . '">here</a>.', $headline, $doEntity, 1, 1);
                                }
                                $msg = "Your form has been submitted.";
                                $this->getDoctrine()->getManager()->flush();
                            } else {
                                $msg = "Progress saved";
                            }
                        } else {
                            $code = 'error';
                            $msg = 'Invalid PDF supplied';
                        }
                    } else {
                        $code = 'error';
                        $msg = 'Invalid PDF supplied';
                    }
                } else {
                    $code = 'error';
                    $msg = 'Your session has expired. Please, proceed to the login page';
                }
            } else {
                $code = 'warning';
                $msg = 'Your request did not include any data';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $filledForm->getId()], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Retrieves the list of filled DSA forms.
     * @FOSRest\Get(path="/api/get-my-dsa-forms")
     */
    public function getMyDsaFormsAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {
                //$entityManager = $this->getDoctrine()->getManager();
                $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($payload->user_id);
                $univ = $user->getUniversity();
                $filledForms = $this->getDoctrine()->getManager()->getRepository(DsaFormFilled::class)->findBy(['user' => $user], ['created_at' => 'desc']);
                $data = [];
                foreach ($filledForms as $filledForm) {
                    $form = $filledForm->getDsaForm();
                    $univForm = $this->getDoctrine()->getManager()->getRepository(UniversityDsaForm::class)->findOneBy(['university' => $univ, 'dsa_form' => $form]);
                    $data[] = [
                        'id' => $filledForm->getId(),
                        'pdf_name' => $form->getName(),
                        'status' => $filledForm->getStatus(),
                        'status_desc' => $this->getFormStatusDesc($filledForm->getStatus()),
                        'route' => '/dsa/' . $univ->getToken() . '/dsa-forms/' . $univForm->getDsa_form_slug() . '/' . $filledForm->getId(),
                        'created_at' => date('Y/m/d H:i:s', $filledForm->getCreated_at()),
                    ];
                }
                $code = 'success';
                $msg = "List of available DSA forms";
            } else {
                $data = null;
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    private function validatePNGImage($data) {
        try {
            $img = imagecreatefrompng($data);
            return true;
        } catch (Exception $exc) {
            return false;
        }
    }

    /**
     * Uploads an image with a signature.
     * @FOSRest\Post(path="/api/upload-signature")
     */
    public function uploadSignatureAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $data = null;
            if ($payload) {
                $file = $request->get('file');
                if ($file && $this->validatePNGImage($file)) {
                    //$entityManager = $this->getDoctrine()->getManager();
                    $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($payload->user_id);
                    $user->setSignature($file);
                    $this->getDoctrine()->getManager()->persist($user);
                    $this->getDoctrine()->getManager()->flush();
                    $data = $file;
                    $code = 'success';
                    $msg = 'Signature has been uploaded';
                } else {
                    $code = 'warning';
                    $msg = 'You must submit a valid PNG image';
                }
            } else {
                $randomCode = $request->get('random_code');
                if ($randomCode) {
                    //$entityManager = $this->getDoctrine()->getManager();
                    $qrCode = $this->getDoctrine()->getManager()->getRepository(QrCode2::class)->findOneBy(['random_code' => $randomCode]);
                    if ($qrCode) {
                        $file = $request->get('file');
                        if ($file && $this->validatePNGImage($file)) {
                            $qrCode->setContent($file);
                            $this->getDoctrine()->getManager()->persist($qrCode);
                            $this->getDoctrine()->getManager()->flush();
                            $code = 'success';
                            $msg = 'Signature has been uploaded';
                        } else {
                            $code = 'warning';
                            $msg = 'You must submit a valid PNG image';
                        }
                    } else {
                        $code = 'error';
                        $msg = 'This code is not available anymore';
                    }
                } else {
                    $code = 'error';
                    $msg = 'Invalid parameter supplied. You may need to renew your session';
                }
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Generates a temporary QR Code.
     * @FOSRest\Get(path="/api/generate-qr-code")
     */
    public function generateQrCodeAction(Request $request) {
        try {
            $data = null;
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {
                //$entityManager = $this->getDoctrine()->getManager();
                $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($payload->user_id);
                $randomCode = StaticMembers::random_str(16);
                $qrEntity = $this->getDoctrine()->getManager()->getRepository(QrCode2::class)->findOneBy(['random_code' => $randomCode]);
                while ($qrEntity) {
                    $randomCode = StaticMembers::random_str(16);
                    $qrEntity = $this->getDoctrine()->getManager()->getRepository(QrCode2::class)->findOneBy(['random_code' => $randomCode]);
                }
                $qrEntity = new QrCode2();
                $qrEntity->setCreated_at(time());
                $qrEntity->setRandom_code($randomCode);
                $qrEntity->setUser($user);
                $this->getDoctrine()->getManager()->persist($qrEntity);
                $this->getDoctrine()->getManager()->flush();
                $options = new QROptions([
                    'version' => 5,
                    'outputType' => QRCode::OUTPUT_MARKUP_SVG,
                    'eccLevel' => QRCode::ECC_L,
                ]);
                $qrCode = new QRCode($options);
                $data = ['qr_code' => 'data:image/svg+xml;base64,' . base64_encode($qrCode->render($randomCode)), 'random_code' => $randomCode];
                $code = 'success';
                $msg = 'Signature has been uploaded';
            } else {
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Validates QR Code.
     * @FOSRest\Get(path="/api/get-signature-by-random-code")
     */
    public function getSignatureByRandomCodeAction(Request $request) {
        try {
            $data = null;
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {
                $randomCode = $request->get('random_code');
                if ($randomCode) {
                    //$entityManager = $this->getDoctrine()->getManager();
                    $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($payload->user_id);
                    $qrEntity = $this->getDoctrine()->getManager()->getRepository(QrCode2::class)->findOneBy(['random_code' => $randomCode, 'user' => $user]);
                    if ($qrEntity && $qrEntity->getContent()) {
                        $data = $qrEntity->getContent();
                        /* $this->getDoctrine()->getManager()->remove($qrEntity);
                          $this->getDoctrine()->getManager()->flush(); */
                        $code = 'success';
                        $msg = 'Signature has been retrieved';
                    } else {
                        $code = 'warning';
                        $msg = "You have not uploaded your signature yet";
                    }
                } else {
                    $code = 'warning';
                    $msg = 'Your request did not include any data';
                }
            } else {
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    private function getFormStatusDesc($status) {
        switch ($status) {
            case 0:
                return 'Draft';
                break;
            case 1:
                return 'Submitted to DO';
                break;
            case 2:
                return 'Revision requested';
                break;
            case 3:
                return 'Approved by DO';
                break;
            default:
                return null;
                break;
        }
    }

    /**
     * Retrieves the list of submitted DSA forms of the DO's unviersity.
     * @FOSRest\Get(path="/api/get-do-dsa-forms")
     */
    public function getDoDsaFormsAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {
                //$entityManager = $this->getDoctrine()->getManager();
                $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($payload->user_id);
                $univ = $user->getUniversity();
                $students = $this->getDoctrine()->getManager()->getRepository(User::class)->findBy(['university' => $univ]);
                $filledForms = $this->getDoctrine()->getManager()->getRepository(DsaFormFilled::class)->findBy([/* 'status' => [1, 2, 3], */'user' => $students]);
                $data = [];

                foreach ($filledForms as $filledForm) {
                    $form = $filledForm->getDsaForm();
                    $univForm = $this->getDoctrine()->getManager()->getRepository(UniversityDsaForm::class)->findOneBy(['university' => $univ, 'dsa_form' => $form]);
                    $data[] = [
                        'id' => $filledForm->getId(),
                        'student_name' => $filledForm->getUser()->__toString(),
                        'student_email' => $filledForm->getUser()->getEmail(),
                        'univ_name' => $univ->getName(),
                        'pdf_name' => $form->getName(),
                        'pdf_code' => $form->getCode(),
                        'filename' => $filledForm->getFilename(),
                        'status' => $filledForm->getStatus(),
                        'route' => '/dsa/' . $univ->getToken() . '/dsa-forms/' . $univForm->getDsa_form_slug() . '/' . $filledForm->getId(),
                        'status_desc' => $this->getFormStatusDesc($filledForm->getStatus()),
                        'created_at' => date('Y/m/d H:i:s', $filledForm->getCreated_at()),
                    ];
                }
                $code = 'success';
                $msg = "List of available DSA forms";
            } else {
                $data = null;
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    private function getInputMetadata($data, $name) {
        //$data = $item->getContent();
        $res = null;
        $dataCount = count($data);
        for ($i = 0; $i < $dataCount; $i++) {
            $components = $data[$i]['components'];
            $componentsCount = count($data[$i]['components']);
            for ($j = 0; $j < $componentsCount; $j++) {
                $colsCount = count($data[$i]['components'][$j]);
                for ($k = 0; $k < $colsCount; $k++) {
                    $col = $data[$i]['components'][$j][$k];
                    if ($col['content_type'] === 'input' && $col['input']['name'] === $name) {
                        return $col['input']['metadata'];
                    }
                }
            }
        }
        return $res;
    }

    private function insertMetadata($data, $pdfContent) {
        foreach ($data as $key => $value) {
            $data[$key] = $this->getInputMetadata($pdfContent, $key);
            $data[$key]['value'] = $value['value'];
        }
        return $data;
    }

    /**
     * Approves a form
     * @FOSRest\Post(path="/api/do-approve-form")
     */
    public function doApproveFormAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $data = null;
            $filledName = null;

            if ($payload) {
                $formId = $request->get('form_id');
                if ($formId) {
                    //$entityManager = $this->getDoctrine()->getManager();
                    $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($payload->user_id);
                    $filledForm = $this->getDoctrine()->getManager()->getRepository(DsaFormFilled::class)->find($formId);
                    if ($filledForm) {
                        switch ($filledForm->getStatus()) {
                            case 0:
                                $code = 'error';
                                $msg = 'This form has not been submitted yet by the student.';
                                break;
                            case 3:
                                $code = 'warning';
                                $msg = 'This form has been already approved.';
                                break;
                            case 1:
                            case 2:
                                $student = $filledForm->getUser();
                                if ($user->getUniversity() === $student->getUniversity()) {
                                    $item = $filledForm->getDsaForm();
                                    $originDir = $this->container->getParameter('kernel.project_dir') . '/public/dsa_forms/';
                                    $originPath = $originDir . $item->getBase();

                                    if (file_exists($originPath)) {
                                        $pdf = new Pdf($originPath);
                                        $pdfNameWithoutExt = str_replace('.pdf', '', $item->getBase());
                                        $filledName = $pdfNameWithoutExt . '-' . time() . '.pdf';
                                        $destinationDir = $this->getDSAFilledFormsDir();
                                        if (!file_exists($destinationDir)) {
                                            mkdir($destinationDir);
                                        }
                                        $destinationPath = $destinationDir . $filledName;

                                        if ($pdf->fillForm($filledForm->getContentForApproval())->needAppearances()->saveAs($destinationPath)) {
                                            //SELECT JSON_EXTRACT(`content`, '$[*].components[*][*].input') FROM `dsa_form` where `code` = 'sfe_dsa_costs_claim_form_1718_d'
                                            $signaturesInfo = $filledForm->getSignatures();
                                            if ($signaturesInfo) {
                                                $signaturesInfo = $this->insertMetadata($signaturesInfo, $item->getContent());
                                                usort($signaturesInfo, function($a, $b) {
                                                    return strcmp($a['page'], $b['page']);
                                                });
                                                $pdf2 = new Pdf($pdf);
                                                $pdfData = $pdf2->getData();
                                                $pagesCount = 0;
                                                if (preg_match('/NumberOfPages: (\d+)/', $pdfData, $m)) {
                                                    $pagesCount = $m[1];
                                                }
                                                $pdf = new TcpdfFpdi();
                                                $pdf->setPrintHeader(false);
                                                $pdf->setPrintFooter(false);
                                                $pdf->SetAutoPageBreak(false, 0);
                                                $currentPage = 0;

                                                foreach ($signaturesInfo as $signatureInfo) {
                                                    while ($signatureInfo['page'] > $currentPage) {
                                                        $pdf->AddPage();
                                                        $currentPage++;
                                                    }
                                                    $image = imagecreatefrompng($signatureInfo['value']);
                                                    $cropped = imagecropauto($image, IMG_CROP_DEFAULT);
                                                    if ($cropped) {
                                                        imagedestroy($image);
                                                        $image = $cropped;
                                                    }
                                                    $signatureTemp = $destinationDir . '_signature_temp.png';
                                                    imagepng($image, $signatureTemp, 0);
                                                    $imgdata = file_get_contents($signatureTemp);
                                                    //$encoded = base64_encode($imgdata);
                                                    /* $imgdata = base64_decode(str_replace('data:image/png;base64,', '', $signatureInfo['value']));
                                                      $pdf->Image('@' . $imgdata, $signatureInfo['x'], $signatureInfo['y'], $signatureInfo['width'], $signatureInfo['height'], 'PNG', '', '', true, 300, '', false, false, 0, true, false, true); */
                                                    $pdf->Image('@' . $imgdata, $signatureInfo['x'], $signatureInfo['y'], $signatureInfo['width'], $signatureInfo['height'], 'PNG', '', '', true, 300, '', false, false, 0, true, false, true);
                                                    //$pdf->Image($signatureTemp, $signatureInfo['x'], $signatureInfo['y'], $signatureInfo['width'], $signatureInfo['height'], 'PNG', '', '', true, 300, '', false, false, 0, true, false, true);
                                                    unlink($signatureTemp);
                                                }
                                                while ($currentPage < $pagesCount) {
                                                    $pdf->AddPage();
                                                    $currentPage++;
                                                }
                                                $signaturePath = $destinationDir . '_signature_temp.pdf';
                                                $pdf->Output($signaturePath, 'F');
                                                $mergedPath = $destinationDir . '/_signed_temp.pdf';
                                                $pdf = new Pdf($destinationPath);
                                                $pdf->multiStamp($signaturePath)->saveAs($mergedPath);
                                                unlink($signaturePath);
                                                unlink($destinationPath);
                                                rename($mergedPath, $destinationPath);
                                                $this->getDoctrine()->getManager()->persist($filledForm);
                                            }
                                            $filledForm->setStatus(3);
                                            $filledForm->setFilename($filledName);
                                            $this->getDoctrine()->getManager()->persist($filledForm);
                                            $now = time();
                                            $this->createNotification('You have approved a new form', 'The "' . $filledForm->getDsaForm()->getName() . '" submitted on ' . date('Y/m/d H:i:s', $filledForm->getCreated_at()) . ' by <b>' . $student->__toString() . '</b>.', date('Y/m/d H:i:s', $now), $user, 1, 2);
                                            $this->createNotification('Your form has been approved', 'Your ' . $filledForm->getDsaForm()->getName() . ', submitted on ' . date('Y/m/d H:i:s', $filledForm->getCreated_at()) . ', has been approved by <i>' . $user->__toString() . '</i>', date('Y/m/d H:i:s', $now), $student, 1, 1);
                                            $this->getDoctrine()->getManager()->flush();
                                            $data = $filledForm->getStatus();
                                            $code = 'success';
                                            $msg = "The form has been approved.";
                                        } else {
                                            $code = 'error';
                                            $msg = $pdf->getError();
                                            $this->getDoctrine()->getManager()->refresh($filledForm);
                                        }
                                    } else {
                                        $code = 'error';
                                        $msg = 'Specified PDF Form does not exist on the filesystem';
                                        $this->getDoctrine()->getManager()->refresh($filledForm);
                                    }
                                } else {
                                    $code = 'error';
                                    $msg = 'Invalid form supplied.';
                                }
                            default:
                                break;
                        }
                    } else {
                        $code = 'error';
                        $msg = 'Invalid form supplied.';
                    }
                } else {
                    $code = 'error';
                    $msg = 'Invalid form supplied.';
                }
            } else {
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'filename' => $filledName, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Sets DSA Form parameters.
     * @FOSRest\Post(path="/api/set-institute-info")
     */
    public function setDsaFormsParamsAction(Request $request) {
        $user = $this->getRequestUser($request);
        if ($user['code'] !== 'success') {
            return new JsonResponse(['code' => 'error', 'msg' => 'Invalid user', 'data' => null], Response::HTTP_OK);
        }

        $user = $user['user'];
        $params = json_decode($request->getContent(), true);
        $univ = $user->getUniversity();

        if (!$user->isDO() || !$univ) {
            return new JsonResponse(['code' => 'error', 'msg' => 'Invalid parameters.', 'data' => null], Response::HTTP_OK);
        }

        $slug = strtolower(trim($params['slug']));

        $slugChars = str_split($slug);
        $slugLength = count($slugChars);

        for ($i = 0; $i < $slugLength; $i++) {
            $charAscii = ord($slugChars[$i]);
            if (!($charAscii === 45 || ($charAscii >= 48 && $charAscii <= 57) || ($charAscii >= 97 && $charAscii <= 122))) {
                return new JsonResponse(['code' => 'error', 'msg' => 'Institute ID cannot contain whitespaces or special characters.', 'data' => null], Response::HTTP_OK);
            }
        }

        if (!$this->getEntityManager()->getRepository(University::class)->isUnique($univ->getId(), 'token', $slug)) {
            return new JsonResponse(['code' => 'error', 'msg' => 'That identifier has been assigned to another institute.', 'data' => null], Response::HTTP_OK);
        }

        $oldToken = $univ->getToken();
        if ($oldToken !== $slug) {
            $univ->setToken($slug);
            $this->getDoctrine()->getManager()->persist($univ);
        }
        foreach ($params['univ_forms'] as $univForm) {
            $item = $this->getDoctrine()->getManager()->getRepository(UniversityDsaForm::class)->find($univForm['id']);
            if ($item && $item->getUniversity() === $univ) {
                $item->setActive($univForm['active']);
                $item->setDsa_form_slug($univForm['slug']);
                $this->getDoctrine()->getManager()->persist($item);
            }
        }
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse(['code' => 'success', 'msg' => 'Your institute has been updated.', 'data' => ['old_token' => $oldToken, 'new_token' => $slug]], Response::HTTP_OK);
    }

    /**
     * Sets DSA Form parameters.
     * @FOSRest\Get(path="/api/validate-random-qr-code")
     */
    public function setValidateRandomCodeAction(Request $request) {
        try {
            $randomCode = $request->get('random_code');
            //$entityManager = $this->getDoctrine()->getManager();
            $qrCode = $this->getDoctrine()->getManager()->getRepository(QrCode2::class)->findOneBy(['random_code' => $randomCode]);
            if ($qrCode) {
                $data = $qrCode->getRandom_code();
                $code = 'success';
                $msg = "Your changes have been submitted.";
            } else {
                $code = 'error';
                $msg = 'Invalid credentials.';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Fills a form with received data.
     * @FOSRest\Post(path="/api/send-dsa-form-comment")
     */
    public function sendDsaFormCommentAction(Request $request) {
        try {
            $entityId = null;
            $headline = null;
            $index = null;
            $formSlug = $request->get('form_slug');
            $univSlug = $request->get('univ_slug');

            if ($formSlug && $univSlug) {
                $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
                $payload = $this->decodeJWT($jwt);
                if ($payload) {
                    //$entityManager = $this->getDoctrine()->getManager();
                    $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($payload->user_id);
                    $univFromSlug = $this->getDoctrine()->getManager()->getRepository(University::class)->findOneBy(['token' => $univSlug]);
                    $univFromUser = $user->getUniversity();
                    $univForm = $this->getDoctrine()->getManager()->getRepository(UniversityDsaForm::class)->findOneBy(['university' => $univFromUser, 'dsa_form_slug' => $formSlug]);

                    if ($univFromSlug === $univFromUser && $univForm) {
                        $dsaForm = $univForm->getDsa_form();
                        $entityId = $request->get('id');
                        if ($entityId === 0) {
                            $filledForm = new DsaFormFilled();
                            $filledForm->setUser($user);
                            $filledForm->setDsaForm($dsaForm);
                            $filledForm->setCreated_at(time());
                            $filledForm->setContent([]);
                            $filledForm->setStatus(0);
                        } else {
                            $filledForm = $this->getDoctrine()->getManager()->getRepository(DsaFormFilled::class)->findOneBy(['id' => $entityId]);
                        }

                        if ($filledForm && ($filledForm->getUser() === $user || $filledForm->getUser()->getUniversity()->getManager() === $user)) {
                            $newComment = $request->get('comment');
                            $fieldName = $request->get('field_name');
                            $index = $request->get('index');
                            $now = time();
                            $headline = date('Y/m/d H:i:s', $now);
                            $newComment['created_at'] = $now;
                            $newComment['headline'] = $headline;
                            $newComment['status'] = 2;
                            $filledForm->addComment($newComment, $fieldName);
                            if ($filledForm->getStatus() === 1 && $user->isDO()) {
                                $filledForm->setStatus(2);
                            }
                            $this->getDoctrine()->getManager()->persist($filledForm);
                            $this->getDoctrine()->getManager()->flush();

                            $route = 'dsa/' . $univFromUser->getToken() . '/dsa-forms/' . $univForm->getDsa_form_slug() . '/' . $filledForm->getId();

                            if ($user->isStudent()) {
                                $this->createNotification('You have submitted a new comment', 'You commented <b>' . $fieldName . '</b> input field from <b>' . $dsaForm->getName() . '</b>. You can check it <a href="/#/' . $route . '">here</a>.', $headline, $user, 1, 2);
                                $disabOfficers = StaticMembers::executeRawSQL($entityManager, "SELECT * FROM `user` where `university_id` = " . $univFromUser->getId() . " and json_contains(roles, json_array('do')) = 1");
                                foreach ($disabOfficers as $do) {
                                    $doEntity = $this->getDoctrine()->getManager()->getRepository(User::class)->find($do['id']);
                                    $this->createNotification('New comment submitted by ' . $user->__toString(), '<b>' . $fieldName . '</b> input field from "' . $dsaForm->getName() . '" has been commented. You can check it <a href="/#/' . $route . '">here</a>.', $headline, $doEntity, 1, 1);
                                }
                            } else {
                                $this->createNotification('You have submitted a new comment', '<b>' . $fieldName . '</b> in a form submitted by <b>' . $filledForm->getUser()->__toString() . '</b>. You can check it <a href="/#/' . $route . '">here</a>.', $headline, $user, 1, 2);
                                $this->createNotification('New comment submitted by ' . $user->__toString(), '<b>' . $fieldName . '</b> input field from "' . $dsaForm->getName() . '" has been commented. You can check it <a href="/#/' . $route . '">here</a>.', $headline, $filledForm->getUser(), 1, 1);
                            }
                            $msg = "Your comment has been submitted.";
                            $code = 'success';
                            $this->getDoctrine()->getManager()->flush();
                            $entityId = $filledForm->getId();
                        } else {
                            $code = 'error';
                            $msg = 'Invalid PDF supplied';
                        }
                    } else {
                        $code = 'error';
                        $msg = 'Invalid PDF supplied';
                    }
                } else {
                    $code = 'error';
                    $msg = 'Your session has expired. Please, proceed to the login page';
                }
            } else {
                $code = 'warning';
                $msg = 'Your request did not include any data';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $entityId, 'headline' => $headline, 'index' => $index], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Validates DSA office
     * @FOSRest\Get(path="/api/get-dsa-info")
     */
    public function getDsaInfo(Request $request) {
        $params = [
            'slug' => $request->get('slug')
        ];
        $univ = $this->getEntityManager()->getRepository(University::class)->findOneBy(['token' => $params['slug']]);
        if (!$univ) {
            return new JsonResponse(['code' => 'error', 'msg' => 'Invalid parameters', 'data' => null], Response::HTTP_OK);
        }
        $data['dsaName'] = $univ->getName();
        $userData = $this->getRequestUser($request);

        if (isset($userData['user'])) {
            $userUniv = $userData['user']->getUniversity();
            if ($userUniv !== $univ) {
                return new JsonResponse(['code' => 'warning', 'msg' => 'You need to cancel your registration with ' . $userUniv->getName() . ' before accessing another DSA service.', 'data' => $data], Response::HTTP_OK);
            }
        }
        return new JsonResponse(['code' => 'success', 'msg' => 'Access granted', 'data' => $data], Response::HTTP_OK);
    }
    
    /**
     * Retrieve user's signature.
     * @FOSRest\Get(path="/api/get-previous-signature")
     */
    public function getPreviousSignature(Request $request) {
        $user = $this->getRequestUser($request);

        if ($user['code'] !== 'success') {
            return new JsonResponse(['code' => 'error', 'msg' => 'Invalid user', 'data' => null], Response::HTTP_OK);
        }

        $user = $user['user'];
        return new JsonResponse(['code' => 'success', 'msg' => 'Signature loaded', 'data' => $user->getSignature()], Response::HTTP_OK);
    }

}
