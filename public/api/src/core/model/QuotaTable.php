<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 4/6/2017
 * Time: 10:14 AM
 */

namespace core\model;

use core\utility\DBUtility;
use PDO;
use PDOException;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/Quota.php';
require_once 'src/core/utility/DBUtility.php';

class QuotaTable
{
    private static $acceptList = array(
        'id' => 'int',
        'degreeId' => 'int',
        'version' => 'string',
        'isActive' => 'string'
    );

    /**
     * @param $option
     * @return array|null
     */
    public static function get($option)
    {
        $db = new PDOData(); $conn = $db->connect();try {
            $stmt = $conn->prepare(
                'SELECT id, degreeId, maxStudent, maxResearcher, maxGraduated, version, isActive,
                        mainFactorStudent, mainFactorResearcher, mainFactorGraduated,
                        coFactorStudent, coFactorResearcher, coFactorGraduated,
                        (SELECT count(*)
                        FROM quotas
                        WHERE ' . DBUtility::parseFilter($option['filter'], self::$acceptList) . '
                        ) AS c
                FROM quotas
                WHERE ' . DBUtility::parseFilter($option['filter'], self::$acceptList) . ' ' .
                "ORDER BY {$option['order']} {$option['direction']};"
            );

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            $ret['count'] = 0;
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new Quota($result);
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
     * @return Quota | null
     */
    public static function getByDegreeId($option) {
        
        $db = new PDOData(); $conn = $db->connect();try {
            $stmt = $conn->prepare(
                'SELECT * FROM quotas
                WHERE degreeId = :degreeId AND isActive = :isActive'
            );

            $stmt->bindParam(':degreeId', $option['degreeId'], PDO::PARAM_INT);
            $stmt->bindParam(':isActive', $option['isActive'], PDO::PARAM_INT);

            $stmt->execute();

            $result = $stmt->fetch();

            if($result) {
                return new Quota($result);
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
     * @param $version
     * @return Quota | null
     */
    public static function getByVersion($version) {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT * FROM quotas
                WHERE version = :version'
            );

            $stmt->bindParam(':version', $version, PDO::PARAM_STR);

            $stmt->execute();

            $result = $stmt->fetch();

            if($result) {
                return new Quota($result);
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
     * @return Quota|null
     */
    public static function getActiveQuotaVersion(){
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT * FROM quotas WHERE isActive = 1'
            );

            $stmt->execute();

            $result = $stmt->fetch();

            if($result) {
                return new Quota($result);
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
     * @param $quota Quota
     * @return bool|int
     */
    public static function addQuota($quota)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'INSERT INTO quotas(degreeId, maxStudent, maxResearcher, maxGraduated, version, isActive,
                  mainFactorStudent, mainFactorResearcher, mainFactorGraduated,
                  coFactorStudent, coFactorResearcher, coFactorGraduated)
                VALUES (:degreeId, :maxStudent, :maxResearcher, :maxGraduated, :version, :isActive,
                  :mainFactorStudent, :mainFactorResearcher, :mainFactorGraduated,
                  :coFactorStudent, :coFactorResearcher, :coFactorGraduated);'
            );

            $stmt->bindValue(':degreeId', $quota->getDegreeID(), PDO::PARAM_INT);
            $stmt->bindValue(':version', $quota->getVersion(), PDO::PARAM_STR);
            $stmt->bindValue(':maxStudent', $quota->getMaxStudent(), PDO::PARAM_INT);
            $stmt->bindValue(':maxResearcher', $quota->getMaxResearcher(), PDO::PARAM_INT);
            $stmt->bindValue(':maxGraduated', $quota->getMaxGraduated(), PDO::PARAM_INT);
            $stmt->bindValue(':isActive', $quota->getIsActive(), PDO::PARAM_INT);
            $stmt->bindValue(':mainFactorStudent', $quota->getMainFactorStudent(), PDO::PARAM_STR);
            $stmt->bindValue(':mainFactorResearcher', $quota->getMainFactorResearcher(), PDO::PARAM_STR);
            $stmt->bindValue(':mainFactorGraduated', $quota->getMainFactorGraduated(), PDO::PARAM_STR);
            $stmt->bindValue(':coFactorStudent', $quota->getCoFactorStudent(), PDO::PARAM_STR);
            $stmt->bindValue(':coFactorResearcher', $quota->getCoFactorResearcher(), PDO::PARAM_STR);
            $stmt->bindValue(':coFactorGraduated', $quota->getCoFactorGraduated(), PDO::PARAM_STR);

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
     * @param $version
     * @param $quota
     * @return bool
     */
    public static function updateQuotaById($version, $quota)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'UPDATE quotas SET ' . DBUtility::parseUpdateQuery($quota) . ' WHERE id = :id AND version = :version'
            );

            $stmt->bindParam(':id', $quota['id'], PDO::PARAM_INT);
            $stmt->bindParam(':version', $version, PDO::PARAM_STR);

            foreach ($quota as $key => $value) {
                if ($value != null) {
                    $stmt->bindParam(":{$key}", $quota[$key]);
                } else {
                    $stmt->bindParam(":{$key}", $quota[$key], PDO::PARAM_NULL);
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
     * @param $version
     * @param $quota
     * @return bool
     */
    public static function updateQuotaByVersion($version, $quota)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'UPDATE quotas SET ' . DBUtility::parseUpdateQuery($quota) . ' WHERE version = :version'
            );

            $stmt->bindParam(':version', $version, PDO::PARAM_STR);

            foreach ($quota as $key => $value) {
                if ($value != null) {
                    $stmt->bindParam(":{$key}", $quota[$key]);
                } else {
                    $stmt->bindParam(":{$key}", $quota[$key], PDO::PARAM_NULL);
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
     *
     * @param $version
     * @param $isActive
     * @return bool
     */
    public static function activeQuotaByVersion($version, $isActive)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'UPDATE quotas SET isActive = :isActive
                 WHERE version = :version;'
            );

            $stmt->bindParam(':version', $version, PDO::PARAM_STR);
            $stmt->bindParam(':isActive', $isActive, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->rowCount() !== 0;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    /**
     *
     * @param $version
     * @return bool
     */
    public static function deActiveOtherQuotaVersion($version)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'UPDATE quotas SET isActive = 0
                 WHERE version <> :version;'
            );

            $stmt->bindParam(':version', $version, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt->rowCount() !== 0;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    /**
     * @param $version
     * @return array|bool
     */
    public static function deleteByVersion($version) {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'DELETE FROM quotas WHERE version = :version AND isActive <> 1'
            );

            $stmt->bindParam(':version', $version, PDO::PARAM_STR);

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
     * @param $version
     * @return array|bool
     */
    public static function checkVersionExisted($version) {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT count(*) AS c FROM quotas
                WHERE version = :version'
            );

            $stmt->bindParam(':version', $version, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();
            return intval($result['c']) !== 0;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }
}