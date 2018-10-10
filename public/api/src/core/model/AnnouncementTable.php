<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2/27/2017
 * Time: 08:32 PM
 */

namespace core\model;

use common\controller\AnnouncementController;
use core\utility\DBUtility;
use PDO;
use PDOException;
use DateTime;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/Announcement.php';
require_once 'src/core/utility/DBUtility.php';

class AnnouncementTable
{
    /**
     * @param $facultyId
     * @return array|null
     */
    public static function getByFacultyId($facultyId)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $today = date('Y-m-d H:i:s');

        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT an.*, attm.id AS attachmentId, attm.name AS attachmentName, attm.url
                 FROM announcements an
                 LEFT JOIN attachments attm ON an.id = attm.announcementId
                 WHERE an.facultyId = :id AND an.showDate <= :today AND an.hideDate >= :today ' .
                'ORDER BY an.showDate ASC;'
            );

            $stmt->bindParam(':id', $facultyId, PDO::PARAM_STR);
            $stmt->bindParam(':today', $today, PDO::PARAM_STR);

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new Announcement($result);
            }

            return $ret;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return null;
    }

    /**
     * @return array|null
     */
    public static function get()
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $today = date('Y-m-d H:i:s');

        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT an.*, attm.id AS attachmentId, attm.name AS attachmentName, attm.url
                 FROM announcements an
                 LEFT JOIN attachments attm ON an.id = attm.announcementId
                 WHERE an.showDate <= :today AND an.hideDate >= :today ' .
                'ORDER BY an.showDate ASC;'
            );

            $stmt->bindParam(':today', $today, PDO::PARAM_STR);

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new Announcement($result);
            }

            return $ret;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return null;
    }

    /**
     * @param $facultyId
     * @return array|null
     */
    public static function adminGet($facultyId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT an.*, attm.id AS attachmentId, attm.name AS attachmentName, attm.url
                 FROM announcements an
                 LEFT JOIN attachments attm ON an.id = attm.announcementId
                 WHERE an.facultyId = :id ' .
                'ORDER BY an.showDate ASC;'
            );

            $stmt->bindParam(':id', $facultyId, PDO::PARAM_STR);

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new Announcement($result);
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
     * @return null
     */
    public static function getById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT an.*, attm.id AS attachmentId, attm.name AS attachmentName, attm.url
                FROM announcements an
                LEFT JOIN attachments attm ON an.id = attm.announcementId
                WHERE an.id = :id;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new Announcement($result);
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
     * @return string|null
     */
    public static function getFacultyIdOf($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT facultyId FROM announcements WHERE id = :id;'
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

    /**
     * @param $announcement Announcement
     * @return bool|int
     */
    public static function adminAddAnnouncement($announcement)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'INSERT IGNORE INTO announcements(facultyId, title, tags, content, showDate, hideDate)
                VALUES (:facultyId, :title, :tags, :content, :showDate, :hideDate); '
            );

            $stmt->bindValue(':facultyId', $announcement->getFacultyId(), PDO::PARAM_STR);
            $stmt->bindValue(':title', $announcement->getTitle(), PDO::PARAM_STR);
            $stmt->bindValue(':tags', $announcement->getTags(), PDO::PARAM_STR);
            $stmt->bindValue(':content', $announcement->getContent(), PDO::PARAM_STR);
            $stmt->bindValue(':showDate', $announcement->getShowDate(), PDO::PARAM_STR);
            $stmt->bindValue(':hideDate', $announcement->getHideDate(), PDO::PARAM_STR);

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
     * @param $an
     * @return bool
     */
    public static function updateById($id, $an)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'UPDATE announcements SET ' . DBUtility::parseUpdateQuery($an) . ' WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            foreach ($an as $key => $value) {
                if ($value != null) {
                    $stmt->bindParam(":{$key}", $an[$key]);
                } else {
                    $stmt->bindParam(":{$key}", $an[$key], PDO::PARAM_NULL);
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
                'DELETE FROM announcements WHERE id = :id'
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