<?php

namespace App\Entity\EA;

use Doctrine\ORM\Mapping as ORM;

/**
 * EaUserSettings
 *
 * @ORM\Table(name="ea_user_settings", indexes={@ORM\Index(name="user_sett_assessment_center", columns={"id_assessment_center"})})
 * @ORM\Entity(repositoryClass="App\Repository\EA\EaUserSettingsRepository")
 */
class EaUserSettings {

    /**
     * @var string|null
     *
     * @ORM\Column(name="username", type="string", length=256, nullable=true)
     */
    private $username;

    /**
     * @var string|null
     *
     * @ORM\Column(name="password", type="string", length=512, nullable=true)
     */
    private $password;

    /**
     * @var string|null
     *
     * @ORM\Column(name="salt", type="string", length=512, nullable=true)
     */
    private $salt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="working_plan", type="text", length=65535, nullable=true)
     */
    private $workingPlan;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="notifications", type="boolean", nullable=true)
     */
    private $notifications = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="google_sync", type="boolean", nullable=true)
     */
    private $googleSync = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="google_token", type="text", length=65535, nullable=true)
     */
    private $googleToken;

    /**
     * @var string|null
     *
     * @ORM\Column(name="google_calendar", type="string", length=128, nullable=true)
     */
    private $googleCalendar;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sync_past_days", type="integer", nullable=true, options={"default"="5"})
     */
    private $syncPastDays = '5';

    /**
     * @var int|null
     *
     * @ORM\Column(name="sync_future_days", type="integer", nullable=true, options={"default"="5"})
     */
    private $syncFutureDays = '5';

    /**
     * @var string|null
     *
     * @ORM\Column(name="calendar_view", type="string", length=32, nullable=true, options={"default"="default"})
     */
    private $calendarView = 'default';

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_assessment_center", type="integer", nullable=false)
     */
    private $id_assessment_center;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_users", type="integer", nullable=false)
     * @ORM\Id
     */
    private $id_users;

    function getUsername() {
        return $this->username;
    }

    function getPassword() {
        return $this->password;
    }

    function getSalt() {
        return $this->salt;
    }

    function getWorkingPlan() {
        return $this->workingPlan;
    }

    function getNotifications() {
        return $this->notifications;
    }

    function getGoogleSync() {
        return $this->googleSync;
    }

    function getGoogleToken() {
        return $this->googleToken;
    }

    function getGoogleCalendar() {
        return $this->googleCalendar;
    }

    function getSyncPastDays() {
        return $this->syncPastDays;
    }

    function getSyncFutureDays() {
        return $this->syncFutureDays;
    }

    function getCalendarView() {
        return $this->calendarView;
    }

    function getIdAssessmentCenter() {
        return $this->id_assessment_center;
    }

    function getIdUsers() {
        return $this->id_users;
    }

    function setUsername($username) {
        $this->username = $username;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setSalt($salt) {
        $this->salt = $salt;
    }

    function setWorkingPlan($workingPlan) {
        $this->workingPlan = $workingPlan;
    }

    function setNotifications($notifications) {
        $this->notifications = $notifications;
    }

    function setGoogleSync($googleSync) {
        $this->googleSync = $googleSync;
    }

    function setGoogleToken($googleToken) {
        $this->googleToken = $googleToken;
    }

    function setGoogleCalendar($googleCalendar) {
        $this->googleCalendar = $googleCalendar;
    }

    function setSyncPastDays($syncPastDays) {
        $this->syncPastDays = $syncPastDays;
    }

    function setSyncFutureDays($syncFutureDays) {
        $this->syncFutureDays = $syncFutureDays;
    }

    function setCalendarView($calendarView) {
        $this->calendarView = $calendarView;
    }

    function setIdAssessmentCenter($id_assessment_center) {
        $this->id_assessment_center = $id_assessment_center;
    }

    function setIdUsers($idUsers) {
        $this->id_users = $idUsers;
    }

}
