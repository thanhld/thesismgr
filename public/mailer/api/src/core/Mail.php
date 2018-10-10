<?php
namespace core;
use JsonSerializable;

/**
 * Class Mail
 * @package core
 */
class Mail implements JsonSerializable
{
    private $id;
    private $receiverId;
    private $topicId;
    private $status;
    private $type;

    /**
     * mail constructor.
     * @param $mail
     * @internal param $id
     * @internal param $receiverId
     * @internal param $topicId
     * @internal param $status
     * @internal param $type
     */
    public function __construct($mail)
    {
        $this->id           = isset($mail['id'])            ? intval($mail['id']) : null;
        $this->receiverId   = isset($mail['receiverId'])    ? $mail['receiverId'] : null;
        $this->topicId      = isset($mail['topicId'])       ? $mail['topicId'] : null;
        $this->status       = isset($mail['status'])        ? intval($mail['status']) : null;
        $this->type         = isset($mail['type'])          ? intval($mail['type']) : null;
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
    public function getReceiverId()
    {
        return $this->receiverId;
    }

    /**
     * @param mixed $receiverId
     */
    public function setReceiverId($receiverId)
    {
        $this->receiverId = $receiverId;
    }

    /**
     * @return mixed
     */
    public function getTopicId()
    {
        return $this->topicId;
    }

    /**
     * @param mixed $topicId
     */
    public function setTopicId($topicId)
    {
        $this->topicId = $topicId;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
            'receiverId' => $this->receiverId,
            'topicId' => $this->topicId,
            'status' => $this->status,
            'type' => $this->type
        );
    }
}