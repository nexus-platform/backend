<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DsaFormFilledRepository")
 * @ApiResource
 */
class DsaFormFilled {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var DsaForm
     * @ORM\ManyToOne(targetEntity="App\Entity\DsaForm")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id", onDelete="RESTRICT")
     */
    private $dsaForm;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $content = [];

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $signatures = [];

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $comments = [];

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $filename;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     */
    private $status;

    function getSignatures() {
        return $this->signatures;
    }

    function setSignatures($signatures) {
        $this->signatures = $signatures;
    }

    function getFilename() {
        return $this->filename;
    }

    function setFilename($filename) {
        $this->filename = $filename;
    }

    function getComments() {
        return $this->comments;
    }

    function setComments($comments) {
        $this->comments = $comments;
    }

    function addComment($comment, $index) {
        $this->comments[$index][] = $comment;
    }

    function getCreated_at() {
        return $this->created_at;
    }

    function setCreated_at($created_at) {
        $this->created_at = $created_at;
    }

    function getStatus() {
        return $this->status;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent(array $content): void {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    function getUser(): User {
        return $this->user;
    }

    function getDsaForm(): DsaForm {
        return $this->dsaForm;
    }

    function setUser(User $user) {
        $this->user = $user;
    }

    function setDsaForm(DsaForm $dsaForm) {
        $this->dsaForm = $dsaForm;
    }

    function getContentForApproval() {
        $filledData = $this->getContent();
        $template = $this->getDsaForm()->getContent();
        $dataCount = count($template);

        for ($i = 0; $i < $dataCount; $i++) {
            $components = $template[$i]['components'];
            $componentsCount = count($components);
            for ($j = 0; $j < $componentsCount; $j++) {
                $colsCount = count($components[$j]);
                for ($k = 0; $k < $colsCount; $k++) {
                    $col = $components[$j][$k];
                    if ($col['content_type'] === 'input_group') {
                        $inputGroupName = $col['name'];
                        $rowsCount = 0;

                        if (isset($filledData[$inputGroupName])) {
                            $rowsCount = $filledData[$inputGroupName];
                            $models = $col['model'];

                            foreach ($models as $model) {
                                $inputName = $model['input']['name'];
                                $filledData[$inputName] = '';
                                for ($l = 1; $l <= $rowsCount; $l++) {
                                    $newName = "$inputName $l";
                                    if (isset($filledData[$newName])) {
                                        $newValue = $filledData[$newName] . "\r";   //chr(10)
                                        $filledData[$inputName] .= $newValue;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $filledData;
    }

}
