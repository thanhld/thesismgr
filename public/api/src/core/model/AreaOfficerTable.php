<?php
namespace core\model;


use PDO;
use PDOException;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/Officer.php';
require_once 'src/core/model/KnowledgeArea.php';
require_once 'src/core/utility/DBUtility.php';

class AreaOfficerTable
{
    private static $acceptList = array(
        'id' => 'int',
        'knowledgeAreaId' => 'int',
        'officerId' => 'string'
    );

    /**
     * @param $id
     * @return array|null
     */
    public static function getOfficerAreas($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT dka.*
                 FROM areas_officers ao
                 INNER JOIN dict_knowledge_areas dka ON ao.knowledgeAreaId = dka.id
                 WHERE ao.officerId = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            while ($result = $stmt->fetch()){
                $ret['data'][] = new KnowledgeArea($result);
            }

            return $ret;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return null;
    }

    /**
     * @param $officerId
     * @param $areaId
     * @return bool
     */
    public static function updateOfficerAreas($officerId, $areaId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'INSERT IGNORE INTO areas_officers(officerId, knowledgeAreaId)
                VALUES (:officerId, :knowledgeAreaId); '
            );

            $stmt->bindParam(':officerId', $officerId, PDO::PARAM_STR);
            $stmt->bindParam(':knowledgeAreaId', $areaId, PDO::PARAM_INT);

            if ($stmt->execute() && $stmt->rowCount() != 0) {
                return true;
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
     * @param $officerId
     * @param $areaId
     * @return bool
     */
    public static function deleteByOfficerIdAndAreaId($officerId, $areaId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'DELETE FROM areas_officers
                WHERE officerId = :officerId AND knowledgeAreaId = :knowledgeAreaId'
            );

            $stmt->bindParam(':officerId', $officerId, PDO::PARAM_STR);
            $stmt->bindParam(':knowledgeAreaId', $areaId, PDO::PARAM_INT);

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
     * @return array|null
     */
    public static function getAreaOfficers($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT DISTINCT(o.id), o.fullname, o.degreeId, o.departmentId
                 FROM areas_officers ao
                 INNER JOIN officers o ON ao.officerId = o.id
                 INNER JOIN accounts a ON o.id = a.uid
                 WHERE (ao.knowledgeAreaId = :id 
                        OR ao.knowledgeAreaId IN (
                            SELECT id FROM dict_knowledge_areas dka WHERE dka.parentId = :id
                        )
                    ) AND a.role <> 4'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            while ($result = $stmt->fetch()){
                $ret['data'][] = new Officer($result);
            }

            return $ret;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return null;
    }


}