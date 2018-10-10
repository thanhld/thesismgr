<?php
/**
 * Created by PhpStorm.
 * User: Can
 * Date: 18-Nov-16
 * Time: 1:57 PM
 */

namespace core\model;


use JsonSerializable;

class Learner implements JsonSerializable
{
    private $id;
    private $username;
    private $vnuMail;
    private $fullname;
    private $learnerType;
    private $otherEmail;
    private $phone;
    private $avatarUrl;
    private $gpa;
    private $description;
    private $learnerCode;
    private $trainingCourseId;
    private $trainingCourseCode;
    private $trainingProgramId;
    private $trainingProgramCode;
    private $trainingAreaId;
    private $trainingAreaCode;

    /**
     * Learner constructor.
     * @param $learner
     * @internal param $id
     * @internal param $vnuMail
     * @internal param $fullname
     * @internal param $learnerType
     * @internal param $otherEmail
     * @internal param $phone
     * @internal param $avatarUrl
     * @internal param $gpa
     * @internal param $description
     * @internal param $learnerCode
     * @internal param $trainingCourseId
     * @internal param $trainingCourseCode
     * @internal param $trainingProgramId
     * @internal param $trainingProgramCode
     * @internal param $trainingAreaId
     * @internal param $trainingAreaCode
     */
    public function __construct($learner)
    {
        $this->id          = isset($learner['id'])          ? $learner['id'] : null;
        $this->username    = isset($learner['username'])    ? $learner['username'] : null;
        $this->vnuMail     = isset($learner['vnuMail'])     ? $learner['vnuMail'] : null;
        $this->fullname    = isset($learner['fullname'])    ? $learner['fullname'] : null;
        $this->learnerType        = isset($learner['learnerType'])        ? intval($learner['learnerType']) : null;
        $this->otherEmail  = isset($learner['otherEmail'])  ? $learner['otherEmail'] : null;
        $this->phone       = isset($learner['phone'])       ? $learner['phone'] : null;
        $this->avatarUrl   = isset($learner['avatarUrl'])   ? $learner['avatarUrl'] : null;
        $this->gpa         = isset($learner['gpa'])         ? doubleval($learner['gpa']) : null;
        $this->description = isset($learner['description']) ? $learner['description'] : null;
        $this->learnerCode = isset($learner['learnerCode']) ? $learner['learnerCode'] : null;
        $this->trainingCourseId      = isset($learner['trainingCourseId'])     ? intval($learner['trainingCourseId']) : null;
        $this->trainingCourseCode    = isset($learner['trainingCourseCode'])   ? $learner['trainingCourseCode'] : null;
        $this->trainingProgramId     = isset($learner['trainingProgramId'])    ? intval($learner['trainingProgramId']) : null;
        $this->trainingProgramCode   = isset($learner['trainingProgramCode'])  ? $learner['trainingProgramCode'] : null;
        $this->trainingAreaId        = isset($learner['trainingAreaId'])       ? intval($learner['trainingAreaId']) : null;
        $this->trainingAreaCode      = isset($learner['trainingAreaCode'])     ? $learner['trainingAreaCode'] : null;
    }

    /**
     * @return bool
     */
    public function checkId() {
        return (!is_null($this->id) && (is_string($this->id) && strlen($this->id) == 32));
    }

    /**
     * @return bool
     */
    public function checkUsername() {
        $usernameRegex = '/^[a-zA-Z0-9_.-]{2,50}$/';
        return (!is_null($this->username) && (is_string($this->username) && strlen($this->username) <= 50) && preg_match($usernameRegex, $this->username) == 1);
    }

    /**
     * @return bool
     */
    public function checkVnuMail() {
        $vnuMailRegex = '/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@vnu.edu.vn$/i';
        return (!is_null($this->vnuMail) && (is_string($this->vnuMail) && strlen($this->vnuMail) <= 255) && preg_match($vnuMailRegex, $this->vnuMail) == 1);
    }

    /**
     * @return bool
     */
    public function checkFullname() {
        return (!is_null($this->fullname) && (is_string($this->fullname) && strlen($this->fullname) <= 255));
    }

    /**
     * @return bool
     */
    public function checkLearnerType() {
        return (is_null($this->learnerType) || is_int($this->learnerType));
    }

