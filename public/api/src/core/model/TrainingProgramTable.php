<?php
namespace core\model;

use core\utility\DBUtility;
use PDO;
use PDOException;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/TrainingProgram.php';
require_once 'src/core/utility/DBUtility.php';

require_once 'src/core/model/PDOData.php';

/**
 * Class to interact with programs table
 */
class TrainingProgramTable
{
    private static $acceptList = array(
        'id' => 'int',
        'facultyId' => 'string',
        'name' => 'string',
        'category' => 'string',
        'educationLevel' => 'string',
        'educationType' => 'string'
    );

    /**
     * @param $facultyId
     * @return array|null
     */
    public static function get($facultyId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'SELECT dtp.id, dtp.departmentId, dtp.trainingAreasId, dtp.trainingLevelsId, dtp.trainingTypesId,
                        dtp.programCode, dtp.name, dtp.vietnameseThesisTitle, dtp.englishThesisTitle, dtp.startTime,
                        dtp.trainingDuration, dtp.isInUse, dtp.thesisNormalizedFactor
                FROM dict_training_programs dtp
                INNER JOIN dict_training_areas dta ON dtp.trainingAreasId = dta.id
                WHERE dta.facultyId = :fid;'
            );

            $stmt->bindParam(':fid', $facultyId, PDO::PARAM_STR);

            $stmt->execute();
            $ret = array();
            $ret['data'] = array();
            //$ret['count'] = 0;
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new TrainingProgram($result);
                //$ret['count'] = intval($result['c']);
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
     * @return TrainingProgram|null
     */
    public static function getById($id, $facultyId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'SELECT dtp.id, dtp.departmentId, dtp.trainingAreasId, dtp.trainingLevelsId, dtp.trainingTypesId,
                        dtp.programCode, dtp.name, dtp.vietnameseThesisTitle, dtp.englishThesisTitle, dtp.startTime,
                        dtp.trainingDuration, dtp.isInUse, dtp.thesisNormalizedFactor
                FROM dict_training_programs dtp
                INNER JOIN dict_training_areas dta ON dtp.trainingAreasId = dta.id
                WHERE dtp.id = :id AND dta.facultyId = :fid;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fid', $facultyId, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new TrainingProgram($result);
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
     * @param $program TrainingProgram
     * @return bool|int
     */
    public static function addProgram($program)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'INSERT IGNORE INTO dict_training_programs(departmentId, trainingAreasId, trainingLevelsId, trainingTypesId,
                                                programCode, name, vietnameseThesisTitle, englishThesisTitle, startTime,
                                                trainingDuration, isInUse)
                VALUES (:departmentId, :trainingAreasId, :trainingLevelsId, :trainingTypesId,
                        :programCode, :name, :vietnameseThesisTitle, :englishThesisTitle, :startTime,
                        :trainingDuration, :isInUse); '
            );

            $stmt->bindValue(':departmentId', $program->getDepartmentId(), PDO::PARAM_STR);
            $stmt->bindValue(':trainingAreasId', $program->getTrainingAreasId(), PDO::PARAM_INT);
            $stmt->bindValue(':trainingLevelsId', $program->getTrainingLevelsId(), PDO::PARAM_INT);
            $stmt->bindValue(':trainingTypesId', $program->getTrainingTypesId(), PDO::PARAM_INT);
            $stmt->bindValue(':programCode', $program->getProgramCode(), PDO::PARAM_STR);
            $stmt->bindValue(':name', $program->getName(), PDO::PARAM_STR);
            $stmt->bindValue(':vietnameseThesisTitle', $program->getVietnameseThesisTitle(), PDO::PARAM_STR);
            $stmt->bindValue(':englishThesisTitle', $program->getEnglishThesisTitle(), PDO::PARAM_STR);
            $stmt->bindValue(':startTime', $program->getStartTime(), PDO::PARAM_STR);
            $stmt->bindValue(':trainingDuration', $program->getTrainingDuration(), PDO::PARAM_STR);
            $stmt->bindValue(':isInUse', $program->getIsInUse(), PDO::PARAM_INT);

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
     * @param $prgId
     * @return null|string
     */
    public static function getFacultyIdOf($prgId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'SELECT dta.facultyId
                FROM dict_training_programs dtp
                INNER JOIN dict_training_areas dta ON dtp.trainingAreasId = dta.id
                WHERE dtp.id = :id'
            );

            $stmt->bindParam(':id', $prgId);

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
     * @return array|bool
     */
    public static function deleteById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'DELETE FROM dict_training_programs WHERE id = :id'
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

    public static function updateById($id, $prg)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'UPDATE dict_training_programs SET  ' . DBUtility::parseUpdateQuery($prg) . '  WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            foreach ($prg as $key => $value) {
                if ($value != null) {
                    $stmt->bindParam(":{$key}", $prg[$key]);
                } else {
                    $stmt->bindParam(":{$key}", $prg[$key], PDO::PARAM_NULL);
                }
            }

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
