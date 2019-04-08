<?php

namespace App\Controller\Api;

use App\Entity\Country;
use App\Entity\EA\EaAppointments;
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
            $data = $this->getEntityManager()->getRepository(Country::class)->getActiveCountries();
            $code = 'success';
            $msg = 'Countries loaded.';
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => []], Response::HTTP_OK);
        }
    }

    /**
     * Retrieves all universities.
     * @FOSRest\Get(path="/api/get-universities")
     */
    public function getUniversities(Request $request) {
        try {
            $data = $this->getEntityManager()->getRepository(University::class)->getActiveUniversitiesByCountry($request->get('country_id'));
            $code = 'success';
            $msg = 'Universities loaded.';
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => []], Response::HTTP_OK);
        }
    }

    /**
     * Retrieves all universities.
     * @FOSRest\Get(path="/api/get-my-bookings")
     */
    public function getMyBookings(Request $request) {
        try {
            $userInfo = $this->getRequestUser($request);
            $user = $userInfo['user'];
            if ($userInfo['code'] !== 'success') {
                return new JsonResponse(['code' => 'error', 'msg' => 'Invalid parameters', 'data' => []], Response::HTTP_OK);
            }
            $appointments = $this->getEntityManager()->getRepository(EaAppointments::class)->findBy($user->isStudent() ? ['idUsersCustomer' => $user] : ['idUsersProvider' => $user, 'is_unavailable' => false]);
            $data = [];
            foreach ($appointments as $appointment) {
                $student = $appointment->getStudent();
                $data[] = [
                    'id' => $appointment->getId(),
                    'student' => $student->getFullname(),
                    'institute' => $student->getUniversity()->getName(),
                    'provider' => $appointment->getProvider()->getFullname(),
                    'service' => $appointment->getService()->getName(),
                    'start' => $appointment->getStart_datetime()->format('Y-m-d H:i'),
                    'end' => $appointment->getEnd_datetime()->format('Y-m-d H:i'),
                ];
            }
            return new JsonResponse(['code' => 'success', 'msg' => 'Bookings loaded', 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => []], Response::HTTP_OK);
        }
    }

    /* public function getMyBookings(Request $request) {
      $code = 'error';
      $msg = 'Invalid user.';
      $data = null;

      try {
      $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
      $payload = $this->decodeJWT($jwt);

      if ($payload) {
      $user = $this->getEntityManager()->getRepository(User::class)->find($payload->user_id);

      if ($user) {
      $appointments = $this->getEntityManager()->getRepository(EaAppointments::class)->findBy($user->isStudent() ? ['student' => $user] : ['provider' => $user, 'is_unavailable' => false]);
      $data = [];
      foreach ($appointments as $appointment) {
      $student = $appointment->getStudent();
      $data[] = [
      'id' => $appointment->getId(),
      'student' => $student->getFullname(),
      'institute' => $student->getUniversity()->getName(),
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
      return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => []], Response::HTTP_OK);
      }
      } */

    /**
     * Cancels a booking.
     * @FOSRest\Post(path="/api/cancel-booking")
     */
    public function cancelBooking(Request $request) {
        try {
            $userInfo = $this->getRequestUser($request);
            if ($userInfo['code'] === 'success') {
                $currentUser = $userInfo['user'];
                $bookingId = $request->get('id');
                $bookingEntity = $this->getEntityManager()->getRepository(EaAppointments::class)->find($bookingId);
                if ($currentUser->isStudent() && $bookingEntity->getStudent() === $currentUser) {
                    $headline = date('Y/m/d H:i:s', time());
                    $this->getEntityManager()->remove($bookingEntity);
                    $this->createNotification('Appointment cancelled', 'The appointment scheduled with you by' . $currentUser->getFullname() . ' from ' . $bookingEntity->getService()->getAc()->getName() . ' between ' . $bookingEntity->getStart_datetime()->format('Y-m-d H:i') . ' and ' . $bookingEntity->getEnd_datetime()->format('Y-m-d H:i') . ' has been cancelled by the student.', $headline, $bookingEntity->getProvider(), 1, 1);
                    $this->createNotification('Appointment cancelled', 'You have cancelled your appointment with ' . $bookingEntity->getProvider()->getFullname() . ', scheduled between ' . $bookingEntity->getStart_datetime()->format('Y-m-d H:i') . ' and ' . $bookingEntity->getEnd_datetime()->format('Y-m-d H:i'), $headline, $currentUser, 1, 2);
                    $this->getEntityManager()->flush();
                    return new JsonResponse(['code' => 'success', 'msg' => 'The appointment has been cancelled.', 'data' => []], Response::HTTP_OK);
                } else if ($currentUser->isNA() && $bookingEntity->getProvider() === $currentUser) {
                    
                }
            }
            return new JsonResponse(['code' => $userInfo['code'], 'msg' => $userInfo['msg'], 'data' => []], Response::HTTP_OK);
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => []], Response::HTTP_OK);
        }
    }

}
