<?php
namespace core\model;

use core\utility\DBUtility;
use PDO;
use PDOException;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/KnowledgeArea.php';
require_once 'src/core/utility/DBUtility.php';

/**
 * Class to interact with knowledge area table
 */
class KnowledgeAreaTable
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
                'SELECT id, name, parentId, facultyId
                FROM dict_knowledge_areas
                WHERE facultyId = :id;'
            );

            $stmt->bindParam(':id', $facultyId, PDO::PARAM_STR);

            $stmt->execute();
            $ret = array();
            $ret['data'] = array();
            //$ret['count'] = 0;
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new KnowledgeArea($result);
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
     * @return array|null
     */
    public static function getById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT id, name, parentId, facultyId FROM dict_knowledge_areas WHERE id = :id;'
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
     * @param $parentId
     * @return array|null
     */
    public static function getChildren($parentId){
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT id, name, parentId, facultyId,
                  (SELECT count(*)
                      FROM dict_knowledge_areas
                      WHERE parentId = :pId) AS c
                  FROM dict_knowledge_areas 
                  WHERE parentId = :pId;'
            );

            $stmt->bindParam(':pId', $parentId, PDO::PARAM_INT);

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            $ret['count'] = 0;
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new KnowledgeArea($result);
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
     * @param $kaId
     * @return null
     */
    public static function getFacultyIdOf($kaId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT facultyId FROM dict_knowledge_areas WHERE id = :id;'
            );

            $stmt->bindParam(':id', $kaId, PDO::PARAM_STR);

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
     * @param $knowledgeArea KnowledgeArea
     * @return bool|int
     */
    public static function addKnowledgeArea($knowledgeArea)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'INSERT IGNORE INTO dict_knowledge_areas(name, parentId, facultyId)
                VALUES (:name, :parentId, :facultyId); '
            );

            $stmt->bindValue(':name', $knowledgeArea->getName(), PDO::PARAM_STR);
            $stmt->bindValue(':parentId', $knowledgeArea->getParentId(), PDO::PARAM_INT);
            $stmt->bindValue(':facultyId', $knowledgeArea->getFacultyId(), PDO::PARAM_STR);

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
                'UPDATE dict_knowledge_areas SET  ' . DBUtility::parseUpdateQuery($fct) . '  WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

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
                'DELETE FROM dict_knowledge_areas WHERE id = :id'
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
     * @param $parentId
     * @return bool|string
     */
    public static function checkParent($id, $parentId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT facultyId AS parentFId, (SELECT facultyId FROM dict_knowledge_areas WHERE id = :id) AS fId
                FROM dict_knowledge_areas WHERE id = :pid'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':pid', $parentId, PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetch();
            if (!$result) {
                return 'Parent not existed';
            } elseif ($result['parentFId'] !== $result['fId']) {
                return 'Parent faculty not match';
            } else {
                return true;
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return 'Not updated';
    }

    /**
     * @param $parentId
     * @param $facultyId
     * @return KnowledgeArea|null
     */
    public static function checkParentExisted($parentId, $facultyId){
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT id, name, parentId, facultyId FROM dict_knowledge_areas 
                WHERE id = :pId AND facultyId = :fId;'
            );

            $stmt->bindParam(':pId', $parentId, PDO::PARAM_INT);
            $stmt->bindParam(':fId', $facultyId, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new KnowledgeArea($result);
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
