<?php
/**
 * Created by PhpStorm.
 * User: Can
 * Date: 18-Nov-16
 * Time: 1:57 PM
 */

namespace core\model;

use JsonSerializable;
use DateTime;

require_once 'src/core/model/Learner.php';
require_once 'src/core/model/TopicChange.php';

class Topic implements JsonSerializable
{
    private $id;
    private $learnerId;
    private $learner;
    private $departmentId;
    private $topicStatus;
    private $topicType;
    private $vietnameseTopicTitle;
    private $englishTopicTitle;
    private $isEnglish;
    private $description;
    private $tags;
    private $referenceUrl;
    private $registerUrl;
    private $expertiseOfficerIds;
    private $mainSupervisorId;
    private $requestedSupervisorId;
    private $coSupervisorIds;
    private $outOfficerIds;
    private $startDate;
    private $defaultDeadlineDate;
    private $deadlineDate;
    private $processDuration;
    private $topicChange;
    private $reviews;

    /**
     * Topic constructor.
     * @param $topic
     * @internal param $id;
     * @internal param $learnerId;
     * @internal param $learner;
     * @internal param $departmentId;
     * @internal param $topicStatus;
     * @internal param $vietnameseTopicTitle;
     * @internal param $englishTopicTitle;
     * @internal param $isEnglish;
     * @internal param $description;
     * @internal param $tags;
     * @internal param $referenceUrl;
     * @internal param $registerUrl;
     * @internal param $expertiseOfficerIds;
     * @internal param $mainSupervisorId;
     * @internal param $requestedSupervisorId;
     * @internal param $coSupervisorIds;
     * @internal param $outOfficerIds;
     * @internal param $startDate;
     * @internal param $defaultDeadlineDate;
     * @internal param $deadlineDate;
     * @internal param $processDuration;
     * @internal param $topicChange;
     * @internal param $reviews;
     */
    public function __construct($topic)
    {
        $this->id = isset($topic['id']) ? $topic['id'] : null;
        $this->learnerId = isset($topic['learnerId']) ? $topic['learnerId'] : null;
        $this->departmentId = isset($topic['departmentId']) ? $topic['departmentId'] : null;
        $this->topicStatus = isset($topic['topicStatus']) ? intval($topic['topicStatus']) : null;
        $this->topicType = isset($topic['topicType']) ? intval($topic['topicType']) : null;
        $this->vietnameseTopicTitle = isset($topic['vietnameseTopicTitle']) ? $topic['vietnameseTopicTitle'] : null;
        $this->englishTopicTitle = isset($topic['englishTopicTitle']) ? $topic['englishTopicTitle'] : null;
        $this->isEnglish = isset($topic['isEnglish']) ? intval($topic['isEnglish']) : null;
        $this->description = isset($topic['description']) ? $topic['description'] : null;
        $this->tags = isset($topic['tags']) ? $topic['tags'] : null;
        $this->referenceUrl = isset($topic['referenceUrl']) ? $topic['referenceUrl'] : null;
        $this->registerUrl = isset($topic['registerUrl']) ? $topic['registerUrl'] : null;
        $this->expertiseOfficerIds = isset($topic['expertiseOfficerIds']) ? $topic['expertiseOfficerIds'] : null;
        $this->mainSupervisorId = isset($topic['mainSupervisorId']) ? $topic['mainSupervisorId'] : null;
        $this->requestedSupervisorId = isset($topic['requestedSupervisorId']) ? $topic['requestedSupervisorId'] : null;
        $this->coSupervisorIds = isset($topic['coSupervisorIds']) ? $topic['coSupervisorIds'] : null;
        $this->outOfficerIds = isset($topic['outOfficerIds']) ? $topic['outOfficerIds'] : null;
        $this->startDate = isset($topic['startDate']) ? $topic['startDate'] : null;
        $this->defaultDeadlineDate = isset($topic['defaultDeadlineDate']) ? $topic['defaultDeadlineDate'] : null;
        $this->deadlineDate = isset($topic['deadlineDate']) ? $topic['deadlineDate'] : null;
        $this->reviews = (count($topic['reviews']) != 0) ? $topic['reviews'] : null;

        if($this->startDate) {
            $this->setTimeZone();
            $this->processDuration = (int)date('m') - (int)date('m',strtotime($this->startDate));
        } else {
            $this->processDuration = 0;
        }

        //construct learner obj
        if(isset($topic['fullname']) && isset($topic['learnerCode'])){
            $learner = array();
            $learner['fullname'] = $topic['fullname'];
            $learner['learnerCode'] = $topic['learnerCode'];
            $learner['trainingCourseId'] = $topic['trainingCourseId'];
            if(isset($topic['courseCode'])){
                $learner['trainingCourseCode'] = $topic['courseCode'];                
            }
            $this->learner = new Learner($learner);
        } else {
            $this->learner = null;
        }

        if(isset($topic['cId'])){
            $topicChange = array();
            $topicChange['id'] = $topic['cId'];
            $topicChange['vietnameseTopicTitle'] = $topic['cVietnameseTopicTitle'];
            $topicChange['englishTopicTitle'] = $topic['cEnglishTopicTitle'];
            $topicChange['isEnglish'] = $topic['cIsEnglish'];
            $topicChange['mainSupervisorId'] = $topic['cMainSupervisorId'];
            $topicChange['coSupervisorIds'] = $topic['cCoSupervisorIds'];
            $topicChange['requestedSupervisorId'] = $topic['cRequestedSupervisorId'];
            $topicChange['description'] = $topic['cDescription'];
            $topicChange['tags'] = $topic['cTags'];
            $topicChange['startPauseDate'] = $topic['startPauseDate'];
            $topicChange['pauseDuration'] = $topic['pauseDuration'];
            $topicChange['delayDuration'] = $topic['delayDuration'];
            $topicChange['cancelReason'] = $topic['cancelReason'];
            $topicChange['registerUrl'] = $topic['cRegisterUrl'];
            $this->topicChange = new TopicChange($topicChange);
        } else {
            $this->topicChange = null;
        }
    }

