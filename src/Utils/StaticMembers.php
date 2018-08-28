<?php

namespace App\Utils;

use App\Entity\AppSettings;
use Doctrine\Common\Persistence\ObjectManager;
use PHPHtmlParser\Dom;
use PHPHtmlParser\StaticDom;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use function mb_strlen;

class StaticMembers {

    private static $globalConfig = [
        'date_format' => 'Y-m-d',
        'date_time_format' => 'Y-m-d H:i:s',
        'secure_key' => 'G7IwX4LkVxH_E1I9jfoXxja2wUOe-xFK',
        'characters' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ];

    public static function encryptString($string_to_encrypt) {
        return openssl_encrypt($string_to_encrypt, "AES-128-ECB", self::$globalConfig['secure_key']);
    }

    public static function decryptString($encrypted_string) {
        return openssl_decrypt($encrypted_string, "AES-128-ECB", self::$globalConfig['secure_key']);
    }
    
    public static function contains($haystack, $needle) {
        foreach ($haystack as $item) {
            if ($item === $needle) {
                return true;
            }
        }
        return false;
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
        $max = mb_strlen(self::$globalConfig['characters'], '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces [] = self::$globalConfig['characters'][random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    public static function sendMail(AppSettings $appSettings, $subject, $body, $recipients) {
        $transport = (new Swift_SmtpTransport())
                ->setHost($appSettings->getMailHost())
                ->setPort($appSettings->getMailPort())
                ->setUsername($appSettings->getMailUsername())
                //->setPassword(self::decryptString($appSettings->getMailPassword()))
                ->setPassword($appSettings->getMailPassword())
                //->setAuthMode('login')
                ->setEncryption($appSettings->getMailEncryption());
        $mailer = new Swift_Mailer($transport);
        $message = (new Swift_Message($subject))
                ->setFrom(['no-reply@nexus.co.uk' => 'Nexus Platform'])
                ->setTo($recipients)
                ->setBody($body, 'text/html');
        return $mailer->send($message);
    }

    public static function validateChecksums() {

        /* $dom = new Dom;
          $dom->loadFromUrl('http://google.com');
          $links = $dom->find('a');
          foreach ($links as $link) {
          $tag = $link->getTag();
          $href = $a->getAttribute('href');
          $pp = 2;
          }

          $sha1file = file_get_contents("sha1file.txt");
          if (sha1_file("test.txt") == $sha1file) {
          echo "The file is ok.";
          } else {
          echo "The file has been changed.";
          } */
    }

    public static function executeRawSQL(ObjectManager $entityManager, $sql, $returnResult = true) {
        $statement = $entityManager->getConnection()->prepare($sql);
        $statement->execute();
        return ($returnResult) ? $statement->fetchAll() : 1;
    }

}
