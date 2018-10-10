<?php
namespace core\model;

use PDO;
use PDOException;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'PDOData.php';
require_once 'src/core/model/Mail.php';

/**
 * Class to interact with mail table
 */
class MailTable
{
    /**
     * @param $mail Mail
     * @return array|null
     */
    public static function insert($mail) {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'INSERT INTO dict_mails(id, receiverId, topicId, status, type)' .
                ' VALUES (:id, :receiverId, :topicId, :status, :type);'
            );

            $stmt->bindValue(':id', $mail->getId(), PDO::PARAM_INT);
            $stmt->bindValue(':receiverId', $mail->getReceiverId(), PDO::PARAM_STR);
            $stmt->bindValue(':topicId', $mail->getTopicId(), PDO::PARAM_STR);
            $stmt->bindValue(':status', $mail->getStatus(), PDO::PARAM_INT);
            $stmt->bindValue(':type', $mail->getType(), PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $conn->lastInsertId();
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