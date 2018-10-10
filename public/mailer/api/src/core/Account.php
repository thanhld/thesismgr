<?php
namespace core;
use JsonSerializable;

/**
 * Class Account
 * @package core\model
 */
class Account implements JsonSerializable
{
    private $uid;
    private $username;
    private $vnuMail;
    private $role;
    private $facultyId;

    /**
     * Account constructor.
     * @param $account
     * @internal param $uid
     * @internal param $username
     * @internal param $vnuMail
     * @internal param $role
     */
    public function __construct($account)
    {
        $this->uid      = isset($account['uid'])      ? $account['uid'] : null;
        $this->username = isset($account['username']) ? $account['username'] : null;
        $this->vnuMail  = isset($account['vnuMail'])  ? $account['vnuMail'] : null;
        $this->role     = isset($account['role'])     ? intval($account['role']) : null;
        $this->facultyId = isset($account['facultyId'])  ? $account['facultyId'] : null;
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
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
    public function getFacultyId()
    {
        return $this->facultyId;
    }

    /**
     * @param mixed $facultyId
     */
    public function setFacultyId($facultyId)
    {
        $this->facultyId = $facultyId;
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
            'uid' => $this->uid,
            'username' => $this->username,
            'vnuMail' => $this->vnuMail,
            'role' => $this->role,
            'facultyId' => $this->facultyId
        );
    }
}

?>