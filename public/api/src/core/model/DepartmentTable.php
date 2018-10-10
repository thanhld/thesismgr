<?php
namespace core\model;

use core\utility\DBUtility;
use core\utility\UUID;
use PDO;
use PDOException;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/Department.php';
require_once 'src/core/utility/DBUtility.php';
require_once 'src/core/utility/UUID.php';

/**
 * Class to interact with departments table
 */
class DepartmentTable
{
    private static $acceptList = array(
        'id' => 'string',
        'facultyId' => 'string',
        'name' => 'string',
        'type' => 'int',
        'address' => 'string',
        'phone' => 'string',
        'website' => 'string'
    );

    /**
     *
     * @param $option
     * @return array|null
     */
    public static function get($option)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT d.id, d.name, d.facultyId, f.name AS facultyName, d.type, d.address, d.phone, d.website,
                    (SELECT count(*) FROM departments d
                    INNER JOIN faculties f ON d.facultyId = f.id
                    WHERE ' . DBUtility::parseFilter($option['filter'], self::$acceptList) . '
                    ) AS c
                FROM departments d
                INNER JOIN faculties f ON d.facultyId = f.id
                WHERE ' . DBUtility::parseFilter($option['filter'], self::$acceptList) . ' ' .
                "ORDER BY {$option['order']} {$option['direction']} ;"
            );

            $stmt->execute();
            $ret = array();
            $ret['data'] = array();
            $ret['count'] = 0;
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new Department($result);
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
     * @return Department
     */
    public static function getById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT d.id, d.name, d.facultyId, f.name AS facultyName, d.type, d.address, d.phone, d.website
                FROM departments d
                INNER JOIN faculties f ON d.facultyId = f.id
                WHERE d.id = :id;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new Department($result);
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
     * @param $departmentId
     * @return null
     */
    public static function getFacultyIdOf($departmentId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT facultyId FROM departments WHERE id = :id'
            );

            $stmt->bindParam(':id', $departmentId);

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
     * @param $department Department
     * @return bool|int
     */
    public static function addDepartment($department)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $id = UUID::v4();

            $stmt = $conn->prepare(
                'INSERT INTO departments(id, name, facultyId, type, address, phone, website)
                VALUES (:id, :name, :facultyId, :type, :address, :phone, :website); '
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->bindValue(':name', $department->getName(), PDO::PARAM_STR);
            $stmt->bindValue(':facultyId', $department->getFacultyId(), PDO::PARAM_STR);
            $stmt->bindValue(':type', $department->getType(), PDO::PARAM_INT);
            $stmt->bindValue(':address', $department->getAddress(), PDO::PARAM_STR);
            $stmt->bindValue(':phone', $department->getPhone(), PDO::PARAM_STR);
            $stmt->bindValue(':website', $department->getWebsite(), PDO::PARAM_STR);

            if ($stmt->execute() && $stmt->rowCount() != 0) {
                return $id;
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
                'UPDATE departments SET ' . DBUtility::parseUpdateQuery($fct) . ' WHERE id = :id'
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
                'DELETE FROM departments WHERE id = :id AND type <> 4'
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
     * @param $name
     * @param $facultyId
     * @return bool
     */
    public static function getDepartmentIdByName($name, $facultyId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT id FROM departments d WHERE facultyId = :fid AND LOWER(d.name) = :name;'
            );

            $stmt->bindParam(':name', mb_strtolower($name), PDO::PARAM_STR);
            $stmt->bindParam(':fid', $facultyId, PDO::PARAM_STR);

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new Department($result);
                break;
            }

            if(count($ret['data']) != 0){
                return $ret['data'][0]->getId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }
}
