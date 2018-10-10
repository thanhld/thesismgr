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
require_once 'src/core/model/Attachment.php';
require_once 'src/core/utility/DBUtility.php';

class AttachmentTable
{
    /**
     * @param $attachment Attachment
     * @return bool|int
     */
    public static function addAttachment($attachment)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'INSERT IGNORE INTO attachments(documentId, announcementId, name, url)
                VALUES (:documentId, :announcementId, :name, :url); '
            );

            $stmt->bindValue(':documentId', $attachment->getDocumentId(), PDO::PARAM_INT);
            $stmt->bindValue(':announcementId', $attachment->getAnnouncementId(), PDO::PARAM_INT);
            $stmt->bindValue(':name', $attachment->getName(), PDO::PARAM_STR);
            $stmt->bindValue(':url', $attachment->getUrl(), PDO::PARAM_STR);

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
     * @param $announcementId
     * @return Attachment || null
     */
    public static function getByAnnouncementId($announcementId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT url FROM attachments
                 WHERE announcementId = :announcementId;
            ');

            $stmt->bindValue(':announcementId', $announcementId, PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new Attachment($result);
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
     * @param $announcementId
     * @return null
     */
    public static function deleteByAnnouncementId($announcementId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'DELETE FROM attachments 
                 WHERE announcementId = :announcementId;
            ');

            $stmt->bindValue(':announcementId', $announcementId, PDO::PARAM_INT);

            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return null;
    }

    /**
     * @param $documentId
     * @return Attachment || null
     */
    public static function getByDocumentId($documentId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT url FROM attachments
                 WHERE documentId = :documentId;
            ');

            $stmt->bindValue(':documentId', $documentId, PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new Attachment($result);
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
     * @param $documentId
     * @return null
     */
    public static function deleteByDocumentId($documentId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'DELETE FROM attachments 
                 WHERE documentId = :documentId;
            ');

            $stmt->bindValue(':documentId', $documentId, PDO::PARAM_INT);

            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return null;
    }
}