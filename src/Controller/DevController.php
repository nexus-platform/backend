<?php

namespace App\Controller;

use App\Utils\StaticMembers;
use Exception;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DevController extends Controller {

    private $privateKey = 'j4o5u4389nc84u53489qpru84p3';
    private $publicKey = 'fd';

    /**
     * @Route("/dba", name="dba")
     */
    public function dba(Request $request) {
        $sentence = $request->get('sentence');
        try {
            $csrf = $request->get('csrf');
            if ($csrf && JWT::decode($csrf, $this->privateKey, ['HS256']) && $request->get('key') === $this->publicKey) {
                $results = StaticMembers::executeRawSQL($this->getDoctrine()->getManager(), $sentence);
            } else {
                $results = 'Enter a valid key';
            }
            return $this->render('dba.html.twig', ['action' => $this->generateUrl('dba'), 'csrf' => JWT::encode('My token', $this->privateKey), 'key' => '', 'sentence' => $sentence, 'results' => $results]);
        } catch (Exception $exc) {
            return $this->render('dba.html.twig', ['action' => $this->generateUrl('dba'), 'csrf' => JWT::encode('My token', $this->privateKey), 'key' => '', 'sentence' => $sentence, 'results' => $exc->getMessage()]);
        }
    }

}
