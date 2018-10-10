<?php
namespace core\model;

use core\utility\DBUtility;
use PDO;
use PDOException;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/Learner.php';
require_once 'src/core/utility/DBUtility.php';

/**
 * Class to interact with learners table
 */
class LearnerTable
{
    private static $acceptList = array(
        'fullname' => 'string',
        'learnerType' => 'int',
        'otherEmail' => 'string',
        'phone' => 'string',
        'avatarUrl' => 'string',
        'gpa' => 'double',
        'description' => 'string',
        'learnerCode' => 'string',
        'trainingCourseId' => 'int'
    );

    private static $publicAcceptList = array(
        'id' => 'string',
        'fullname' => 'string',
        'learnerType' => 'int',
        'gpa' => 'double',
        'learnerCode' => 'string',
        'trainingCourseId' => 'int'
    );

    /**
     * @param $learner Learner
     * @return string|null
     */
    public static function insert($learner)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'INSERT INTO learners(id, learnerCode, fullname, trainingCourseId, learnerType)' .
                ' VALUES (:id, :learnerCode, :fullname, :trainingCourseId, :learnerType);'
            );

            $stmt->bindValue(':id', $learner->getId(), PDO::PARAM_STR);
            $stmt->bindValue(':learnerCode', $learner->getLearnerCode(), PDO::PARAM_STR);
            $stmt->bindValue(':fullname', $learner->getFullname(), PDO::PARAM_STR);
            $stmt->bindValue(':trainingCourseId', $learner->getTrainingCourseId(), PDO::PARAM_INT);
            $stmt->bindValue(':learnerType', $learner->getLearnerType(), PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $conn->lastInsertId();
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
     * @param $learner Learner
     * @return string|null
     */
    public static function backUpLearner($learner)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'INSERT INTO learners(id, learnerCode, fullname, trainingCourseId, learnerType,
                            otherEmail, phone, avatarUrl, gpa, description)' .
                ' VALUES (:id, :learnerCode, :fullname, :trainingCourseId, :learnerType,
                            :otherEmail, :phone, :avatarUrl, :gpa, :description);'
            );

            $stmt->bindValue(':id', $learner->getId(), PDO::PARAM_STR);
            $stmt->bindValue(':learnerCode', $learner->getLearnerCode(), PDO::PARAM_STR);
            $stmt->bindValue(':fullname', $learner->getFullname(), PDO::PARAM_STR);
            $stmt->bindValue(':trainingCourseId', $learner->getTrainingCourseId(), PDO::PARAM_INT);
            $stmt->bindValue(':learnerType', $learner->getLearnerType(), PDO::PARAM_INT);
            $stmt->bindValue(':otherEmail', $learner->getOtherEmail(), PDO::PARAM_STR);
            $stmt->bindValue(':phone', $learner->getPhone(), PDO::PARAM_STR);
            $stmt->bindValue(':avatarUrl', $learner->getAvatarUrl(), PDO::PARAM_STR);
            $stmt->bindValue(':gpa', $learner->getGpa(), PDO::PARAM_STR);
            $stmt->bindValue(':description', $learner->getDescription(), PDO::PARAM_STR);

            if ($stmt->execute()) {
                return true;
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
     * @param $option
     * @param $facultyId
     * @return array|null
     */
    public static function adminGet($option, $facultyId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            if($option['limit'] != 0 && $option['offset'] != -1) {
                $limitQuery = 'LIMIT :limit OFFSET :offset;';
            } else {
                $limitQuery = ';';
            }

            $stmt = $conn->prepare(
                'SELECT l.id, a.username, a.vnuMail, l.learnerCode, l.fullname, l.gpa, l.trainingCourseId, l.learnerType,
                    dtc.courseCode AS trainingCourseCode,
                    dtp.programCode AS trainingProgramCode,
                    dta.areaCode AS trainingAreaCode,
                    (SELECT count(*)
                        FROM learners l
                        INNER JOIN accounts a ON l.id = a.uid
                        INNER JOIN dict_training_courses dtc ON l.trainingCourseId = dtc.id
                        INNER JOIN dict_training_programs dtp ON  dtc.trainingProgramId = dtp.id
                        INNER JOIN dict_training_areas dta ON  dtp.trainingAreasId = dta.id
                        WHERE dta.facultyId = :id AND ' . DBUtility::parseFilter($option['filter'], self::$publicAcceptList) . '
                    ) AS c
                FROM learners l
                INNER JOIN accounts a ON l.id = a.uid
                INNER JOIN dict_training_courses dtc ON l.trainingCourseId = dtc.id
                INNER JOIN dict_training_programs dtp ON  dtc.trainingProgramId = dtp.id
                INNER JOIN dict_training_areas dta ON  dtp.trainingAreasId = dta.id
                WHERE dta.facultyId = :id AND ' . DBUtility::parseFilter($option['filter'], self::$acceptList) . ' ' .
                "ORDER BY {$option['order']} {$option['direction']} ". $limitQuery
            );

            $stmt->bindParam(':id', $facultyId, PDO::PARAM_STR);

            if($option['limit'] != 0 && $option['offset'] != -1){
                $stmt->bindParam(':limit', $option['limit'], PDO::PARAM_INT);
                $stmt->bindParam(':offset', $option['offset'], PDO::PARAM_INT);
            }

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            $ret['count'] = 0;
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new Learner($result);
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
     * @return null
     */
    public static function getFacultyIdOf($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT facultyId FROM learners
                INNER JOIN dict_training_courses dtc ON learners.trainingCourseId = dtc.id
                INNER JOIN dict_training_programs dtp ON  dtc.trainingProgramId = dtp.id
                INNER JOIN dict_training_areas dta ON  dtp.trainingAreasId = dta.id
                WHERE learners.id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

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
     * @return null
     */
    public static function getDepartmentIdOf($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT dtp.departmentId FROM learners l
                INNER JOIN dict_training_courses dtc ON l.trainingCourseId = dtc.id
                INNER JOIN dict_training_programs dtp ON  dtc.trainingProgramId = dtp.id
                WHERE l.id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return $result['departmentId'];
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
    public static function getById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT l.id, a.username, a.vnuMail, l.fullname, l.otherEmail, l.phone, l.avatarUrl,
                        l.gpa, l.description, l.learnerCode, l.learnerType, l.trainingCourseId,
                        dtc.courseCode AS trainingCourseCode,
                        dtp.programCode AS trainingProgramCode,
                        dta.areaCode AS trainingAreaCode
                FROM learners l
                INNER JOIN accounts a ON l.id = a.uid
                INNER JOIN dict_training_courses dtc ON l.trainingCourseId = dtc.id
                INNER JOIN dict_training_programs dtp ON  dtc.trainingProgramId = dtp.id
                INNER JOIN dict_training_areas dta ON  dtp.trainingAreasId = dta.id
                WHERE l.id = :id;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new Learner($result);
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
    public static function adminGetById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT l.id, a.username, a.vnuMail, l.learnerCode, l.fullname, l.gpa, l.trainingCourseId, l.learnerType,
                        dtc.courseCode AS trainingCourseCode,
                        dtp.programCode AS trainingProgramCode,
                        dta.areaCode AS trainingAreaCode
                FROM learners l
                INNER JOIN accounts a ON l.id = a.uid
                INNER JOIN dict_training_courses dtc ON l.trainingCourseId = dtc.id
                INNER JOIN dict_training_programs dtp ON  dtc.trainingProgramId = dtp.id
                INNER JOIN dict_training_areas dta ON  dtp.trainingAreasId = dta.id
                WHERE l.id = :id;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new Learner($result);
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
     * @param $learnerCode
     * @return null
     */
    public static function getIdByLearnerCode($learnerCode)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT id, learnerType
                FROM learners
                WHERE learnerCode = :learnerCode;'
            );

            $stmt->bindParam(':learnerCode', $learnerCode, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return $result;
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
     * @param $lrn
     * @return bool
     */
    public static function updateById($id, $lrn)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'UPDATE learners SET ' . DBUtility::parseUpdateQuery($lrn) . ' WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            foreach ($lrn as $key => $value) {
                if ($value != null) {
                    $stmt->bindParam(":{$key}", $lrn[$key]);
                } else {
                    $stmt->bindParam(":{$key}", $lrn[$key], PDO::PARAM_NULL);
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
                'DELETE FROM learners WHERE id = :id;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

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
