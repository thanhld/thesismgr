<?php
namespace core\model;

use JsonSerializable;

class Faculty implements JsonSerializable
{
    private $id;
    private $username;
    private $vnuMail;
    private $name;
    private $shortName;
    private $phone;
    private $website;
    private $address;

    /**
     * construct function has parameter as json
     * @param $faculty
     * @internal param $id
     * @internal param $username
     * @internal param $vnuMail
     * @internal param $name
     * @internal param $shortName
     * @internal param $phone
     * @internal param $website
     * @internal param $address
     */
    public function __construct($faculty)
    {
        $this->id           = isset($faculty['id'])         ? $faculty['id'] : null;
        $this->name         = isset($faculty['name'])       ? $faculty['name'] : null;
        $this->username     = isset($faculty['username'])   ? $faculty['username'] : null;
        $this->vnuMail      = isset($faculty['vnuMail'])    ? $faculty['vnuMail'] : null;
        $this->shortName    = isset($faculty['shortName'])  ? $faculty['shortName'] : null;
        $this->phone        = isset($faculty['phone'])      ? $faculty['phone'] : null;
        $this->website      = isset($faculty['website'])    ? $faculty['website'] : null;
        $this->address      = isset($faculty['address'])    ? $faculty['address'] : null;
    }

    /**
     * @return bool
     */
    public function checkId()
    {
        return (!is_null($this->id) && (is_string($this->id) && strlen($this->id) == 32));
    }

    /**
     * @return bool
     */
    public function checkName()
    {
        return (!is_null($this->name) && (is_string($this->name) && strlen($this->name) <= 255));
    }

    /**
     * @return bool
     */
    public function checkShortName()
    {
        return (is_null($this->shortName) || (is_string($this->shortName) && strlen($this->shortName) <= 50));
    }

    /**
     * @return bool
     */
    public function checkPhone()
    {
        return (is_null($this->phone) || (is_string($this->phone) && strlen($this->phone) <= 20));
    }

    /**
     * @return bool
     */
    public function checkWebsite()
    {
        return (is_null($this->website) || (is_string($this->website) && strlen($this->website) <= 255));
    }

    /**
     * @return bool
     */
    public function checkAddress()
    {
        return (is_null($this->address) || (is_string($this->address) && strlen($this->address) <= 255));
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @param mixed $shortName
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;
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
            'name' => $this->name,
            'shortName' => $this->shortName,
            'phone' => $this->phone,
            'website' => $this->website,
            'address' => $this->address,
        );
    }
}

?>
