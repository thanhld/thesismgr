<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2/16/2017
 * Time: 04:26 PM
 */

namespace core\model;

use JsonSerializable;

class TrainingLevel implements JsonSerializable
{
    private $id;
    private $name;
    private $levelType;
    /**
     * Training type constructor.
     * @param $type
     * @internal param $id
     * @internal param $name
     * @internal param $levelType
     */
    public function __construct($type)
    {
        $this->id           = isset($type['id'])            ? intval($type['id']) : null;
        $this->name         = isset($type['name'])          ? $type['name'] : null;
        $this->levelType    = isset($type['levelType'])     ? intval($type['levelType']) : null;
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
    public function checkName()
    {
        return (!is_null($this->name) && (is_string($this->name) && strlen($this->name) <= 255));
    }

    /**
     * @return bool
     */
    public function checkLevelType()
    {
        return (!is_null($this->levelType) && is_int($this->levelType));
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int|null
     */
    public function getLevelType()
    {
        return $this->levelType;
    }

    /**
     * @param int|null $levelType
     */
    public function setLevelType($levelType)
    {
        $this->levelType = $levelType;
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
            'levelType' => $this->levelType
        );
    }
}