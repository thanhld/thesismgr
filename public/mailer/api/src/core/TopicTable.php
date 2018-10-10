<?php
namespace core;

use PDO;
use PDOException;

require_once './../../api/src/core/model/PDOData.php';

/**
 * Class to interact with mail table
 */
class TopicTable
{
    /**
     * @param $topicId
     * @return array|null
     */
    public static function getById($topicId) {
        $db = new model\PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT t.vietnameseTopicTitle, t.englishTopicTitle, l.fullname AS learnerName FROM topics t
                INNER JOIN learners l ON t.learnerId = l.id
                WHERE t.id = :topicId;'
            );

            $stmt->bindParam(':topicId', $topicId, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return $result;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $db->disconnect();
        return null;
    }
}