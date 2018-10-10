<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 3/11/2017
 * Time: 11:20 AM
 */

namespace core\model;

use PDOException;
use PDO;
use core\utility\DBUtility;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/OutOfficer.php';
require_once 'src/core/utility/DBUtility.php';


class OutOfficerTable
{
    /**
     * @return array|null
     */
    public static function get() {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT id, fullname, degreeId, departmentName 
                  FROM out_officers;'
            );

            $stmt->execute();
            $ret = array();
            $ret['data'] = array();
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new OutOfficer($result);
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
     * @return array|null
     */
    public static function getById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT id, fullname, degreeId, departmentName 
                  FROM out_officers WHERE id = :id;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

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
     * @param $outOfficer OutOfficer
     * @return bool|int
     */
    public static function addOutOfficer($outOfficer) {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'INSERT INTO out_officers(fullname, degreeId, departmentName)
                VALUES (:fullname, :degreeId, :departmentName);'
            );

            $stmt->bindValue(':fullname', $outOfficer->getFullName(), PDO::PARAM_STR);
            $stmt->bindValue(':degreeId', $outOfficer->getDegreeId(), PDO::PARAM_INT);
            $stmt->bindValue(':departmentName', $outOfficer->getDepartmentName(), PDO::PARAM_STR);

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
     * @return array|bool
     */
    public static function deleteById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'DELETE FROM out_officers WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt->rowCount() != 0;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    /**
     * @param $oofc
     * @return bool
     */
    public static function checkOutOfficerExisted($oofc)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT * FROM out_officers oo
                  WHERE LOWER(oo.fullname) = :fullname AND LOWER(oo.departmentName) = :departmentName;'
            );

            $stmt->bindParam(':fullname', mb_strtolower($oofc['fullname']), PDO::PARAM_STR);
            $stmt->bindParam(':departmentName', mb_strtolower($oofc['departmentName']), PDO::PARAM_STR);

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new OutOfficer($result);
                break;
            }

            if(count($ret['data']) != 0){
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return null;
    }
}