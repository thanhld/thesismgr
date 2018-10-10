<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 5/21/2017
 * Time: 02:55 PM
 */

namespace core\model;

use PDOException;
use PDO;
use core\utility\DBUtility;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/Review.php';
require_once 'src/core/utility/DBUtility.php';

class ReviewTable
{
    /**
     * @param $review
     * @return bool|int
     */
    public static function create($review) {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'INSERT INTO reviews(topicId, topicStatus, departmentSuperId, officerId, reviewStatus, content, iteration)
                VALUES (:topicId, :topicStatus, :departmentSuperId, :officerId, :reviewStatus, :content, :iteration);'
            );

            $stmt->bindValue(':topicId', $review->getTopicId(), PDO::PARAM_STR);
            $stmt->bindValue(':topicStatus', $review->getTopicStatus(), PDO::PARAM_INT);
            $stmt->bindValue(':departmentSuperId', $review->getDepartmentSuperId(), PDO::PARAM_STR);
            $stmt->bindValue(':officerId', $review->getOfficerId(), PDO::PARAM_STR);
            $stmt->bindValue(':reviewStatus', $review->getReviewStatus(), PDO::PARAM_INT);
            $stmt->bindValue(':content', $review->getContent(), PDO::PARAM_STR);
            $stmt->bindValue(':iteration', $review->getIteration(), PDO::PARAM_STR);

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
     * @param $topicId
     * @return array|null
     */
    public static function getByTopic($topicId) {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT *
                FROM reviews
                WHERE topicId = :topicId;'
            );

            $stmt->bindParam(':topicId', $topicId, PDO::PARAM_STR);

            $stmt->execute();

            $ret = array();
            while ($result = $stmt->fetch()) {
                $ret[] = new Review($result);
            }

            return $ret;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }

        $db->disconnect();
        return null;
    }

    /**
     * @param $review Review
     * @return array|null
     */
    public static function getOfficerReviews($review) {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT *
                FROM reviews
                WHERE topicId = :topicId AND topicStatus = :topicStatus AND officerId = :officerId
                ORDER BY iteration DESC;'
            );

            $stmt->bindParam(':topicId', $review->getTopicId(), PDO::PARAM_STR);
            $stmt->bindParam(':topicStatus', $review->getTopicStatus(), PDO::PARAM_STR);
            $stmt->bindParam(':officerId', $review->getOfficerId(), PDO::PARAM_STR);

            $stmt->execute();

            $ret = array();
            while ($result = $stmt->fetch()) {
                $ret[] = new Review($result);
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
     * @return Review|null
     */
    public static function getById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT * FROM reviews WHERE id = :id
                ORDER BY created DESC;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new Review($result);
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
     * @param $review
     * @return bool
     */
    public static function getLastestIteration($review){
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'SELECT iteration
                FROM reviews
                WHERE topicId = :topicId AND topicStatus = :topicStatus
                ORDER BY iteration DESC;'
            );

            $stmt->bindParam(':topicId', $review['topicId'], PDO::PARAM_STR);
            $stmt->bindParam(':topicStatus', $review['topicStatus'], PDO::PARAM_INT);

            $stmt->execute();
            //print_r($iteration);

            $result = $stmt->fetch();

            if ($result !== false) {
                return $result['iteration'];
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
     * @param $review
     * @return bool
     */
    public static function update($review) {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'UPDATE reviews SET ' . DBUtility::parseUpdateQuery($review) 
                . ' WHERE topicId = :topicId AND topicStatus = :topicStatus
                     AND officerId = :officerId AND iteration =:iteration;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':topicId', $review['topicId'], PDO::PARAM_STR);
            $stmt->bindParam(':topicStatus', $review['topicStatus'], PDO::PARAM_INT);
            $stmt->bindParam(':officerId', $review['officerId'], PDO::PARAM_STR);
            $stmt->bindParam(':iteration', $review['iteration'], PDO::PARAM_INT);

            foreach ($review as $key => $value) {
                if ($value != null) {
                    $stmt->bindParam(":{$key}", $review[$key]);
                } else {
                    $stmt->bindParam(":{$key}", $review[$key], PDO::PARAM_NULL);
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
     * @param $topicId
     * @return array|bool
     */
    public static function deleteById($id) {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'DELETE FROM reviews WHERE id =:id'
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
     * @param $review
     * @param $officeId
     * @param $iteration
     * @return array|bool
     */
    public static function removeReview($review, $officeId, $iteration) {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'DELETE FROM reviews 
                WHERE topicId = :topicId AND topicStatus = :topicStatus
                    AND officerId = :officerId AND iteration = :iteration;'
            );

            $stmt->bindParam(':topicId', $review['topicId'], PDO::PARAM_STR);
            $stmt->bindParam(':topicStatus', $review['topicStatus'], PDO::PARAM_INT);
            $stmt->bindParam(':officerId', $officeId, PDO::PARAM_STR);
            $stmt->bindParam(':iteration', $iteration, PDO::PARAM_INT);

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