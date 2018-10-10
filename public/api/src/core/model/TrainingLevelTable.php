<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2/16/2017
 * Time: 04:26 PM
 */

namespace core\model;

use PDO;
use PDOException;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/TrainingLevel.php';
require_once 'src/core/model/PDOData.php';


class TrainingLevelTable
{
    /**
     *
     * @return array|null
     */
    public static function get()
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT id, name, levelType FROM dict_training_levels;'
            );

            $stmt->execute();
            $ret = array();
            $ret['data'] = array();
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new TrainingLevel($result);
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
     * @return TrainingLevel|null
     */
    public static function getById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT id, name, levelType FROM dict_training_levels WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new TrainingLevel($result);
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
     * @param $trainingLevel TrainingLevel
     */
    public static function addTrainingLevel($trainingLevel)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'INSERT IGNORE INTO dict_training_levels(name, levelType) VALUES (:name, :levelType); '
            );

            $stmt->bindValue(':name', $trainingLevel->getName(), PDO::PARAM_STR);
            $stmt->bindValue(':levelType', $trainingLevel->getLevelType(), PDO::PARAM_INT);

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
     * @param $trainingLevel TrainingLevel
     * @return bool
     */
    public static function updateById($id, $trainingLevel)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'UPDATE dict_training_levels SET name = :name, levelType = :levelType WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $trainingLevel->getName(), PDO::PARAM_STR);
            $stmt->bindValue(':levelType', $trainingLevel->getLevelType(), PDO::PARAM_INT);

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
                'DELETE FROM dict_training_levels WHERE id = :id'
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