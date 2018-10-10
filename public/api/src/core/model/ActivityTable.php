<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2/27/2017
 * Time: 08:39 PM
 */

namespace core\model;

use PDOException;
use PDO;
use core\utility\DBUtility;
use core\utility\Constant;
use core\model\Review;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/Activity.php';
require_once 'src/core/model/Review.php';
require_once 'src/core/utility/DBUtility.php';

class ActivityTable
{

    /**
     * @param $id
     * @return array|null
     */
    public static function getActivities($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT avt.id, av.documentId, av.accountId, av.stepId, av.requestedSupervisorId, av.created,
                        dc.facultyId, dc.documentCode, dc.createdDate,
                        attm.id AS attachmentId, attm.name AS attachmentName, attm.url,
                        th.id AS thId, th.topicId, th.activityId, th.vietnameseTopicTitle, th.englishTopicTitle,
                        th.isEnglish, th.description, th.tags, th.mainSupervisorId, th.coSupervisorIds, th.deadlineDate, th.cancelReason,
                        th.registerUrl
                 FROM activities_topics avt
                 LEFT JOIN activities av ON av.id = avt.activitiesId
                 LEFT JOIN topics_histories th ON th.activityId = av.id AND th.topicId = avt.topicId 
                 LEFT JOIN documents dc ON av.documentId = dc.id
                 LEFT JOIN attachments attm ON dc.id = attm.documentId
                 WHERE avt.topicId = :id
                 ORDER BY av.created ASC;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            while ($result = $stmt->fetch()){
                $ret['data'][] = new Activity($result);
            }

            return $ret;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return null;
    }

    /**
     * @param $stepId
     * @param $review Review
     * @return array|null
     */
    public static function getDepartmentApprovedActivity($stepId, $review) {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                "SELECT * FROM activities a 
                    INNER JOIN activities_topics avt ON a.id = avt.activitiesId
                    INNER JOIN reviews r ON avt.topicId = r.topicId
                WHERE a.stepId = :stepId AND avt.topicId = :topicId 
                    AND r.topicStatus = :topicStatus AND r.officerId = :officerId;"
            );

            $stmt->bindParam(':stepId', $stepId, PDO::PARAM_INT);
            $stmt->bindParam(':topicId', $review->getTopicId(), PDO::PARAM_STR);
            $stmt->bindParam(':topicStatus', $review->getTopicStatus(), PDO::PARAM_INT);
            $stmt->bindParam(':officerId', $review->getOfficerId(), PDO::PARAM_STR);

            $stmt->execute();
            $ret = array();
            while ($result = $stmt->fetch()){
                $ret[] = new Activity($result);
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
    public static function getDocumentIdOf($id){
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                "SELECT act.documentId FROM activities act WHERE act.id = :id;"
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();

            $ret = array();
            while ($result = $stmt->fetch()) {
                $ret[] = $result['documentId'];
            }

            return $ret;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return null;
    }

    /**
     * @param $activity Activity
     * @return bool|int
     */
    public static function createActivity($activity)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'INSERT INTO activities(documentId, accountId, stepId, requestedSupervisorId)
                VALUES (:documentId, :accountId, :stepId, :requestedSupervisorId);'
            );

            $stmt->bindValue(':documentId', $activity->getDocumentId(), PDO::PARAM_INT);
            $stmt->bindValue(':accountId', $activity->getAccountId(), PDO::PARAM_STR);
            $stmt->bindValue(':stepId', $activity->getStepId(), PDO::PARAM_INT);
            $stmt->bindValue(':requestedSupervisorId', $activity->getRequestedSupervisorId(), PDO::PARAM_STR);

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
     * @param $activityId
     * @param $topicId
     * @return bool|int
     */
    public static function updateActivityTopic($activityId, $topicId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'INSERT INTO activities_topics(topicId, activitiesId)
                VALUES (:topicId, :activityId);'
            );

            $stmt->bindValue(':activityId', $activityId, PDO::PARAM_INT);
            $stmt->bindValue(':topicId', $topicId, PDO::PARAM_STR);

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
                'DELETE FROM activities WHERE id = :id'
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