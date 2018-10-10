<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 3/11/2017
 * Time: 11:20 AM
 */

namespace core\model;


use JsonSerializable;

class OutOfficer implements JsonSerializable
{
    private $id;
    private $fullname;
    private $departmentName;
    private $degreeId;

    /**
     * Degree constructor.
     * @param $outOfficer
     * @internal param $id
     * @internal param $fullname
     * @internal param $departmentName
     * @internal param $degreeId
     */
    public function __construct($outOfficer)
    {
        $this->id   = isset($outOfficer['id']) ? intval($outOfficer['id']) : null;
        $this->fullname = isset($outOfficer['fullname']) ? $outOfficer['fullname'] : null;
        $this->degreeId   = isset($outOfficer['degreeId']) ? intval($outOfficer['degreeId']) : null;
        $this->departmentName = isset($outOfficer['departmentName']) ? $outOfficer['departmentName'] : null;
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
    public function checkFullName()
    {
        return (!is_null($this->fullname) && (is_string($this->fullname) && strlen($this->fullname) <= 50));
    }

    /**
     * @return bool
     */
    public function checkDegreeId()
    {
        return (is_null($this->degreeId) || is_int($this->degreeId));
    }

    /**
     * @return bool
     */
    public function checkDepartmentName()
    {
        return (!is_null($this->departmentName) && (is_string($this->departmentName) && strlen($this->departmentName) <= 255));
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
    public function getDepartmentName()
    {
        return $this->departmentName;
    }

    /**
     * @param mixed $departmentName
     */
    public function setDepartmentName($departmentName)
    {
        $this->departmentName = $departmentName;
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
            'fullname' => $this->fullname,
            'departmentName' => $this->departmentName,
            'degreeId' => $this->degreeId,
        );
    }
}