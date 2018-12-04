<?php

namespace App\Controller\Api;

use App\Entity\Notification;
use App\Entity\User;
use Exception;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Notifications controller.
 *
 * @Route("/")
 */
class NotificationsController extends MyRestController {

    private function deleteActivsOrNotifs(Request $request, $type) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $data = $request->get('data', null);
            $res = ['notif' => [], 'activ' => []];
            if ($data) {
                $payload = $this->decodeJWT($jwt);
                if ($payload) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                    foreach ($data as $value) {
                        $entity = $entityManager->getRepository(Notification::class)->findOneBy(['id' => $value, 'user' => $user, 'type' => $type]);
                        if ($entity) {
                            $entityManager->remove($entity);
                        }
                    }
                    $entityManager->flush();
                    $res['notif'] = $this->getNotifications($payload->user_id, 1);
                    $res['activ'] = $this->getNotifications($payload->user_id, 2);
                    $code = 'success';
                    $msg = 'Activities removed';
                } else {
                    $code = 'error';
                    $msg = 'Your session has expired. Please, proceed to the login page';
                }
            } else {
                $code = 'warning';
                $msg = 'Your request did not include any data';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $res], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => []], Response::HTTP_OK);
        }
    }

    /**
     * Deletes user notifications.
     * @FOSRest\Post(path="/api/delete-activities")
     */
    public function deleteActivitiesAction(Request $request) {
        return $this->deleteActivsOrNotifs($request, 2);
    }

    /**
     * Deletes user notifications.
     * @FOSRest\Post(path="/api/delete-notifications")
     */
    public function deleteNotificationsAction(Request $request) {
        return $this->deleteActivsOrNotifs($request, 1);
    }

    private function getNotifications($user_id, $type) {
        $res = [];
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($user_id);
        $entities = $entityManager->getRepository(Notification::class)->findBy(['user' => $user, 'type' => $type], ['created_at' => 'desc']);
        $notifCount = count($entities);
        $counter = 0;
        foreach ($entities as $entity) {
            if ($counter < 5) {
                $currentTime = time();
                $diff = $this->dateDiff($entity->getCreated_at(), $currentTime);
                $res[] = [
                    'id' => $entity->getId(),
                    'created_at' => $entity->getHeadline(),
                    'title' => $entity->getTitle(),
                    'subtitle' => $entity->getSubtitle(),
                    'headline' => $diff
                ];
                $counter++;
            } else {
                break;
            }
        }
        return ['count' => $notifCount, 'items' => $res];
    }

    /**
     * Deletes user notifications.
     * @FOSRest\Post(path="/api/get-notifications")
     */
    public function getNotificationsAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $res = ['notif' => [], 'activ' => []];
            $payload = $this->decodeJWT($jwt);
            if ($payload) {
                $res['notif'] = $this->getNotifications($payload->user_id, 1);
                $res['activ'] = $this->getNotifications($payload->user_id, 2);
                $code = 'success';
                $msg = 'Notifications updated';
            } else {
                $code = 'error';
                $msg = 'Your session has expired. Please, proceed to the login page';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $res], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => []], Response::HTTP_OK);
        }
    }

}
