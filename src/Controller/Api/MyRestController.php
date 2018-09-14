<?php

namespace App\Controller\Api;

use Exception;
use Firebase\JWT\JWT;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as FOSRest;

class MyRestController extends FOSRestController {

    private $key = "vUrQrZL50m7qL3uosytRJbeW8fzSwUqd";
    
    public function getDSALettersDir() {
        return $this->container->getParameter('kernel.project_dir') . '/app_data/dsa_letters/';
    }
    
    public function getDSAFilledFormsDir() {
        return $this->container->getParameter('kernel.project_dir') . '/app_data/dsa_forms_filled/';
    }
    
    public function encodeJWT($payload) {
        try {
            return JWT::encode($payload, $this->key);
        } catch (Exception $exc) {
            return null;
        }
    }

    public function decodeJWT($jwt) {
        try {
            $payload = JWT::decode($jwt, $this->key, ['HS256']);
            return ($payload->exp > time()) ? $payload : null;
        } catch (Exception $exc) {
            return null;
        }
    }
    
    public function createNotification($title, $subtitle, $headline, $user, $status, $type) {
        $notif = new Notification();
        $notif->setTitle($title);
        $notif->setSubtitle($subtitle);
        $notif->setHeadline($headline);
        $notif->setUser($user);
        $notif->setStatus($status);
        $notif->setType($type);
        $notif->setCreated_at(time());
        return $notif;
    }
    
    public function dateDiff($time1, $time2, $precision = 6) {
        // If not numeric then convert texts to unix timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }

        // If time1 is bigger than time2
        // Then swap time1 and time2
        if ($time1 > $time2) {
            $ttime = $time1;
            $time1 = $time2;
            $time2 = $ttime;
        }

        // Set up intervals and diffs arrays
        $intervals = array('year', 'month', 'day', 'hour', 'minute', 'second');
        $diffs = array();

        // Loop thru all intervals
        foreach ($intervals as $interval) {
            // Create temp time from time1 and interval
            $ttime = strtotime('+1 ' . $interval, $time1);
            // Set initial values
            $add = 1;
            $looped = 0;
            // Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
                // Create new temp time from time1 and interval
                $add++;
                $ttime = strtotime("+" . $add . " " . $interval, $time1);
                $looped++;
            }

            $time1 = strtotime("+" . $looped . " " . $interval, $time1);
            $diffs[$interval] = $looped;
        }

        $count = 0;
        $times = array();
        // Loop thru all diffs
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
            // Add value and interval 
            // if value is bigger than 0
            if ($value > 0) {
                // Add s if value is not 1
                if ($value != 1) {
                    $interval .= "s";
                }
                // Add value and interval to times array
                $times[] = $value . " " . $interval;
                $count++;
            }
        }
        array_splice($times, 1);
        // Return string with times
        return implode(", ", $times);
    }
    
    
    /**
     * Returns a filled PDF.
     * @FOSRest\Get(path="/api/get-file")
     */
    protected function getFileAction(Request $request) {
        try {
            $file = $request->get('file');
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $data = null;

            if ($payload) {
                $entityManager = $this->getDoctrine()->getManager();
                $filledForm = $entityManager->getRepository(DsaFormFilled::class)->find($file);
                if ($filledForm) {
                    $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                    $filledUser = $filledForm->getUser();
                    $userId = null;
                    if ($filledUser === $user) {
                        $userId = $user->getId();
                    } else if ($filledUser->getUniversity() === $user->getUniversity()) {
                        $userId = $filledUser->getId();
                    }
                    $filename = $filledForm->getFilename();
                    $file = new File($this->getDSAFilledFormsDir() . $filename);
                    return $this->file($file);
                } else {
                    $code = 'error';
                    $msg = 'File not found';
                }
            } else {
                $code = 'error';
                $msg = 'Invalid request';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => null], Response::HTTP_OK);
        }
    }

    
}
