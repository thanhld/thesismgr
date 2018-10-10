<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2/27/2017
 * Time: 08:33 PM
 */

namespace core\model;

use PDOException;
use PDO;
use core\utility\DBUtility;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/Document.php';
require_once 'src/core/utility/DBUtility.php';


class DocumentTable
{
    private static $acceptList = array(
        'learnerId' => 'string',
        'departmentId' => 'string',
        'topicStatus' => 'int',
        'topicType' => 'int',
        'vietnameseTopicTitle' => 'string',
        'englishTopicTitle' => 'string',
        'isEnglish' => 'int',
        'tags' => 'string',
        'expertiseOfficerIds' => 'string',
        'mainSupervisorId' => 'string',
        'requestedSupervisorId' => 'string',
        'outOfficerIds' => 'string',
        'startDate' => 'string',
        'defaultDeadlineDate' => 'string',
        'deadlineDate' => 'string',
    );

    /**
     * @param $facultyId
     * @return array|null
     */
    public static function adminGet($facultyId)
    {
        $db = new PDOData(); $conn = $db->connect();try {
            
            $stmt = $conn->prepare(
                'SELECT DISTINCT(dc.id), dc.facultyId, dc.documentCode, dc.createdDate, 
                    attm.id AS attachmentId, attm.name AS attachmentName, attm.url
                 FROM documents dc
                 LEFT JOIN attachments attm ON dc.id = attm.documentId
                 INNER JOIN activities act ON act.documentId = dc.id
                 INNER JOIN activities_topics ac_tc ON ac_tc.activitiesId = act.id
                 INNER JOIN topics t ON ac_tc.topicId = t.id
                 WHERE dc.facultyId = :id AND t.topicStatus NOT IN (0,2,3);'
            );

            $stmt->bindParam(':id', $facultyId, PDO::PARAM_STR);

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new Document($result);
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
    public static function getTopics($option, $documentId, $facultyId) {
        $db = new PDOData(); $conn = $db->connect();
        try {
            if($option['limit'] != 0 && $option['offset'] != -1) {
                
                $limitQuery = 'LIMIT :limit OFFSET :offset;';
            } else {
                $limitQuery = ';';
            }

            $stmt = $conn->prepare(
                'SELECT t.id, t.learnerId, l.fullname, l.learnerCode, l.trainingCourseId,
                        t.departmentId, t.topicStatus, t.topicType, t.vietnameseTopicTitle, t.englishTopicTitle,
                        t.isEnglish, t.description, t.tags, t.referenceUrl, t.registerUrl, t.expertiseOfficerIds,
                        t.mainSupervisorId, t.requestedSupervisorId, t.coSupervisorIds, t.outOfficerIds, 
                        t.startDate, t.defaultDeadlineDate, t.deadlineDate,
                        (SELECT count(*)
                        FROM topics t
                        INNER JOIN departments d ON t.departmentId = d.id
                        INNER JOIN learners l ON t.learnerId = l.id
                        INNER JOIN activities_topics avt ON t.id = avt.topicId
                        INNER JOIN activities av ON av.id = avt.activitiesId
                        WHERE d.facultyId = :fId AND av.documentId = :documentId AND ' . DBUtility::parseFilter($option['filter'], self::$acceptList) . '
                        ) AS c
                FROM topics t
                INNER JOIN departments d ON t.departmentId = d.id
                INNER JOIN learners l ON t.learnerId = l.id
                INNER JOIN activities_topics avt ON t.id = avt.topicId
                INNER JOIN activities av ON av.id = avt.activitiesId
                WHERE d.facultyId = :fId AND av.documentId = :documentId AND ' . DBUtility::parseFilter($option['filter'], self::$acceptList) . ' ' .
                "ORDER BY {$option['order']} {$option['direction']} " . $limitQuery
            );

            $stmt->bindParam(':fId', $facultyId, PDO::PARAM_STR);
            $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);

            if($option['limit'] != 0 && $option['offset'] != -1){
                $stmt->bindParam(':limit', $option['limit'], PDO::PARAM_INT);
                $stmt->bindParam(':offset', $option['offset'], PDO::PARAM_INT);
            }

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            $ret['count'] = 0;
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new Topic($result);
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
     * @param $document Document
     * @return bool|int
     */
    public static function addDocument($document)
    {
        $db = new PDOData(); $conn = $db->connect();try {
            
            $stmt = $conn->prepare(
                'INSERT IGNORE INTO documents(facultyId, documentCode, createdDate)
                VALUES (:facultyId, :documentCode, :createdDate); '
            );

            $stmt->bindValue(':facultyId', $document->getFacultyId(), PDO::PARAM_STR);
            $stmt->bindValue(':documentCode', $document->getDocumentCode(), PDO::PARAM_STR);
            $stmt->bindValue(':createdDate', $document->getCreatedDate(), PDO::PARAM_STR);

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
     * @param $doc
     * @return bool
     */
    public static function updateById($id, $doc)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'UPDATE documents SET ' . DBUtility::parseUpdateQuery($doc) . ' WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            foreach ($doc as $key => $value) {
                if ($value != null) {
                    $stmt->bindParam(":{$key}", $doc[$key]);
                } else {
                    $stmt->bindParam(":{$key}", $doc[$key], PDO::PARAM_NULL);
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
                'DELETE FROM documents WHERE id = :id;'
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
     * @return string|null
     */
    public static function getFacultyIdOf($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT facultyId FROM documents WHERE id = :id;'
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
}