    /**
     * @return bool
     */
    public function checkId()
    {
        return (!is_null($this->id) && (is_string($this->id) && strlen($this->id) == 32));
    }


    /**
     * @return bool
     */
    public function checkLearnerId()
    {
        return (!is_null($this->learnerId) && (is_string($this->learnerId) && strlen($this->learnerId) == 32));
    }

    /**
     * @return bool
     */
    public function checkDepartmentId()
    {
        return (!is_null($this->departmentId) && (is_string($this->departmentId) && strlen($this->departmentId) == 32));
    }

    /**
     * @return bool
     */
    public function checkTopicStatus()
    {
        return (is_null($this->topicStatus) || is_int($this->topicStatus));
    }

    /**
     * @return bool
     */
    public function checkTopicType()
    {
        return (is_null($this->topicType) || is_int($this->topicType));
    }

    /**
     * @return bool
     */
    public function checkVietnameseTopicTitle()
    {
        return (is_null($this->vietnameseTopicTitle) || (is_string($this->vietnameseTopicTitle) && strlen($this->vietnameseTopicTitle) <= 255));
    }

    /**
     * @return bool
     */
    public function checkEnglishTopicTitle()
    {
        return (is_null($this->englishTopicTitle) || (is_string($this->englishTopicTitle) && strlen($this->englishTopicTitle) <= 255));
    }

    /**
     * @return bool
     */
    public function checkIsEnglish()
    {
        return (is_null($this->isEnglish) || is_int($this->isEnglish));
    }

    /**
     * @return bool
     */
    public function checkDescription() {
        return (is_null($this->description) || is_string($this->description));
    }

    /**
     * @return bool
     */
    public function checkTags() {
        return (is_null($this->tags) || (is_string($this->tags) && strlen($this->tags) <= 255));
    }

    /**
     * @return bool
     */
    public function checkReferenceUrl() {
        return (is_null($this->referenceUrl) || (is_string($this->referenceUrl) && strlen($this->referenceUrl) <= 255));
    }

    /**
     * @return bool
     */
    public function checkRegisterUrl() {
        return (is_null($this->registerUrl) || (is_string($this->registerUrl) && strlen($this->registerUrl) <= 255));
    }

