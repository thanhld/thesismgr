<?php
namespace core\model;

use core\utility\DBUtility;
use core\utility\UUID;
use PDO;
use PDOException;
use core\utility\Constant;
use core\model\ReviewTable;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/Topic.php';
require_once 'src/core/model/ReviewTable.php';
require_once 'src/core/utility/DBUtility.php';
require_once 'src/core/utility/UUID.php';

/**
 * Class to interact with topic table
 */
class TopicTable
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
     * @param $sessionId
     * @return array
     */
    public static function getNotRegisteredLearner($sessionId)
    {
        $ret = array();
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'SELECT rl.learnerId AS l FROM registration_learners rl
                  LEFT JOIN topics t ON rl.registrationSessionId = t.registrationSessionId AND rl.learnerId = t.learnerId
                 WHERE rl.registrationSessionId = :id AND t.id IS NULL '
            );

            $stmt->bindParam(':id', $sessionId, PDO::PARAM_STR);

            $stmt->execute();

            $ret = array();
            while ($result = $stmt->fetch()){
                $ret[] = $result['l'];
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return $ret;
    }

    /**
     * @param $option
     * @param $facultyId
     * @return array|null
     */
    public static function get($option, $facultyId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            if($option['limit'] != 0 && $option['offset'] != -1) {
                $limitQuery = 'LIMIT :limit OFFSET :offset;';
            } else {
                $limitQuery = ';';
            }

            $stmt = $conn->prepare(
                'SELECT t.id, t.learnerId, l.fullname, l.learnerCode, l.trainingCourseId, dtc.courseCode,
                        t.departmentId, t.topicStatus, t.topicType, t.vietnameseTopicTitle, t.englishTopicTitle,
                        t.isEnglish, t.description, t.tags, t.referenceUrl, t.registerUrl, t.expertiseOfficerIds,
                        t.mainSupervisorId, t.requestedSupervisorId, t.coSupervisorIds, t.outOfficerIds,
                        t.startDate, t.defaultDeadlineDate, t.deadlineDate,
                        tc.id AS cId, tc.vietnameseTopicTitle AS cVietnameseTopicTitle, tc.englishTopicTitle AS cEnglishTopicTitle,
                        tc.isEnglish AS cIsEnglish, tc.description AS cDescription, tc.tags AS cTags, tc.mainSupervisorId AS cMainSupervisorId,
                        tc.requestedSupervisorId AS cRequestedSupervisorId, tc.coSupervisorIds AS cCoSupervisorIds,
                        tc.startPauseDate, tc.pauseDuration, tc.delayDuration, tc.cancelReason,
                        tc.registerUrl AS cRegisterUrl,
                        (SELECT count(*)
                        FROM topics t
                        INNER JOIN departments d ON t.departmentId = d.id
                        INNER JOIN learners l ON t.learnerId = l.id
                        WHERE d.facultyId = :fId AND ' . DBUtility::parseFilter($option['filter'], self::$acceptList) . '
                        ) AS c
                FROM topics t
                INNER JOIN departments d ON t.departmentId = d.id
                INNER JOIN learners l ON t.learnerId = l.id
                LEFT JOIN dict_training_courses dtc ON l.trainingCourseId = dtc.id
                LEFT JOIN topics_changes tc ON t.id = tc.id
                WHERE d.facultyId = :fId AND ' . DBUtility::parseFilter($option['filter'], self::$acceptList) . ' ' .
                "ORDER BY {$option['order']} {$option['direction']} " . $limitQuery
            );

            $stmt->bindParam(':fId', $facultyId, PDO::PARAM_STR);

            if($option['limit'] != 0 && $option['offset'] != -1){
                $stmt->bindParam(':limit', $option['limit'], PDO::PARAM_INT);
                $stmt->bindParam(':offset', $option['offset'], PDO::PARAM_INT);
            }

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            $ret['count'] = 0;
            while ($result = $stmt->fetch()) {
                $result['reviews'] = array();
                $result['reviews'] = ReviewTable::getByTopic($result['id']);
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
     * @param $option
     * @param $learnerId
     * @return array|null
     */
     public static function getLearnerTopic($option, $learnerId) {
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
                        tc.id AS cId, tc.vietnameseTopicTitle AS cVietnameseTopicTitle, tc.englishTopicTitle AS cEnglishTopicTitle,
                        tc.isEnglish AS cIsEnglish, tc.description AS cDescription, tc.tags AS cTags, tc.mainSupervisorId AS cMainSupervisorId,
                        tc.requestedSupervisorId AS cRequestedSupervisorId, tc.coSupervisorIds AS cCoSupervisorIds,
                        tc.startPauseDate, tc.pauseDuration, tc.delayDuration, tc.cancelReason,
                        tc.registerUrl AS cRegisterUrl,
                        (SELECT count(*)
                        FROM topics t
                        INNER JOIN departments d ON t.departmentId = d.id
                        INNER JOIN learners l ON t.learnerId = l.id
                        WHERE t.learnerId = :lId AND ' . DBUtility::parseFilter($option['filter'], self::$acceptList) . '
                        ) AS c
                FROM topics t
                INNER JOIN departments d ON t.departmentId = d.id
                INNER JOIN learners l ON t.learnerId = l.id
                LEFT JOIN topics_changes tc ON t.id = tc.id
                WHERE t.learnerId = :lId AND ' . DBUtility::parseFilter($option['filter'], self::$acceptList) . ' ' .
                "ORDER BY {$option['order']} {$option['direction']} "
            );

            $stmt->bindParam(':lId', $learnerId, PDO::PARAM_STR);

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            $ret['count'] = 0;
            while ($result = $stmt->fetch()) {
                $result['reviews'] = array();
                $result['reviews'] = ReviewTable::getByTopic($result['id']);
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
     * @param $learnerId
     * @return array|null
     */
     public static function getTopicByLearnerId($learnerId) {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'SELECT topicStatus FROM topics
                WHERE learnerId = :lId ORDER BY topicStatus DESC'
            );

            $stmt->bindParam(':lId', $learnerId, PDO::PARAM_STR);

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
        return false;
     }

    /**
     * @param $option
     * @param $officerId
     * @return array|null
     */
    public static function getOfficerTopic($option, $officerId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                "SELECT t.id, t.learnerId, l.fullname, l.learnerCode, l.trainingCourseId,
                        t.departmentId, t.topicStatus, t.topicType, t.vietnameseTopicTitle, t.englishTopicTitle,
                        t.isEnglish, t.description, t.tags, t.referenceUrl, t.registerUrl, t.expertiseOfficerIds,
                        t.mainSupervisorId, t.requestedSupervisorId, t.coSupervisorIds, t.outOfficerIds,
                        t.startDate, t.defaultDeadlineDate, t.deadlineDate,
                        tc.id AS cId, tc.vietnameseTopicTitle AS cVietnameseTopicTitle, tc.englishTopicTitle AS cEnglishTopicTitle,
                        tc.isEnglish AS cIsEnglish, tc.description AS cDescription, tc.tags AS cTags, tc.mainSupervisorId AS cMainSupervisorId,
                        tc.requestedSupervisorId AS cRequestedSupervisorId, tc.coSupervisorIds AS cCoSupervisorIds,
                        tc.startPauseDate, tc.pauseDuration, tc.delayDuration, tc.cancelReason,
                        tc.registerUrl AS cRegisterUrl,
                        (SELECT count(*)
                        FROM topics t
                        INNER JOIN departments d ON t.departmentId = d.id
                        INNER JOIN learners l ON t.learnerId = l.id
                        WHERE (t.mainSupervisorId = :ofId OR t.requestedSupervisorId = :ofId
                            OR t.coSupervisorIds LIKE :pattern OR t.expertiseOfficerIds LIKE :pattern)
                        AND " . DBUtility::parseFilter($option['filter'], self::$acceptList) . "
                        ) AS c
                FROM topics t
                INNER JOIN departments d ON t.departmentId = d.id
                INNER JOIN learners l ON t.learnerId = l.id
                LEFT JOIN topics_changes tc ON t.id = tc.id
                WHERE (t.mainSupervisorId = :ofId OR t.requestedSupervisorId = :ofId
                    OR t.coSupervisorIds LIKE :pattern  OR t.expertiseOfficerIds LIKE :pattern)
                AND " . DBUtility::parseFilter($option['filter'], self::$acceptList) . " " .
                "ORDER BY {$option['order']} {$option['direction']} "
            );

            $pattern = '%' . $officerId . '%';
            $stmt->bindParam(':ofId', $officerId, PDO::PARAM_STR);
            $stmt->bindParam(':pattern', $pattern, PDO::PARAM_STR);

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            $ret['count'] = 0;
            while ($result = $stmt->fetch()) {
                $result['reviews'] = array();
                $result['reviews'] = ReviewTable::getByTopic($result['id']);
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
     * @param $officerId
     * @return array|null
     */
    public static function getOfficerTopicChange($officerId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                "SELECT t.id, t.learnerId, l.fullname, l.learnerCode, l.trainingCourseId,
                        t.departmentId, t.topicStatus, t.topicType, t.vietnameseTopicTitle, t.englishTopicTitle,
                        t.isEnglish, t.description, t.tags, t.referenceUrl, t.registerUrl, t.expertiseOfficerIds,
                        t.mainSupervisorId, t.requestedSupervisorId, t.coSupervisorIds, t.outOfficerIds,
                        t.startDate, t.defaultDeadlineDate, t.deadlineDate,
                        tc.id AS cId, tc.vietnameseTopicTitle AS cVietnameseTopicTitle, tc.englishTopicTitle AS cEnglishTopicTitle,
                        tc.isEnglish AS cIsEnglish, tc.description AS cDescription, tc.tags AS cTags, tc.mainSupervisorId AS cMainSupervisorId,
                        tc.requestedSupervisorId AS cRequestedSupervisorId, tc.coSupervisorIds AS cCoSupervisorIds,
                        tc.startPauseDate, tc.pauseDuration, tc.delayDuration, tc.cancelReason,
                        tc.registerUrl AS cRegisterUrl,
                        (SELECT count(*)
                        FROM topics t
                        INNER JOIN departments d ON t.departmentId = d.id
                        INNER JOIN topics_changes tc ON t.id = tc.id
                        WHERE tc.mainSupervisorId = :ofId OR tc.requestedSupervisorId = :ofId OR tc.coSupervisorIds LIKE :pattern
                        ) AS c
                FROM topics t
                INNER JOIN departments d ON t.departmentId = d.id
                INNER JOIN learners l ON t.learnerId = l.id
                INNER JOIN topics_changes tc ON t.id = tc.id
                WHERE tc.mainSupervisorId = :ofId OR tc.requestedSupervisorId = :ofId OR tc.coSupervisorIds LIKE :pattern;"
            );

            $pattern = '%' . $officerId . '%';
            $stmt->bindParam(':ofId', $officerId, PDO::PARAM_STR);
            $stmt->bindParam(':pattern', $pattern, PDO::PARAM_STR);

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            $ret['count'] = 0;
            while ($result = $stmt->fetch()) {
                $result['reviews'] = array();
                $result['reviews'] = ReviewTable::getByTopic($result['id']);
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
     * @param $id
     * @return Topic|null
     */
    public static function getById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'SELECT t.id, t.learnerId, l.fullname, l.learnerCode, l.trainingCourseId,
                        t.departmentId, t.topicStatus, t.topicType, t.vietnameseTopicTitle, t.englishTopicTitle,
                        t.isEnglish, t.description, t.tags, t.referenceUrl, t.registerUrl, t.expertiseOfficerIds,
                        t.mainSupervisorId, t.requestedSupervisorId, t.coSupervisorIds, t.outOfficerIds,
                        t.startDate, t.defaultDeadlineDate, t.deadlineDate,
                        tc.id AS cId, tc.vietnameseTopicTitle AS cVietnameseTopicTitle, tc.englishTopicTitle AS cEnglishTopicTitle,
                        tc.isEnglish AS cIsEnglish, tc.description AS cDescription, tc.tags AS cTags, tc.mainSupervisorId AS cMainSupervisorId,
                        tc.requestedSupervisorId AS cRequestedSupervisorId, tc.coSupervisorIds AS cCoSupervisorIds,
                        tc.startPauseDate, tc.pauseDuration, tc.delayDuration, tc.cancelReason,
                        tc.registerUrl AS cRegisterUrl
                FROM topics t
                INNER JOIN departments d ON t.departmentId = d.id
                INNER JOIN learners l ON t.learnerId = l.id
                LEFT JOIN topics_changes tc ON t.id = tc.id
                WHERE t.id = :id;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                $result['reviews'] = array();
                $result['reviews'] = ReviewTable::getByTopic($result['id']);
                return new Topic($result);
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
     * @param $topicId
     * @return null
     */
    public static function getLearnerIdOf($topicId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'SELECT learnerId FROM topics WHERE id = :id'
            );

            $stmt->bindParam(':id', $topicId, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return $result['learnerId'];
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
    */
    public static function newIter($topicType) {
      if ($topicType == 1) {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'Update officers set numberOfStudent = 0 where departmentId in (select id from  departments where facultyId = :facultyId);'
            );

            $stmt->bindValue(':facultyId', $_SESSION['facultyId'], PDO::PARAM_STR);

            if ($stmt->execute() && $stmt->rowCount() != 0) {

            } else {

            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
      }

    }

    /**
     * @param $departmentId
     * @param $learner
     * @return bool|int
     */
    public static function initializeTopic($departmentId, $learner)
    {
        $id = UUID::v4();
        $topicStatus = 100;
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'INSERT INTO topics(id, learnerId, topicType, departmentId, topicStatus)
                VALUES (:id, :learnerId, :topicType, :departmentId, :topicStatus); '
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->bindValue(':learnerId', $learner['id'], PDO::PARAM_STR);
            $stmt->bindValue(':topicType', $learner['learnerType'], PDO::PARAM_INT);
            $stmt->bindValue(':departmentId', $departmentId, PDO::PARAM_STR);
            $stmt->bindValue(':topicStatus', $topicStatus, PDO::PARAM_INT);

            if ($stmt->execute() && $stmt->rowCount() != 0) {
                return $id;
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
     * @param $topicId
     * @param $tp
     * @return bool
     */
    public static function updateById($topicId, $tp)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'UPDATE topics SET ' . DBUtility::parseUpdateQuery($tp) . ' WHERE id = :topicId'
            );

            $stmt->bindParam(':topicId', $topicId, PDO::PARAM_STR);

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
     *
     * @param $topicId
     * @return bool|mixed
     */
    public static function getTopicStatus($topicId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'SELECT topicStatus, topicType, requestedSupervisorId FROM topics WHERE id = :topicId;'
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
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    /**
     * @param $topicId
     * @return bool|null
     */
    public static function getMainSupervisorId($topicId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'SELECT mainSupervisorId FROM topics
                  WHERE id = :topicId;'
            );

            $stmt->bindParam(':topicId', $topicId, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return $result['mainSupervisorId'];
            } else {
                return null;
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    /**
     * @param $topicId
     * @return bool|null
     */
    public static function getCoSupervisorIds($topicId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'SELECT coSupervisorIds FROM topics
                  WHERE id = :topicId;'
            );

            $stmt->bindParam(':topicId', $topicId, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return $result['coSupervisorIds'];
            } else {
                return null;
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    /**
     * @param $topicId
     * @return bool|null
     */
    public static function getExpertiseOfficerIds($topicId)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'SELECT expertiseOfficerIds FROM topics
                  WHERE id = :topicId;'
            );

            $stmt->bindParam(':topicId', $topicId, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return $result['expertiseOfficerIds'];
            } else {
                return null;
            }
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
    public static function getActivitiesByTopicId($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                "SELECT avt.activitiesId FROM activities_topics avt WHERE avt.topicId = :id;"
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();

            $ret = array();
            while ($result = $stmt->fetch()) {
                $ret[] = $result['activitiesId'];
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
    public static function getFacultyIdOf($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'SELECT d.facultyId
                FROM topics t
                INNER JOIN departments d ON t.departmentId = d.id
                WHERE t.id = :id'
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
     * @param $id
     * @return null
     */
    public static function getDepartmentIdOf($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'SELECT t.departmentId
                FROM topics t
                WHERE t.id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return $result['departmentId'];
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
     * @param $topicId
     * @param $topicStatus
     * @return bool
     */
    public static function updateStatus($topicId, $topicStatus)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {

            $stmt = $conn->prepare(
                'UPDATE topics SET topicStatus = :topicStatus
                 WHERE id = :topicId;'
            );

            $stmt->bindParam(':topicId', $topicId, PDO::PARAM_STR);
            $stmt->bindParam(':topicStatus', $topicStatus, PDO::PARAM_INT);

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
                'DELETE t.* FROM topics t
                  LEFT JOIN topics_changes tc ON tc.id = t.id
                  LEFT JOIN activities_topics avt ON t.id = avt.topicId
                  LEFT JOIN topics_histories th ON th.topicId = t.id
                  WHERE t.id = :id'
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
