<?php

namespace App\Controller\Api;

use App\Entity\AppSettings;
use App\Entity\AssessmentCenter;
use App\Entity\AssessmentCenterService;
use App\Entity\AssessmentCenterServiceAssessor;
use App\Entity\AssessmentCenterUser;
use App\Entity\User;
use App\Entity\UserInvitation;
use App\Utils\StaticMembers;
use Exception;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Brand controller.
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
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                $userACs = $user->getAssessmentCentres();
                $entities = $entityManager->getRepository(AssessmentCenterUser::class)->getActiveACs();
                $data = [];
                foreach ($entities as $entity) {
                    $admin = $entityManager->getRepository(AssessmentCenterUser::class)->getACAdmin($entity);
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
     * Retrieves an university.
     * @FOSRest\Get(path="/api/get-ac-info")
     */
    public function getACInfo(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $slug = $request->get('slug');
            $invitationToken = $request->get('invitation_token');
            $entityManager = $this->getDoctrine()->getManager();
            $user = $payload ? $entityManager->getRepository(User::class)->find($payload->user_id) : null;
            $ac = $entityManager->getRepository(AssessmentCenter::class)->findOneBy(['url' => $slug]);
            $data = null;
            $code = '';
            $msg = '';
            $userRole = '';

            if ($invitationToken) {
                $invitation = $entityManager->getRepository(UserInvitation::class)->findOneBy(['token' => $invitationToken]);
                if ($ac && $invitation && $ac === $invitation->getAc()) {
                    $userRole = $invitation->getRole();
                } else {
                    $code = 'error';
                }
            }
            if ($ac && $code === '') {
                $admin = $entityManager->getRepository(AssessmentCenterUser::class)->findOneBy(['ac' => $ac, 'is_admin' => 1]);
                $registered = false;
                $userData = [
                    'name' => '',
                    'last_name' => '',
                    'email' => '',
                    'postcode' => '',
                    'address' => '',
                    'password' => '',
                    'password_confirm' => '',
                ];
                $students = [];
                $needsAssessors = [];
                $services = [];
                $settings = [];

                if ($user) {
                    if (!$user->isDO()) {
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
                        if ($isAdmin) {
                            $acServices = $ac->getAssessment_center_services();
                            $acUsers = $ac->getAssessment_center_users();

                            foreach ($acUsers as $acUser) {
                                $userAux = $acUser->getUser();
                                if ($userAux->isStudent()) {
                                    $students[] = [
                                        'id' => $userAux->getId(),
                                        'name' => $userAux->getFullname(),
                                        'institute' => $userAux->getUniversity()->getName(),
                                    ];
                                } else if ($userAux->isNA()) {
                                    $naServicesAux = $entityManager->getRepository(AssessmentCenterServiceAssessor::class)->findNAServicesByAC($ac, $userAux);
                                    $naServices = [];
                                    foreach ($naServicesAux as $naService) {
                                        $serviceAux = $naService->getService();
                                        $naServices[] = [
                                            'id' => $serviceAux->getId(),
                                            'name' => $serviceAux->getName(),
                                            'description' => $serviceAux->getDescription(),
                                            'duration' => $serviceAux->getDuration(),
                                            'attendants_number' => $serviceAux->getAttendants_number(),
                                            'price' => $serviceAux->getPrice(),
                                            'currency' => $serviceAux->getCurrency(),
                                        ];
                                    }
                                    $needsAssessors[] = [
                                        'id' => $userAux->getId(),
                                        'name' => $userAux->getFullname(),
                                        'email' => $userAux->getEmail(),
                                        'services' => $naServices
                                    ];
                                }
                            }

                            foreach ($acServices as $acService) {
                                $services[] = [
                                    'id' => $acService->getId(),
                                    'name' => $acService->getName(),
                                    'description' => $acService->getDescription(),
                                    'duration' => $acService->getDuration(),
                                    'attendants_number' => $acService->getAttendants_number(),
                                    'price' => $acService->getPrice(),
                                    'currency' => $acService->getCurrency(),
                                ];
                            }
                            
                            $settings['availability_type'] = $ac->getAvailability_type();
                            $settings['name'] = $ac->getName();
                            $settings['token'] = $ac->getUrl();
                            $settings['address'] = $ac->getAddress();
                            $settings['telephone'] = $ac->getTelephone();
                        }
                    } else {
                        $code = 'error';
                        $msg = 'Access denied';
                    }
                } else {
                    $isAdmin = false;
                    if ($userRole === '') {
                        $userRole = 'student';
                    }
                }

                if ($code === '') {
                    $data = [
                        'id' => $ac->getId(),
                        'name' => $ac->getName(),
                        'token' => $ac->getUrl(),
                        'registered' => $registered,
                        'is_admin' => $isAdmin,
                        'role' => $userRole,
                        'admin' => $admin ? $admin->getUser()->__toString() : null,
                        'user_data' => $userData,
                        'students' => $students,
                        'needs_assessors' => $needsAssessors,
                        'services' => $services,
                        'settings' => $settings,
                    ];
                    $code = 'success';
                    $msg = 'AC loaded';
                }
            } else {
                $code = 'error';
                $msg = 'Invalid parameter.';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
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
            $data = null;
            $userOk = false;
            $entityManager = $this->getDoctrine()->getManager();
            $invitation = null;

            if ($payload) {
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                $userOk = !is_null($payload);
            } else {
                $userParams = $acParams->user_data;
                $params = [
                    'name' => $userParams->name,
                    'last_name' => $userParams->last_name,
                    'postcode' => $userParams->postcode,
                    'address' => $userParams->address,
                    'email' => $userParams->email,
                    'password' => $userParams->password,
                    'activation_url' => $request->get('url'),
                ];
                $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $params['email']]);

                if ($user) {
                    $code = 'warning';
                    $msg = 'The email address you entered is already registered';
                    $userOk = false;
                } else {
                    $invitationToken = $request->get('invitation_token');
                    $invitation = $entityManager->getRepository(UserInvitation::class)->findOneBy(['token' => $invitationToken]);
                    $role = ($invitation) ? $invitation->getRole() : 'student';
                    $user = new User();
                    $user->setAddress($params['address']);
                    $user->setPostcode($params['postcode']);
                    $user->setCreatedAt(time());
                    $user->setEmail($params['email']);
                    $user->setLastname($params['last_name']);
                    $user->setName($params['name']);
                    $user->setPassword(sha1($params['password']));
                    $user->setRoles([$role]);
                    $user->setStatus(0);
                    $user->setToken(sha1(StaticMembers::random_str()));
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $userOk = true;
                }
            }

            if ($userOk) {
                $ac = $entityManager->getRepository(AssessmentCenter::class)->find($acParams->id);
                if ($ac) {
                    $acUser = $entityManager->getRepository(AssessmentCenterUser::class)->findOneBy(['ac' => $ac, 'user' => $user]);
                    if (!$acUser) {
                        $acUser = new AssessmentCenterUser();
                        $acUser->setAc($ac);
                        $acUser->setIs_admin(0);
                        $acUser->setUser($user);
                        $entityManager->persist($acUser);
                        $preRegisterInfo = $user->getPre_register();
                        $dsaLetter = $request->files->get('dsa_letter');
                        if ($dsaLetter) {
                            $dsaLetterFilename = $user->getId() . '.' . $dsaLetter->getClientOriginalExtension();
                            $preRegisterInfo['dsa_letter'] = $dsaLetterFilename;
                            $user->setPre_register($preRegisterInfo);
                            $entityManager->persist($user);
                            $dsaLetter->move($this->getDSALettersDir(), $dsaLetterFilename);
                        }
                        if ($invitation) {
                            $entityManager->remove($invitation);
                        }
                        $entityManager->flush();
                        $code = 'success';
                        $msg = 'You have registered with this Centre.' . ($payload ? '' : ' Redirecting to login page...');
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
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                $ac = $entityManager->getRepository(AssessmentCenter::class)->find($acId);
                $acUser = $entityManager->getRepository(AssessmentCenterUser::class)->findOneBy(['ac' => $ac, 'user' => $user]);
                if ($acUser) {
                    $entityManager->remove($acUser);
                    $preRegisterInfo = $user->getPre_register();
                    if (isset($preRegisterInfo['dsa_letter'])) {
                        $dsaLetterFilename = $preRegisterInfo['dsa_letter'];
                        $dsaLetterPath = $this->getDSALettersDir() . $dsaLetterFilename;
                        if (file_exists($dsaLetterPath)) {
                            unlink($dsaLetterPath);
                        }
                        unset($preRegisterInfo['dsa_letter']);
                        $user->setPre_register($preRegisterInfo);
                        $entityManager->persist($user);
                    }
                    $entityManager->flush();
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
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                $member = $entityManager->getRepository(User::class)->find($userId);
                $ac = $entityManager->getRepository(AssessmentCenter::class)->find($acId);
                $acUser = $entityManager->getRepository(AssessmentCenterUser::class)->findOneBy(['ac' => $ac, 'user' => $member]);
                if ($acUser && $ac->getAdmin() === $user) {
                    $entityManager->remove($acUser);
                    $preRegisterInfo = $member->getPre_register();
                    if (isset($preRegisterInfo['dsa_letter'])) {
                        $dsaLetterFilename = $preRegisterInfo['dsa_letter'];
                        $dsaLetterPath = $this->getDSALettersDir() . $dsaLetterFilename;
                        if (file_exists($dsaLetterPath)) {
                            unlink($dsaLetterPath);
                        }
                        unset($preRegisterInfo['dsa_letter']);
                        $member->setPre_register($preRegisterInfo);
                        $entityManager->persist($member);
                    }
                    $entityManager->flush();
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
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                $ac = $entityManager->getRepository(AssessmentCenter::class)->find($acId);
                $na = $entityManager->getRepository(User::class)->findOneBy(['email' => $invitation['email']]);
                $acUser = $entityManager->getRepository(AssessmentCenterUser::class)->findOneBy(['ac' => $ac, 'user' => $na]);
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
                    if (StaticMembers::sendMail($entityManager->getRepository(AppSettings::class)->find(1), $subject, $body, $recipients) > 0) {
                        $userInv = new UserInvitation();
                        $userInv->setEmail($receiverEmail);
                        $userInv->setName($receiverName);
                        $userInv->setText($senderMsg);
                        $userInv->setToken($token);
                        $userInv->setUser($user);
                        $userInv->setRole('na');
                        $userInv->setAc($ac);
                        $entityManager->persist($userInv);
                        $entityManager->flush();
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
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                $ac = $entityManager->getRepository(AssessmentCenter::class)->find($acId);

                if ($ac->getAdmin() === $user) {
                    $service = $request->get('item');
                    $action = $request->get('action');
                    $acService = $entityManager->getRepository(AssessmentCenterService::class)->find($service['id']);

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
                                $entityManager->persist($acService);
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
                                $entityManager->persist($acService);
                                $code = 'success';
                                $msg = 'The specified service has been updated.';
                            } else {
                                $msg = 'The specified service does not exist.';
                            }
                            break;
                        case 'Delete service':
                            if ($acService) {
                                $acServiceAssessors = $entityManager->getRepository(AssessmentCenterServiceAssessor::class)->findBy(['service' => $acService]);
                                foreach ($acServiceAssessors as $acServiceAssessor) {
                                    $entityManager->remove($acServiceAssessor);
                                }
                                $entityManager->remove($acService);
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
                        $entityManager->flush();
                        $data = $acService->getId();
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
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                $assessor = $entityManager->getRepository(User::class)->find($userId);
                $ac = $entityManager->getRepository(AssessmentCenter::class)->find($acId);

                if ($ac->getAdmin() === $user && $assessor->hasRegisteredWith($ac)) {
                    StaticMembers::executeRawSQL($entityManager, 'delete from `assessment_center_service_assessor` where `assessor_id` = ' . $assessor->getId() . ' and `ac_service_id` in (select `id` from `assessment_center_service` where `ac_id` = ' . $ac->getId() . ')', false);
                    $services = $request->get('services');
                    foreach ($services as $service) {
                        $serviceEntity = $entityManager->getRepository(AssessmentCenterService::class)->find($service['id']);
                        if ($serviceEntity && $serviceEntity->getAc() === $ac) {
                            $naService = new AssessmentCenterServiceAssessor();
                            $naService->setAssessor($assessor);
                            $naService->setService($serviceEntity);
                            $entityManager->persist($naService);
                        }
                    }
                    $entityManager->flush();
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
     * Update NA services
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
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                $ac = $entityManager->getRepository(AssessmentCenter::class)->find($acId);

                if ($ac && $ac->getAdmin() === $user) {
                    $uniqueName = false;
                    if (!$entityManager->getRepository(AssessmentCenter::class)->getAnotherACByName($ac)) {
                        $ac->setName($settings['name']);
                        $uniqueName = true;
                    }
                    $uniqueSlug = false;
                    $pp = $entityManager->getRepository(AssessmentCenter::class)->getAnotherACBySlug($ac);
                    if (!$entityManager->getRepository(AssessmentCenter::class)->getAnotherACBySlug($ac)) {
                        //$ac->setUrl($settings['token']);
                        $uniqueSlug = true;
                    }
                    $ac->setTelephone($settings['telephone']);
                    $ac->setAddress($settings['address']);
                    $ac->setAvailability_type($settings['availability_type']);
                    $entityManager->persist($ac);
                    $entityManager->flush();
                    
                    if (!$uniqueName) {
                        $code = 'warning';
                        $msg = 'That name belongs to another Assessment Centre.';
                    }
                    else if (!$uniqueSlug) {
                        $code = 'warning';
                        $msg = 'That slug belongs to another Assessment Centre.';
                    }
                    else {
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

}
