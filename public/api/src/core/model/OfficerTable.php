<?php
namespace core\model;

use PDOException;
use core\utility\DBUtility;
use PDO;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/Officer.php';
require_once 'src/core/utility/DBUtility.php';
require_once 'src/core/model/PDOData.php';

/**
 * Class to interact with officer table
 */
class OfficerTable
{
    private static $publicAcceptList = array(
        'officerCode' => 'string',
        'departmentId' => 'string',
        'facultyId' => 'string',
        'fullname' => 'string',
        'degreeId' => 'int',
        'website' => 'string',
        'address' => 'string',
        'numberOfStudent' => 'int',
        'numberOfResearcher' => 'int',
        'numberOfGraduated' => 'int',
        'role' => 'int',
    );

    /**
     * @param $officer Officer
     * @return bool
     */
    public static function insert($officer)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'INSERT INTO officers(id, officerCode, departmentId, fullname, degreeId)
                VALUES (:id, :officerCode, :departmentId, :fullname, :degreeId);'
            );

            $stmt->bindValue(':id', $officer->getId(), PDO::PARAM_STR);
            $stmt->bindValue(':officerCode', $officer->getOfficerCode(), PDO::PARAM_STR);
            $stmt->bindValue(':departmentId', $officer->getDepartmentId(), PDO::PARAM_STR);
            $stmt->bindValue(':fullname', $officer->getFullname(), PDO::PARAM_STR);
            $stmt->bindValue(':degreeId', $officer->getDegreeId(), PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $conn->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return null;
    }

    /**
     * @param $officer Officer
     * @return bool
     */
    public static function backUpOfficer($officer)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'INSERT INTO officers(id, officerCode, departmentId, fullname, degreeId,
                        otherEmail, avatarUrl, phone, website, description, address,
                        numberOfStudent, numberOfGraduated, numberOfResearcher)
                VALUES (:id, :officerCode, :departmentId, :fullname, :degreeId,
                        :otherEmail, :avatarUrl, :phone, :website, :description, :address,
                        :numberOfStudent, :numberOfGraduated, :numberOfResearcher);'
            );

            $stmt->bindValue(':id', $officer->getId(), PDO::PARAM_STR);
            $stmt->bindValue(':officerCode', $officer->getOfficerCode(), PDO::PARAM_STR);
            $stmt->bindValue(':departmentId', $officer->getDepartmentId(), PDO::PARAM_STR);
            $stmt->bindValue(':fullname', $officer->getFullname(), PDO::PARAM_STR);
            $stmt->bindValue(':degreeId', $officer->getDegreeId(), PDO::PARAM_INT);
            $stmt->bindValue(':otherEmail', $officer->getOtherEmail(), PDO::PARAM_STR);
            $stmt->bindValue(':avatarUrl', $officer->getAvatarUrl(), PDO::PARAM_STR);
            $stmt->bindValue(':phone', $officer->getPhone(), PDO::PARAM_STR);
            $stmt->bindValue(':website', $officer->getWebsite(), PDO::PARAM_STR);
            $stmt->bindValue(':description', $officer->getDescription(), PDO::PARAM_STR);
            $stmt->bindValue(':address', $officer->getAddress(), PDO::PARAM_STR);
            $stmt->bindValue(':numberOfStudent', $officer->getNumberOfStudent(), PDO::PARAM_INT);
            $stmt->bindValue(':numberOfGraduated', $officer->getNumberOfGraduated(), PDO::PARAM_INT);
            $stmt->bindValue(':numberOfResearcher', $officer->getNumberOfResearcher(), PDO::PARAM_INT);

            if ($stmt->execute()) {
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


    /**
     * @param $option
     * @return array|null
     */
    public static function get($option)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT o.id, a.role, o.departmentId, d.name as departmentName, d.type as departmentType, d.facultyId, o.degreeId, o.fullname, o.numberOfStudent,
                        o.numberOfResearcher, o.numberOfGraduated,
                        (SELECT count(*)
                          FROM officers o
                          INNER JOIN accounts a ON o.id = a.uid
                          INNER JOIN departments d ON o.departmentId = d.id
                          WHERE ' . DBUtility::parseFilter($option['filter'], self::$publicAcceptList) . '
                         ) AS c
                FROM officers o
                INNER JOIN accounts a ON o.id = a.uid
                INNER JOIN departments d ON o.departmentId = d.id
                WHERE ' . DBUtility::parseFilter($option['filter'], self::$publicAcceptList) . ' ' .
                "ORDER BY {$option['order']} {$option['direction']} ;"
            );

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            $ret['count'] = 0;
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new Officer($result);
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
     * @param $option
     * @param $facultyId
     * @return array|null
     */
    public static function adminGet($option, $facultyId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT o.id, a.username, a.vnuMail, a.role, o.officerCode, o.departmentId,
                        o.degreeId, o.fullname, o.website, o.address,
                        (SELECT count(*)
                          FROM officers o
                          INNER JOIN accounts a ON o.id = a.uid
                          INNER JOIN departments d ON o.departmentId = d.id
                          WHERE d.facultyId = :fid AND ' . DBUtility::parseFilter($option['filter'], self::$publicAcceptList) . '
                         ) AS c
                FROM officers o
                INNER JOIN accounts a ON o.id = a.uid
                INNER JOIN departments d ON o.departmentId = d.id
                WHERE d.facultyId = :fid AND ' . DBUtility::parseFilter($option['filter'], self::$publicAcceptList) . ' ' .
                "ORDER BY {$option['order']} {$option['direction']} ;"
            );

            $stmt->bindParam(':fid', $facultyId, PDO::PARAM_STR);

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            $ret['count'] = 0;
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new Officer($result);
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
     * @return Officer | null
     */
    public static function getById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT o.id, a.vnuMail, o.officerCode, o.departmentId,
                        o.degreeId, o.fullname, o.otherEmail, o.avatarUrl, o.phone,
                        o.website, o.address, o.description, o.numberOfStudent,
                        o.numberOfResearcher, o.numberOfGraduated
                FROM officers o
                INNER JOIN accounts a ON o.id = a.uid
                INNER JOIN departments d ON o.departmentId = d.id
                WHERE o.id = :id;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new Officer($result);
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
                'SELECT o.id, a.username, a.vnuMail, a.role, o.officerCode, o.departmentId,
                        o.fullname, o.degreeId, o.website, o.address
                FROM officers o
                INNER JOIN accounts a ON o.id = a.uid
                WHERE o.id = :id;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new Officer($result);
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
    public static function getFacultyIdOf($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT departments.facultyId AS facultyId FROM officers
                INNER JOIN departments ON officers.departmentId = departments.id
                WHERE officers.id = :id'
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
     * @param $officerId
     * @return null
     */
    public static function getDepartmentIdOf($officerId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT departmentId FROM officers WHERE id = :id'
            );

            $stmt->bindParam(':id', $officerId);

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
     * @param $ofc
     * @return bool
     */
    public static function updateById($id, $ofc)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'UPDATE officers SET ' . DBUtility::parseUpdateQuery($ofc) . ' WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            foreach ($ofc as $key => $value) {
                if ($value != null) {
                    $stmt->bindParam(":{$key}", $ofc[$key]);
                } else {
                    $stmt->bindParam(":{$key}", $ofc[$key], PDO::PARAM_NULL);
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
                'DELETE FROM officers WHERE id = :id;'
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
