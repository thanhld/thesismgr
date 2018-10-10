<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 3/13/2017
 * Time: 09:38 PM
 */

namespace core\model;

use core\utility\DBUtility;
use core\utility\UUID;
use PDO;
use PDOException;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/TopicChange.php';
require_once 'src/core/utility/DBUtility.php';
require_once 'src/core/utility/UUID.php';


class TopicChangeTable
{
    /**
     * @param $tpc TopicChange
     * @return int|string
     */
    public static function insert($topicId, $tpc)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'INSERT INTO topics_changes(id, vietnameseTopicTitle, englishTopicTitle, isEnglish,
                  description, tags, mainSupervisorId, coSupervisorIds, requestedSupervisorId, 
                  startPauseDate, pauseDuration, delayDuration, cancelReason, registerUrl) 
                VALUES (:id, :vietnameseTopicTitle, :englishTopicTitle, :isEnglish,
                  :description, :tags, :mainSupervisorId, :coSupervisorIds, :requestedSupervisorId,
                  :startPauseDate, :pauseDuration, :delayDuration, :cancelReason, :registerUrl); '
            );

            $stmt->bindParam(":id", $topicId, PDO::PARAM_STR);
            $stmt->bindValue(":vietnameseTopicTitle", $tpc->getVietnameseTopicTitle(), PDO::PARAM_STR);
            $stmt->bindValue(":englishTopicTitle", $tpc->getEnglishTopicTitle(), PDO::PARAM_STR);
            $stmt->bindValue(":isEnglish", $tpc->getIsEnglish(), PDO::PARAM_INT);
            $stmt->bindParam(":description", $tpc->getDescription(), PDO::PARAM_STR);
            $stmt->bindValue(":tags",  $tpc->getTags(), PDO::PARAM_STR);
            $stmt->bindValue(":mainSupervisorId",  $tpc->getMainSupervisorId(), PDO::PARAM_STR);
            $stmt->bindParam(":coSupervisorIds",  $tpc->getCoSupervisorIds(), PDO::PARAM_STR);
            $stmt->bindValue(":requestedSupervisorId",  $tpc->getRequestedSupervisorId(), PDO::PARAM_STR);
            $stmt->bindValue(":startPauseDate",  $tpc->getStartPauseDate(), PDO::PARAM_STR);
            $stmt->bindValue(":pauseDuration",  $tpc->getPauseDuration(), PDO::PARAM_INT);
            $stmt->bindParam(":delayDuration",  $tpc->getDelayDuration(), PDO::PARAM_INT);
            $stmt->bindValue(":cancelReason",  $tpc->getCancelReason(), PDO::PARAM_STR);
            $stmt->bindValue(":registerUrl", $tpc->getRegisterUrl(), PDO::PARAM_STR);

            if ($stmt->execute() && $stmt->rowCount() != 0) {
                return $topicId;
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
     * @return TopicChange|null
     */
    public static function getById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT tc.*, t.outOfficerIds
                 FROM topics_changes tc 
                 INNER JOIN topics t ON tc.id = t.id
                 WHERE tc.id = :id;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new TopicChange($result);
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
     * @param $tp
     * @return bool
     */
    public static function updateById($id, $tp)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'UPDATE topics_changes SET ' . DBUtility::parseUpdateQuery($tp) . ' WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

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
     * @return array|bool
     */
    public static function deleteById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'DELETE FROM topics_changes WHERE id = :id'
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
}