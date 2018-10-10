<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2/27/2017
 * Time: 08:33 PM
 */

namespace core\model;


use JsonSerializable;

class Attachment implements JsonSerializable
{
    private $id;
    private $documentId;
    private $announcementId;
    private $name;
    private $url;

    /**
     * Attachment constructor.
     * @param $attachment
     * @internal param $id
     * @internal param $documentId
     * @internal param announcementId
     * @internal param $name
     * @internal param $url
     */
    public function __construct($attachment)
    {
        $this->id = isset($attachment['id']) ? intval($attachment['id']) : null;
        $this->documentId = isset($attachment['documentId']) ? intval($attachment['documentId']) : null;
        $this->announcementId = isset($attachment['announcementId']) ? intval($attachment['announcementId']) : null;
        $this->name = isset($attachment['name']) ? $attachment['name'] : null;
        $this->url = isset($attachment['url']) ? $attachment['url'] : null;
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
    public function checkDocumentId()
    {
        return (is_null($this->documentId) || is_int($this->documentId));
    }

    /**
     * @return bool
     */
    public function checkAnnouncementId()
    {
        return (is_null($this->announcementId) || is_int($this->announcementId));
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
    public function checkUrl()
    {
        return (!is_null($this->url) && (is_string($this->url) && strlen($this->url) <= 255));
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
    public function getDocumentId()
    {
        return $this->documentId;
    }

    /**
     * @param mixed $documentId
     */
    public function setDocumentId($documentId)
    {
        $this->documentId = $documentId;
    }

    /**
     * @return mixed
     */
    public function getAnnouncementId()
    {
        return $this->announcementId;
    }

    /**
     * @param mixed $announcementId
     */
    public function setAnnouncementId($announcementId)
    {
        $this->announcementId = $announcementId;
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
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
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
            'documentId' => $this->documentId,
            'announcementId' => $this->announcementId,
            'name' => $this->name,
            'url' => $this->url
        );
    }
}