    /**
     * @return bool
     */
    public function checkOtherEmail() {
        return (is_null($this->otherEmail) || (is_string($this->otherEmail) && strlen($this->otherEmail) <= 255));
    }

    /**
     * @return bool
     */
    public function checkPhone() {
        return (is_null($this->phone) || (is_string($this->phone) && strlen($this->phone) <= 20));
    }

    /**
     * @return bool
     */
    public function checkAvatarUrl() {
        return (is_null($this->avatarUrl) || (is_string($this->avatarUrl) && strlen($this->avatarUrl) <= 255));
    }

    /**
     * @return bool
     */
    public function checkGpa() {
        return (is_null($this->gpa) || is_double($this->gpa) || is_int($this->gpa));
    }

    /**
     * @return bool
     */
    public function checkDescription() {
        return (is_null($this->description) || is_string($this->description));
    }

    /**
     * @return bool
     */
    public function checkLearnerCode() {
        return (!is_null($this->learnerCode) && (is_string($this->learnerCode) && strlen($this->learnerCode) <= 20));
    }

    /**
     * @return bool
     */
    public function checkTrainingCourseId() {
        return (!is_null($this->trainingCourseId) && is_int($this->trainingCourseId));
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getVnuMail()
    {
        return $this->vnuMail;
    }

    /**
     * @param mixed $vnuMail
     */
    public function setVnuMail($vnuMail)
    {
        $this->vnuMail = $vnuMail;
    }

    /**
     * @return mixed
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * @param mixed $fullname
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }

    /**
     * @return int|null
     */
    public function getLearnerType()
    {
        return $this->learnerType;
    }

    /**
     * @param int|null $learnerType
     */
    public function setLearnerType($learnerType)
    {
        $this->learnerType = $learnerType;
    }

    /**
     * @return mixed
     */
    public function getOtherEmail()
    {
        return $this->otherEmail;
    }

    /**
     * @param mixed $otherEmail
     */
    public function setOtherEmail($otherEmail)
    {
        $this->otherEmail = $otherEmail;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }

    /**
     * @param mixed $avatarUrl
     */
    public function setAvatarUrl($avatarUrl)
    {
        $this->avatarUrl = $avatarUrl;
    }

    /**
     * @return mixed
     */
    public function getGpa()
    {
        return $this->gpa;
    }

    /**
     * @param mixed $gpa
     */
    public function setGpa($gpa)
    {
        $this->gpa = $gpa;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getLearnerCode()
    {
        return $this->learnerCode;
    }

    /**
     * @param mixed $learnerCode
     */
    public function setLearnerCode($learnerCode)
    {
        $this->learnerCode = $learnerCode;
    }

    /**
     * @return mixed
     */
    public function getTrainingCourseId()
    {
        return $this->trainingCourseId;
    }

    /**
     * @param mixed $trainingCourseId
     */
    public function setTrainingCourseId($trainingCourseId)
    {
        $this->trainingCourseId = $trainingCourseId;
    }

    /**
     * @return mixed
     */
    public function getTrainingCourseCode()
    {
        return $this->trainingCourseCode;
    }

    /**
     * @return mixed
     */
    public function getTrainingProgramId()
    {
        return $this->trainingProgramId;
    }

    /**
     * @return mixed
     */
    public function getTrainingProgramCode()
    {
        return $this->trainingProgramCode;
    }

    /**
     * @return mixed
     */
    public function getTrainingAreaId()
    {
        return $this->trainingAreaId;
    }

    /**
     * @return mixed
     */
    public function getTrainingAreaCode()
    {
        return $this->trainingAreaCode;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'username' => $this->username,
            'vnuMail' => $this->vnuMail,
            'fullname' => $this->fullname,
            'learnerType' => $this->learnerType,
            'otherEmail' => $this->otherEmail,
            'phone' => $this->phone,
            'avatarUrl' => $this->avatarUrl,
            'gpa' => $this->gpa,
            'description' => $this->description,
            'learnerCode' => $this->learnerCode,
            'trainingCourseId' => $this->trainingCourseId,
            'trainingCourseCode' => $this->trainingCourseCode,
            'trainingProgramId' => $this->trainingProgramId,
            'trainingProgramCode' => $this->trainingProgramCode,
            'trainingAreaId' => $this->trainingAreaId,
            'trainingAreaCode' => $this->trainingAreaCode
        );
    }
}