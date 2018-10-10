<?php
/**
 * Created by PhpStorm.
 * User: Can
 * Date: 18-Nov-16
 * Time: 3:50 PM
 */

namespace core\model;


use JsonSerializable;

class Quota implements JsonSerializable
{
    private $id;
    private $degreeId;
    private $maxStudent;
    private $maxResearcher;
    private $maxGraduated;
    private $version;
    private $isActive;
    private $mainFactorStudent;
    private $mainFactorGraduated;
    private $mainFactorResearcher;
    private $coFactorStudent;
    private $coFactorGraduated;
    private $coFactorResearcher;


    /**
     * MaximumTopic constructor.
     * @param $quota
     * @internal param $id
     * @internal param $degreeId
     * @internal param $maxStudent
     * @internal param $maxResearcher
     * @internal param $maxGraduated
     * @internal param $version
     * @internal param $isActive
     * @internal param $mainFactorStudent
     * @internal param $mainFactorGraduated
     * @internal param $mainFactorResearcher
     * @internal param $coFactorStudent
     * @internal param $coFactorGraduated
     * @internal param $coFactorResearcher
     */
    public function __construct($quota)
    {
        $this->id                       = isset($quota['id'])                        ? intval($quota['id']) : null;
        $this->degreeId                 = isset($quota['degreeId'])                  ? intval($quota['degreeId']) : null;
        $this->maxStudent               = isset($quota['maxStudent'])                ? intval($quota['maxStudent']) : null;
        $this->maxResearcher            = isset($quota['maxResearcher'])             ? intval($quota['maxResearcher']) : null;
        $this->maxGraduated             = isset($quota['maxGraduated'])              ? intval($quota['maxGraduated']) : null;
        $this->version                  = isset($quota['version'])                   ? $quota['version'] : null;
        $this->isActive                 = isset($quota['isActive'])                  ? intval($quota['isActive']) : null;
        $this->mainFactorStudent        = isset($quota['mainFactorStudent'])         ? doubleval($quota['mainFactorStudent']) : null;
        $this->mainFactorGraduated      = isset($quota['mainFactorGraduated'])       ? doubleval($quota['mainFactorGraduated']) : null;
        $this->mainFactorResearcher     = isset($quota['mainFactorResearcher'])      ? doubleval($quota['mainFactorResearcher']) : null;
        $this->coFactorStudent          = isset($quota['coFactorStudent'])           ? doubleval($quota['coFactorStudent']) : null;
        $this->coFactorGraduated        = isset($quota['coFactorGraduated'])         ? doubleval($quota['coFactorGraduated']) : null;
        $this->coFactorResearcher       = isset($quota['coFactorResearcher'])        ? doubleval($quota['coFactorResearcher']) : null;
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
    public function checkDegreeId() {
        return (!is_null($this->degreeId) && is_int($this->degreeId));
    }

    /**
     * @return bool
     */
    public function checkMaxStudent() {
        return (!is_null($this->maxStudent) && is_int($this->maxStudent));
    }

    /**
     * @return bool
     */
    public function checkMaxResearcher() {
        return (!is_null($this->maxResearcher) && is_int($this->maxResearcher));
    }

    /**
     * @return bool
     */
    public function checkMaxGraduated() {
        return (!is_null($this->maxGraduated) && is_int($this->maxGraduated));
    }

    /**
     * @return bool
     */
    public function checkVersion() {
        return (!is_null($this->version) && (is_string($this->version) && strlen($this->version) <= 20));
    }

    /**
     * @return bool
     */
    public function checkIsActive() {
        return (!is_null($this->isActive) && is_int($this->isActive));
    }

    /**
     * @return bool
     */
    public function checkMainFactorStudent() {
        return (is_double($this->mainFactorStudent) && ($this->mainFactorStudent >= $this->coFactorStudent));
    }

    /**
     * @return bool
     */
    public function checkMainFactorResearcher() {
        return (is_double($this->mainFactorResearcher) && ($this->mainFactorResearcher >= $this->coFactorResearcher));
    }

    /**
     * @return bool
     */
    public function checkMainFactorGraduated() {
        return (is_double($this->mainFactorGraduated) && ($this->mainFactorGraduated >= $this->coFactorGraduated));
    }

    /**
     * @return bool
     */
    public function checkCoFactorStudent() {
        return (is_double($this->coFactorStudent) && ($this->coFactorStudent > 0));
    }

    /**
     * @return bool
     */
    public function checkCoFactorResearcher() {
        return (is_double($this->coFactorResearcher) && ($this->coFactorResearcher > 0));
    }

    /**
     * @return bool
     */
    public function checkCoFactorGraduated() {
        return (is_double($this->coFactorGraduated) && ($this->coFactorGraduated > 0));
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getDegreeId()
    {
        return $this->degreeId;
    }

    /**
     * @param int|null $degreeId
     */
    public function setDegreeId($degreeId)
    {
        $this->degreeId = $degreeId;
    }

    /**
     * @return int|null
     */
    public function getMaxStudent()
    {
        return $this->maxStudent;
    }

    /**
     * @param int|null $maxStudent
     */
    public function setMaxStudent($maxStudent)
    {
        $this->maxStudent = $maxStudent;
    }

    /**
     * @return int|null
     */
    public function getMaxResearcher()
    {
        return $this->maxResearcher;
    }

    /**
     * @param int|null $maxResearcher
     */
    public function setMaxResearcher($maxResearcher)
    {
        $this->maxResearcher = $maxResearcher;
    }

    /**
     * @return int|null
     */
    public function getMaxGraduated()
    {
        return $this->maxGraduated;
    }

    /**
     * @param int|null $maxGraduated
     */
    public function setMaxGraduated($maxGraduated)
    {
        $this->maxGraduated = $maxGraduated;
    }

    /**
     * @return null
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param null $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return int|null
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param int|null $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }


    /**
     * @return null|string
     */
    public function getMainFactorStudent()
    {
        return $this->mainFactorStudent;
    }

    /**
     * @param null|string $mainFactorStudent
     */
    public function setMainFactorStudent($mainFactorStudent)
    {
        $this->mainFactorStudent = $mainFactorStudent;
    }

    /**
     * @return null|string
     */
    public function getMainFactorGraduated()
    {
        return $this->mainFactorGraduated;
    }

    /**
     * @param null|string $mainFactorGraduated
     */
    public function setMainFactorGraduated($mainFactorGraduated)
    {
        $this->mainFactorGraduated = $mainFactorGraduated;
    }

    /**
     * @return null|string
     */
    public function getMainFactorResearcher()
    {
        return $this->mainFactorResearcher;
    }

    /**
     * @param null|string $mainFactorResearcher
     */
    public function setMainFactorResearcher($mainFactorResearcher)
    {
        $this->mainFactorResearcher = $mainFactorResearcher;
    }

    /**
     * @return null|string
     */
    public function getCoFactorStudent()
    {
        return $this->coFactorStudent;
    }

    /**
     * @param null|string $coFactorStudent
     */
    public function setCoFactorStudent($coFactorStudent)
    {
        $this->coFactorStudent = $coFactorStudent;
    }

    /**
     * @return null|string
     */
    public function getCoFactorGraduated()
    {
        return $this->coFactorGraduated;
    }

    /**
     * @param null|string $coFactorGraduated
     */
    public function setCoFactorGraduated($coFactorGraduated)
    {
        $this->coFactorGraduated = $coFactorGraduated;
    }

    /**
     * @return null|string
     */
    public function getCoFactorResearcher()
    {
        return $this->coFactorResearcher;
    }

    /**
     * @param null|string $coFactorResearcher
     */
    public function setCoFactorResearcher($coFactorResearcher)
    {
        $this->coFactorResearcher = $coFactorResearcher;
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
            'degreeId' => $this->degreeId,
            'maxStudent' => $this->maxStudent,
            'maxResearcher' => $this->maxResearcher,
            'maxGraduated' => $this->maxGraduated,
            'version' => $this->version,
            'isActive' => $this->isActive,
            'mainFactorStudent' => $this->mainFactorStudent,
            'mainFactorResearcher' => $this->mainFactorResearcher,
            'mainFactorGraduated' => $this->mainFactorGraduated,
            'coFactorStudent' => $this->coFactorStudent,
            'coFactorResearcher' => $this->coFactorResearcher,
            'coFactorGraduated' => $this->coFactorGraduated
        );
    }
}