<?php

namespace App\Controller\Api;

use App\Entity\Country;
use App\Entity\University;
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

}
