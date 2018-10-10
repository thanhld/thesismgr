<?php
/**
 * Created by PhpStorm.
 * User: Can
 * Date: 18-Nov-16
 * Time: 3:50 PM
 */

namespace core\model;


use JsonSerializable;

class KnowledgeArea implements JsonSerializable
{
    private $id;
    private $name;
    private $parentId;
    private $facultyId;

    /**
     * KnowledgeArea constructor.
     * @param $knowledgeArea
     * @internal param $id
     * @internal param $name
     * @internal param $parentId
     * @internal param $facultyId
     */
    public function __construct($knowledgeArea)
    {
        $this->id        = isset($knowledgeArea['id'])        ? intval($knowledgeArea['id']) : null;
        $this->name      = isset($knowledgeArea['name'])      ? $knowledgeArea['name'] : null;
        $this->parentId  = isset($knowledgeArea['parentId'])  ? intval($knowledgeArea['parentId']): null;
        $this->facultyId = isset($knowledgeArea['facultyId']) ? $knowledgeArea['facultyId'] : null;
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
    public function checkParentId()
    {
        return (is_null($this->parentId) || is_int($this->parentId));
    }

    /**
     * @return bool
     */
    public function checkFacultyId()
    {
        return (!is_null($this->facultyId) && (is_string($this->facultyId) && strlen($this->facultyId) == 32));
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
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
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
            'id' => $this->id,
            'name' => $this->name,
            'facultyId' => $this->facultyId,
            'parentId' => $this->parentId
        );
    }
}