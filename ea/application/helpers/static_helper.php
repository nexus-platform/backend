<?php

class static_helper extends CI_Controller {

    const key = "vUrQrZL50m7qL3uosytRJbeW8fzSwUqd";
    const globalConfig = [
        'date_format' => 'Y-m-d',
        'date_time_format' => 'Y-m-d H:i:s',
        'secure_key' => 'G7IwX4LkVxH_E1I9jfoXxja2wUOe-xFK',
        'characters' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ];

    public static function encryptString($string_to_encrypt) {
        return openssl_encrypt($string_to_encrypt, "AES-128-ECB", self::globalConfig['secure_key']);
    }

    public static function decryptString($encrypted_string) {
        return openssl_decrypt($encrypted_string, "AES-128-ECB", self::globalConfig['secure_key']);
    }

    /**
     * Generate a random string, using a cryptographically secure 
     * pseudorandom number generator (random_int)
     *
     * @param int $length      How many characters do we want?
     * @param string $keyspace A string of all possible characters
     *                         to select from
     * @return string
     */
    public static function random_str($length = 10) {
        $pieces = [];
        $max = mb_strlen(self::globalConfig['characters'], '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces [] = self::globalConfig['characters'][random_int(0, $max)];
        }
        return implode('', $pieces);
    }

}
