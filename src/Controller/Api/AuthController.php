<?php

namespace App\Controller\Api;

use App\Entity\AppSettings;
use App\Entity\AssessmentCenter;
use App\Entity\AssessmentCenterUser;
use App\Entity\University;
use App\Entity\User;
use App\Entity\UserInvitation;
use App\Utils\StaticMembers;
use Exception;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthController extends MyRestController {

    /**
     * Logs an user out.
     * @FOSRest\Post(path="/api/logout")
     */
    public function logoutAction(Request $request) {
        $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
        if ($this->decodeJWT($jwt)) {
            $jwt = null;
            $code = 'success';
            $msg = 'You have successfully logged out.';
        } else {
            $code = 'error';
            $msg = 'Invalid token sent';
        }
        return new JsonResponse(['msg' => $msg, 'code' => $code], Response::HTTP_OK);
    }

    /**
     * Generates a new token for resetting password.
     * @FOSRest\Post(path="/api/request-password-reset")
     */
    public function requestPasswordReset(Request $request) {
        $params = json_decode($request->getContent(), true);

        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => $params['email']]);
        if (!$user) {
            return new JsonResponse(['code' => 'warning', 'msg' => 'The email address you entered was not found on our server.'], Response::HTTP_OK);
        }

        $user->setToken(sha1(StaticMembers::random_str()));
        $this->getEntityManager()->persist($user);

        $subject = 'Reset your Nexus password';
        $fullName = $user->getName() . ' ' . $user->getLastname();
        $univ = $user->getUniversity();
        $company = $univ ? $univ->getName() : 'Nexus Platform';
        $body = $this->renderView('email/request_password_reset.html.twig', ['dsa' => $company, 'name' => $fullName, 'activation_url' => $params['activation_url'] . '/' . $user->getToken(), 'home_url' => $params['home_url']]);
        $recipients = [$user->getEmail() => $fullName];

        if (StaticMembers::sendMail($this->getEntityManager()->getRepository(AppSettings::class)->find(1), $subject, $body, $recipients) > 0) {
            $code = 'success';
            $msg = "Check your inbox for instructions on how to reset your password.";
            $this->getEntityManager()->flush();
        } else {
            $code = 'error';
            $msg = 'The email server is not responding. Please, try again later.';
        }

        return new JsonResponse(['code' => $code, 'msg' => $msg], Response::HTTP_OK);
    }

    /**
     * Resets user password.
     * @FOSRest\Post(path="/api/reset-password")
     */
    public function resetPassword(Request $request) {
        $params = json_decode($request->getContent(), true);
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['token' => $params['token']]);
        if (!$user) {
            return new JsonResponse(['code' => 'error', 'msg' => 'Invalid parameter supplied: ' . $params['token']], Response::HTTP_OK);
        }
        if (!($params['password'] && $params['password'] === $params['password_confirm'])) {
            return new JsonResponse(['code' => 'error', 'msg' => 'Passwords do not match'], Response::HTTP_OK);
        }

        $user->setPassword(sha1($params['password']));
        $user->setToken(sha1(StaticMembers::random_str()));
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        $subject = 'Your Nexus password has been reset';
        $fullName = $user->getName() . ' ' . $user->getLastname();
        $univ = $user->getUniversity();
        $company = $univ ? $univ->getName() : 'Nexus Platform';
        $body = $this->renderView('email/reset_password.html.twig', ['dsa' => $company, 'name' => $fullName, 'home_url' => $params['home_url']]);
        $recipients = [$user->getEmail() => $fullName];
        StaticMembers::sendMail($this->getEntityManager()->getRepository(AppSettings::class)->find(1), $subject, $body, $recipients);
        $code = 'success';
        $msg = "Your password has been successfully restored. You may now proceed to the login page.";

        return new JsonResponse(['code' => $code, 'msg' => $msg], Response::HTTP_OK);
    }

    /**
     * Verifies if a token is valid or not.
     * @FOSRest\Post(path="/api/verify-token")
     */
    public function verifyTokenAction(Request $request) {
        $params = json_decode($request->getContent(), true);
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['token' => $params['token']]);

        if ($params['token'] && $user) {
            $code = 'success';
            $msg = 'Token verified';
            $univ = $user->getUniversity();
            $homeUrl = '';
            if ($univ) {
                $homeUrl = '/dsa/' . $univ->getToken() . '/login';
            } else {
                $homeUrl = $user->getPre_register()['reset_password_origin'];
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'home_url' => $homeUrl], Response::HTTP_OK);
        }
        return new JsonResponse(['code' => 'error', 'msg' => 'Invalid parameter supplied: ' . $params['token']], Response::HTTP_OK);
    }

    /**
     * Retrieves the user saved signature.
     * @FOSRest\Get(path="/api/get-profile-info")
     */
    public function getProfileInfo(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $data = null;

            if ($payload) {

                $user = $this->getEntityManager()->getRepository(User::class)->find($payload->user_id);
                if ($user) {
                    $preRegisterInfo = $user->getPre_register();
                    $dsaLetterName = isset($preRegisterInfo['dsa_letter']) && file_exists($this->getDSALettersDir() . $preRegisterInfo['dsa_letter']) ? $preRegisterInfo['dsa_letter'] : '';
                    $data = [
                        'name' => $user->getName(),
                        'lastname' => $user->getLastname(),
                        'postcode' => $user->getPostcode(),
                        'address' => $user->getAddress(),
                        'email' => $user->getEmail(),
                        'current_password' => '',
                        'password' => '',
                        'password_confirm' => '',
                        'is_student' => $user->isStudent(),
                        'ac_registered' => $user->getAssessment_center_users() ? true : false,
                        'signature' => $user->getSignature(),
                        'dsa_letter_name' => $dsaLetterName,
                    ];
                    $code = 'success';
                    $msg = 'User data loaded.';
                } else {
                    $code = 'error';
                    $msg = 'Invalid user information';
                }
            } else {
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Updates profile
     * @FOSRest\Post(path="/api/update-user-profile")
     */
    public function updateUserProfile(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $data = null;


            if ($payload) {
                $user = $this->getEntityManager()->getRepository(User::class)->find($payload->user_id);
                if ($user) {
                    $userData = json_decode($request->get('user_data'));
                    $user->setName($userData->name);
                    $user->setLastname($userData->lastname);
                    $user->setPostcode($userData->postcode);
                    $user->setAddress($userData->address);
                    $uniqueEmail = false;
                    if (!$this->getEntityManager()->getRepository(User::class)->findByEmailUnique($user->getId(), $user->getEmail())) {
                        $user->setEmail($userData->email);
                        $uniqueEmail = true;
                    }
                    $passwordOk = true;
                    if ($userData->password) {
                        if ($user->getPassword() === sha1($userData->current_password)) {
                            if ($userData->password === $userData->password_confirm) {
                                $user->setPassword(sha1($userData->password));
                            } else {
                                $passwordOk = false;
                                $msg = 'The new passwords do not match.';
                            }
                        } else {
                            $passwordOk = false;
                            $msg = 'The current password entered is invalid.';
                        }
                    }
                    $dsaLetter = $request->files->get('dsa_letter');
                    if ($dsaLetter) {
                        $dsaLetterFilename = $user->getId() . '.' . $dsaLetter->getClientOriginalExtension();
                        $preRegisterInfo = $user->getPre_register();
                        if (isset($preRegisterInfo['dsa_letter']) && file_exists($this->getDSALettersDir() . $preRegisterInfo['dsa_letter'])) {
                            unlink($this->getDSALettersDir() . $preRegisterInfo['dsa_letter']);
                        }
                        $preRegisterInfo['dsa_letter'] = $dsaLetterFilename;
                        $user->setPre_register($preRegisterInfo);
                        $dsaLetter->move($this->getDSALettersDir(), $dsaLetterFilename);
                    }
                    $this->getEntityManager()->persist($user);
                    $this->getEntityManager()->flush();
                    if (!$uniqueEmail) {
                        $code = 'warning';
                        $msg = 'The email address belongs to another registered user.';
                    } else if (!$passwordOk) {
                        $code = 'warning';
                    } else {
                        $code = 'success';
                        $msg = 'Profile updated.';
                    }
                } else {
                    $code = 'error';
                    $msg = 'Invalid parameters.';
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
     * Changes password
     * @FOSRest\Post(path="/api/change-password")
     */
    public function changePassword(Request $request) {
        $user = $this->getRequestUser($request);
        if ($user['code'] !== 'success') {
            return new JsonResponse(['code' => 'error', 'msg' => 'Invalid user', 'data' => null], Response::HTTP_OK);
        }
        $user = $user['user'];
        $params = json_decode($request->getContent(), true);

        if ($user->getPassword() !== sha1($params['current_password'])) {
            return new JsonResponse(['code' => 'error', 'msg' => 'Invalid current password'], Response::HTTP_OK);
        }

        if ($params['password'] !== $params['password_confirm']) {
            return new JsonResponse(['code' => 'error', 'msg' => 'Password do not match'], Response::HTTP_OK);
        }

        $user->setPassword(sha1($params['password']));
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        return new JsonResponse(['code' => 'success', 'msg' => 'Password updated'], Response::HTTP_OK);
    }

    /**
     * Activates an user account.
     * @FOSRest\Post(path="/api/cancel-registration")
     */
    public function cancelRegistration(Request $request) {
        $user = $this->getRequestUser($request);
        if ($user['code'] !== 'success') {
            return new JsonResponse(['code' => 'error', 'msg' => 'Invalid user', 'data' => null], Response::HTTP_OK);
        }
        $user = $user['user'];
        $params = json_decode($request->getContent(), true);

        switch ($params['type']) {
            case 'dsa':
                $univ = $this->getEntityManager()->getRepository(University::class)->findOneBy(['token' => $params['slug']]);
                if (!$univ || $univ !== $user->getUniversity()) {
                    return new JsonResponse(['code' => 'error', 'msg' => 'Invalid parameters', 'data' => null], Response::HTTP_OK);
                }
                $user->setUniversity(null);
                $this->getEntityManager()->flush();
                return new JsonResponse(['code' => 'success', 'msg' => 'You are no longer registered with ' . $univ->getName(), 'data' => null], Response::HTTP_OK);
            default:
                return new JsonResponse(['code' => 'error', 'msg' => 'Invalid parameters', 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Logs an user in.
     * @FOSRest\Post(path="/api/login")
     */
    public function login(Request $request) {
        $params = json_decode($request->getContent(), true);
        $pass = sha1($params['password']);

        if ($params['target'] === 'dsa') {
            $target = $this->getEntityManager()->getRepository(University::class)->findOneBy(['token' => $params['slug']]);
            if (!$target) {
                return new JsonResponse(['code' => 'error', 'msg' => 'Invalid parameters', 'data' => null], Response::HTTP_OK);
            }
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $params['email'], 'password' => $pass, 'university' => $target]);
        } else if ($params['target'] === 'ac') {
            $target = $this->getEntityManager()->getRepository(AssessmentCenter::class)->findOneBy(['url' => $params['slug']]);
            if (!$target) {
                return new JsonResponse(['code' => 'error', 'msg' => 'Invalid parameters', 'data' => null], Response::HTTP_OK);
            }
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $params['email'], 'password' => $pass]);
        }

        if (!$user || $user->getStatus() === 0) {
            return new JsonResponse(['code' => 'error', 'msg' => !$user ? "Invalid username or password." : "Your user account is inactive.", 'data' => null], Response::HTTP_OK);
        }

        $now = time();
        $homeUrl = $this->generateUrl("default_index", [], UrlGeneratorInterface::ABSOLUTE_URL);
        $payload = [
            'iss' => $homeUrl,
            'aud' => $homeUrl,
            'iat' => $now,
            'exp' => $now + 43200, //12 hours
            'user_id' => $user->getId(),
            'ip' => $request->getClientIp(),
        ];
        $jwt = $this->encodeJWT($payload);

        if (!$jwt) {
            return new JsonResponse(['code' => 'error', 'msg' => 'Your data could not be encoded.', 'data' => null], Response::HTTP_OK);
        }
        $data = [
            'is_guest' => false,
            'email' => $user->getEmail(),
            'jwt' => $jwt,
            'roles' => $user->getRoles(),
            'acs' => $user->getAssessmentCentres('slug'),
            'is_univ_manager' => $params['target'] === 'dsa' ? ($target ? $target->getManager() === $user : false) : false,
            'fullname' => $user->getFullname(),
            'institute' => [
                'type' => $params['target'],
                'slug' => $params['target'] === 'dsa' ? $target->getToken() : $target->getUrl(),
                'name' => $target->getName(),
            ],
        ];

        return new JsonResponse(['code' => 'success', 'msg' => 'Credentials verified', 'data' => $data], Response::HTTP_OK);
    }

    /**
     * Registers a new user.
     * @FOSRest\Post(path="/api/signup")
     */
    public function signup(Request $request) {
        $params = json_decode($request->getContent(), true);

        if ($params['target'] === 'dsa') {
            $target = $this->getEntityManager()->getRepository(University::class)->findOneBy(['token' => $params['slug']]);
        } else {
            $target = $this->getEntityManager()->getRepository(AssessmentCenter::class)->findOneBy(['url' => $params['slug']]);
        }

        if (!$target) {
            return new JsonResponse(['code' => 'error', 'msg' => 'Invalid parameters'], Response::HTTP_OK);
        }

        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => $params['email']]);

        if ($user) {
            $acUser = $this->getEntityManager()->getRepository(AssessmentCenterUser::class)->findOneBy(['user' => $user]);
            if (($params['target'] === 'dsa' && $user->getUniversity()) || ($params['target'] === 'ac' && $acUser)) {
                return new JsonResponse(['code' => 'error', 'msg' => 'The email address you entered is already registered.'], Response::HTTP_OK);
            }
        } else {
            $user = new User();
        }

        $user->setAddress($params['address']);
        $user->setCreatedAt(time());
        $user->setEmail($params['email']);
        $user->setLastname($params['last_name']);
        $user->setName($params['name']);
        $user->setPostcode($params['postcode']);
        $user->setPassword(sha1($params['password']));
        $user->setStatus(0);
        $user->setPre_register(['target' => $params['target'], 'institute_id' => $target->getId(), 'redirect_url' => $params['redirect_url'] ? $params['redirect_url'] : $params['redirect_url']]);
        $user->setToken(sha1(StaticMembers::random_str()));
        $invitation = null;
        if (isset($params['invitation_token'])) {
            $invitation = $this->getEntityManager()->getRepository(UserInvitation::class)->findOneBy(['token' => $params['invitation_token']]);
        }
        $role = ($invitation) ? $invitation->getRole() : 'student';
        $user->setRoles([$role]);

        if ($params['target'] === 'dsa') {
            $user->setUniversity($target);
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
        } else if ($params['target'] === 'ac') {
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
            $acUser = new AssessmentCenterUser();
            $acUser->setAc($target);
            $acUser->setIs_admin(0);
            $acUser->setStatus(1);
            $acUser->setUser($user);
            $this->getEntityManager()->persist($acUser);
            StaticMembers::syncEaUser($this->getEntityManager(), $acUser);
        }

        $subject = 'Activate your Nexus account';
        $fullName = $user->getName() . ' ' . $user->getLastname();
        $body = $this->renderView('email/signup.html.twig', ['homeUrl' => $params['home_url'], 'dsa' => $target->getName(), 'subject' => $subject, 'name' => $fullName, 'activation_url' => $params['activation_url'] . '/' . $user->getToken()]);
        $recipients = [$user->getEmail() => $fullName];

        if (StaticMembers::sendMail($this->getEntityManager()->getRepository(AppSettings::class)->find(1), $subject, $body, $recipients) > 0) {
            $code = 'success';
            $msg = "Check your inbox for instructions on how to activate your account.";
            $this->getEntityManager()->flush();
        } else {
            $code = 'error';
            $msg = 'The email server is not responding. Please, try again later.';
            $this->getEntityManager()->remove($user);
        }
        return new JsonResponse(['code' => $code, 'msg' => $msg], Response::HTTP_OK);
    }

    /**
     * Activates an user account.
     * @FOSRest\Post(path="/api/activate-account")
     */
    public function activateAccount(Request $request) {
        $params = json_decode($request->getContent(), true);
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['token' => $params['token'], 'status' => 0]);
        
        if (!$user) {
            return new JsonResponse(['code' => 'error', 'msg' => 'Invalid parameter supplied: ' . $params['token'], 'data' => null], Response::HTTP_OK);
        }

        $user->setStatus(1);
        $preRegister = $user->getPre_register();
        
        if ($preRegister['target'] === 'dsa') {
            $target = $user->getUniversity();
        } else if ($preRegister['target'] === 'ac') {
            $target = $this->getEntityManager()->getRepository(AssessmentCenter::class)->find($preRegister['institute_id']);
            if (!$target) {
                return new JsonResponse(['code' => 'error', 'msg' => 'This institution no longer exists', 'data' => null], Response::HTTP_OK);
            }
        }

        $subject = 'Your Nexus account is active!';
        $fullName = $user->getName() . ' ' . $user->getLastname();
        $body = $this->renderView('email/activated_account.html.twig', ['name' => $fullName, 'homeUrl' => $preRegister['redirect_url'], 'dsa' => $target->getName(), 'subject' => $subject]);
        $recipients = [$user->getEmail() => $fullName];

        StaticMembers::sendMail($this->getEntityManager()->getRepository(AppSettings::class)->find(1), $subject, $body, $recipients);
        $code = 'success';

        $msg = "Your account has been activated. You'll be redirected in a few seconds...";
        $now = time();
        $homeUrl = $this->generateUrl("default_index", [], UrlGeneratorInterface::ABSOLUTE_URL);
        $payload = [
            'iss' => $homeUrl,
            'aud' => $homeUrl,
            'iat' => $now,
            'exp' => $now + 604800, //a week
            'user_id' => $user->getId(),
            'ip' => $request->getClientIp(),
        ];

        $jwt = $this->encodeJWT($payload);
        $data = [
            'is_guest' => false,
            'email' => $user->getEmail(),
            'jwt' => $jwt,
            'roles' => $user->getRoles(),
            'acs' => $user->getAssessmentCentres('slug'),
            'is_univ_manager' => ($preRegister['target'] === 'dsa' ? $target->getManager() === $user : false),
            'fullname' => $user->getFullname(),
            'redirect' => parse_url($preRegister['redirect_url'], PHP_URL_FRAGMENT),
        ];
        $this->getEntityManager()->flush();
        return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
    }

}
