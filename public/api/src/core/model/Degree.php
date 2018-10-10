<?php
/**
 * Created by PhpStorm.
 * User: Can
 * Date: 18-Nov-16
 * Time: 1:57 PM
 */

namespace core\model;


use JsonSerializable;

class Degree implements JsonSerializable
{
    private $id;
    private $name;

    /**
     * Degree constructor.
     * @param $degree
     * @internal param $id
     * @internal param $name
     */
    public function __construct($degree)
    {
        $this->id   = isset($degree['id'])   ? intval($degree['id']) : null;
        $this->name = isset($degree['name']) ? $degree['name'] : null;
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
