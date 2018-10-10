<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2/16/2017
 * Time: 04:26 PM
 */

namespace core\model;

use JsonSerializable;

class TrainingType implements JsonSerializable
{
    private $id;
    private $name;
    /**
     * Training type constructor.
     * @param $type
     * @internal param $id
     * @internal param $name
     */
    public function __construct($type)
    {
        $this->id           = isset($type['id'])            ? intval($type['id']) : null;
        $this->name         = isset($type['name'])          ? $type['name'] : null;
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
            'name' => $this->name
        );
    }
}