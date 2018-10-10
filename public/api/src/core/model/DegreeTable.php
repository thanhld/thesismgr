<?php
namespace core\model;

use PDOException;
use PDO;
use core\utility\DBUtility;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/Degree.php';
require_once 'src/core/utility/DBUtility.php';

/**
 * Class to interact with degree table
 */
class DegreeTable
{
    /**
     * @return array|null
     */
    public static function get() {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare('SELECT id, name FROM dict_degrees;');

            $stmt->execute();
            $ret = array();
            $ret['data'] = array();
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new Degree($result);
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
     * @return Degree|null
     */
    public static function getById($id) {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT * FROM dict_degrees WHERE id = :id;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            $result = $stmt->fetch();

            if($result) {
                return new Degree($result);
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
     * @param $degree
     * @return bool|int
     */
    public static function addDegree($degree) {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'INSERT INTO dict_degrees(name)
                VALUES (:name);'
            );

            $stmt->bindValue(':name', $degree->getName(), PDO::PARAM_STR);

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
     * @param $fct
     * @return bool
     */
    public static function updateById($id, $fct)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'UPDATE dict_degrees SET ' . DBUtility::parseUpdateQuery($fct) . ' WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            foreach ($fct as $key => $value) {
                if ($value != null) {
                    $stmt->bindParam(":{$key}", $fct[$key]);
                } else {
                    $stmt->bindParam(":{$key}", $fct[$key], PDO::PARAM_NULL);
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
                'DELETE FROM dict_degrees WHERE id = :id'
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

    /**
     * @param int $id
     * @return bool|mixed
     */
    public static function checkDegree($id = -1)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare('SELECT 1 FROM dict_degrees WHERE id= :id;');

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch();
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }
}
