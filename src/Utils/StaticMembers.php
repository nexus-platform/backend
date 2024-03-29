<?php

namespace App\Utils;

use App\Entity\AppSettings;
use App\Entity\AssessmentCenterUser;
use App\Entity\Debug;
use App\Entity\EA\EaUsers;
use App\Entity\EA\EaUserSettings;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Firebase\JWT\JWT;
use PHPHtmlParser\Dom;
use PHPHtmlParser\StaticDom;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use function mb_strlen;

class StaticMembers {

    private $key = "vUrQrZL50m7qL3uosytRJbeW8fzSwUqd";
    private static $globalConfig = [
        'date_format' => 'Y-m-d',
        'date_time_format' => 'Y-m-d H:i:s',
        'secure_key' => 'G7IwX4LkVxH_E1I9jfoXxja2wUOe-xFK',
        'characters' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ];

    public static function decodeJWT($jwt) {
        try {
            $payload = JWT::decode($jwt, $this->key, ['HS256']);
            return ($payload->exp > time()) ? $payload : null;
        } catch (Exception $exc) {
            return null;
        }
    }

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
        try {
            $transport = (new Swift_SmtpTransport())
                    ->setHost($appSettings->getMailHost())
                    ->setPort($appSettings->getMailPort())
                    ->setUsername($appSettings->getMailUsername())
                    //->setPassword(self::decryptString($appSettings->getMailPassword()))
                    ->setPassword($appSettings->getMailPassword())
                    ->setEncryption($appSettings->getMailEncryption());
            $mailer = new Swift_Mailer($transport);
            $message = (new Swift_Message($subject))
                    ->setFrom([$appSettings->getMailUsername() => 'Nexus Platform'])
                    ->setTo($recipients)
                    ->setBody($body, 'text/html');
            return $mailer->send($message);
        } catch (Exception $exc) {
            return false;
        }
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
        try {
            return ($returnResult) ? $statement->fetchAll() : 1;
        } catch (Exception $exc) {
            $statement = $entityManager->getConnection()->prepare('SELECT ROW_COUNT() as `AFFECTED_ROWS`');
            $statement->execute();
            return $statement->fetchAll();
        }
    }

    public static function syncEaUser(ObjectManager $entityManager, AssessmentCenterUser $acUser, $mode = 1) {
        $user = $acUser->getUser();
        $ac = $acUser->getAc();
        $userId = $user->getId();
        $acId = $ac->getId();
        $eaUser = $entityManager->getRepository(EaUsers::class)->findOneBy(['id' => $userId, 'id_assessment_center' => $acId]);
        if ($eaUser) {
            $entityManager->remove($eaUser);
            $entityManager->flush();
        }

        if ($mode === 1) {
            $status = $acUser->getStatus();
            $eaRole = $user->getEaRole();
            $email = $user->getEmail();
            $firstName = $user->getName();
            $lastName = $user->getName();
            
            $statement = $entityManager->getConnection()->prepare("insert into `ea_users` (`id`, `id_assessment_center`, `status`, `email`, `first_name`, `last_name`, `id_roles`) values ($userId, $acId, $status, '$email', '$firstName', '$lastName', $eaRole)");
            $statement->execute();
            
            $statement = $entityManager->getConnection()->prepare("insert into `ea_user_settings` (`id_users`, `id_assessment_center`) values ($userId, $acId)");
            $statement->execute();
            /* $eaUser = new EaUsers();
              $eaUser->setId($userId);
              $eaUser->setId_assessment_center($acId);
              $eaUser->setId_roles($user->getEaRole());
              $eaUser->setAddress($user->getAddress());
              $eaUser->setFirstName($user->getName());
              $eaUser->setLastName($user->getLastname());
              $eaUser->setEmail($user->getEmail());
              $eaUser->setStatus($acUser->getStatus());
              $entityManager->persist($eaUser);
              $entityManager->flush();

              $eaUser = $entityManager->getRepository(EaUsers::class)->findOneBy(['id' => $userId, 'id_assessment_center' => $acId]);
              if ($eaUser) {
              $eaUserSettings = new EaUserSettings();
              $eaUserSettings->setIdUsers($userId);
              $eaUserSettings->setIdAssessmentCenter($acId);
              $entityManager->persist($eaUserSettings);
              $entityManager->flush();
              }
              else {
              $debug = new Debug();
              $debug->setText("User $userId, AC $acId");
              $entityManager->persist($debug);
              $entityManager->flush();
              } */
        }
    }

}
