<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 5/21/2017
 * Time: 02:49 PM
 */

namespace core\model;

use JsonSerializable;

class Review implements JsonSerializable
{
    private $id;
    private $topicId;
    private $topicStatus;
    private $departmentSuperId;
    private $officerId;
    private $reviewStatus;
    private $content;
    private $iteration;
    private $created;


    /**
     * Review constructor.
     * @param $review
     * @internal param $id
     * @internal param $topicId
     * @internal param $topicStatus
     * @internal param $departmentSuperId
     * @internal param $officerId
     * @internal param $reviewStatus
     * @internal param $iteration
     * @internal param $content
     */
    public function __construct($review)
    {
        $this->id = isset($review['id']) ? $review['id'] : null;
        $this->topicId = isset($review['topicId']) ? $review['topicId'] : null;
        $this->topicStatus = isset($review['topicStatus']) ? intval($review['topicStatus']) : null;
        $this->departmentSuperId = isset($review['departmentSuperId']) ? $review['departmentSuperId'] : null;
        $this->officerId = isset($review['officerId']) ? $review['officerId'] : null;
        $this->reviewStatus = isset($review['reviewStatus']) ? intval($review['reviewStatus']) : null;
        $this->content = isset($review['content']) ? $review['content'] : null;
        $this->iteration = isset($review['iteration']) ? intval($review['iteration']) : null;
        $this->created = isset($review['created']) ? $review['created'] : null;
    }

    /**
     * @return bool
     */
    public function checkId() {
        return (!is_null($this->id) && is_int($this->id));
    }

    /**
     * @return bool
     */
    public function checkTopicId()
    {
        return (!is_null($this->topicId) && (is_string($this->topicId) && strlen($this->topicId) == 32));
    }

    /**
     * @return bool
     */
    public function checkTopicStatus()
    {
        return (!is_null($this->topicStatus) && is_int($this->topicStatus));
    }

    /**
     * @return bool
     */
    public function checkDepartmentSuperId()
    {
        return (!is_null($this->departmentSuperId) && (is_string($this->departmentSuperId) && strlen($this->departmentSuperId) == 32));
    }

    /**
     * @return bool
     */
    public function checkOfficerId()
    {
        return (!is_null($this->officerId) && (is_string($this->officerId) && strlen($this->officerId) == 32));
    }

    /**
     * @return bool
     */
    public function checkReviewStatus()
    {
        return (!is_null($this->reviewStatus) && is_int($this->reviewStatus) && ($this->reviewStatus >= 0 && $this->reviewStatus <= 5));
    }

    /**
     * @return bool
     */
    public function checkContent()
    {
        return (!is_null($this->content) || is_string($this->content));
    }

    /**
     * @return bool
     */
    public function checkIteration()
    {
        return (is_int($this->iteration));
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
    public function getTopicStatus()
    {
        return $this->topicStatus;
    }

    /**
     * @param mixed $topicStatus
     */
    public function setTopicStatus($topicStatus)
    {
        $this->topicStatus = $topicStatus;
    }

    /**
     * @return mixed
     */
    public function getDepartmentSuperId()
    {
        return $this->departmentSuperId;
    }

    /**
     * @param mixed $departmentSuperId
     */
    public function setDepartmentSuperId($departmentSuperId)
    {
        $this->departmentSuperId = $departmentSuperId;
    }

    /**
     * @return mixed
     */
    public function getOfficerId()
    {
        return $this->officerId;
    }

    /**
     * @param mixed $officerId
     */
    public function setOfficerId($officerId)
    {
        $this->officerId = $officerId;
    }

    /**
     * @return mixed
     */
    public function getReviewStatus()
    {
        return $this->reviewStatus;
    }

    /**
     * @param mixed $reviewStatus
     */
    public function setReviewStatus($reviewStatus)
    {
        $this->reviewStatus = $reviewStatus;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getIteration()
    {
        return $this->iteration;
    }

    /**
     * @param mixed $iteration
     */
    public function setIteration($iteration)
    {
        $this->iteration = $iteration;
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

    function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'topicId' => $this->topicId,
            'topicStatus' => $this->topicStatus,
            'departmentSuperId' => $this->departmentSuperId,
            'officerId' => $this->officerId,
            'reviewStatus' => $this->reviewStatus,
            'content' => $this->content,
            'iteration' => $this->iteration,
            'created' => $this->created
        );
    }
}