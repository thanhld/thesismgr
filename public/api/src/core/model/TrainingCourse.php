<?php
/**
 * Created by PhpStorm.
 * User: Can
 * Date: 18-Nov-16
 * Time: 1:57 PM
 */

namespace core\model;


use JsonSerializable;

class TrainingCourse implements JsonSerializable
{
    private $id;
    private $trainingProgramId;
    private $trainingProgramCode;
    private $trainingAreaCode;
    private $courseCode;
    private $courseName;
    private $admissionYear;
    private $isCompleted;

    /**
     * Program constructor.
     * @param $course
     * @internal param $id
     * @internal param $trainingProgramId
     * @internal param $trainingProgramCode
     * @internal param $trainingAreaCode
     * @internal param $courseCode
     * @internal param $courseName
     * @internal param $admissionYear
     * @internal param $isCompleted
     */
    public function __construct($course)
    {
        $this->id                   = isset($course['id'])                      ? intval($course['id']) : null;
        $this->trainingProgramId    = isset($course['trainingProgramId'])       ? intval($course['trainingProgramId']) : null;
        $this->trainingProgramCode  = isset($course['trainingProgramCode'])     ? $course['trainingProgramCode'] : null;
        $this->trainingAreaCode     = isset($course['trainingAreaCode'])        ? $course['trainingAreaCode'] : null;
        $this->courseCode           = isset($course['courseCode'])              ? $course['courseCode'] : null;
        $this->courseName           = isset($course['courseName'])              ? $course['courseName'] : null;
        $this->admissionYear        = isset($course['admissionYear'])           ? $course['admissionYear'] : null;
        $this->isCompleted          = isset($course['isCompleted'])             ? intval($course['isCompleted']) : null;
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
    public function checkTrainingProgramId()
    {
        return (!is_null($this->trainingProgramId) && is_int($this->trainingProgramId));
    }

    /**
     * @return bool
     */
    public function checkAdmissionYear()
    {
        return (!is_null($this->admissionYear) && (is_string($this->admissionYear) && strlen($this->admissionYear) <= 4));
    }

    /**
     * @return bool
     */
    public function checkCourseCode()
    {
        return (!is_null($this->courseCode) && (is_string($this->courseCode) && strlen($this->courseCode) <= 20));
    }

    /**
     * @return bool
     */
    public function checkCourseName()
    {
        return (!is_null($this->courseName) && (is_string($this->courseName) && strlen($this->courseName) <= 255));
    }

    /**
     * @return bool
     */
    public function checkIsCompleted()
    {
        return (!is_null($this->isCompleted) && is_int($this->isCompleted));
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
    public function getAdmissionYear()
    {
        return $this->admissionYear;
    }

    /**
     * @param mixed $admissionYear
     */
    public function setAdmissionYear($admissionYear)
    {
        $this->admissionYear = $admissionYear;
    }

    /**
     * @return mixed
     */
    public function getCourseName()
    {
        return $this->courseName;
    }

    /**
     * @param mixed $courseName
     */
    public function setCourseName($courseName)
    {
        $this->courseName = $courseName;
    }

    /**
     * @return mixed
     */
    public function getTrainingProgramId()
    {
        return $this->trainingProgramId;
    }

    /**
     * @param mixed $trainingProgramId
     */
    public function setTrainingProgramId($trainingProgramId)
    {
        $this->trainingProgramId = $trainingProgramId;
    }

    /**
     * @return mixed
     */
    public function getCourseCode()
    {
        return $this->courseCode;
    }

    /**
     * @param mixed $courseCode
     */
    public function setCourseCode($courseCode)
    {
        $this->courseCode = $courseCode;
    }

    /**
     * @return mixed
     */
    public function getIsCompleted()
    {
        return $this->isCompleted;
    }

    /**
     * @param mixed $isCompleted
     */
    public function setIsCompleted($isCompleted)
    {
        $this->isCompleted = $isCompleted;
    }



    /**
     * @return null
     */
    public function getTrainingProgramCode()
    {
        return $this->trainingProgramCode;
    }

    /**
     * @return null
     */
    public function getTrainingAreaCode()
    {
        return $this->trainingAreaCode;
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
            'trainingProgramId' => $this->trainingProgramId,
            'trainingProgramCode' => $this->trainingProgramCode,
            'trainingAreaCode' => $this->trainingAreaCode,
            'courseCode' => $this->courseCode,
            'courseName' => $this->courseName,
            'admissionYear' => $this->admissionYear,
            'isCompleted' => $this->isCompleted
        );
    }

}