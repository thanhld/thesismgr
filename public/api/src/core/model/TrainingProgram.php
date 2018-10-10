<?php
/**
 * Created by PhpStorm.
 * User: Can
 * Date: 18-Nov-16
 * Time: 1:57 PM
 */

namespace core\model;


use JsonSerializable;

class TrainingProgram implements JsonSerializable
{
    private $id;
    private $departmentId;
    private $trainingAreasId;
    private $trainingLevelsId;
    private $trainingTypesId;
    private $programCode;
    private $name;
    private $vietnameseThesisTitle;
    private $englishThesisTitle;
    private $startTime;
    private $trainingDuration;
    private $isInUse;
    private $thesisNormalizedFactor;

    /**
     * Program constructor.
     * @param $program
     * @internal param $id
     * @internal param $name
     * @internal param $facultyId
     * @internal param $category
     * @internal param $educationLevel
     * @internal param $educationType
     */
    public function __construct($program)
    {
        $this->id                       = isset($program['id'])                         ? intval($program['id']) : null;
        $this->departmentId             = isset($program['departmentId'])               ? $program['departmentId'] : null;
        $this->trainingLevelsId         = isset($program['trainingLevelsId'])           ? intval($program['trainingLevelsId']) : null;
        $this->trainingAreasId          = isset($program['trainingAreasId'])            ? intval($program['trainingAreasId']) : null;
        $this->trainingTypesId          = isset($program['trainingTypesId'])            ? intval($program['trainingTypesId']) : null;
        $this->programCode              = isset($program['programCode'])                ? $program['programCode'] : null;
        $this->name                     = isset($program['name'])                       ? $program['name'] : null;
        $this->vietnameseThesisTitle    = isset($program['vietnameseThesisTitle'])      ? $program['vietnameseThesisTitle'] : null;
        $this->englishThesisTitle       = isset($program['englishThesisTitle'])         ? $program['englishThesisTitle'] : null;
        $this->startTime                = isset($program['startTime'])                  ? $program['startTime'] : null;
        $this->trainingDuration         = isset($program['trainingDuration'])           ? doubleval($program['trainingDuration']) : null;
        $this->isInUse                  = isset($program['isInUse'])                    ? intval($program['isInUse']) : null;
        $this->thesisNormalizedFactor   = isset($program['thesisNormalizedFactor'])     ? floatval($program['thesisNormalizedFactor']) : null;
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
    public function checkDepartmentId()
    {
        return (!is_null($this->departmentId) && (is_string($this->departmentId) && strlen($this->departmentId) <= 32));
    }

    /**
     * @return bool
     */
    public function checkTrainingAreasId()
    {
        return (!is_null($this->trainingAreasId) && is_int($this->trainingAreasId));
    }

    /**
     * @return bool
     */
    public function checkTrainingLevelsId()
    {
        return (!is_null($this->trainingLevelsId) && is_int($this->trainingLevelsId));
    }

    /**
     * @return bool
     */
    public function checkTrainingTypesId()
    {
        return (!is_null($this->trainingTypesId) && is_int($this->trainingTypesId));
    }

    /**
     * @return bool
     */
    public function checkProgramCode()
    {
        return (!is_null($this->programCode) && (is_string($this->programCode) && strlen($this->programCode) <= 20));
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
    public function checkVietnameseThesisTitle()
    {
        return (!is_null($this->vietnameseThesisTitle) && (is_string($this->vietnameseThesisTitle) && strlen($this->vietnameseThesisTitle) <= 255));
    }

    /**
     * @return bool
     */
    public function checkEnglishThesisTitle()
    {
        return (is_null($this->englishThesisTitle) || (is_string($this->englishThesisTitle) && strlen($this->englishThesisTitle) <= 255));
    }

    /**
     * @return bool
     */
    public function checkStartTime()
    {
        return (is_null($this->startTime) || (is_string($this->startTime) && strlen($this->startTime) <= 4));
    }

    /**
     * @return bool
     */
    public function checkTrainingDuration()
    {
        return (!is_null($this->trainingDuration) && is_double($this->trainingDuration));
    }

    /**
     * @return bool
     */
    public function checkIsInUse()
    {
        return (!is_null($this->isInUse) && is_int($this->isInUse));
    }

    /**
     * @return bool
     */
    public function checkThesisNormalizedFactor()
    {
        return (!is_null($this->thesisNormalizedFactor) && is_float($this->thesisNormalizedFactor));
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
     * @return mixed
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    /**
     * @param mixed $departmentId
     */
    public function setDepartmentId($departmentId)
    {
        $this->departmentId = $departmentId;
    }

    /**
     * @return mixed
     */
    public function getTrainingAreasId()
    {
        return $this->trainingAreasId;
    }

    /**
     * @param mixed $trainingAreasId
     */
    public function setTrainingAreasId($trainingAreasId)
    {
        $this->trainingAreasId = $trainingAreasId;
    }

    /**
     * @return mixed
     */
    public function getTrainingLevelsId()
    {
        return $this->trainingLevelsId;
    }

    /**
     * @param mixed $trainingLevelsId
     */
    public function setTrainingLevelsId($trainingLevelsId)
    {
        $this->trainingLevelsId = $trainingLevelsId;
    }

    /**
     * @return mixed
     */
    public function getTrainingTypesId()
    {
        return $this->trainingTypesId;
    }

    /**
     * @param mixed $trainingTypesId
     */
    public function setTrainingTypesId($trainingTypesId)
    {
        $this->trainingTypesId = $trainingTypesId;
    }

    /**
     * @return mixed
     */
    public function getProgramCode()
    {
        return $this->programCode;
    }

    /**
     * @param mixed $programCode
     */
    public function setProgramCode($programCode)
    {
        $this->programCode = $programCode;
    }

    /**
     * @return mixed
     */
    public function getVietnameseThesisTitle()
    {
        return $this->vietnameseThesisTitle;
    }

    /**
     * @param mixed $vietnameseThesisTitle
     */
    public function setVietnameseThesisTitle($vietnameseThesisTitle)
    {
        $this->vietnameseThesisTitle = $vietnameseThesisTitle;
    }

    /**
     * @return mixed
     */
    public function getEnglishThesisTitle()
    {
        return $this->englishThesisTitle;
    }

    /**
     * @param mixed $englishThesisTitle
     */
    public function setEnglishThesisTitle($englishThesisTitle)
    {
        $this->englishThesisTitle = $englishThesisTitle;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param mixed $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return mixed
     */
    public function getTrainingDuration()
    {
        return $this->trainingDuration;
    }

    /**
     * @param mixed $trainingDuration
     */
    public function setTrainingDuration($trainingDuration)
    {
        $this->trainingDuration = $trainingDuration;
    }

    /**
     * @return mixed
     */
    public function getIsInUse()
    {
        return $this->isInUse;
    }

    /**
     * @param mixed $isInUse
     */
    public function setIsInUse($isInUse)
    {
        $this->isInUse = $isInUse;
    }

    /**
     * @return mixed
     */
    public function getThesisNormalizedFactor()
    {
        return $this->thesisNormalizedFactor;
    }
    /**
     * @param mixed $thesisNormalizedFactor
     */
    public function setThesisNormalizedFactor($thesisNormalizedFactor)
    {
        $this->thesisNormalizedFactor = $thesisNormalizedFactor;
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
            'departmentId' => $this->departmentId,
            'trainingAreasId' => $this->trainingAreasId,
            'trainingLevelsId' => $this->trainingLevelsId,
            'trainingTypesId' => $this->trainingTypesId,
            'programCode' => $this->programCode,
            'name' => $this->name,
            'vietnameseThesisTitle' => $this->vietnameseThesisTitle,
            'englishThesisTitle' => $this->englishThesisTitle,
            'startTime' => $this->startTime,
            'trainingDuration' => $this->trainingDuration,
            'isInUse' => $this->isInUse,
            'thesisNormalizedFactor' => $this->thesisNormalizedFactor,
        );
    }
}
