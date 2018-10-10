<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2/27/2017
 * Time: 08:33 PM
 */

namespace core\model;

use JsonSerializable;

require_once 'src/core/model/Attachment.php';

class Document implements JsonSerializable
{
    private $id;
    private $facultyId;
    private $documentCode;
    private $attachment;
    private $createdDate;

    /**
     * Document constructor.
     * @param $document
     * @internal param $id
     * @internal param $facultyId
     * @internal param $documentCode
     * @internal param $attachmentId
     * @internal param $createdDate
     */
    public function __construct($document)
    {
        $this->id = isset($document['id']) ? intval($document['id']) : null;
        $this->facultyId = isset($document['facultyId']) ? $document['facultyId'] : null;
        $this->documentCode = isset($document['documentCode']) ? $document['documentCode'] : null;
        $this->createdDate = isset($document['createdDate']) ? $document['createdDate'] : null;

        $attm = array();
        $attm['id'] = isset($document['attachmentId']) ? intval($document['attachmentId']) : null;
        $attm['documentId'] = isset($document['id']) ? intval($document['id']) : null;
        $attm['name'] = isset($document['attachmentName']) ? $document['attachmentName'] : null;
        $attm['url'] = isset($document['url']) ? $document['url'] : null;
        $this->attachment = new Attachment($attm);
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
    public function checkDocumentCode()
    {
        return (!is_null($this->documentCode) && (is_string($this->documentCode) && strlen($this->documentCode) <= 255));
    }

    /**
     * @return bool
     */
    public function checkAttachmentId()
    {
        return (is_null($this->attachmentId) || is_int($this->attachmentId));
    }

    /**
     * @return bool
     */
    public function checkCreatedDate()
    {
        $this->setTimeZone();
        return (is_null($this->createdDate) || (is_string($this->createdDate) && $this->createdDate == date('Y-m-d H:i:s',strtotime($this->createdDate))));
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
    public function getDocumentCode()
    {
        return $this->documentCode;
    }

    /**
     * @param mixed $documentCode
     */
    public function setDocumentCode($documentCode)
    {
        $this->documentCode = $documentCode;
    }

    /**
     * @return mixed
     */
    public function getAttachmentId()
    {
        return $this->attachmentId;
    }

    /**
     * @param mixed $attachmentId
     */
    public function setAttachmentId($attachmentId)
    {
        $this->attachmentId = $attachmentId;
    }

    /**
     * @return mixed
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param mixed $createdDate
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }

    private function setTimeZone(){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
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
            'documentCode' => $this->documentCode,
            'attachment' => $this->attachment,
            'createdDate' => $this->createdDate,
        );
    }
}