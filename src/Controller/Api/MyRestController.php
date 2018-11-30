<?php

namespace App\Controller\Api;

use App\Entity\DsaFormFilled;
use App\Entity\EA\EaUsers;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Firebase\JWT\JWT;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MyRestController extends FOSRestController {

    private $key = "vUrQrZL50m7qL3uosytRJbeW8fzSwUqd";
    private $entityManager = null;

    function getKey() {
        return $this->key;
    }

    public function getEntityManager(): ObjectManager {
        if (!$this->entityManager) {
            $this->entityManager = $this->getDoctrine()->getManager();
        }
        return $this->entityManager;
    }

    protected function getRequestUser(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = JWT::decode($jwt, $this->key, ['HS256']);
            if ($payload->exp > time() && $payload->ip === $request->getClientIp()) {
                $user = $this->getEntityManager()->getRepository(User::class)->find($payload->user_id);
                if ($user) {
                    return ['code' => 'success', 'msg' => 'User verified', 'user' => $user];
                }
            }
            return ['code' => 'error', 'msg' => 'Invalid credentials.'];
        } catch (Exception $exc) {
            return ['code' => 'error', 'msg' => 'Invalid credentials.'];
        }
    }

    /* protected function addMonthToDate($date_str, $months) {
      $date = new DateTime($date_str);
      // We extract the day of the month as $start_day
      $start_day = $date->format('j');
      // We add 1 month to the given date
      $date->modify("+{$months} month");
      // We extract the day of the month again so we can compare
      $end_day = $date->format('j');
      if ($start_day != $end_day) {
      // The day of the month isn't the same anymore, so we correct the date
      $date->modify('last day of last month');
      }

      return $date;
      } */

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
        $this->getEntityManager()->persist($notif);
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
    public function getFileAction(Request $request) {
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

    public function updateEaUser(User $user, $action) {
        switch ($action) {
            case 'create':
                $eaUser = new EaUsers();
                $eaUser->setAddress($user->getAddress());
                $eaUser->setEmail($user->getEmail());
                $eaUser->setFirstName($user->getName());
                $eaUser->setLastName($user->getLastname());
                $eaUser->setId_roles($user->getEaRole());
                $eaUser->setZipCode($user->getPostcode());
                $this->getEntityManager()->persist($eaUser);
                break;
        }
    }
    
    protected function getStarAssessmentForm($acFormProgress) {
        $dir = $this->container->getParameter('kernel.project_dir') . '/src/DataFixtures/data/star_assessment_form.json';
        $emptyContent = json_decode(file_get_contents($dir), true);
        $formContent = $emptyContent;

        $dataCount = count($formContent);
        for ($i = 0; $i < $dataCount; $i++) {
            $components = $formContent[$i]['components'];
            $componentsCount = count($formContent[$i]['components']);
            for ($j = 0; $j < $componentsCount; $j++) {
                $colsCount = count($formContent[$i]['components'][$j]);
                for ($k = 0; $k < $colsCount; $k++) {
                    $col = $formContent[$i]['components'][$j][$k];
                    if ($col['content_type'] === 'input') {
                        $name = $col['input']['name'];
                        if (isset($acFormProgress[$name])) {
                            $formContent[$i]['components'][$j][$k]['input']['value'] = $acFormProgress[$name];
                        }
                        //Input group
                    } else if ($col['content_type'] === 'input_group') {
                        $inputGroupName = $col['name'];
                        $rowsCount = 0;
                        $rows = [];
                        if (isset($acFormProgress[$inputGroupName])) {
                            $rowsCount = $acFormProgress[$inputGroupName];
                            $models = $col['model'];
                            for ($l = 1; $l <= $rowsCount; $l++) {
                                $newRow = [];
                                foreach ($models as $model) {
                                    $newName = $model['input']['name'] .= " $l";
                                    $model['input']['name'] = $newName;
                                    if (isset($acFormProgress[$newName])) {
                                        $model['input']['value'] = $acFormProgress[$newName];
                                    }
                                    $newRow[] = $model;
                                }
                                $rows[] = $newRow;
                            }
                        }
                        $formContent[$i]['components'][$j][$k]['rows'] = $rows;
                    }
                }
            }
        }
        return [$emptyContent, $formContent];
    }

}
