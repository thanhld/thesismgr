<?php
/**
 * Created by PhpStorm.
 * User: Can
 * Date: 18-Nov-16
 * Time: 1:57 PM
 */

namespace core\model;

use JsonSerializable;


class TopicChange implements JsonSerializable
{
    private $id;
    private $vietnameseTopicTitle;
    private $englishTopicTitle;
    private $isEnglish;
    private $description;
    private $tags;
    private $mainSupervisorId;
    private $coSupervisorIds;
    private $requestedSupervisorId;
    private $startPauseDate;
    private $pauseDuration;
    private $delayDuration;
    private $cancelReason;
    private $registerUrl;

    /**
     * Topic constructor.
     * @param $topic
     * @internal param $id;
     * @internal param $vietnameseTopicTitle;
     * @internal param $englishTopicTitle;
     * @internal param $isEnglish;
     * @internal param $description;
     * @internal param $tags;
     * @internal param $mainSupervisorId;
     * @internal param $coSupervisorIds;
     * @internal param $requestedSupervisorId;
     * @internal param $startPauseDate;
     * @internal param $pauseDuration;
     * @internal param $delayDuration;
     * @internal param $cancelReason;
     * @internal param $registerUrl;
     */
    public function __construct($topic)
    {
        $this->id = isset($topic['id']) ? $topic['id'] : null;
        $this->vietnameseTopicTitle = isset($topic['vietnameseTopicTitle']) ? $topic['vietnameseTopicTitle'] : null;
        $this->englishTopicTitle = isset($topic['englishTopicTitle']) ? $topic['englishTopicTitle'] : null;
        $this->isEnglish = isset($topic['isEnglish']) ? intval($topic['isEnglish']) : null;
        $this->description = isset($topic['description']) ? $topic['description'] : null;
        $this->tags = isset($topic['tags']) ? $topic['tags'] : null;
        $this->mainSupervisorId = isset($topic['mainSupervisorId']) ? $topic['mainSupervisorId'] : null;
        $this->coSupervisorIds = isset($topic['coSupervisorIds']) ? $topic['coSupervisorIds'] : null;
        $this->requestedSupervisorId = isset($topic['requestedSupervisorId']) ? $topic['requestedSupervisorId'] : null;
        $this->startPauseDate = isset($topic['startPauseDate']) ? $topic['startPauseDate'] : null;
        $this->pauseDuration = isset($topic['pauseDuration']) ? intval($topic['pauseDuration']) : null;
        $this->delayDuration = isset($topic['delayDuration']) ? intval($topic['delayDuration']) : null;
        $this->cancelReason = isset($topic['cancelReason']) ? $topic['cancelReason'] : null;
        $this->registerUrl = isset($topic['registerUrl']) ? $topic['registerUrl'] : null;
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
    public function checkMainSupervisorId()
    {
        return (is_null($this->mainSupervisorId) || (is_string($this->mainSupervisorId) && strlen($this->mainSupervisorId) == 32));
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
    public function checkRequestedSupervisorId()
    {
        return (is_null($this->requestedSupervisorId) || (is_string($this->requestedSupervisorId) && strlen($this->requestedSupervisorId) == 32));
    }

    /**
     * @return bool
     */
    public function checkStartPauseDate() {
        $this->setTimeZone();
        return (is_null($this->startPauseDate) || (is_string($this->startPauseDate) && $this->startPauseDate == date('Y-m-d H:i:s',strtotime($this->startPauseDate))));
    }

    /**
     * @return bool
     */
    public function checkPauseDuration() {
        return (is_null($this->pauseDuration) || is_int($this->pauseDuration));
    }

    /**
     * @return bool
     */
    public function checkDelayDuration() {
       return (is_null($this->delayDuration) || is_int($this->delayDuration));
    }

    /**
     * @return bool
     */
    public function checkCancelReason() {
        return (is_null($this->cancelReason) || is_string($this->cancelReason));
    }

    /**
     * @return bool
     */
    public function checkRegisterUrl() {
        return (is_null($this->registerUrl) || (is_string($this->registerUrl) && strlen($this->registerUrl) <= 255));
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
    public function getStartPauseDate()
    {
        return $this->startPauseDate;
    }

    /**
     * @param null $startPauseDate
     */
    public function setStartPauseDate($startPauseDate)
    {
        $this->startPauseDate = $startPauseDate;
    }

    /**
     * @return null
     */
    public function getPauseDuration()
    {
        return $this->pauseDuration;
    }

    /**
     * @param null $pauseDuration
     */
    public function setPauseDuration($pauseDuration)
    {
        $this->pauseDuration = $pauseDuration;
    }

    /**
     * @return null
     */
    public function getDelayDuration()
    {
        return $this->delayDuration;
    }

    /**
     * @return null
     */
    public function getCancelReason()
    {
        return $this->cancelReason;
    }

    /**
     * @param null $cancelReason
     */
    public function setCancelReason($cancelReason)
    {
        $this->cancelReason = $cancelReason;
    }

    /**
     * @param null $delayDuration
     */
    public function setDelayDuration($delayDuration)
    {
        $this->delayDuration = $delayDuration;
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
            'vietnameseTopicTitle' => $this->vietnameseTopicTitle,
            'englishTopicTitle' => $this->englishTopicTitle,
            'isEnglish' => $this->isEnglish,
            'description' => $this->description,
            'tags' => $this->tags,
            'mainSupervisorId' => $this->mainSupervisorId,
            'coSupervisorIds' => $this->coSupervisorIds,
            'requestedSupervisorId' => $this->requestedSupervisorId,
            'startPauseDate' => $this->startPauseDate,
            'pauseDuration' => $this->pauseDuration,
            'delayDuration' => $this->delayDuration,
            'cancelReason' => $this->cancelReason,
            'registerUrl' => $this->registerUrl,
        );
    }
}
