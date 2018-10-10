<?php
/**
 * Created by PhpStorm.
 * User: Can
 * Date: 15-Feb-17
 * Time: 9:57 PM
 */

namespace core\model;

use JsonSerializable;

class TrainingArea implements JsonSerializable
{
    private $id;
    private $facultyId;
    private $areaCode;
    private $name;

    /**
     * Training area constructor.
     * @param $area
     * @internal param $id
     * @internal param $facultyId
     * @internal param $areaCode
     * @internal param $name
     */
    public function __construct($area)
    {
        $this->id           = isset($area['id'])            ? intval($area['id']) : null;
        $this->facultyId    = isset($area['facultyId'])     ? $area['facultyId'] : null;
        $this->areaCode     = isset($area['areaCode'])      ? $area['areaCode'] : null;
        $this->name         = isset($area['name'])          ? $area['name'] : null;
    }

    /**
     * @return bool
     */
    public function checkId()
    {
        return (!is_null($this->id) && is_int($this->id));
    }

    /**
     * @return bool
     */
    public function checkFacultyId()
    {
        return (!is_null($this->facultyId) && (is_string($this->facultyId) && strlen($this->facultyId) == 32));
    }

    /**
     * @return bool
     */
    public function checkAreaCode()
    {
        return (!is_null($this->areaCode) && (is_string($this->areaCode) && strlen($this->areaCode) <= 20));
    }

    /**
     * @return bool
     */
    public function checkName()
    {
        return (!is_null($this->name) && (is_string($this->name) && strlen($this->name) <= 255));
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getAreaCode()
    {
        return $this->areaCode;
    }

    /**
     * @param mixed $areaCode
     */
    public function setAreaCode($areaCode)
    {
        $this->areaCode = $areaCode;
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
            'facultyId' => $this->facultyId,
            'areaCode' => $this->areaCode,
            'name' => $this->name
        );
    }

}