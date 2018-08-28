<?php

namespace App\Controller\Api;

use App\Entity\AssessmentCenter;
use App\Entity\AssessmentCenterUser;
use App\Entity\Country;
use App\Entity\DsaForm;
use App\Entity\DsaFormFilled;
use App\Entity\Notification;
use App\Entity\QrCode as QrCode2;
use App\Entity\University;
use App\Entity\UniversityDsaForm;
use App\Entity\User;
use App\Utils\StaticMembers;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use mikehaertl\pdftk\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use setasign\Fpdi\TcpdfFpdi;
use Symfony\Component\HttpFoundation\File\File;
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
     * Retrieves the user saved signature.
     * @FOSRest\Get(path="/api/get-previous-signature")
     */
    public function getPreviousSignatureAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $data = null;

            if ($payload) {
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                if ($user) {
                    $data = $user->getSignature();
                    if ($data) {
                        $code = 'success';
                        $msg = 'Your previously saved signature has been retrieved';
                    } else {
                        $code = 'warning';
                        $msg = 'You have not saved a signature yet';
                    }
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
     * Retrieves an university.
     * @FOSRest\Get(path="/api/get-university")
     */
    public function getUniversityAction(Request $request) {
        try {
            $token = $request->get('university_token', null);
            $entityManager = $this->getDoctrine()->getManager();
            $item = $entityManager->getRepository(University::class)->findOneBy(['token' => $token]);

            if ($item) {
                $data = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                    'country' => $item->getCountry()->getName()
                ];
                $code = 'success';
                $msg = '';
            } else {
                $data = null;
                $code = 'error';
                $msg = 'Invalid university identifier';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Retrieves an university.
     * @FOSRest\Get(path="/api/get-ac-info")
     */
    public function getACInfo(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $data = null;
            $slug = $request->get('slug');
            $entityManager = $this->getDoctrine()->getManager();
            $user = $payload ? $entityManager->getRepository(User::class)->find($payload->user_id) : null;
            $ac = $entityManager->getRepository(AssessmentCenter::class)->findOneBy(['url' => $slug]);
            if ($ac) {
                $admin = $entityManager->getRepository(AssessmentCenterUser::class)->findOneBy(['ac' => $ac, 'is_admin' => 1]);
                $registered = false;
                $userData = [
                    'name' => '',
                    'last_name' => '',
                    'email' => '',
                    'postcode' => '',
                    'address' => '',
                    'password' => '',
                    'password_confirm' => '',
                ];

                if ($user) {
                    $registered = $user->hasRegisteredWith($ac);
                    $userData = [
                        'name' => $user->getName(),
                        'last_name' => $user->getLastname(),
                        'email' => $user->getEmail(),
                        'postcode' => $user->getPostcode(),
                        'address' => $user->getAddress(),
                        'password' => 'password',
                        'password_confirm' => 'password',
                    ];
                }

                $data = [
                    'id' => $ac->getId(),
                    'name' => $ac->getName(),
                    'address' => $ac->getAddress(),
                    'phone' => $ac->getTelephone(),
                    'registered' => $registered,
                    'admin' => $admin ? $admin->getUser()->__toString() : null,
                    'user_data' => $userData,
                ];
                $code = 'success';
                $msg = '';
            } else {
                $code = 'error';
                $msg = 'Invalid identifier';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => null], Response::HTTP_OK);
        }
    }

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
     * Returns the list of desserts.
     * @FOSRest\Get(path="/api/get-desserts")
     */
    public function getDessertsAction(Request $request) {
        $search = $request->get('search', null);
        $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
        $payload = $this->decodeJWT($jwt);
        if ($payload) {
            $res = [
                    [
                    'value' => false,
                    'name' => 'Frozen Yogurt',
                    'calories' => 159,
                    'fat' => 6.0,
                    'carbs' => 24,
                    'protein' => 4.0,
                    'iron' => '1%'
                ],
                    [
                    'value' => false,
                    'name' => 'Ice cream sandwich',
                    'calories' => 237,
                    'fat' => 9.0,
                    'carbs' => 37,
                    'protein' => 4.3,
                    'iron' => '1%'
                ],
                    [
                    'value' => false,
                    'name' => 'Eclair',
                    'calories' => 262,
                    'fat' => 16.0,
                    'carbs' => 23,
                    'protein' => 6.0,
                    'iron' => '7%'
                ],
                    [
                    'value' => false,
                    'name' => 'Cupcake',
                    'calories' => 305,
                    'fat' => 3.7,
                    'carbs' => 67,
                    'protein' => 4.3,
                    'iron' => '8%'
                ],
                    [
                    'value' => false,
                    'name' => 'Gingerbread',
                    'calories' => 356,
                    'fat' => 16.0,
                    'carbs' => 49,
                    'protein' => 3.9,
                    'iron' => '16%'
                ],
                    [
                    'value' => false,
                    'name' => 'Jelly bean',
                    'calories' => 375,
                    'fat' => 0.0,
                    'carbs' => 94,
                    'protein' => 0.0,
                    'iron' => '0%'
                ],
                    [
                    'value' => false,
                    'name' => 'Lollipop',
                    'calories' => 392,
                    'fat' => 0.2,
                    'carbs' => 98,
                    'protein' => 0,
                    'iron' => '2%'
                ],
                    [
                    'value' => false,
                    'name' => 'Honeycomb',
                    'calories' => 408,
                    'fat' => 3.2,
                    'carbs' => 87,
                    'protein' => 6.5,
                    'iron' => '45%'
                ],
                    [
                    'value' => false,
                    'name' => 'Donut',
                    'calories' => 452,
                    'fat' => 25.0,
                    'carbs' => 51,
                    'protein' => 4.9,
                    'iron' => '22%'
                ],
                    [
                    'value' => false,
                    'name' => 'KitKat',
                    'calories' => 518,
                    'fat' => 26.0,
                    'carbs' => 65,
                    'protein' => 7,
                    'iron' => '6%'
                ]
            ];
        } else {
            $res = [];
        }
        return new JsonResponse($res, Response::HTTP_OK);
    }

    /**
     * Retrieves the list of DSA forms.
     * @FOSRest\Get(path="/api/get-dsa-forms")
     */
    public function getDsaFormsAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            if ($payload) {
                $data = $this->getDsaForms($this->getDoctrine()->getManager(), $payload);
                $code = 'success';
                $msg = "List of available DSA forms";
            } else {
                $data = null;
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    private function getDsaForms(ObjectManager $entityManager, $payload) {
        $formsDir = $this->container->getParameter('kernel.project_dir') . '/public/dsa_forms/';
        $items = $entityManager->getRepository(DsaForm::class)->findBy(['active' => 1]);
        $user = $entityManager->getRepository(User::class)->find($payload->user_id);
        $univ = $user->getUniversity();
        $univForms = $univ->getUniv_dsa_form();
        $res = [];
        if ($user->isDO()) {
            foreach ($items as $item) {
                //$univForm = $entityManager->getRepository(UniversityDsaForm::class)->findOneBy(['dsa_form' => $item, 'university' => $univ]);
                $res[] = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                    'code' => $item->getCode(),
                    'active' => $item->getActive(),
                    'file_status' => file_exists($formsDir . $item->getBase()),
                ];
            }
        } else {
            foreach ($univForms as $univForm) {
                if ($univForm->getActive()) {
                    $item = $univForm->getDsa_form();
                    $res[] = [
                        'id' => $item->getId(),
                        'name' => $item->getName(),
                        'code' => $item->getCode(),
                        'active' => $item->getActive(),
                        'route' => 'dsa-form/' . $univ->getToken() . '/' . $univForm->getDsa_form_slug(),
                        'file_status' => file_exists($formsDir . $item->getBase()),
                    ];
                }
            }
        }
        return $res;
    }

    /**
     * Retrieves the Institute info
     * @FOSRest\Get(path="/api/get-my-institute-info")
     */
    public function getMyInstituteInfoAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            if ($payload) {
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                $univ = $user->getUniversity();
                $univForms = $univ->getUniv_dsa_form();
                $forms = [];
                foreach ($univForms as $form) {
                    $forms[] = [
                        'id' => $form->getId(),
                        'name' => $form->getDsa_form()->getName(),
                        'active' => $form->getActive(),
                        'slug' => $form->getDsa_form_slug()
                    ];
                }
                $data = [
                    'univ_slug' => $univ->getToken(),
                    'forms' => $forms,
                ];
                $code = 'success';
                $msg = $univ->getName();
            } else {
                $data = null;
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Retrieves the list of inputs for a form.
     * @FOSRest\Get(path="/api/get-pdf-content")
     */
    public function getPdfContentAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $data = null;

            if ($payload) {
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                $univSlug = $request->get('univ_slug');
                $univFromSlug = $entityManager->getRepository(University::class)->findOneBy(['token' => $univSlug]);
                $univFromUser = $user->getUniversity();
                $formSlug = $request->get('form_slug');
                $univForm = $entityManager->getRepository(UniversityDsaForm::class)->findOneBy(['university' => $univFromUser, 'dsa_form_slug' => $formSlug]);

                if ($univFromSlug === $univFromUser && $univForm) {
                    $item = $univForm->getDsa_form();
                    $formsDir = $this->container->getParameter('kernel.project_dir') . '/public/dsa_forms/';
                    $formPath = $formsDir . $item->getBase();

                    $formId = $request->get('entity_id', null);
                    $data = $item->getContent();
                    if ($formId != '0') {
                        $filledForm = $entityManager->getRepository(DsaFormFilled::class)->find($formId);
                        if ($filledForm) {
                            $filledFormUser = $filledForm->getUser();
                            if (($user === $filledFormUser && ($filledForm->getStatus() == 0 || $filledForm->getStatus() == 2)) || ($user->isDO() && $user->getUniversity() === $filledFormUser->getUniversity())) {
                                $filledData = $filledForm->getContent();
                                $comments = $filledForm->getComments();
                                $signatures = $filledForm->getSignatures();
                                $dataCount = count($data);
                                for ($i = 0; $i < $dataCount; $i++) {
                                    $components = $data[$i]['components'];
                                    $componentsCount = count($data[$i]['components']);
                                    for ($j = 0; $j < $componentsCount; $j++) {
                                        $colsCount = count($data[$i]['components'][$j]);
                                        for ($k = 0; $k < $colsCount; $k++) {
                                            $col = $data[$i]['components'][$j][$k];
                                            if ($col['content_type'] === 'input') {
                                                $name = $col['input']['name'];
                                                if ($filledForm->getStatus() == 1 || $filledForm->getStatus() == 2 || $user !== $filledFormUser) {
                                                    $data[$i]['components'][$j][$k]['input']['disabled'] = true;
                                                    $data[$i]['components'][$j][$k]['input']['read_only'] = true;
                                                }
                                                if (isset($filledData[$name])) {
                                                    $data[$i]['components'][$j][$k]['input']['value'] = $filledData[$name];
                                                }
                                                if (isset($comments[$name])) {
                                                    if ($user === $filledFormUser) {
                                                        $data[$i]['components'][$j][$k]['input']['disabled'] = false;
                                                        $data[$i]['components'][$j][$k]['input']['read_only'] = false;
                                                    }
                                                    $data[$i]['components'][$j][$k]['input']['comments'] = $comments[$name];
                                                }
                                                if (isset($signatures[$name])) {
                                                    $data[$i]['components'][$j][$k]['input']['value'] = $signatures[$name]['value'];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $code = 'success';
                    $msg = $item->getName();
                    $formSlug = $item->getCode();
                }
            } else {
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'pdf_code' => $formSlug, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Retrieves the content of a partially filled form.
     * @FOSRest\Get(path="/api/get-my-dsa-form-content")
     */
    public function getMyDsaFormContentAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $formId = $request->get('pdf_code', null);
            $payload = $this->decodeJWT($jwt);
            $data = null;

            if ($payload) {
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                $filledForm = $entityManager->getRepository(DsaFormFilled::class)->findOneBy(['id' => $formId, 'user' => $user]);
                if (!$filledForm) {
                    $filledForm = new DsaFormFilled();
                }
                $item = $filledForm->getDsaForm();

                if ($item) {
                    $data = $item->getContent();
                    $dataCount = count($data);
                    $filledData = $filledForm->getContent();

                    for ($i = 0; $i < $dataCount; $i++) {
                        $components = $data[$i]['components'];
                        $componentsCount = count($data[$i]['components']);
                        for ($j = 0; $j < $componentsCount; $j++) {
                            $colsCount = count($data[$i]['components'][$j]);
                            for ($k = 0; $k < $colsCount; $k++) {
                                $col = $data[$i]['components'][$j][$k];
                                if ($col['content_type'] === 'input') {
                                    $name = $col['input']['name'];
                                    if (isset($filledData[$name])) {
                                        $data[$i]['components'][$j][$k]['input']['value'] = $filledData[$name];
                                    }
                                }
                            }
                        }
                    }
                    $code = 'success';
                    $msg = $item->getName();
                } else {
                    $code = 'error';
                    $msg = 'Invalid form identifier supplied';
                }
            } else {
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Fills a form with received data.
     * @FOSRest\Post(path="/api/fill-pdf-form")
     */
    public function fillPdfFormAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $data = $request->get('data');
            $signaturesInfo = $request->get('signaturesInfo');

            if ($data || $signaturesInfo) {
                $payload = $this->decodeJWT($jwt);
                if ($payload) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                    $univFromSlug = $entityManager->getRepository(University::class)->findOneBy(['token' => $request->get('univ_slug')]);
                    $univFromUser = $user->getUniversity();
                    $univForm = $entityManager->getRepository(UniversityDsaForm::class)->findOneBy(['university' => $univFromUser, 'dsa_form_slug' => $request->get('form_slug')]);

                    if ($univFromSlug === $univFromUser && $univForm) {
                        $item = $univForm->getDsa_form();
                        if ($data['id'] === 0) {
                            $filledForm = new DsaFormFilled();
                        } else {
                            $filledForm = $entityManager->getRepository(DsaFormFilled::class)->findOneBy(['id' => $data['id'], 'user' => $user]);
                        }

                        if ($filledForm) {
                            $cleanData = $data;
                            unset($cleanData['id']);
                            unset($cleanData['full_submit']);
                            $now = time();
                            $filledForm->setContent($cleanData);
                            $filledForm->setDsaForm($item);
                            $filledForm->setUser($user);
                            $filledForm->setCreated_at($now);
                            $filledForm->setSignatures($signaturesInfo);
                            $filledForm->setStatus($data['full_submit'] ? 1 : 0);
                            $entityManager->persist($filledForm);
                            $entityManager->flush();
                            $code = 'success';

                            if ($data['full_submit']) {
                                $disabOfficers = StaticMembers::executeRawSQL($entityManager, "SELECT * FROM `user` where `university_id` = " . $univFromUser->getId() . " and json_contains(roles, json_array('do')) = 1");
                                $headline = date('Y/m/d H:i:s', $now);
                                $route = 'dsa-form/' . $univFromUser->getToken() . '/' . $univForm->getDsa_form_slug() . '/' . $filledForm->getId();
                                $notifAux = $this->createNotification('You have submitted a new DSA Form', 'Your "' . $item->getName() . '" has been submitted. You can check its status <a href="/#/my-dsa-forms">here</a>.', $headline, $user, 1, 2);
                                $entityManager->persist($notifAux);
                                foreach ($disabOfficers as $do) {
                                    $doEntity = $entityManager->getRepository(User::class)->find($do['id']);
                                    $notifAux = $this->createNotification('New DSA Form submitted by ' . $user->__toString(), 'A new "' . $item->getName() . '" has been submitted. You can review it <a href="/#/' . $route . '">here</a>.', $headline, $doEntity, 1, 1);
                                    $entityManager->persist($notifAux);
                                }
                                $msg = "Your form has been submitted.";
                                $entityManager->flush();
                            } else {
                                $msg = "Progress saved";
                            }
                        } else {
                            $code = 'error';
                            $msg = 'Invalid PDF supplied';
                        }
                    } else {
                        $code = 'error';
                        $msg = 'Invalid PDF supplied';
                    }
                } else {
                    $code = 'error';
                    $msg = 'Your session has expired. Please, proceed to the login page';
                }
            } else {
                $code = 'warning';
                $msg = 'Your request did not include any data';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $filledForm->getId()], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Returns a filled PDF.
     * @FOSRest\Get(path="/api/get-file")
     */
    public function getFileAction(Request $request) {
        try {
            $file = $request->get('file');
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $data = null;

            if ($payload) {
                $entityManager = $this->getDoctrine()->getManager();
                $filledForm = $entityManager->getRepository(DsaFormFilled::class)->find($file);
                if ($filledForm) {
                    $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                    $filledUser = $filledForm->getUser();
                    $userId = null;
                    if ($filledUser === $user) {
                        $userId = $user->getId();
                    } else if ($filledUser->getUniversity() === $user->getUniversity()) {
                        $userId = $filledUser->getId();
                    }
                    $filename = $filledForm->getFilename();
                    $file = new File($this->container->getParameter('kernel.project_dir') . '/app_data/dsa_forms_filled/' . $filename);
                    return $this->file($file);
                } else {
                    $code = 'error';
                    $msg = 'File not found';
                }
            } else {
                $code = 'error';
                $msg = 'Invalid request';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            return new JsonResponse(['code' => 'error', 'msg' => $exc->getMessage(), 'data' => null], Response::HTTP_OK);
        }
    }

    private function deleteActivsOrNotifs(Request $request, $type) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $data = $request->get('data', null);
            $res = ['notif' => [], 'activ' => []];
            if ($data) {
                $payload = $this->decodeJWT($jwt);
                if ($payload) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                    foreach ($data as $value) {
                        $entity = $entityManager->getRepository(Notification::class)->findOneBy(['id' => $value, 'user' => $user, 'type' => $type]);
                        if ($entity) {
                            $entityManager->remove($entity);
                        }
                    }
                    $entityManager->flush();
                    $res['notif'] = $this->getNotifications($payload->user_id, 1);
                    $res['activ'] = $this->getNotifications($payload->user_id, 2);
                    $code = 'success';
                    $msg = 'Activities removed';
                } else {
                    $code = 'error';
                    $msg = 'Your session has expired. Please, proceed to the login page';
                }
            } else {
                $code = 'warning';
                $msg = 'Your request did not include any data';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $res], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Deletes user notifications.
     * @FOSRest\Post(path="/api/delete-activities")
     */
    public function deleteActivitiesAction(Request $request) {
        return $this->deleteActivsOrNotifs($request, 2);
    }

    /**
     * Deletes user notifications.
     * @FOSRest\Post(path="/api/delete-notifications")
     */
    public function deleteNotificationsAction(Request $request) {
        return $this->deleteActivsOrNotifs($request, 1);
    }

    private function getNotifications($user_id, $type) {
        $res = [];
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($user_id);
        $entities = $entityManager->getRepository(Notification::class)->findBy(['user' => $user, 'type' => $type], ['created_at' => 'desc']);
        $notifCount = count($entities);
        $counter = 0;
        foreach ($entities as $entity) {
            if ($counter < 5) {
                $currentTime = time();
                $diff = $this->dateDiff($entity->getCreated_at(), $currentTime);
                $res[] = [
                    'id' => $entity->getId(),
                    'created_at' => $entity->getHeadline(),
                    'title' => $entity->getTitle(),
                    'subtitle' => $entity->getSubtitle(),
                    'headline' => $diff
                ];
                $counter++;
            } else {
                break;
            }
        }
        return ['count' => $notifCount, 'items' => $res];
    }

    private function dateDiff($time1, $time2, $precision = 6) {
        // If not numeric then convert texts to unix timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }

        // If time1 is bigger than time2
        // Then swap time1 and time2
        if ($time1 > $time2) {
            $ttime = $time1;
            $time1 = $time2;
            $time2 = $ttime;
        }

        // Set up intervals and diffs arrays
        $intervals = array('year', 'month', 'day', 'hour', 'minute', 'second');
        $diffs = array();

        // Loop thru all intervals
        foreach ($intervals as $interval) {
            // Create temp time from time1 and interval
            $ttime = strtotime('+1 ' . $interval, $time1);
            // Set initial values
            $add = 1;
            $looped = 0;
            // Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
                // Create new temp time from time1 and interval
                $add++;
                $ttime = strtotime("+" . $add . " " . $interval, $time1);
                $looped++;
            }

            $time1 = strtotime("+" . $looped . " " . $interval, $time1);
            $diffs[$interval] = $looped;
        }

        $count = 0;
        $times = array();
        // Loop thru all diffs
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
            // Add value and interval 
            // if value is bigger than 0
            if ($value > 0) {
                // Add s if value is not 1
                if ($value != 1) {
                    $interval .= "s";
                }
                // Add value and interval to times array
                $times[] = $value . " " . $interval;
                $count++;
            }
        }
        array_splice($times, 1);
        // Return string with times
        return implode(", ", $times);
    }

    /**
     * Deletes user notifications.
     * @FOSRest\Post(path="/api/get-notifications")
     */
    public function getNotificationsAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $res = ['notif' => [], 'activ' => []];
            $payload = $this->decodeJWT($jwt);
            if ($payload) {
                $res['notif'] = $this->getNotifications($payload->user_id, 1);
                $res['activ'] = $this->getNotifications($payload->user_id, 2);
                $code = 'success';
                $msg = 'Notifications updated';
            } else {
                $code = 'error';
                $msg = 'Your session has expired. Please, proceed to the login page';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $res], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Retrieves the list of filled DSA forms.
     * @FOSRest\Get(path="/api/get-my-dsa-forms")
     */
    public function getMyDsaFormsAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                $univ = $user->getUniversity();
                $filledForms = $entityManager->getRepository(DsaFormFilled::class)->findBy(['user' => $user], ['created_at' => 'desc']);
                $data = [];
                foreach ($filledForms as $filledForm) {
                    $form = $filledForm->getDsaForm();
                    $univForm = $entityManager->getRepository(UniversityDsaForm::class)->findOneBy(['university' => $univ, 'dsa_form' => $form]);
                    $data[] = [
                        'id' => $filledForm->getId(),
                        'pdf_name' => $form->getName(),
                        'status' => $filledForm->getStatus(),
                        'status_desc' => $this->getFormStatusDesc($filledForm->getStatus()),
                        'route' => 'dsa-form/' . $univ->getToken() . '/' . $univForm->getDsa_form_slug() . '/' . $filledForm->getId(),
                        'created_at' => date('Y/m/d H:i:s', $filledForm->getCreated_at()),
                    ];
                }
                $code = 'success';
                $msg = "List of available DSA forms";
            } else {
                $data = null;
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Retrieves the list of active ACs.
     * @FOSRest\Get(path="/api/get-active-assessment-centres")
     */
    public function getActiveAssessmentCentresAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                $userACs = $user->getAssessmentCentres();
                $entities = $entityManager->getRepository(AssessmentCenterUser::class)->getActiveACs();
                $data = [];
                foreach ($entities as $entity) {
                    $admin = $entityManager->getRepository(AssessmentCenterUser::class)->getACAdmin($entity);
                    $data[] = [
                        'id' => $entity->getId(),
                        'name' => $entity->getName(),
                        'address' => $entity->getAddress(),
                        'manager' => $admin ? $admin->__toString() : null,
                        'registered' => StaticMembers::contains($userACs, $entity),
                        'route' => 'assessment-centre/' . $entity->getUrl()
                    ];
                }
                $code = 'success';
                $msg = "Active ACs";
            } else {
                $data = null;
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    private function validatePNGImage($data) {
        try {
            $img = imagecreatefrompng($data);
            return true;
        } catch (Exception $exc) {
            return false;
        }
    }

    /**
     * Uploads an image with a signature.
     * @FOSRest\Post(path="/api/upload-signature")
     */
    public function uploadSignatureAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $data = null;
            if ($payload) {
                $file = $request->get('file');
                if ($file && $this->validatePNGImage($file)) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                    $user->setSignature($file);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $data = $file;
                    $code = 'success';
                    $msg = 'Signature has been uploaded';
                } else {
                    $code = 'warning';
                    $msg = 'You must submit a valid PNG image';
                }
            } else {
                $randomCode = $request->get('random_code');
                if ($randomCode) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $qrCode = $entityManager->getRepository(QrCode2::class)->findOneBy(['random_code' => $randomCode]);
                    if ($qrCode) {
                        $file = $request->get('file');
                        if ($file && $this->validatePNGImage($file)) {
                            $qrCode->setContent($file);
                            $entityManager->persist($qrCode);
                            $entityManager->flush();
                            $code = 'success';
                            $msg = 'Signature has been uploaded';
                        } else {
                            $code = 'warning';
                            $msg = 'You must submit a valid PNG image';
                        }
                    } else {
                        $code = 'error';
                        $msg = 'This code is not available anymore';
                    }
                } else {
                    $code = 'error';
                    $msg = 'Invalid parameter supplied. You may need to renew your session';
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
     * Generates a temporary QR Code.
     * @FOSRest\Get(path="/api/generate-qr-code")
     */
    public function generateQrCodeAction(Request $request) {
        try {
            $data = null;
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                $randomCode = StaticMembers::random_str(16);
                $qrEntity = $entityManager->getRepository(QrCode2::class)->findOneBy(['random_code' => $randomCode]);
                while ($qrEntity) {
                    $randomCode = StaticMembers::random_str(16);
                    $qrEntity = $entityManager->getRepository(QrCode2::class)->findOneBy(['random_code' => $randomCode]);
                }
                $qrEntity = new QrCode2();
                $qrEntity->setCreated_at(time());
                $qrEntity->setRandom_code($randomCode);
                $qrEntity->setUser($user);
                $entityManager->persist($qrEntity);
                $entityManager->flush();
                $options = new QROptions([
                    'version' => 5,
                    'outputType' => QRCode::OUTPUT_MARKUP_SVG,
                    'eccLevel' => QRCode::ECC_L,
                ]);
                $qrCode = new QRCode($options);
                $data = ['qr_code' => 'data:image/svg+xml;base64,' . base64_encode($qrCode->render($randomCode)), 'random_code' => $randomCode];
                $code = 'success';
                $msg = 'Signature has been uploaded';
            } else {
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Validates QR Code.
     * @FOSRest\Get(path="/api/get-signature-by-random-code")
     */
    public function getSignatureByRandomCodeAction(Request $request) {
        try {
            $data = null;
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {
                $randomCode = $request->get('random_code');
                if ($randomCode) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                    $qrEntity = $entityManager->getRepository(QrCode2::class)->findOneBy(['random_code' => $randomCode, 'user' => $user]);
                    if ($qrEntity && $qrEntity->getContent()) {
                        $data = $qrEntity->getContent();
                        /* $entityManager->remove($qrEntity);
                          $entityManager->flush(); */
                        $code = 'success';
                        $msg = 'Signature has been retrieved';
                    } else {
                        $code = 'warning';
                        $msg = "You have not uploaded your signature yet";
                    }
                } else {
                    $code = 'warning';
                    $msg = 'Your request did not include any data';
                }
            } else {
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    private function getFormStatusDesc($status) {
        switch ($status) {
            case 0:
                return 'Draft';
                break;
            case 1:
                return 'Submitted to DO';
                break;
            case 2:
                return 'Revision requested';
                break;
            case 3:
                return 'Approved by DO';
                break;
            default:
                return null;
                break;
        }
    }

    /**
     * Retrieves the list of submitted DSA forms of the DO's unviersity.
     * @FOSRest\Get(path="/api/get-do-dsa-forms")
     */
    public function getDoDsaFormsAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                $univ = $user->getUniversity();
                $students = $entityManager->getRepository(User::class)->findBy(['university' => $univ]);
                $filledForms = $entityManager->getRepository(DsaFormFilled::class)->findBy([/* 'status' => [1, 2, 3], */'user' => $students]);
                $data = [];

                foreach ($filledForms as $filledForm) {
                    $form = $filledForm->getDsaForm();
                    $univForm = $entityManager->getRepository(UniversityDsaForm::class)->findOneBy(['university' => $univ, 'dsa_form' => $form]);
                    $data[] = [
                        'id' => $filledForm->getId(),
                        'student_name' => $filledForm->getUser()->__toString(),
                        'student_email' => $filledForm->getUser()->getEmail(),
                        'univ_name' => $univ->getName(),
                        'pdf_name' => $form->getName(),
                        'pdf_code' => $form->getCode(),
                        'filename' => $filledForm->getFilename(),
                        'status' => $filledForm->getStatus(),
                        'route' => 'dsa-form/' . $univ->getToken() . '/' . $univForm->getDsa_form_slug() . '/' . $filledForm->getId(),
                        'status_desc' => $this->getFormStatusDesc($filledForm->getStatus()),
                        'created_at' => date('Y/m/d H:i:s', $filledForm->getCreated_at()),
                    ];
                }
                $code = 'success';
                $msg = "List of available DSA forms";
            } else {
                $data = null;
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    private function createNotification($title, $subtitle, $headline, $user, $status, $type) {
        $notif = new Notification();
        $notif->setTitle($title);
        $notif->setSubtitle($subtitle);
        $notif->setHeadline($headline);
        $notif->setUser($user);
        $notif->setStatus($status);
        $notif->setType($type);
        $notif->setCreated_at(time());
        return $notif;
    }

    private function getInputMetadata($data, $name) {
        //$data = $item->getContent();
        $res = null;
        $dataCount = count($data);
        for ($i = 0; $i < $dataCount; $i++) {
            $components = $data[$i]['components'];
            $componentsCount = count($data[$i]['components']);
            for ($j = 0; $j < $componentsCount; $j++) {
                $colsCount = count($data[$i]['components'][$j]);
                for ($k = 0; $k < $colsCount; $k++) {
                    $col = $data[$i]['components'][$j][$k];
                    if ($col['content_type'] === 'input' && $col['input']['name'] === $name) {
                        return $col['input']['metadata'];
                    }
                }
            }
        }
        return $res;
    }

    private function insertMetadata($data, $pdfContent) {
        foreach ($data as $key => $value) {
            $data[$key] = $this->getInputMetadata($pdfContent, $key);
            $data[$key]['value'] = $value['value'];
        }
        return $data;
    }

    /**
     * Approves a form
     * @FOSRest\Post(path="/api/do-approve-form")
     */
    public function doApproveFormAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $data = null;
            $filledName = null;

            if ($payload) {
                $formId = $request->get('form_id');
                if ($formId) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                    $filledForm = $entityManager->getRepository(DsaFormFilled::class)->find($formId);
                    if ($filledForm) {
                        switch ($filledForm->getStatus()) {
                            case 0:
                                $code = 'error';
                                $msg = 'This form has not been submitted yet by the student.';
                                break;
                            case 3:
                                $code = 'warning';
                                $msg = 'This form has been already approved.';
                                break;
                            case 1:
                            case 2:
                                $student = $filledForm->getUser();
                                if ($user->getUniversity() === $student->getUniversity()) {
                                    $item = $filledForm->getDsaForm();
                                    $originDir = $this->container->getParameter('kernel.project_dir') . '/public/dsa_forms/';
                                    $originPath = $originDir . $item->getBase();

                                    if (file_exists($originPath)) {
                                        $pdf = new Pdf($originPath);
                                        $pdfNameWithoutExt = str_replace('.pdf', '', $item->getBase());
                                        $filledName = $pdfNameWithoutExt . '-' . time() . '.pdf';
                                        $destinationDir = $this->container->getParameter('kernel.project_dir') . '/app_data/dsa_forms_filled/';
                                        if (!file_exists($destinationDir)) {
                                            mkdir($destinationDir);
                                        }
                                        $destinationPath = $destinationDir . $filledName;

                                        if ($pdf->fillForm($filledForm->getContent())->needAppearances()->saveAs($destinationPath)) {
                                            //SELECT JSON_EXTRACT(`content`, '$[*].components[*][*].input') FROM `dsa_form` where `code` = 'sfe_dsa_costs_claim_form_1718_d'
                                            $signaturesInfo = $filledForm->getSignatures();
                                            if ($signaturesInfo) {
                                                $signaturesInfo = $this->insertMetadata($signaturesInfo, $item->getContent());
                                                usort($signaturesInfo, function($a, $b) {
                                                    return strcmp($a['page'], $b['page']);
                                                });
                                                $pdf2 = new Pdf($pdf);
                                                $pdfData = $pdf2->getData();
                                                $pagesCount = 0;
                                                if (preg_match('/NumberOfPages: (\d+)/', $pdfData, $m)) {
                                                    $pagesCount = $m[1];
                                                }
                                                $pdf = new TcpdfFpdi();
                                                $pdf->setPrintHeader(false);
                                                $pdf->setPrintFooter(false);
                                                $pdf->SetAutoPageBreak(false, 0);
                                                $currentPage = 0;

                                                foreach ($signaturesInfo as $signatureInfo) {
                                                    while ($signatureInfo['page'] > $currentPage) {
                                                        $pdf->AddPage();
                                                        $currentPage++;
                                                    }
                                                    $image = imagecreatefrompng($signatureInfo['value']);
                                                    $cropped = imagecropauto($image, IMG_CROP_DEFAULT);
                                                    if ($cropped) {
                                                        imagedestroy($image);
                                                        $image = $cropped;
                                                    }
                                                    $signatureTemp = $destinationDir . '_signature_temp.png';
                                                    imagepng($image, $signatureTemp, 0);
                                                    $imgdata = file_get_contents($signatureTemp);
                                                    //$encoded = base64_encode($imgdata);
                                                    /* $imgdata = base64_decode(str_replace('data:image/png;base64,', '', $signatureInfo['value']));
                                                      $pdf->Image('@' . $imgdata, $signatureInfo['x'], $signatureInfo['y'], $signatureInfo['width'], $signatureInfo['height'], 'PNG', '', '', true, 300, '', false, false, 0, true, false, true); */
                                                    $pdf->Image('@' . $imgdata, $signatureInfo['x'], $signatureInfo['y'], $signatureInfo['width'], $signatureInfo['height'], 'PNG', '', '', true, 300, '', false, false, 0, true, false, true);
                                                    //$pdf->Image($signatureTemp, $signatureInfo['x'], $signatureInfo['y'], $signatureInfo['width'], $signatureInfo['height'], 'PNG', '', '', true, 300, '', false, false, 0, true, false, true);
                                                    unlink($signatureTemp);
                                                }
                                                while ($currentPage < $pagesCount) {
                                                    $pdf->AddPage();
                                                    $currentPage++;
                                                }
                                                $signaturePath = $destinationDir . '_signature_temp.pdf';
                                                $pdf->Output($signaturePath, 'F');
                                                $mergedPath = $destinationDir . '/_signed_temp.pdf';
                                                $pdf = new Pdf($destinationPath);
                                                $pdf->multiStamp($signaturePath)->saveAs($mergedPath);
                                                unlink($signaturePath);
                                                unlink($destinationPath);
                                                rename($mergedPath, $destinationPath);
                                                $entityManager->persist($filledForm);
                                            }
                                            $filledForm->setStatus(3);
                                            $filledForm->setFilename($filledName);
                                            $entityManager->persist($filledForm);
                                            $now = time();
                                            $notif = $this->createNotification('You have approved a new form', 'The "' . $filledForm->getDsaForm()->getName() . '" submitted on ' . date('Y/m/d H:i:s', $filledForm->getCreated_at()) . 'by <i>' . $student->__toString() . '</i>', date('Y/m/d H:i:s', $now), $user, 1, 2);
                                            $entityManager->persist($notif);
                                            $notif = $this->createNotification('Your form has been approved', 'Your ' . $filledForm->getDsaForm()->getName() . ', submitted on ' . date('Y/m/d H:i:s', $filledForm->getCreated_at()) . ', has been approved by <i>' . $user->__toString() . '</i>', date('Y/m/d H:i:s', $now), $student, 1, 1);
                                            $entityManager->persist($notif);
                                            $entityManager->flush();
                                            $data = $filledForm->getStatus();
                                            $code = 'success';
                                            $msg = "The form has been approved.";
                                        } else {
                                            $code = 'error';
                                            $msg = $pdf->getError();
                                            $entityManager->refresh($filledForm);
                                        }
                                    } else {
                                        $code = 'error';
                                        $msg = 'Specified PDF Form does not exist on the filesystem';
                                        $entityManager->refresh($filledForm);
                                    }
                                } else {
                                    $code = 'error';
                                    $msg = 'Invalid form supplied.';
                                }
                            default:
                                break;
                        }
                    } else {
                        $code = 'error';
                        $msg = 'Invalid form supplied.';
                    }
                } else {
                    $code = 'error';
                    $msg = 'Invalid form supplied.';
                }
            } else {
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'filename' => $filledName, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Register with AC
     * @FOSRest\Post(path="/api/register-with-ac")
     */
    public function registerWithAC(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $data = null;

            if ($payload) {
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                $ac = $entityManager->getRepository(AssessmentCenter::class)->find($request->get('id'));
                if ($ac) {
                    $acUser = $entityManager->getRepository(AssessmentCenterUser::class)->findOneBy(['ac' => $ac, 'user' => $user]);
                    if (!$acUser) {
                        $acUser = new AssessmentCenterUser();
                        $acUser->setAc($ac);
                        $acUser->setIs_admin(0);
                        $acUser->setUser($user);
                        $entityManager->persist($acUser);
                        $entityManager->flush();
                        $code = 'success';
                        $msg = 'AC registered';
                        $data = true;
                    }
                } else {
                    $code = 'error';
                    $msg = 'Invalid id supplied.';
                }
            } else {
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Approves a form
     * @FOSRest\Post(path="/api/cancel-reg-with-ac")
     */
    public function cancelRegWithAC(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);
            $data = null;

            if ($payload) {
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                $ac = $entityManager->getRepository(AssessmentCenter::class)->find($request->get('ac_id'));
                $acUser = $entityManager->getRepository(AssessmentCenterUser::class)->findOneBy(['ac' => $ac, 'user' => $user]);
                if ($acUser) {
                    $entityManager->remove($acUser);
                    $entityManager->flush();
                    $code = 'success';
                    $msg = 'Registration has been cancelled';
                    $data = false;
                } else {
                    $code = 'error';
                    $msg = 'Invalid id supplied.';
                }
            } else {
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Sets DSA Form parameters.
     * @FOSRest\Post(path="/api/set-institute-info")
     */
    public function setDsaFormsParamsAction(Request $request) {
        try {
            $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
            $payload = $this->decodeJWT($jwt);

            if ($payload) {
                $univSlug = $request->get('slug');
                $data = $request->get('data');
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($payload->user_id);

                if ($user->isDO() && $univSlug) {
                    $univ = $user->getUniversity();
                    $univ->setToken($univSlug);
                    $entityManager->persist($univ);
                    foreach ($data as $itemData) {
                        $item = $entityManager->getRepository(UniversityDsaForm::class)->find($itemData['id']);
                        if ($item) {
                            $item->setActive($itemData['active']);
                            $item->setDsa_form_slug($itemData['slug']);
                            $entityManager->persist($item);
                        }
                    }
                    $entityManager->flush();
                    $code = 'success';
                    $msg = "Your changes have been submitted.";
                } else {
                    $code = 'error';
                    $msg = 'Invalid or missing parameters.';
                }
            } else {
                $code = 'error';
                $msg = 'Invalid parameter supplied. You may need to renew your session.';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Sets DSA Form parameters.
     * @FOSRest\Get(path="/api/validate-random-qr-code")
     */
    public function setValidateRandomCodeAction(Request $request) {
        try {
            $randomCode = $request->get('random_code');
            $entityManager = $this->getDoctrine()->getManager();
            $qrCode = $entityManager->getRepository(QrCode2::class)->findOneBy(['random_code' => $randomCode]);
            if ($qrCode) {
                $data = $qrCode->getRandom_code();
                $code = 'success';
                $msg = "Your changes have been submitted.";
            } else {
                $code = 'error';
                $msg = 'Invalid credentials.';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $data], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

    /**
     * Fills a form with received data.
     * @FOSRest\Post(path="/api/send-dsa-form-comment")
     */
    public function sendDsaFormCommentAction(Request $request) {
        try {
            $entityId = null;
            $headline = null;
            $index = null;
            $formSlug = $request->get('form_slug');
            $univSlug = $request->get('univ_slug');

            if ($formSlug && $univSlug) {
                $jwt = str_replace('Bearer ', '', $request->headers->get('authorization'));
                $payload = $this->decodeJWT($jwt);
                if ($payload) {
                    $entityManager = $this->getDoctrine()->getManager();
                    $user = $entityManager->getRepository(User::class)->find($payload->user_id);
                    $univFromSlug = $entityManager->getRepository(University::class)->findOneBy(['token' => $univSlug]);
                    $univFromUser = $user->getUniversity();
                    $univForm = $entityManager->getRepository(UniversityDsaForm::class)->findOneBy(['university' => $univFromUser, 'dsa_form_slug' => $formSlug]);

                    if ($univFromSlug === $univFromUser && $univForm) {
                        $dsaForm = $univForm->getDsa_form();
                        $entityId = $request->get('id');
                        if ($entityId === 0) {
                            $filledForm = new DsaFormFilled();
                            $filledForm->setUser($user);
                            $filledForm->setDsaForm($dsaForm);
                            $filledForm->setCreated_at(time());
                            $filledForm->setContent([]);
                            $filledForm->setStatus(0);
                        } else {
                            $filledForm = $entityManager->getRepository(DsaFormFilled::class)->findOneBy(['id' => $entityId]);
                        }

                        if ($filledForm && ($filledForm->getUser() === $user || $filledForm->getUser()->getUniversity()->getManager() === $user)) {
                            $newComment = $request->get('comment');
                            $fieldName = $request->get('field_name');
                            $index = $request->get('index');
                            $now = time();
                            $headline = date('Y/m/d H:i:s', $now);
                            $newComment['created_at'] = $now;
                            $newComment['headline'] = $headline;
                            $newComment['status'] = 2;
                            $filledForm->addComment($newComment, $fieldName);
                            if ($filledForm->getStatus() === 1 && $user->isDO()) {
                                $filledForm->setStatus(2);
                            }
                            $entityManager->persist($filledForm);
                            $entityManager->flush();

                            $route = 'dsa-form/' . $univFromUser->getToken() . '/' . $univForm->getDsa_form_slug() . '/' . $filledForm->getId();

                            if ($user->isStudent()) {
                                $notifAux = $this->createNotification('You have submitted a new comment', '<b>' . $fieldName . '</b> input field from your <b>' . $dsaForm->getName() . '</b>. You can check it <a href="/#/' . $route . '">here</a>.', $headline, $user, 1, 2);
                                $entityManager->persist($notifAux);
                                $disabOfficers = StaticMembers::executeRawSQL($entityManager, "SELECT * FROM `user` where `university_id` = " . $univFromUser->getId() . " and json_contains(roles, json_array('do')) = 1");
                                foreach ($disabOfficers as $do) {
                                    $doEntity = $entityManager->getRepository(User::class)->find($do['id']);
                                    $notifAux = $this->createNotification('New comment submitted by ' . $user->__toString(), '<b>' . $fieldName . '</b> input field from "' . $dsaForm->getName() . '" has been commented. You can check it <a href="/#/' . $route . '">here</a>.', $headline, $doEntity, 1, 1);
                                    $entityManager->persist($notifAux);
                                }
                            } else {
                                $notifAux = $this->createNotification('You have submitted a new comment', '<b>' . $fieldName . '</b> in a form submitted by <b>' . $filledForm->getUser()->__toString() . '</b>. You can check it <a href="/#/' . $route . '">here</a>.', $headline, $user, 1, 2);
                                $entityManager->persist($notifAux);
                                $notifAux = $this->createNotification('New comment submitted by ' . $user->__toString(), '<b>' . $fieldName . '</b> input field from "' . $dsaForm->getName() . '" has been commented. You can check it <a href="/#/' . $route . '">here</a>.', $headline, $filledForm->getUser(), 1, 1);
                                $entityManager->persist($notifAux);
                            }
                            $msg = "Your comment has been submitted.";
                            $code = 'success';
                            $entityManager->flush();
                            $entityId = $filledForm->getId();
                        } else {
                            $code = 'error';
                            $msg = 'Invalid PDF supplied';
                        }
                    } else {
                        $code = 'error';
                        $msg = 'Invalid PDF supplied';
                    }
                } else {
                    $code = 'error';
                    $msg = 'Your session has expired. Please, proceed to the login page';
                }
            } else {
                $code = 'warning';
                $msg = 'Your request did not include any data';
            }
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => $entityId, 'headline' => $headline, 'index' => $index], Response::HTTP_OK);
        } catch (Exception $exc) {
            $code = 'error';
            $msg = $exc->getMessage();
            return new JsonResponse(['code' => $code, 'msg' => $msg, 'data' => null], Response::HTTP_OK);
        }
    }

}
