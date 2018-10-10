<?php
/**
 * Created by PhpStorm.
 * User: Can
 * Date: 18-Nov-16
 * Time: 3:50 PM
 */

namespace core\model;


use JsonSerializable;

class Department implements JsonSerializable
{
    private $id;
    private $name;
    private $facultyId;
    private $facultyName;
    private $type;
    private $address;
    private $phone;
    private $website;

    /**
     * Department constructor.
     * @param $department
     * @internal param $id
     * @internal param $name
     * @internal param $facultyId
     * @internal param $facultyName
     * @internal param $type
     * @internal param $address
     * @internal param $phone
     * @internal param $website
     */
    public function __construct($department)
    {
        $this->id        = isset($department['id'])        ? $department['id'] : null;
        $this->name      = isset($department['name'])      ? $department['name'] : null;
        $this->facultyId = isset($department['facultyId']) ? $department['facultyId'] : null;
        $this->facultyName = isset($department['facultyName']) ? $department['facultyName'] : null;
        $this->type    = isset($department['type'])    ? $department['type'] : null;
        $this->address  = isset($department['address'])  ? $department['address'] : null;
        $this->phone     = isset($department['phone'])     ? $department['phone'] : null;
        $this->website   = isset($department['website'])   ? $department['website'] : null;
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
    public function checkName() {
        return (!is_null($this->name) && (is_string($this->name) && strlen($this->name) <= 255));
    }

    /**
     * @return bool
     */
    public function checkFacultyId() {
        return (!is_null($this->facultyId) && (is_string($this->facultyId) && strlen($this->facultyId) == 32));
    }

    /**
     * @return bool
     */
    public function checkType() {
        return (!is_null($this->type) && is_int($this->type));
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
     * @return null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param null $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return null
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param null $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
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
     * @return mixed
     */
    public function getFacultyName()
    {
        return $this->facultyName;
    }

    /**
     * @param mixed $facultyName
     */
    public function setFacultyName($facultyName)
    {
        $this->facultyName = $facultyName;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
            'name' => $this->name,
            'facultyId' => $this->facultyId,
            'facultyName' => $this->facultyName,
            'type' => $this->type,
            'address' => $this->address,
            'phone' => $this->phone,
            'website' => $this->website
        );
    }
}