    /**
     * @return bool
     */
    public function checkExpertiseOfficerIds() {
        return (is_null($this->expertiseOfficerIds) || (is_string($this->expertiseOfficerIds) && strlen($this->expertiseOfficerIds) <= 255));
    }

    /**
     * @return bool
     */
    public function checkMainSupervisorId()
    {
        return (is_null($this->mainSupervisorId) || (is_string($this->mainSupervisorId) && strlen($this->mainSupervisorId) == 32));
    }

    /**
     * @return bool
     */
    public function checkRequestedSupervisorId()
    {
        return (is_null($this->requestedSupervisorId) || (is_string($this->requestedSupervisorId) && strlen($this->requestedSupervisorId) == 32));
    }

    /**
     * @return bool
     */
    public function checkCoSupervisorIds()
    {
        return (is_null($this->coSupervisorIds) || (is_string($this->coSupervisorIds) && strlen($this->coSupervisorIds) <= 255));
    }

    /**
     * @return bool
     */
    public function checkOutOfficerIds()
    {
        return (is_null($this->outOfficerIds) || (is_string($this->outOfficerIds) && strlen($this->outOfficerIds) <= 255));
    }

    /**
     * @return bool
     */
    public function checkStartDate() {
        $this->setTimeZone();
        return (is_null($this->startDate) || (is_string($this->startDate) && $this->startDate == date('Y-m-d H:i:s',strtotime($this->startDate))));
    }

    /**
     * @return bool
     */
    public function checkDefaultDeadlineDate() {
        $this->setTimeZone();
        return (is_null($this->defaultDeadlineDate) || (is_string($this->defaultDeadlineDate) && $this->defaultDeadlineDate == date('Y-m-d H:i:s',strtotime($this->defaultDeadlineDate))));
    }

