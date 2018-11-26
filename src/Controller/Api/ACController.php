<?php

namespace App\Controller\Api;

use App\Entity\AppSettings;
use App\Entity\AssessmentCenter;
use App\Entity\AssessmentCenterService;
use App\Entity\AssessmentCenterServiceAssessor;
use App\Entity\AssessmentCenterUser;
use App\Entity\EA\EaAppointment;
use App\Entity\EA\EaUsers;
use App\Entity\EA\EaUserSettings;
use App\Entity\User;
use App\Entity\UserInvitation;
use App\Utils\StaticMembers;
use DateTime;
use Exception;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AC controller.
 *
 * @Route("/")
 */
class ACController extends MyRestController {

    /**
     * Retrieves the list of active ACs.
     * @FOSRest\Get(path="/api/get-active-assessment-centres")
     */
    public function getActiveAssessmentCentresAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {

                $user = $this->getEntityManager()->getRepository(User::class)->find($payload->user_id);
                $userACs = $user->getAssessmentCentres();
                $entities = $this->getEntityManager()->getRepository(AssessmentCenterUser::class)->getActiveACs();
                $data = [];
                foreach ($entities as $entity) {
                    $admin = $this->getEntityManager()->getRepository(AssessmentCenterUser::class)->getACAdmin($entity);
                    $data[] = [
                        'id' => $entity->getId(),
                        'name' => $entity->getName(),
                        'address' => $entity->getAddress(),
                        'manager' => $admin ? $admin->__toString() : null,
                        'registered' => StaticMembers::contains($userACs, $entity),
                        'route' => 'assessment-centre/' . $entity->getUrl()
                    ];
                }
                $code = 'success';
                $msg = "Active ACs";
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
     * Retrieves data from AC.
     * @FOSRest\Get(path="/api/get-ac-info")
     */
    public function getACInfo(Request $request) {
        try {
            $user = $this->getRequestUser($request);
            if ($user['code'] === 'success') {
                $user = $user['user'];
            } else {
                $user = null;
            }
            
            $params = [
                'slug' => $request->get('slug'),
                'invitation_token' => $request->get('invitation_token'),
            ];
            $ac = $this->getEntityManager()->getRepository(AssessmentCenter::class)->findOneBy(['url' => $params['slug']]);
            $userRole = null;
            $invitation = null;

            if ($params['invitation_token']) {
                $invitation = $this->getEntityManager()->getRepository(UserInvitation::class)->findOneBy(['token' => $params['invitation_token']]);
                if ($ac && $invitation && $ac === $invitation->getAc()) {
                    $userRole = $invitation->getRole();
                } else {
                    return new JsonResponse(['code' => 'error', 'msg' => 'Your request includes some incorrect parameters.<br/>Please, verify your information and try again.', 'data' => null], Response::HTTP_OK);
                }
            }

            $admin = $this->getEntityManager()->getRepository(AssessmentCenterUser::class)->findOneBy(['ac' => $ac, 'is_admin' => 1]);
            $registered = false;

            if ($user) {
                if ($user->isDO()) {
                    return new JsonResponse(['code' => 'error', 'msg' => 'Access denied.', 'data' => null], Response::HTTP_OK);
                }
                $userRole = $user->getRoles()[0];
                $isAdmin = $admin->getUser() === $user;
                $registered = $user->hasRegisteredWith($ac);
                $userData = [
                    'name' => $user->getName(),
                    'last_name' => $user->getLastname(),
                    'email' => $user->getEmail(),
                    'postcode' => $user->getPostcode(),
                    'address' => $user->getAddress(),
                    'password' => 'password',
                    'password_confirm' => 'password',
                ];
                if ($user->isStudent()) {
                    $preRegister = $user->getPre_register();
                    $userData['assessment_form_sent'] = ($preRegister['assessment_form'] !== null);
                    $userData['booking_available'] = ($preRegister['booking_available'] === 1);
                }
            } else {
                $userData = [
                    'name' => '',
                    'last_name' => '',
                    'email' => $invitation ? $invitation->getEmail() : '',
                    'postcode' => '',
                    'address' => '',
                    'password' => '',
                    'password_confirm' => '',
                ];
                $isAdmin = false;
                $userRole = $userRole ? $userRole : 'student';
            }

            $data = [
                'id' => $ac->getId(),
                'registered' => $registered,
                'is_admin' => $isAdmin,
                'role' => $userRole,
                'admin' => $admin ? $admin->getUser()->__toString() : null,
                'user_data' => $userData,
                'slug' => $ac->getUrl(),
                'name' => $ac->getName(),
                'star_assessment_form' => $this->getStarAssessmentForm(),
            ];

            return new JsonResponse(['code' => 'success', 'msg' => 'AC loaded', 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Registers with AC
     * @FOSRest\Post(path="/api/register-with-ac")
     */
    public function registerWithAC(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $acParams = json_decode($request->get('ac'));
            $formData = json_decode($request->get('data'), true);
            $data = null;
            $userOk = false;

            $invitation = null;

            if ($payload) {
                $user = $this->getEntityManager()->getRepository(User::class)->find($payload->user_id);
                $userOk = !is_null($payload);
            } else {
                $userParams = $acParams->user_data;

                $params = [
                    'name' => $userParams->name,
                    'address' => $userParams->address,
                    'email' => $userParams->email,
                    'last_name' => $userParams->last_name,
                    'postcode' => $userParams->postcode,
                    'password' => $userParams->password,
                    'activation_url' => $request->get('url'),
                ];
                $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => $params['email']]);

                if ($user) {
                    $code = 'warning';
                    $msg = 'The email address you entered is already registered';
                    $userOk = false;
                } else {
                    $invitationToken = $request->get('invitation_token');
                    $invitation = $this->getEntityManager()->getRepository(UserInvitation::class)->findOneBy(['token' => $invitationToken]);
                    $role = ($invitation) ? $invitation->getRole() : 'student';
                    $user = new User();
                    $user->setAddress($params['address']);
                    $user->setPostcode(isset($params['postcode']) ? $params['postcode'] : '');
                    $user->setCreatedAt(time());
                    $user->setEmail($params['email']);
                    $user->setLastname($params['last_name']);
                    $user->setName($params['name']);
                    $user->setPassword(sha1($params['password']));
                    $user->setRoles([$role]);
                    $user->setStatus(1);
                    $user->setToken(sha1(StaticMembers::random_str()));
                    $this->getEntityManager()->persist($user);
                    $this->getEntityManager()->flush();
                    $userOk = true;
                }
            }

            if ($userOk) {
                $ac = $this->getEntityManager()->getRepository(AssessmentCenter::class)->find($acParams->id);
                if ($ac) {
                    $acUser = $this->getEntityManager()->getRepository(AssessmentCenterUser::class)->findOneBy(['ac' => $ac, 'user' => $user]);
                    if (!$acUser) {
                        $acUser = new AssessmentCenterUser();
                        $acUser->setAc($ac);
                        $acUser->setIs_admin(0);
                        $acUser->setStatus(1);
                        $acUser->setUser($user);
                        $this->getEntityManager()->persist($acUser);
                        $this->getEntityManager()->flush();
                        StaticMembers::syncEaUser($this->getEntityManager(), $acUser);
                        $preRegisterInfo = $user->getPre_register();
                        $dsaLetter = $request->files->get('dsa_letter');
                        if ($dsaLetter) {
                            $dsaLetterFilename = $user->getId() . '.' . $dsaLetter->getClientOriginalExtension();
                            $preRegisterInfo['dsa_letter'] = $dsaLetterFilename;
                            $user->setPre_register($preRegisterInfo);
                            $this->getEntityManager()->persist($user);
                            $dsaLetter->move($this->getDSALettersDir(), $dsaLetterFilename);
                        }
                        if ($invitation) {
                            $this->getEntityManager()->remove($invitation);
                        }
                        $this->getEntityManager()->flush();
                        $code = 'success';
                        $msg = 'Registration successful.';
                        $data = true;
                    } else {
                        $code = 'warning';
                        $msg = 'You have already registered with this Centre.';
                    }
                } else {
                    $code = 'error';
                    $msg = 'Invalid user.';
                }
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    private function removeUserFromAC($acUser) {
        $user = $acUser->getUser();
        $ac = $acUser->getAc();
        $arrayAux = $this->getEntityManager()->getRepository(EaAppointment::class)->getAppointmentsByUser($user);
        foreach ($arrayAux as $item) {
            if ($item->getService()->getAc() === $ac) {
                $this->getEntityManager()->remove($item);
            }
        }
        $arrayAux = $this->getEntityManager()->getRepository(AssessmentCenterServiceAssessor::class)->findBy(['assessor' => $user]);
        foreach ($arrayAux as $item) {
            if ($item->getService()->getAc() === $ac) {
                $this->getEntityManager()->remove($item);
            }
        }
        $this->getEntityManager()->remove($acUser);
        $preRegisterInfo = $user->getPre_register();
        if (isset($preRegisterInfo['dsa_letter'])) {
            $dsaLetterFilename = $preRegisterInfo['dsa_letter'];
            $dsaLetterPath = $this->getDSALettersDir() . $dsaLetterFilename;
            if (file_exists($dsaLetterPath)) {
                unlink($dsaLetterPath);
            }
            unset($preRegisterInfo['dsa_letter']);
            $user->setPre_register($preRegisterInfo);
            $this->getEntityManager()->persist($user);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * Unregisters from AC
     * @FOSRest\Post(path="/api/unregister-from-ac")
     */
    public function unregisterFromAC(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $data = null;
            $msg = '';

            if ($payload) {
                $acId = $request->get('ac_id');

                $user = $this->getEntityManager()->getRepository(User::class)->find($payload->user_id);
                $ac = $this->getEntityManager()->getRepository(AssessmentCenter::class)->find($acId);
                $acUser = $this->getEntityManager()->getRepository(AssessmentCenterUser::class)->findOneBy(['ac' => $ac, 'user' => $user]);
                if ($acUser) {
                    $this->removeUserFromAC($acUser);
                    $code = 'success';
                    $msg = 'You have cancelled your registration with this Centre.';
                } else {
                    $code = 'warning';
                    $msg = 'You are not registered with this Centre.';
                }
            } else {
                $code = 'error';
                $msg = 'Invalid user.';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Unregisters from AC
     * @FOSRest\Post(path="/api/unregister-user-from-ac")
     */
    public function unregisterUserFromAC(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $data = null;
            $msg = '';

            if ($payload) {
                $acId = $request->get('ac_id');
                $userId = $request->get('user_id');

                $user = $this->getEntityManager()->getRepository(User::class)->find($payload->user_id);
                $member = $this->getEntityManager()->getRepository(User::class)->find($userId);
                $ac = $this->getEntityManager()->getRepository(AssessmentCenter::class)->find($acId);
                $acUser = $this->getEntityManager()->getRepository(AssessmentCenterUser::class)->findOneBy(['ac' => $ac, 'user' => $member]);
                if ($acUser && $ac->getAdmin() === $user) {
                    $this->removeUserFromAC($acUser);
                    $code = 'success';
                    $msg = $member->getFullname() . ' is no longer registered in this Centre.';
                } else {
                    $code = 'warning';
                    $msg = 'You are not registered with this Centre.';
                }
            } else {
                $code = 'error';
                $msg = 'Invalid user.';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Invite need assessor
     * @FOSRest\Post(path="/api/invite-user")
     */
    public function inviteNA(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $data = null;
            $msg = '';

            if ($payload) {
                $invitation = $request->get('invitation');
                $acId = $request->get('ac_id');
                $url = $request->get('url');

                $user = $this->getEntityManager()->getRepository(User::class)->find($payload->user_id);
                $ac = $this->getEntityManager()->getRepository(AssessmentCenter::class)->find($acId);
                $na = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => $invitation['email']]);
                $acUser = $this->getEntityManager()->getRepository(AssessmentCenterUser::class)->findOneBy(['ac' => $ac, 'user' => $na]);
                if (!$acUser && $ac->getAdmin() === $user) {
                    $subject = 'Join my Assessment Centre on Nexus!';
                    $receiverName = $invitation['name'];
                    $receiverEmail = $invitation['email'];
                    $senderName = $user->getFullname();
                    $acName = $ac->getName();
                    $senderMsg = $invitation['text'];
                    $token = StaticMembers::random_str(64);
                    $body = $this->renderView('email/invite_user.html.twig', ['name' => $receiverName, 'sender' => $senderName, 'ac_name' => $acName, 'message' => $senderMsg, 'url' => $url . $token]);
                    $recipients = [$receiverEmail => $receiverName];
                    if (StaticMembers::sendMail($this->getEntityManager()->getRepository(AppSettings::class)->find(1), $subject, $body, $recipients) > 0) {
                        $userInv = new UserInvitation();
                        $userInv->setEmail($receiverEmail);
                        $userInv->setName($receiverName);
                        $userInv->setText($senderMsg);
                        $userInv->setToken($token);
                        $userInv->setUser($user);
                        $userInv->setRole('na');
                        $userInv->setAc($ac);
                        $this->getEntityManager()->persist($userInv);
                        $this->getEntityManager()->flush();
                        $code = 'success';
                        $msg = "Your invitation has been sent.";
                    } else {
                        $code = 'error';
                        $msg = 'The email server is not responding. Please, try again later.';
                    }
                } else {
                    $code = 'warning';
                    $msg = 'This email address already belongs to someone from your Centre.';
                }
            } else {
                $code = 'error';
                $msg = 'Invalid user.';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Update AC service
     * @FOSRest\Post(path="/api/update-ac-service")
     */
    public function updateACService(Request $request) {
        $code = 'error';
        $msg = 'Invalid user.';
        $data = null;

        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {
                $acId = $request->get('ac_id');

                $user = $this->getEntityManager()->getRepository(User::class)->find($payload->user_id);
                $ac = $this->getEntityManager()->getRepository(AssessmentCenter::class)->find($acId);

                if ($ac->getAdmin() === $user) {
                    $service = $request->get('item');
                    $action = $request->get('action');
                    $acService = $this->getEntityManager()->getRepository(AssessmentCenterService::class)->find($service['id']);

                    switch ($action) {
                        case 'Add service':
                            if (!$acService) {
                                $acService = new AssessmentCenterService();
                                $acService->setAc($ac);
                                $acService->setAttendants_number($service['attendants_number']);
                                $acService->setCurrency($service['currency']);
                                $acService->setDescription(isset($service['description']) ? $service['description'] : '');
                                $acService->setDuration($service['duration']);
                                $acService->setName($service['name']);
                                $acService->setPrice($service['price']);
                                $this->getEntityManager()->persist($acService);
                                $code = 'success';
                                $msg = 'The service has been added.';
                            } else {
                                $msg = 'The new service already exists.';
                            }
                            break;
                        case 'Update service':
                            if ($acService) {
                                $acService->setAttendants_number($service['attendants_number']);
                                $acService->setCurrency($service['currency']);
                                $acService->setDescription($service['description']);
                                $acService->setDuration($service['duration']);
                                $acService->setName($service['name']);
                                $acService->setPrice($service['price']);
                                $this->getEntityManager()->persist($acService);
                                $code = 'success';
                                $msg = 'The specified service has been updated.';
                            } else {
                                $msg = 'The specified service does not exist.';
                            }
                            break;
                        case 'Delete service':
                            if ($acService) {
                                $acServiceAssessors = $this->getEntityManager()->getRepository(AssessmentCenterServiceAssessor::class)->findBy(['service' => $acService]);
                                foreach ($acServiceAssessors as $acServiceAssessor) {
                                    $this->getEntityManager()->remove($acServiceAssessor);
                                }
                                $appointments = $this->getEntityManager()->getRepository(EaAppointment::class)->findBy(['service' => $acService]);
                                foreach ($appointments as $appointment) {
                                    $this->getEntityManager()->remove($appointment);
                                }
                                $this->getEntityManager()->remove($acService);
                                $code = 'success';
                                $msg = 'The specified service has been deleted.';
                            } else {
                                $msg = 'The specified service does not exist.';
                            }
                            break;
                        default:
                            break;
                    }
                    if ($code === 'success') {
                        $this->getEntityManager()->flush();
                        $data = $acService->getId();
                    }
                } else {
                    $msg = 'Not allowed.';
                }
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => 'error', 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Update NA services
     * @FOSRest\Post(path="/api/update-na-services")
     */
    public function updateNAServices(Request $request) {
        $code = 'error';
        $msg = 'Invalid user.';
        $data = null;

        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {
                $acId = $request->get('ac_id');
                $userId = $request->get('user_id');

                $user = $this->getEntityManager()->getRepository(User::class)->find($payload->user_id);
                $assessor = $this->getEntityManager()->getRepository(User::class)->find($userId);
                $ac = $this->getEntityManager()->getRepository(AssessmentCenter::class)->find($acId);

                if ($ac->getAdmin() === $user && $assessor->hasRegisteredWith($ac)) {
                    StaticMembers::executeRawSQL($this->getEntityManager(), 'delete from `assessment_center_service_assessor` where `assessor_id` = ' . $assessor->getId() . ' and `ac_service_id` in (select `id` from `assessment_center_service` where `ac_id` = ' . $ac->getId() . ')', false);
                    $services = $request->get('services');
                    foreach ($services as $service) {
                        $serviceEntity = $this->getEntityManager()->getRepository(AssessmentCenterService::class)->find($service['id']);
                        if ($serviceEntity && $serviceEntity->getAc() === $ac) {
                            $naService = new AssessmentCenterServiceAssessor();
                            $naService->setAssessor($assessor);
                            $naService->setService($serviceEntity);
                            $this->getEntityManager()->persist($naService);
                        }
                    }
                    $this->getEntityManager()->flush();
                    $code = 'success';
                    $msg = 'Services updated.';
                } else {
                    $msg = 'Not allowed.';
                }
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Update AC settings
     * @FOSRest\Post(path="/api/update-ac-settings")
     */
    public function updateACSettings(Request $request) {
        $code = 'error';
        $msg = 'Invalid user.';
        $data = null;

        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {
                $acId = $request->get('ac_id');
                $settings = $request->get('settings');

                $user = $this->getEntityManager()->getRepository(User::class)->find($payload->user_id);
                $ac = $this->getEntityManager()->getRepository(AssessmentCenter::class)->find($acId);

                if ($ac && $ac->getAdmin() === $user) {
                    $uniqueName = false;
                    if ($this->getEntityManager()->getRepository(AssessmentCenter::class)->isUniqueField($ac->getId(), 'name', $settings['name'])) {
                        $ac->setName($settings['name']);
                        $uniqueName = true;
                    }
                    $uniqueSlug = false;

                    if ($this->getEntityManager()->getRepository(AssessmentCenter::class)->isUniqueField($ac->getId(), 'url', $settings['token'])) {
                        $ac->setUrl($settings['token']);
                        $uniqueSlug = true;
                    }
                    $ac->setTelephone($settings['telephone']);
                    $ac->setAddress($settings['address']);
                    $ac->setAvailability_type($settings['availability_type']);
                    $this->getEntityManager()->persist($ac);
                    $this->getEntityManager()->flush();

                    if (!$uniqueName) {
                        $code = 'warning';
                        $msg = 'That name belongs to another Assessment Centre.';
                    } else if (!$uniqueSlug) {
                        $code = 'warning';
                        $msg = 'That slug belongs to another Assessment Centre.';
                    } else {
                        $code = 'success';
                        $msg = 'Centre updated.';
                    }
                } else {
                    $msg = 'Not allowed.';
                }
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Available appointment dates
     * @FOSRest\Get(path="/api/get-allowed-dates")
     */
    public function getServiceAllowedDates(Request $request) {
        $code = 'error';
        $msg = 'Invalid user.';
        $data = null;

        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {
                $acId = $request->get('ac_id');

                $user = $this->getEntityManager()->getRepository(User::class)->find($payload->user_id);
                $ac = $this->getEntityManager()->getRepository(AssessmentCenter::class)->find($acId);

                if ($user && $ac && $user->isStudent() && $user->hasRegisteredWith($ac)) {
                    $currTimestamp = time();
                    $minTimestamp = $currTimestamp + 86400;
                    $maxTimestamp = $currTimestamp + 2592000;
                    $minDate = date('Y-m-d', $minTimestamp);
                    $maxDate = date('Y-m-d', $maxTimestamp);
                    $allowedDates = [];

                    for ($i = $minTimestamp; $i <= $maxTimestamp; $i += 86400) {
                        $date = date('N', $i);
                        if (!in_array($date, [6, 7])) {
                            $allowedDates[] = date('Y-m-d', $i);
                        }
                    }

                    $data = [
                        'min_date' => $minDate,
                        'max_date' => $maxDate,
                        'allowed_dates' => $allowedDates,
                    ];
                    $code = 'success';
                    $msg = 'Dates loaded.';
                } else {
                    $msg = 'Invalid parameters.';
                }
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Available appointment hours by date
     * @FOSRest\Get(path="/api/get-available-hours")
     */
    public function getServiceAvailableHours(Request $request) {
        $code = 'error';
        $msg = 'Invalid user.';
        $data = null;

        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {
                $serviceId = $request->get('service_id');


                $user = $this->getEntityManager()->getRepository(User::class)->find($payload->user_id);
                $service = $this->getEntityManager()->getRepository(AssessmentCenterService::class)->find($serviceId);
                $ac = $service->getAc();

                if ($service && $user && $user->isStudent() && $user->hasRegisteredWith($ac)) {
                    $assessorId = $request->get('assessor_id');
                    $acAvailabilityType = $ac->getAvailability_type();
                    $assessors = [];

                    if ($assessorId && $acAvailabilityType === 'Individual') {
                        $assessor = $this->getEntityManager()->getRepository(User::class)->find($assessorId);
                        if ($assessor->isNA() && $assessor->hasRegisteredWith($ac)) {
                            $code = 'success';
                        }
                        $assessors[] = $assessor;
                    } else if ($acAvailabilityType === 'Combined') {
                        $acServiceAssessors = $this->getEntityManager()->getRepository(AssessmentCenterServiceAssessor::class)->findBy(['service' => $service]);
                        foreach ($acServiceAssessors as $acServiceAssessor) {
                            $assessor = $acServiceAssessor->getAssessor();
                            if ($assessor->isNA() && $assessor->hasRegisteredWith($ac)) {
                                $assessors[] = $assessor;
                            }
                        }
                        $code = 'success';
                    }

                    if ($code === 'success') {
                        $data = [];
                        $serviceDuration = $service->getDuration();
                        $date = $request->get('date');

                        foreach ($assessors as $assessor) {
                            $hours = [];
                            $scheduledAppointments = $this->getEntityManager()->getRepository(EaAppointment::class)->getAppointmentsByAssessorAndDate($assessor, $date);
                            $fullDate = new DateTime("$date 09:00");

                            do {
                                $hour = $fullDate->format('H:i');
                                $availableHour = true;
                                foreach ($scheduledAppointments as $appointment) {
                                    $start = $appointment->getStart_datetime();
                                    $end = $appointment->getEnd_datetime();
                                    if ($fullDate >= $start && $fullDate < $end) {
                                        $availableHour = false;
                                        break;
                                    }
                                }
                                if ($availableHour) {
                                    $hourObj = ['name' => $hour];
                                    if (!in_array($hourObj, $data, true)) {
                                        $data[] = $hourObj;
                                    }
                                    $hours[] = ['name' => $hour];
                                }
                                $fullDate->modify("+$serviceDuration minutes");
                            } while ($hour < '18:00');
                            /* $diff = array_diff($data, $hours);
                              array_merge($data, $diff); */
                        }
                        usort($data, function($a, $b) {
                            return strcmp($a['name'], $b['name']);
                        });
                        $msg = 'Hours loaded.';
                    } else {
                        $msg = 'Invalid parameters.';
                    }
                } else {
                    $msg = 'Invalid parameters.';
                }
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Create a new appointment
     * @FOSRest\Post(path="/api/create-appointment")
     */
    public function createAppointment(Request $request) {
        $code = 'error';
        $msg = 'Invalid user.';
        $data = null;

        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {
                $params = $request->get('appointment');

                $user = $this->getEntityManager()->getRepository(User::class)->find($payload->user_id);
                $service = $this->getEntityManager()->getRepository(AssessmentCenterService::class)->find($params['service']['id']);
                $ac = $service->getAc();

                if ($service && $user && $user->isStudent() && $user->hasRegisteredWith($ac)) {
                    $acAvailabilityType = $ac->getAvailability_type();
                    $assessor = null;
                    $startDateStr = $params['date'] . ' ' . $params['hour'];
                    $startDateTime = new DateTime($startDateStr);

                    if (isset($params['assessor']['id']) && $acAvailabilityType === 'Individual') {
                        $assessor = $this->getEntityManager()->getRepository(User::class)->find($params['assessor']['id']);
                        $appointmentOnDate = $this->getEntityManager()->getRepository(EaAppointment::class)->isAssessorAvailableByDate($assessor, $startDateTime);
                        if ($assessor->isEnabledInAC($this->getEntityManager(), $ac) && count($appointmentOnDate) === 0) {
                            $code = 'success';
                        } else {
                            $assessor = null;
                            $msg = 'The selected provider is not available.';
                        }
                    } else if ($acAvailabilityType === 'Combined') {
                        $acServiceAssessors = $this->getEntityManager()->getRepository(AssessmentCenterServiceAssessor::class)->findBy(['service' => $service]);
                        foreach ($acServiceAssessors as $acServiceAssessor) {
                            $assessor = $acServiceAssessor->getAssessor();
                            if ($assessor->isNA() && $assessor->hasRegisteredWith($ac) && $assessor->isEnabledInAC($this->getEntityManager(), $ac)) {
                                $appointmentOnDate = $this->getEntityManager()->getRepository(EaAppointment::class)->isAssessorAvailableByDate($assessor, $startDateTime);
                                if (count($appointmentOnDate) === 0) {
                                    $code = 'success';
                                    break;
                                } else {
                                    $assessor = null;
                                    $msg = 'No providers available.';
                                }
                            }
                        }
                    }

                    if ($code === 'success') {
                        $data = [];
                        $endDateTime = new DateTime($startDateStr);
                        $endDateTime->modify('+' . $service->getDuration() . ' minutes');
                        $newAppointment = new EaAppointment();
                        $newAppointment->setBook_datetime(new DateTime());
                        $newAppointment->setEnd_datetime($endDateTime);
                        $newAppointment->setHash(StaticMembers::random_str(32));
                        $newAppointment->setIs_unavailable(false);
                        $newAppointment->setProvider($assessor);
                        $newAppointment->setService($service);
                        $newAppointment->setStart_datetime($startDateTime);
                        $newAppointment->setStudent($user);
                        $this->getEntityManager()->persist($newAppointment);
                        $msg = 'Your appointment has been scheduled.';

                        $headline = date('Y/m/d H:i:s', time());
                        $this->createNotification('New appointment', 'A new appointment has been scheduled by ' . $user->getFullname() . ' from ' . $ac->getName(), $headline, $assessor, 1, 1);
                        $this->createNotification('New appointment', 'You have scheduled a new appointment with ' . $assessor->getFullname(), $headline, $user, 1, 2);
                        $this->getEntityManager()->flush();

                        $url = $request->get('home_url');
                        try {
                            $body = $this->renderView('email/new_appointment_student.html.twig', ['name' => $user->getFullname(), 'home_url' => $url, 'service' => $service->getName(), 'provider' => $assessor->getFullname(), 'date_time' => $startDateStr]);
                            StaticMembers::sendMail($this->getEntityManager()->getRepository(AppSettings::class)->find(1), 'New appointment created on Nexus', $body, [$user->getEmail() => $user->getFullname()]);
                            $body = $this->renderView('email/new_appointment_provider.html.twig', ['name' => $assessor->getFullname(), 'home_url' => $url, 'student' => $user->getFullname(), 'service' => $service->getName(), 'date_time' => $startDateStr]);
                            StaticMembers::sendMail($this->getEntityManager()->getRepository(AppSettings::class)->find(1), 'New appointment created on Nexus', $body, [$assessor->getEmail() => $assessor->getFullname()]);
                        } catch (Exception $exc) {
                            
                        }
                    }
                }
            } else {
                $msg = 'Invalid parameters.';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Unavailability period
     * @FOSRest\Post(path="/api/set-unavailable-period")
     */
    public function setUnavailablePeriod(Request $request) {
        $code = 'error';
        $msg = 'Invalid user.';
        $data = null;
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            if ($payload) {
                $params = $request->get('period');
                $acId = $request->get('ac_id');
                $user = $this->getEntityManager()->getRepository(User::class)->find($payload->user_id);
                $ac = $this->getEntityManager()->getRepository(AssessmentCenter::class)->find($acId);
                if ($user && $user->isNA() && $user->hasRegisteredWith($ac)) {
                    $startDateTime = new DateTime($params['start_date'] . ' ' . $params['start_hour']);
                    $endDateTime = new DateTime($params['end_date'] . ' ' . $params['end_hour']);
                    $repository = $this->getEntityManager()->getRepository(EaAppointment::class);
                    $existingAppointment = false;

                    $extendByLower = $repository->extendUnavailabilityByLowerLimit($user, $startDateTime, $endDateTime);
                    if (count($extendByLower) > 0) {
                        $newAppointment = $repository->find($extendByLower[0]['id']);
                        $newAppointment->setStart_datetime($startDateTime);
                        $newAppointment->setBook_datetime(new DateTime());
                        $this->getEntityManager()->persist($newAppointment);
                        $this->getEntityManager()->flush();
                        $existingAppointment = true;
                    }

                    $extendByUpper = $repository->extendUnavailabilityByUpperLimit($user, $startDateTime, $endDateTime);
                    if (count($extendByUpper) > 0) {
                        $newAppointment = $repository->find($extendByUpper[0]['id']);
                        $newAppointment->setEnd_datetime($endDateTime);
                        $newAppointment->setBook_datetime(new DateTime());
                        $this->getEntityManager()->persist($newAppointment);
                        $this->getEntityManager()->flush();
                        $existingAppointment = true;
                    }

                    if ($repository->unavailabilityInRange($user, $startDateTime, $endDateTime)) {
                        $existingAppointment = true;
                    }

                    if (!$existingAppointment) {
                        $newAppointment = new EaAppointment();
                        $newAppointment->setStart_datetime($startDateTime);
                        $newAppointment->setEnd_datetime($endDateTime);
                        $newAppointment->setHash(StaticMembers::random_str(32));
                        $newAppointment->setIs_unavailable(true);
                        $newAppointment->setProvider($user);
                        $newAppointment->setBook_datetime(new DateTime());
                        $this->getEntityManager()->persist($newAppointment);
                        $headline = date('Y/m/d H:i:s', time());
                        $this->createNotification('New unavailable period', 'You have set a new unavailable period from ' . $startDateTime->format('YYYY-MM-DD H:i') . ' to ' . $endDateTime->format('YYYY-MM-DD H:i'), $headline, $user, 1, 2);
                        $this->getEntityManager()->flush();
                    }
                    $msg = 'Unavailable period saved.';
                    $code = 'success';


                    /* $subject = 'Activate your Nexus account';
                      $fullName = $user->getName() . ' ' . $user->getLastname();
                      $body = $this->renderView('email/signup.html.twig', ['name' => $fullName, 'url' => $params['activation_url'] . '/' . $user->getToken()]);
                      $recipients = [$user->getEmail() => $fullName];

                      if (StaticMembers::sendMail($this->getEntityManager()->getRepository(AppSettings::class)->find(1), $subject, $body, $recipients) > 0) {
                      $code = 'success';
                      $msg = "Thanks for joining us! An email has been sent to your address with instructions on how to activate your account.";
                      $this->getEntityManager()->flush();
                      } */
                }
            } else {
                $msg = 'Invalid parameters.';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Toggle user status
     * @FOSRest\Post(path="/api/set-ac-user-status")
     */
    public function setACUserStatus(Request $request) {
        $code = 'error';
        $msg = 'Invalid parameters.';
        $data = null;
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            if ($payload) {
                $acId = $request->get('ac_id');
                $memberId = $request->get('user_id');
                $status = $request->get('status');
                $user = $this->getEntityManager()->getRepository(User::class)->find($payload->user_id);
                $acMember = $this->getEntityManager()->getRepository(User::class)->find($memberId);
                $ac = $this->getEntityManager()->getRepository(AssessmentCenter::class)->find($acId);

                if ($user && $ac->getAdmin() === $user && $acMember && $acMember->hasRegisteredWith($ac) && !is_null($status)) {
                    $acUser = $this->getEntityManager()->getRepository(AssessmentCenterUser::class)->findOneBy(['ac' => $ac, 'user' => $acMember]);
                    $acUser->setStatus($status);
                    $this->getEntityManager()->persist($acUser);
                    $this->getEntityManager()->flush();
                    $code = 'success';
                    $msg = 'User status updated.';
                }
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * @FOSRest\Post(path="/api/get-calendar-events")
     */
    public function getCalendarEvents(Request $request) {
        try {
            $userInfo = $this->getRequestUser($request);
            if ($userInfo['code'] === 'success') {
                $currentUser = $userInfo['user'];
                $bookingId = $request->get('id');
                $bookingEntity = $this->getEntityManager()->getRepository(EaAppointment::class)->find($bookingId);
                if ($currentUser->isStudent() && $bookingEntity->getStudent() === $currentUser) {
                    $headline = date('Y/m/d H:i:s', time());
                    $this->getEntityManager()->remove($bookingEntity);
                    $this->createNotification('Appointment cancelled', 'The appointment scheduled with you by' . $currentUser->getFullname() . ' from ' . $bookingEntity->getService()->getAc()->getName() . ' between ' . $bookingEntity->getStart_datetime()->format('Y-m-d H:i') . ' and ' . $bookingEntity->getEnd_datetime()->format('Y-m-d H:i') . ' has been cancelled by the student.', $headline, $bookingEntity->getProvider(), 1, 1);
                    $this->createNotification('Appointment cancelled', 'You have cancelled your appointment with ' . $bookingEntity->getProvider()->getFullname() . ', scheduled between ' . $bookingEntity->getStart_datetime()->format('Y-m-d H:i') . ' and ' . $bookingEntity->getEnd_datetime()->format('Y-m-d H:i'), $headline, $currentUser, 1, 2);
                    $this->getEntityManager()->flush();
                    return new JsonResponse(['code' => 'success', 'msg' => 'The appointment has been cancelled.', 'data' => null], Response::HTTP_OK);
                } else if ($currentUser->isNA() && $bookingEntity->getProvider() === $currentUser) {
                    
                }
            }
            return new JsonResponse(['code' => $userInfo['code'], 'msg' => $userInfo['msg'], 'data' => null], Response::HTTP_OK);
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => null], Response::HTTP_OK);
        }
    }

    private function getStarAssessmentForm() {
        $dir = $this->container->getParameter('kernel.project_dir') . '/src/DataFixtures/data/star_assessment_form.json';
        $res = json_decode(file_get_contents($dir), true);
        return $res;
    }

}
