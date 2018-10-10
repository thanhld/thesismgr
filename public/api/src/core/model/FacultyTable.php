<?php
namespace core\model;

use core\utility\DBUtility;
use core\utility\Paging;
use PDO;
use PDOException;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/Faculty.php';
require_once 'src/core/utility/Paging.php';
require_once 'src/core/utility/DBUtility.php';

/**
 * Class to interact with faculties table
 */
class FacultyTable
{
    private static $acceptList = array(
        'id' => 'string',
        'name' => 'string',
        'shortName' => 'string',
        'phone' => 'string',
        'website' => 'string',
        'address' => 'string',
    );

    /**
     * @param $faculty Faculty
     * @return int|string
     */
    public static function insert(&$faculty)
    {

        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'INSERT INTO faculties(id, name, shortName, phone, website, address)
                VALUES (:id, :name, :shortName, :phone, :website, :address);'
            );

            $stmt->bindValue(':id', $faculty->getId(), PDO::PARAM_STR);
            $stmt->bindValue(':name', $faculty->getName(), PDO::PARAM_STR);
            $stmt->bindValue(':shortName', $faculty->getShortName(), PDO::PARAM_STR);
            $stmt->bindValue(':phone', $faculty->getPhone(), PDO::PARAM_STR);
            $stmt->bindValue(':website', $faculty->getWebsite(), PDO::PARAM_STR);
            $stmt->bindValue(':address', $faculty->getAddress(), PDO::PARAM_STR);

            if ($stmt->execute()) {
                return $conn->lastInsertId();
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return -1;
    }

    /**
     * @param $id
     * @return Faculty|null
     */
    public static function getById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT f.id, a.username, a. vnuMail, f.name,
                        f.shortName, f.phone, f.website, f.address
                FROM faculties f
                INNER JOIN accounts a ON f.id = a.uid
                WHERE id = :id;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new Faculty($result);
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
                'DELETE FROM faculties WHERE id = :id;'
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
     *
     * @return array
     */
    public static function get()
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT f.id, f.name, a.username, a.vnuMail, f.shortName, f.phone, f.website, f.address
                FROM faculties f
                INNER JOIN accounts a ON f.id = a.uid'
            );

            $stmt->execute();
            $ret = array();
            $ret['data'] = array();
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new Faculty($result);
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
     * @param $fct
     * @return bool
     */
    public static function updateById($id, $fct)
    {
        $db = new PDOData(); $conn = $db->connect()
        ;try {
            $stmt = $conn->prepare(
                'UPDATE faculties SET ' . DBUtility::parseUpdateQuery($fct) . ' WHERE id = :id'
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
     * @param $facultyId
     * @return string | bool
     */
    public static function getFacultyOfficeId($facultyId)
    {
        $type = 4;
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT d.id 
                 FROM departments d 
                 INNER JOIN faculties f ON d.facultyId = f.id
                 WHERE f.id = :fId AND d.type = :type'
            );

            $stmt->bindParam(':fId', $facultyId, PDO::PARAM_STR);
            $stmt->bindParam(':type', $type, PDO::PARAM_INT);

            $stmt->execute();
            $retId = null;
            while ($result = $stmt->fetch()) {
                $retId = $result[0];

                if(is_string($retId) && strlen($retId) == 32) {
                    break;
                } else {
                    $retId = null;
                }
            }

            return $retId;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }
}
