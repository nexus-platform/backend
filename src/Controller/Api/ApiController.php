<?php

namespace App\Controller\Api;

use App\Entity\Country;
use App\Entity\EaAppointment;
use App\Entity\University;
use App\Entity\User;
use Exception;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Brand controller.
 *
 * @Route("/")
 */
class ApiController extends MyRestController {

    /**
     * Retrieves all countries.
     * @FOSRest\Get(path="/api/get-countries")
     */
    public function getCountries(Request $request) {
        try {
            $entityManager = $this->getDoctrine()->getManager();
            $data = $entityManager->getRepository(Country::class)->getActiveCountries();
            $code = 'success';
            $msg = 'Countries loaded.';
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Retrieves all universities.
     * @FOSRest\Get(path="/api/get-universities")
     */
    public function getUniversities(Request $request) {
        try {
            $entityManager = $this->getDoctrine()->getManager();
            $data = $entityManager->getRepository(University::class)->getActiveUniversitiesByCountry($request->get('country_id'));
            $code = 'success';
            $msg = 'Universities loaded.';
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Retrieves all universities.
     * @FOSRest\Get(path="/api/get-my-bookings")
     */
    public function getMyBookings(Request $request) {
        $code = 'error';
        $msg = 'Invalid user.';
        $data = null;

        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);

                if ($user) {
                    $appointments = $entityManager->getRepository(EaAppointment::class)->findBy($user->isStudent() ? ['student' => $user] : ['provider' => $user, 'is_unavailable' => false]);
                    $data = [];
                    foreach ($appointments as $appointment) {
                        $data[] = [
                            'id' => $appointment->getId(),
                            'student' => $appointment->getStudent()->getFullname(),
                            'provider' => $appointment->getProvider()->getFullname(),
                            'service' => $appointment->getService()->getName(),
                            'start' => $appointment->getStart_datetime()->format('Y-m-d H:i'),
                            'end' => $appointment->getEnd_datetime()->format('Y-m-d H:i'),
                        ];
                    }
                    $code = 'success';
                    $msg = 'Appointments loaded.';
                }
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => null], Response::HTTP_OK);
        }
    }

}
