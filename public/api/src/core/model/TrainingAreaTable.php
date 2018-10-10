<?php
namespace core\model;

use core\utility\DBUtility;
use PDO;
use PDOException;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/TrainingArea.php';
require_once 'src/core/utility/DBUtility.php';
require_once 'src/core/model/PDOData.php';

/**
 * Class to interact with training course table
 */
class TrainingAreaTable
{
    /**
     *
     * @param $facultyId
     * @return array|null
     */
    public static function get($facultyId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT id, facultyId, areaCode, name
                  FROM dict_training_areas
                  WHERE facultyId = :id;'
            );

            $stmt->bindParam(':id', $facultyId, PDO::PARAM_INT);

            $stmt->execute();
            $ret = array();
            $ret['data'] = array();
            //$ret['count'] = 0;
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new TrainingArea($result);
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
     * @return TrainingArea|null
     */
    public static function getById($id, $facultyId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT id, facultyId, areaCode, name FROM dict_training_areas 
                  WHERE id = :id  AND facultyId = :fid;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':fid', $facultyId, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new TrainingArea($result);
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
     * @return bool|int
     * @param $trainingArea TrainingArea
     */
    public static function addTrainingArea($trainingArea)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'INSERT IGNORE INTO dict_training_areas(facultyId, areaCode, name)
                VALUES (:facultyId, :areaCode, :name); '
            );

            $stmt->bindValue(':facultyId', $trainingArea->getFacultyId(), PDO::PARAM_STR);
            $stmt->bindValue(':areaCode', $trainingArea->getAreaCode(), PDO::PARAM_STR);
            $stmt->bindValue(':name', $trainingArea->getName(), PDO::PARAM_STR);

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
     * @param $id
     * @param $tra
     * @return bool
     */
    public static function updateById($id, $tra)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'UPDATE dict_training_areas SET  ' . DBUtility::parseUpdateQuery($tra) . '  WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            foreach ($tra as $key => $value) {
                if ($value != null) {
                    $stmt->bindParam(":{$key}", $tra[$key]);
                } else {
                    $stmt->bindParam(":{$key}", $tra[$key], PDO::PARAM_NULL);
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
                'DELETE FROM dict_training_areas WHERE id = :id'
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

    /**
     * @param $id
     * @return null
     */
    public static function getFacultyIdOf($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT facultyId FROM dict_training_areas WHERE id = :id'
            );

            $stmt->bindParam(':id', $id);

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
}
