<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2/28/2017
 * Time: 10:00 PM
 */

namespace core\model;


use JsonSerializable;

class Step implements JsonSerializable
{
    private $id;
    private $stepCode;
    private $stepName;

    /**
     * Activity constructor.
     * @param $step
     * @internal param $id
     * @internal param $stepCode
     * @internal param $stepName
     */
    public function __construct($step)
    {
        $this->id = isset($step['id']) ? intval($step['id']) : null;
        $this->stepCode = isset($step['stepCode']) ? intval($step['stepCode']) : null;
        $this->stepName = isset($step['stepName']) ? $step['stepName'] : null;
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
    public function checkStepCode()
    {
        return (!is_null($this->stepCode) && (is_string($this->stepCode) && strlen($this->stepCode) <= 20));
    }

    /**
     * @return bool
     */
    public function checkStepName()
    {
        return (!is_null($this->stepName) && (is_string($this->stepName) && strlen($this->stepName) <= 255));
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
    public function getStepCode()
    {
        return $this->stepCode;
    }

    /**
     * @param mixed $stepCode
     */
    public function setStepCode($stepCode)
    {
        $this->stepCode = $stepCode;
    }

    /**
     * @return mixed
     */
    public function getStepName()
    {
        return $this->stepName;
    }

    /**
     * @param mixed $stepName
     */
    public function setStepName($stepName)
    {
        $this->stepName = $stepName;
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
            'stepCode' => $this->stepCode,
            'stepName' => $this->stepName
        );
    }
}
