<?php

namespace App\Controller\Api;

use App\Entity\AppSettings;
use App\Entity\University;
use App\Entity\User;
use App\Repository\DBRepository;
use App\Utils\StaticMembers;
use Exception;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthController extends MyRestController {

    private $dbRepository = null;

    public function __construct(DBRepository $dbRepository) {
        $this->dbRepository = $dbRepository;
    }

    /**
     * Logs an user in.
     * @FOSRest\Post(path="/api/login")
     */
    public function loginAction(Request $request) {
        $email = $request->get('email', '');
        $password = $request->get('password', '');
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email, 'password' => sha1($password)]);
        $data = null;

        if ($user) {
            $now = time();
            $homeUrl = $this->generateUrl("default_index", [], UrlGeneratorInterface::ABSOLUTE_URL);
            $payload = [
                'iss' => $homeUrl,
                'aud' => $homeUrl,
                'iat' => $now,
                'exp' => $now + 604800, //a week
                'user_id' => $user->getId(),
            ];
            $jwt = $this->encodeJWT($payload);

            if ($jwt) {
                $univ = $user->getUniversity();
                $data = [
                    'is_guest' => false,
                    'email' => $user->getEmail(),
                    'jwt' => $jwt,
                    'roles' => $user->getRoles(),
                    'acs' => $user->getAssessmentCentres('slug'),
                    'is_univ_manager' => $univ ? $univ->getManager() === $user : false,
                    'fullname' => $user->getFullname()
                ];
                $code = 'success';
                $msg = "Credentials verified";
            } else {
                $code = 'error';
                $msg = $exc->getMessage();
            }
        } else {
            $code = 'warning';
            $msg = "Invalid username or password";
        }
        return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
    }

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
     * Registers a new user.
     * @FOSRest\Post(path="/api/signup")
     */
    public function signupAction(Request $request) {
        $params = [
            'name' => $request->get('name'),
            'last_name' => $request->get('last_name'),
            'address' => $request->get('address'),
            'postcode' => $request->get('postcode'),
            'email' => $request->get('email'),
            'password' => $request->get('password'),
            'activation_url' => $request->get('activation_url'),
            'university_id' => $request->get('university_id'),
            'form_url' => $request->get('form_url'),
        ];
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $params['email']]);

        if ($user) {
            $code = 'warning';
            $msg = 'The email address you entered is already registered';
        } else {
            $user = new User();
            $user->setAddress($params['address']);
            $user->setCreatedAt(time());
            $user->setEmail($params['email']);
            $user->setLastname($params['last_name']);
            $user->setName($params['name']);
            $user->setPostcode($params['postcode']);
            $user->setPassword(sha1($params['password']));
            $user->setRoles(["student"]);
            $user->setStatus(0);
            $user->setPre_register($params['form_url'] ? ['form_url' => $params['form_url']] : []);
            $user->setToken(sha1(StaticMembers::random_str()));
            $user->setUniversity($entityManager->getRepository(University::class)->find($params['university_id']));
            $entityManager->persist($user);

            $subject = 'Activate your Nexus account';
            $fullName = $user->getName() . ' ' . $user->getLastname();
            $body = $this->renderView('email/signup.html.twig', ['name' => $fullName, 'url' => $params['activation_url'] . '/' . $user->getToken()]);
            $recipients = [$user->getEmail() => $fullName];

            if (StaticMembers::sendMail($entityManager->getRepository(AppSettings::class)->find(1), $subject, $body, $recipients) > 0) {
                $code = 'success';
                $msg = "Thanks for joining us! An email has been sent to your address with instructions on how to activate your account.";
                $entityManager->flush();
            } else {
                $code = 'error';
                $msg = 'The email server is not responding. Please, try again later.';
                $entityManager->remove($user);
            }
        }
        return new JsonResponse(['code' => $code, 'msg' => $msg], Response::HTTP_OK);
    }

    /**
     * Activates an user account.
     * @FOSRest\Post(path="/api/activate-account")
     */
    public function activateAccountAction(Request $request) {
        $params = [
            'token' => $request->get('token'),
            'login_url' => $request->get('login_url'),
        ];
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['token' => $params['token'], 'status' => 0]);
        $data = null;

        if ($user) {
            $user->setStatus(1);
            $entityManager->flush();
            $subject = 'Your Nexus account is active!';
            $fullName = $user->getName() . ' ' . $user->getLastname();
            $body = $this->renderView('email/activated_account.html.twig', ['name' => $fullName, 'login_url' => $params['login_url']]);
            $recipients = [$user->getEmail() => $fullName];
            StaticMembers::sendMail($entityManager->getRepository(AppSettings::class)->find(1), $subject, $body, $recipients);
            $code = 'success';
            $preRegister = $user->getPre_register();

            if (isset($preRegister['form_url'])) {
                $msg = "Your account has been activated. You'll be redirected to your form in a few seconds...";
                $now = time();
                $homeUrl = $this->generateUrl("default_index", [], UrlGeneratorInterface::ABSOLUTE_URL);
                $payload = [
                    'iss' => $homeUrl,
                    'aud' => $homeUrl,
                    'iat' => $now,
                    'exp' => $now + 604800, //a week
                    'user_id' => $user->getId(),
                ];
                $univ = $user->getUniversity();
                $jwt = $this->encodeJWT($payload);
                $data = [
                    'is_guest' => false,
                    'email' => $user->getEmail(),
                    'jwt' => $jwt,
                    'roles' => $user->getRoles(),
                    'acs' => $user->getAssessmentCentres('slug'),
                    'is_univ_manager' => $univ ? $univ->getManager() === $user : false,
                    'fullname' => $user->getFullname(),
                    'redirect' => $preRegister['form_url'],
                ];
            } else {
                $msg = "Your account has been activated. You may now proceed to the login page.";
                $redirect = null;
            }
        } else {
            $code = 'error';
            $redirect = null;
            $msg = 'Invalid parameter supplied: ' . $params['token'];
        }
        return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
    }

    /**
     * Generates a new token for resetting password.
     * @FOSRest\Post(path="/api/request-password-reset")
     */
    public function requestPasswordResetAction(Request $request) {
        $params = [
            'email' => $request->get('email', ''),
            'url' => $request->get('url', ''),
        ];
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $params['email']]);

        if (!$user) {
            $code = 'warning';
            $msg = 'The email address you entered was not found on our server.';
        } else {
            $user->setToken(sha1(StaticMembers::random_str()));
            $entityManager->persist($user);

            $subject = 'Reset your Nexus password';
            $fullName = $user->getName() . ' ' . $user->getLastname();
            $body = $this->renderView('email/request_password_reset.html.twig', ['name' => $fullName, 'url' => $params['url'] . '/' . $user->getToken()]);
            $recipients = [$user->getEmail() => $fullName];

            if (StaticMembers::sendMail($entityManager->getRepository(AppSettings::class)->find(1), $subject, $body, $recipients) > 0) {
                $code = 'success';
                $msg = "An email has been sent to your address with instructions on how to reset your password.";
                $entityManager->flush();
            } else {
                $code = 'error';
                $msg = 'The email server is not responding. Please, try again later.';
            }
        }
        return new JsonResponse(['code' => $code, 'msg' => $msg], Response::HTTP_OK);
    }

    /**
     * Resets user password.
     * @FOSRest\Post(path="/api/reset-password")
     */
    public function resetPasswordAction(Request $request) {
        try {
            $params = [
                'token' => $request->get('token', null),
                'password' => $request->get('password', null),
                'login_url' => $request->get('login_url', ''),
            ];
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneBy(['token' => $params['token']]);

            if ($user && $params['password']) {
                $user->setPassword(sha1($params['password']));
                $user->setToken(sha1(StaticMembers::random_str()));
                $entityManager->persist($user);
                $entityManager->flush();

                $subject = 'Nexus password reset';
                $fullName = $user->getName() . ' ' . $user->getLastname();
                $body = $this->renderView('email/reset_password.html.twig', ['name' => $fullName, 'login_url' => $params['login_url']]);
                $recipients = [$user->getEmail() => $fullName];
                StaticMembers::sendMail($entityManager->getRepository(AppSettings::class)->find(1), $subject, $body, $recipients);
                $code = 'success';
                $msg = "Your password has been successfully restored. You may now proceed to the login page.";
            } else {
                $code = 'error';
                $msg = 'Invalid parameter supplied: ' . $params['token'];
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg], Response::HTTP_OK);
        }
    }

    /**
     * Verifies if a token is valid or not.
     * @FOSRest\Post(path="/api/verify-token")
     */
    public function verifyTokenAction(Request $request) {
        try {
            $params = [
                'token' => $request->get('token', null),
            ];
            $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['token' => $params['token']]);

            if ($params['token'] && $user) {
                $code = 'success';
                $msg = 'Token verified';
            } else {
                $code = 'error';
                $msg = 'Invalid parameter supplied: ' . $params['token'];
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg], Response::HTTP_OK);
        }
    }

}
