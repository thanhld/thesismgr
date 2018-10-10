<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2/27/2017
 * Time: 08:39 PM
 */

namespace core\model;


use JsonSerializable;

require_once 'src/core/model/TopicHistory.php';
require_once 'src/core/model/Document.php';

class Activity implements JsonSerializable
{
    private $id;
    private $documentId;
    private $document;
    private $accountId;
    private $stepId;
    private $requestedSupervisorId;
    private $created;
    private $topicHistory;

    /**
     * Activity constructor.
     * @param $activity
     * @internal param $id
     * @internal param $documentId
     * @internal param $document
     * @internal param $accountId
     * @internal param $stepId
     * @internal param $requestedSupervisorId
     * @internal param $created
     * @internal param $topicHistory
     */
    public function __construct($activity)
    {
        $this->id = isset($activity['id']) ? intval($activity['id']) : null;
        $this->documentId = isset($activity['documentId']) ? intval($activity['documentId']) : null;
        $this->accountId = isset($activity['accountId']) ? $activity['accountId'] : null;
        $this->stepId = isset($activity['stepId']) ? intval($activity['stepId']) : null;
        $this->requestedSupervisorId = isset($activity['requestedSupervisorId']) ? $activity['requestedSupervisorId'] : null;
        $this->created = isset($activity['created']) ? $activity['created'] : null;

        if(isset($activity['thId'])){
            $topicHistory = array();
            $topicHistory['id'] = isset($activity['thId']) ? intval($activity['thId']) : null;
            $topicHistory['topicId'] = isset($activity['topicId']) ? $activity['topicId'] : null;
            $topicHistory['activityId'] = isset($activity['activityId']) ? intval($activity['activityId']) : null;
            $topicHistory['vietnameseTopicTitle'] = isset($activity['vietnameseTopicTitle']) ? $activity['vietnameseTopicTitle'] : null;
            $topicHistory['englishTopicTitle'] = isset($activity['englishTopicTitle']) ? $activity['englishTopicTitle'] : null;
            $topicHistory['isEnglish'] = isset($activity['isEnglish']) ? intval($activity['isEnglish']) : null;
            $topicHistory['description'] = isset($activity['description']) ? $activity['description'] : null;
            $topicHistory['tags'] = isset($activity['tags']) ? $activity['tags'] : null;
            $topicHistory['mainSupervisorId'] = isset($activity['mainSupervisorId']) ? $activity['mainSupervisorId'] : null;
            $topicHistory['coSupervisorIds'] = isset($activity['coSupervisorIds']) ? $activity['coSupervisorIds'] : null;
            $topicHistory['startPauseDate'] = isset($activity['startPauseDate']) ? $activity['startPauseDate'] : null;
            $topicHistory['pauseDuration'] = isset($activity['pauseDuration']) ? $activity['pauseDuration'] : null;
            $topicHistory['deadlineDate'] = isset($activity['deadlineDate']) ? $activity['deadlineDate'] : null;
            $topicHistory['registerUrl'] = isset($activity['registerUrl']) ? $activity['registerUrl'] : null;

            $this->topicHistory = new TopicHistory($topicHistory);
        } else {
            $this->topicHistory = null;
        }

        if(isset($activity['documentId'])){
            $document = array();
            $document['id'] = isset($activity['documentId']) ? intval($activity['documentId']) : null;
            $document['facultyId'] = isset($activity['facultyId']) ? $activity['facultyId'] : null;
            $document['documentCode'] = isset($activity['documentCode']) ? $activity['documentCode'] : null;
            $document['createdDate'] = isset($activity['createdDate']) ? $activity['createdDate'] : null;
            $document['attachmentId'] = isset($activity['attachmentId']) ? intval($activity['attachmentId']) : null;
            $document['attachmentName'] = isset($activity['attachmentName']) ? $activity['attachmentName'] : null;
            $document['url'] = isset($activity['url']) ? $activity['url'] : null;

            $this->document = new Document($document);
        } else { $this->document = null; }
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
    public function checkAccountId()
    {
        return (!is_null($this->accountId) && (is_string($this->accountId) && strlen($this->accountId) == 32));
    }

    /**
     * @return bool
     */
    public function checkStepId()
    {
        return (!is_null($this->stepId) && is_int($this->stepId));
    }

    /**
     * @return bool
     */
    public function checkRequestedSupervisorId()
    {
        return (is_null($this->requestedSupervisorId) || (is_string($this->requestedSupervisorId) && strlen($this->requestedSupervisorId) == 32));
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
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @param mixed $document
     */
    public function setDocument($document)
    {
        $this->document = $document;
    }

    /**
     * @return mixed
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param mixed $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @return mixed
     */
    public function getStepId()
    {
        return $this->stepId;
    }

    /**
     * @param mixed $stepId
     */
    public function setStepId($stepId)
    {
        $this->stepId = $stepId;
    }

    /**
     * @return mixed
     */
    public function getRequestedSupervisorId()
    {
        return $this->requestedSupervisorId;
    }

    /**
     * @param mixed $requestedSupervisorId
     */
    public function setRequestedSupervisorId($requestedSupervisorId)
    {
        $this->requestedSupervisorId = $requestedSupervisorId;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
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
            'document' => $this->document,
            'accountId' => $this->accountId,
            'stepId' => $this->stepId,
            'requestedSupervisorId' => $this->requestedSupervisorId,
            'created' => $this->created,
            'topicHistory' => $this->topicHistory,
        );
    }
}