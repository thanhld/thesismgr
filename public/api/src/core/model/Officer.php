<?php
/**
 * Created by PhpStorm.
 * User: Can
 * Date: 18-Nov-16
 * Time: 1:57 PM
 */

namespace core\model;


use JsonSerializable;

class Officer implements JsonSerializable
{
    private $id;
    private $role;
    private $username;
    private $vnuMail;
    private $officerCode;
    private $departmentId;
    private $departmentName;
    private $departmentType;
    private $fullname;
    private $otherEmail;
    private $avatarUrl;
    private $phone;
    private $degreeId;
    private $website;
    private $address;
    private $description;
    private $numberOfStudent;
    private $numberOfResearcher;
    private $numberOfGraduated;

    /**
     * Officer constructor.
     * @param $officer
     * @internal param $id
     * @internal param $role
     * @internal param $username
     * @internal param $vnuMail
     * @internal param $officerCode
     * @internal param $departmentId
     * @internal param $departmentName
     * @internal param $departmentType
     * @internal param $fullname
     * @internal param $otherEmail
     * @internal param $avatarUrl
     * @internal param $phoneNumber
     * @internal param $degreeId
     * @internal param $website
     * @internal param $office
     * @internal param $description
     * @internal param $numberOfStudent
     * @internal param $numberOfResearcher
     * @internal param $numberOfGraduatedStudent
     */
    public function __construct($officer)
    {
        $this->id                       = isset($officer['id'])                         ? $officer['id'] : null;
        $this->username                 = isset($officer['username'])                   ? $officer['username'] : null;
        $this->vnuMail                  = isset($officer['vnuMail'])                    ? $officer['vnuMail'] : null;
        $this->role                     = isset($officer['role'])                       ? intval($officer['role']) : null;
        $this->officerCode              = isset($officer['officerCode'])                ? $officer['officerCode'] : null;
        $this->departmentId             = isset($officer['departmentId'])               ? $officer['departmentId'] : null;
        $this->departmentName           = isset($officer['departmentName'])             ? $officer['departmentName'] : null;
        $this->departmentType           = isset($officer['departmentType'])             ? $officer['departmentType'] : null;
        $this->fullname                 = isset($officer['fullname'])                   ? $officer['fullname'] : null;
        $this->otherEmail               = isset($officer['otherEmail'])                 ? $officer['otherEmail'] : null;
        $this->avatarUrl                = isset($officer['avatarUrl'])                  ? $officer['avatarUrl'] : null;
        $this->phone                    = isset($officer['phone'])                      ? $officer['phone'] : null;
        $this->degreeId                 = isset($officer['degreeId'])                   ? intval($officer['degreeId']) : null;
        $this->website                  = isset($officer['website'])                    ? $officer['website'] : null;
        $this->address                  = isset($officer['address'])                    ? $officer['address'] : null;
        $this->description              = isset($officer['description'])                ? $officer['description'] : null;
        $this->numberOfStudent          = isset($officer['numberOfStudent'])            ? doubleval($officer['numberOfStudent']) : null;
        $this->numberOfResearcher       = isset($officer['numberOfResearcher'])         ? doubleval($officer['numberOfResearcher']) : null;
        $this->numberOfGraduated        = isset($officer['numberOfGraduated'])          ? doubleval($officer['numberOfGraduated']) : null;
    }

    /**
     * @return null
     */
    public function getNumberOfResearcher()
    {
        return $this->numberOfResearcher;
    }

    /**
     * @param null $numberOfResearcher
     */
    public function setNumberOfResearcher($numberOfResearcher)
    {
        $this->numberOfResearcher = $numberOfResearcher;
    }

    /**
     * @return null
     */
    public function getNumberOfGraduated()
    {
        return $this->numberOfGraduated;
    }

    /**
     * @param null $numberOfGraduated
     */
    public function setNumberOfGraduatedStudent($numberOfGraduated)
    {
        $this->numberOfGraduated = $numberOfGraduated;
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
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
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
    public function getOfficerCode()
    {
        return $this->officerCode;
    }

    /**
     * @param mixed $officerCode
     */
    public function setOfficerCode($officerCode)
    {
        $this->officerCode = $officerCode;
    }

    /**
     * @return mixed
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    /**
     * @param mixed $departmentId
     */
    public function setDepartmentId($departmentId)
    {
        $this->departmentId = $departmentId;
    }

    /**
     * @return mixed
     */
    public function getDepartmentName()
    {
        return $this->departmentName;
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
    public function getDegreeId()
    {
        return $this->degreeId;
    }

    /**
     * @param mixed $degreeId
     */
    public function setDegreeId($degreeId)
    {
        $this->degreeId = $degreeId;
    }

    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param mixed $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
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
    public function getNumberOfStudent()
    {
        return $this->numberOfStudent;
    }

    /**
     * @param mixed $numberOfStudent
     */
    public function setNumberOfStudent($numberOfStudent)
    {
        $this->numberOfStudent = $numberOfStudent;
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
    public function checkRole() {
        return (!is_null($this->role) && is_int($this->role) && $this->role >= 3 && $this->role <= 6);
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
    public function checkOfficerCode() {
        return (is_null($this->officerCode) || (is_string($this->officerCode) && strlen($this->officerCode) <= 20));
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
    public function checkDegreeId() {
        return (is_null($this->degreeId) || is_int($this->degreeId));
    }

    /**
     * @return bool
     */
    public function checkDepartmentId() {
        return (!is_null($this->departmentId) && (is_string($this->departmentId) && strlen($this->departmentId) == 32));
    }

    /**
     * @return bool
     */
    public function checkNumberOfStudent() {
        return is_double($this->numberOfStudent) && $this->numberOfStudent >= 0;
    }

    /**
     * @return bool
     */
    public function checkNumberOfResearcher() {
        return is_double($this->numberOfResearcher) && $this->numberOfResearcher >= 0;
    }

    /**
     * @return bool
     */
    public function checkNumberOfGraduated() {
        return is_double($this->numberOfGraduated) && $this->numberOfGraduated >= 0;
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
    public function checkAvatarUrl() {
        return (is_null($this->avatarUrl) || (is_string($this->avatarUrl) && strlen($this->avatarUrl) <= 255));
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
    public function checkWebsite() {
        return (is_null($this->website) || (is_string($this->website) && strlen($this->website) <= 255));
    }

    /**
     * @return bool
     */
    public function checkAddress() {
        return (is_null($this->address) || (is_string($this->address) && strlen($this->address) <= 255));
    }

    /**
     * @return bool
     */
    public function checkDescription() {
        return (is_null($this->description) || is_string($this->description));
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
            'role' => $this->role,
            'officerCode' => $this->officerCode,
            'departmentId' => $this->departmentId,
            'departmentName' => $this->departmentName,
            'departmentType' => $this->departmentType,
            'fullname' => $this->fullname,
            'otherEmail' => $this->otherEmail,
            'avatarUrl' => $this->avatarUrl,
            'phone' => $this->phone,
            'degreeId' => $this->degreeId,
            'website' => $this->website,
            'address' => $this->address,
            'description' => $this->description,
            'numberOfStudent' => $this->numberOfStudent,
            'numberOfResearch' => $this->numberOfResearcher,
            'numberOfGraduated' => $this->numberOfGraduated,
        );
    }
}
