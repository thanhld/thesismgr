<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2/27/2017
 * Time: 08:32 PM
 */

namespace core\model;

use JsonSerializable;

require_once 'src/core/model/Attachment.php';

class Announcement implements JsonSerializable
{
    private $id;
    private $facultyId;
    private $title;
    private $tags;
    private $content;
    private $attachment;
    private $showDate;
    private $hideDate;

    /**
     * Announcement constructor.
     * @param $announcement
     * @internal param $id
     * @internal param $facultyId
     * @internal param $title
     * @internal param $tags
     * @internal param $content
     * @internal param $attachment
     * @internal param $showDate
     * @internal param $hideDate
     */
    public function __construct($announcement)
    {
        $this->id = isset($announcement['id']) ? intval($announcement['id']) : null;
        $this->facultyId = isset($announcement['facultyId']) ? $announcement['facultyId'] : null;
        $this->title = isset($announcement['title']) ? $announcement['title'] : null;
        $this->tags = isset($announcement['tags']) ? $announcement['tags'] : null;
        $this->content = isset($announcement['content']) ? $announcement['content'] : null;
        $this->showDate = isset($announcement['showDate']) ? $announcement['showDate'] : null;
        $this->hideDate = isset($announcement['hideDate']) ? $announcement['hideDate'] : null;

        $attm = array();
        $attm['id'] = isset($announcement['attachmentId']) ? intval($announcement['attachmentId']) : null;
        $attm['announcementId'] = isset($announcement['id']) ? intval($announcement['id']) : null;
        $attm['name'] = isset($announcement['attachmentName']) ? $announcement['attachmentName'] : null;
        $attm['url'] = isset($announcement['url']) ? $announcement['url'] : null;
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
    public function checkTitle()
    {
        return (!is_null($this->title) && (is_string($this->title) && strlen($this->title) <= 255));
    }

    /**
     * @return bool
     */
    public function checkTags()
    {
        return (is_null($this->tags) || (is_string($this->tags) && strlen($this->tags) <= 255));
    }

    /**
     * @return bool
     */
    public function checkContent()
    {
        return (is_null($this->content) || (is_string($this->content) && strlen($this->content) <= 255));
    }

    /**
     * @return bool
     */
    public function checkShowDate() {
        self::setTimeZone();
        return (!is_null($this->showDate) && (is_string($this->showDate) && $this->showDate == date('Y-m-d H:i:s',strtotime($this->showDate))));
    }

    /**
     * @return bool
     */
    public function checkHideDate() {
        self::setTimeZone();
        return (!is_null($this->hideDate) && (is_string($this->hideDate) && $this->hideDate == date('Y-m-d H:i:s',strtotime($this->hideDate))));
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return null
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
     * @return Attachment
     */
    public function getAttachment()
    {
        return $this->attachment;
    }
    
    /**
     * @return mixed
     */
    public function getShowDate()
    {
        return $this->showDate;
    }

    /**
     * @param null $showDate
     */
    public function setShowDate($showDate)
    {
        $this->showDate = $showDate;
    }

    /**
     * @return mixed
     */
    public function getHideDate()
    {
        return $this->hideDate;
    }

    /**
     * @param mixed $hideDate
     */
    public function setHideDate($hideDate)
    {
        $this->hideDate = $hideDate;
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
            'title' => $this->title,
            'tags' => $this->tags,
            'content' => $this->content,
            'attachment' => $this->attachment,
            'showDate' => $this->showDate,
            'hideDate' => $this->hideDate,
        );
    }
}