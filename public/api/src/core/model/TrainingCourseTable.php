<?php
namespace core\model;

use core\utility\DBUtility;
use PDO;
use PDOException;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/TrainingCourse.php';
require_once 'src/core/utility/DBUtility.php';

require_once 'src/core/model/PDOData.php';

/**
 * Class to interact with training course table
 */
class TrainingCourseTable
{

    private static $acceptList = array(
        'id' => 'int',
        'trainingProgramId' => 'int',
        'courseCode' => 'string',
        'courseName' => 'string',
        'admissionYear' => 'string',
        'isCompleted' => 'int'
    );

    /**
     * @param $option
     * @param $facultyId
     * @return array|null
     */
    public static function get($option, $facultyId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT dtc.id, dtc.admissionYear, dtc.isCompleted, dtc.courseCode, dtc.courseName,
                        dtc.trainingProgramId,
                        dtp.programCode AS trainingProgramCode, 
                        dta.areaCode AS trainingAreaCode,
                        (SELECT count(*)
                            FROM dict_training_courses dtc
                            INNER JOIN dict_training_programs dtp ON  dtc.trainingProgramId = dtp.id
                            INNER JOIN dict_training_areas dta ON  dtp.trainingAreasId = dta.id
                            WHERE dta.facultyId = :id AND ' . DBUtility::parseFilter($option['filter'], self::$acceptList) . '
                        ) AS c
                FROM dict_training_courses dtc
                INNER JOIN dict_training_programs dtp ON  dtc.trainingProgramId = dtp.id
                INNER JOIN dict_training_areas dta ON  dtp.trainingAreasId = dta.id
                WHERE dta.facultyId = :id AND ' . DBUtility::parseFilter($option['filter'], self::$acceptList) . ' ' .
                "ORDER BY {$option['order']} {$option['direction']};"
            );

            $stmt->bindParam(':id', $facultyId, PDO::PARAM_STR);

            $stmt->execute();
            $ret = array();
            $ret['data'] = array();
            $ret['count'] = 0;
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new TrainingCourse($result);
                $ret['count'] = intval($result['c']);
            }

            return $ret;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return null;
    }

    /**
     * @param $id
     * @param $facultyId
     * @return TrainingCourse|null
     */
    public static function getById($id, $facultyId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT dtc.id, dtc.admissionYear, dtc.isCompleted, dtc.courseCode, dtc.courseName,
                        dtc.trainingProgramId,
                        dtp.programCode AS trainingProgramCode, 
                        dta.areaCode AS trainingAreaCode
                FROM dict_training_courses dtc
                INNER JOIN dict_training_programs dtp ON  dtc.trainingProgramId = dtp.id
                INNER JOIN dict_training_areas dta ON  dtp.trainingAreasId = dta.id
                WHERE dtc.id = :id AND dta.facultyId = :fid;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fid', $facultyId, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new TrainingCourse($result);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return null;
    }

    /**
     * @param $id
     * @return null
     */
    public static function getLevelByCourseId($id){
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT dtl.levelType
                FROM dict_training_courses dtc
                INNER JOIN dict_training_programs dtp ON  dtc.trainingProgramId = dtp.id
                INNER JOIN dict_training_levels dtl ON  dtp.trainingLevelsId = dtl.id
                WHERE dtc.id = :id;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return $result['levelType'];
            } else {
                return null;
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return null;
    }

    /**
     * @param $trainingCourse TrainingCourse
     * @return bool|int
     */
    public static function addTrainingCourse($trainingCourse)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'INSERT IGNORE INTO dict_training_courses(trainingProgramId, courseCode, courseName, admissionYear, isCompleted)
                VALUES (:trainingProgramId, :courseCode, :courseName, :admissionYear, :isCompleted); '
            );

            $stmt->bindValue(':trainingProgramId', $trainingCourse->getTrainingProgramId(), PDO::PARAM_INT);
            $stmt->bindValue(':courseCode', $trainingCourse->getCourseCode(), PDO::PARAM_STR);
            $stmt->bindValue(':courseName', $trainingCourse->getcourseName(), PDO::PARAM_STR);
            $stmt->bindValue(':admissionYear', $trainingCourse->getAdmissionYear(), PDO::PARAM_STR);
            $stmt->bindValue(':isCompleted', $trainingCourse->getIsCompleted(), PDO::PARAM_INT);

            if ($stmt->execute() && $stmt->rowCount() != 0) {
                return intval($conn->lastInsertId('id'));
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    /**
     * @param $trcId
     * @return null
     */
    public static function getFacultyIdOf($trcId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT dta.facultyId 
                  FROM dict_training_courses dtc
                  INNER JOIN dict_training_programs dtp ON  dtc.trainingProgramId = dtp.id
                  INNER JOIN dict_training_areas dta ON  dtp.trainingAreasId = dta.id
                  WHERE dtc.id = :id'
            );

            $stmt->bindParam(':id', $trcId);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return $result['facultyId'];
            } else {
                return null;
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return null;
    }

    /**
     * @param $id
     * @param $trc
     * @return bool
     */
    public static function updateById($id, $trc)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'UPDATE dict_training_courses SET  ' . DBUtility::parseUpdateQuery($trc) . '  WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            foreach ($trc as $key => $value) {
                if ($value != null) {
                    $stmt->bindParam(":{$key}", $trc[$key]);
                } else {
                    $stmt->bindParam(":{$key}", $trc[$key], PDO::PARAM_NULL);
                }
            }

            $stmt->execute();
            return $stmt->rowCount() !== 0;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    /**
     * @param $id
     * @return array|bool
     */
    public static function deleteById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'DELETE FROM dict_training_courses WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            $ret['data'] = $stmt->errorInfo();
            $ret['rowCount'] = $stmt->rowCount();
            return $ret;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }
}
