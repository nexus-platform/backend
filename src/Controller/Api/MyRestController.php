<?php

namespace App\Controller\Api;

use Exception;
use Firebase\JWT\JWT;
use FOS\RestBundle\Controller\FOSRestController;

class MyRestController extends FOSRestController {

    protected $key = "vUrQrZL50m7qL3uosytRJbeW8fzSwUqd";

    protected function encodeJWT($payload) {
        try {
            return JWT::encode($payload, $this->key);
        } catch (Exception $exc) {
            return null;
        }
    }

    protected function decodeJWT($jwt) {
        try {
            $payload = JWT::decode($jwt, $this->key, ['HS256']);
            return ($payload->exp > time()) ? $payload : null;
        } catch (Exception $exc) {
            return null;
        }
    }
    
}
