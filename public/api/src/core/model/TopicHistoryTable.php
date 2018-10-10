<?php
namespace core\model;

use core\utility\DBUtility;
use core\utility\UUID;
use PDO;
use PDOException;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/TopicHistory.php';
require_once 'src/core/utility/DBUtility.php';
require_once 'src/core/utility/UUID.php';

/**
 * Class to interact with topic table
 */
class TopicHistoryTable
{
    private static $acceptList = array(
        'topicId' => 'string',
        'activityId' => 'int',
        'vietnameseTopicTitle' => 'string',
        'englishTopicTitle' => 'string',
        'isEnglish' => 'int',
        'tags' => 'string',
        'mainSupervisorId' => 'string',
        'outOfficerIds' => 'string',
        'deadlineDate' => 'string',
    );

    /**
     * @param $topic Topic
     * @param $topicChange TopicChange
     * @return bool|int
     */
    public static function create($topic, $topicChange)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'INSERT INTO topics_histories(topicId, vietnameseTopicTitle, englishTopicTitle,
                  isEnglish, description, tags, mainSupervisorId, coSupervisorIds, startPauseDate, pauseDuration, deadlineDate, cancelReason, registerUrl)
                VALUES (:topicId, :vietnameseTopicTitle, :englishTopicTitle,
                  :isEnglish, :description, :tags, :mainSupervisorId, :coSupervisorIds, :startPauseDate, :pauseDuration, :deadlineDate, :cancelReason, :registerUrl); '
            );

            $stmt->bindValue(':topicId', $topic->getId(), PDO::PARAM_STR);
            $stmt->bindValue(':vietnameseTopicTitle', $topic->getVietnameseTopicTitle(), PDO::PARAM_STR);
            $stmt->bindValue(':englishTopicTitle', $topic->getEnglishTopicTitle(), PDO::PARAM_STR);
            $stmt->bindValue(':isEnglish', $topic->getIsEnglish(), PDO::PARAM_INT);
            $stmt->bindValue(':description', $topic->getDescription(), PDO::PARAM_STR);
            $stmt->bindValue(':tags', $topic->getTags(), PDO::PARAM_STR);
            $stmt->bindValue(':mainSupervisorId', $topic->getMainSupervisorId(), PDO::PARAM_STR);
            $stmt->bindValue(':coSupervisorIds', $topic->getCoSupervisorIds(), PDO::PARAM_STR);
            $stmt->bindValue(':deadlineDate', $topic->getDeadlineDate(), PDO::PARAM_STR);
            $stmt->bindValue(':startPauseDate', $topicChange->getStartPauseDate(), PDO::PARAM_STR);
            $stmt->bindValue(':pauseDuration', $topicChange->getPauseDuration(), PDO::PARAM_STR);
            $stmt->bindValue(':cancelReason', $topicChange->getCancelReason(), PDO::PARAM_STR);
            $stmt->bindValue(":registerUrl", $topic->getRegisterUrl(), PDO::PARAM_STR);

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
     * @param $tp
     * @return bool
     */
    public static function updateById($id, $tp)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'UPDATE topics_histories SET ' . DBUtility::parseUpdateQuery($tp) .
                ' WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            foreach ($tp as $key => $value) {
                if ($value != null) {
                    $stmt->bindParam(":{$key}", $tp[$key]);
                } else {
                    $stmt->bindParam(":{$key}", $tp[$key], PDO::PARAM_NULL);
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
     * @param $activityId
     * @return bool
     */
    public static function updateActivityId($id, $activityId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'UPDATE topics_histories SET activityId = :activityId WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':activityId', $activityId, PDO::PARAM_INT);

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
                'DELETE FROM topics_histories WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->rowCount() != 0;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }
}