    /**
     * @return bool
     */
    public function checkDeadlineDate() {
        $this->setTimeZone();
        return (is_null($this->deadlineDate) || (is_string($this->deadlineDate) && $this->deadlineDate == date('Y-m-d H:i:s',strtotime($this->deadlineDate))));
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
    public function getLearnerId()
    {
        return $this->learnerId;
    }

    /**
     * @param null $learnerId
     */
    public function setLearnerId($learnerId)
    {
        $this->learnerId = $learnerId;
    }

    /**
     * @return null
     */
    public function getLearner()
    {
        return $this->learner;
    }

    /**
     * @param null $learner
     */
    public function setLearner($learner)
    {
        $this->learner = $learner;
    }

    /**
     * @return null
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    /**
     * @param null $departmentId
     */
    public function setDepartmentId($departmentId)
    {
        $this->departmentId = $departmentId;
    }

    /**
     * @return int|null
     */
    public function getTopicStatus()
    {
        return intval($this->topicStatus);
    }

    /**
     * @param int|null $topicStatus
     */
    public function setTopicStatus($topicStatus)
    {
        $this->topicStatus = $topicStatus;
    }

    /**
     * @return int|null
     */
    public function getTopicType()
    {
        return intval($this->topicType);
    }

    /**
     * @param int|null $topicType
     */
    public function setTopicType($topicType)
    {
        $this->topicType = $topicType;
    }

    /**
     * @return null
     */
    public function getVietnameseTopicTitle()
    {
        return $this->vietnameseTopicTitle;
    }

    /**
     * @param null $vietnameseTopicTitle
     */
    public function setVietnameseTopicTitle($vietnameseTopicTitle)
    {
        $this->vietnameseTopicTitle = $vietnameseTopicTitle;
    }

    /**
     * @return null
     */
    public function getEnglishTopicTitle()
    {
        return $this->englishTopicTitle;
    }

    /**
     * @param null $englishTopicTitle
     */
    public function setEnglishTopicTitle($englishTopicTitle)
    {
        $this->englishTopicTitle = $englishTopicTitle;
    }

    /**
     * @return int|null
     */
    public function getIsEnglish()
    {
        return $this->isEnglish;
    }

    /**
     * @param int|null $isEnglish
     */
    public function setIsEnglish($isEnglish)
    {
        $this->isEnglish = $isEnglish;
    }

    /**
     * @return bool|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param bool|null $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return null
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param null $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return null
     */
    public function getReferenceUrl()
    {
        return $this->referenceUrl;
    }

    /**
     * @param null $referenceUrl
     */
    public function setReferenceUrl($referenceUrl)
    {
        $this->referenceUrl = $referenceUrl;
    }

    /**
     * @return null
     */
    public function getRegisterUrl()
    {
        return $this->registerUrl;
    }

    /**
     * @param null $registerUrl
     */
    public function setRegisterUrl($registerUrl)
    {
        $this->registerUrl = $registerUrl;
    }

    /**
     * @return null
     */
    public function getExpertiseOfficerIds()
    {
        return $this->expertiseOfficerIds;
    }

    /**
     * @param null $expertiseOfficerIds
     */
    public function setExpertiseOfficerIds($expertiseOfficerIds)
    {
        $this->expertiseOfficerIds = $expertiseOfficerIds;
    }

    /**
     * @return null
     */
    public function getMainSupervisorId()
    {
        return $this->mainSupervisorId;
    }

    /**
     * @param null $mainSupervisorId
     */
    public function setMainSupervisorId($mainSupervisorId)
    {
        $this->mainSupervisorId = $mainSupervisorId;
    }

    /**
     * @return null
     */
    public function getRequestedSupervisorId()
    {
        return $this->requestedSupervisorId;
    }

    /**
     * @param null $requestedSupervisorId
     */
    public function setRequestedSupervisorId($requestedSupervisorId)
    {
        $this->requestedSupervisorId = $requestedSupervisorId;
    }

    /**
     * @return null
     */
    public function getCoSupervisorIds()
    {
        return $this->coSupervisorIds;
    }

    /**
     * @param null $coSupervisorIds
     */
    public function setCoSupervisorIds($coSupervisorIds)
    {
        $this->coSupervisorIds = $coSupervisorIds;
    }

    /**
     * @return null
     */
    public function getOutOfficerIds()
    {
        return $this->outOfficerIds;
    }


    /**
     * @param null $outOfficerIds
     */
    public function setOutOfficerIds($outOfficerIds)
    {
        $this->outOfficerIds = $outOfficerIds;
    }

    /**
     * @return null
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param null $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return null
     */
    public function getDefaultDeadlineDate()
    {
        return $this->defaultDeadlineDate;
    }

    /**
     * @param null $defaultDeadlineDate
     */
    public function setDefaultDeadlineDate($defaultDeadlineDate)
    {
        $this->defaultDeadlineDate = $defaultDeadlineDate;
    }

    /**
     * @return null
     */
    public function getDeadlineDate()
    {
        return $this->deadlineDate;
    }

    /**
     * @param null $deadlineDate
     */
    public function setDeadlineDate($deadlineDate)
    {
        $this->deadlineDate = $deadlineDate;
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
            'learnerId' => $this->learnerId,
            'learner' => $this->learner,
            'departmentId' => $this->departmentId,
            'topicStatus' => $this->topicStatus,
            'topicType' => $this->topicType,
            'vietnameseTopicTitle' => $this->vietnameseTopicTitle,
            'englishTopicTitle' => $this->englishTopicTitle,
            'isEnglish' => $this->isEnglish,
            'description' => $this->description,
            'tags' => $this->tags,
            'referenceUrl' => $this->referenceUrl,
            'registerUrl' => $this->registerUrl,
            'expertiseOfficerIds' => $this->expertiseOfficerIds,
            'mainSupervisorId' => $this->mainSupervisorId,
            'requestedSupervisorId' => $this->requestedSupervisorId,
            'coSupervisorIds' => $this->coSupervisorIds,
            'outOfficerIds' => $this->outOfficerIds,
            'startDate' => $this->startDate,
            'defaultDeadlineDate' => $this->defaultDeadlineDate,
            'deadlineDate' => $this->deadlineDate,
            'processDuration' => $this->processDuration,
            'topicChange' => $this->topicChange,
            'reviews' => $this->reviews,
        );
    }
}
