<?php
namespace department\controller;

use DateTime;
use core\utility\Constant;
use core\utility\Middleware;
use core\model\Review;
use core\model\ActivityTable;
use core\model\ReviewTable;
use core\model\OfficerTable;
use core\model\TopicTable;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/utility/Middleware.php';
require_once 'src/core/model/Review.php';
require_once 'src/core/model/ActivityTable.php';
require_once 'src/core/model/ReviewTable.php';
require_once 'src/core/model/OfficerTable.php';
require_once 'src/core/model/TopicTable.php';

/**
 * DepartmentTopicController
 */
class DepartmentTopicController
{
    /**
     * API
     * assignReviewOfficers()
     *
     * HOW-TO-DO: des
     */
    public function assignReviewOfficers($oldTopic, $expertiseOfficerIds, $activity)
    {
        $ret = array();

        $api = 'api/review-topic-email';

        switch ($_SESSION['role']) {
            case 6:
                $departmentSuperId = $_SESSION['uid'];
                break;
            default:
                $ret = array(
                    'success' => false,
                    'message' => Constant::notPermissionText
                );
                return $ret;
        }

        $reviews = array();
        $topic = array();
        $reviews['departmentSuperId'] = $departmentSuperId;
        $reviews['topicId'] = $oldTopic->getId();
        $reviews['topicStatus'] = $oldTopic->getTopicStatus();
        $topic['expertiseOfficerIds'] = $expertiseOfficerIds;
        $review = new Review($reviews);

        $departmentId = OfficerTable::getDepartmentIdOf($departmentSuperId);
        $departmentTopicId = TopicTable::getDepartmentIdOf($reviews['topicId']);

        if($departmentId != $departmentTopicId) {
            $ret = array(
                'success' => false,
                'message' => ucfirst(Constant::objectNames['topic']) . " không thuộc quyền quản lý của bộ môn"
            );
            return $ret;
        }

        $oldExpertiseOfficerIds = $oldTopic->getExpertiseOfficerIds();
        $oldOfficerIds = explode(',', $oldExpertiseOfficerIds);

        //update topic: expertiseOfficerIds field
        $updated = TopicTable::updateById($reviews['topicId'], $topic);
        
        //Initialize review for each expertise officer
        $officerIds = array();
        $officerIds = explode(',', $topic['expertiseOfficerIds']);
        $errorCount = 0;
        $successReviewIds = array();

        //Get lastest iteration of reviews
        $lastestIteration = ReviewTable::getLastestIteration($reviews);

        //Check and remove changed officer
        foreach($oldOfficerIds as $oldOfficerId) {
            if(!in_array($oldOfficerId, $officerIds)) {
                //remove officer review in current iteration
                ReviewTable::removeReview($reviews, $oldOfficerId, $lastestIteration);
            }
        }

        foreach($officerIds as $officerId) {
            if($officerId != null) {
                foreach ($reviews as $key => $value) {
                    $action = 'check' . ucfirst($key);
                    if (!$review->$action()) {
                        $ret = array(
                            'success' => false,
                            'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                        );
                        return $ret;
                    }
                }

                $review->setOfficerId($officerId);

                //Check whether lastest reviews of officer is existed - order by iteration, so alway get lastest iteration
                $existedReviews = ReviewTable::getOfficerReviews($review);
                $existedReview = $existedReviews[0];    //lastest created review || lastest iteration

                //Create new review if not existed, very first time
                if(!$existedReview) {
                    $review->setReviewStatus(0);
                    $iteration = (!$lastestIteration) ? 1 : $lastestIteration;
                    $review->setIteration($iteration);
                    $reviewResult = ReviewTable::create($review);

                    if($reviewResult) {
                        $successReviewIds[] = $reviewResult;

                        //Create activity
                        $activity->setRequestedSupervisorId($officerId);
                        $actResult = ActivityTable::createActivity($activity);
                        if($actResult) {
                            ActivityTable::updateActivityTopic($actResult, $reviews['topicId']);

                            //queue email to ready to be send
                            Middleware::queueEmail($officerId,$reviews['topicId'],4);

                            //Call background sending mail application
                            //Middleware::activeCurl($api);
                        }
                        
                        continue;
                    } else {
                        $errorCount += 1;
                        break;
                    }
                }
                // Review is existed
                else {
                    $content = $existedReview->getContent();
                    $reviewStatus = $existedReview->getReviewStatus();

                    //Officer reviewed before - Topic is modified and submit to request approving 2nd time
                    if($content != null && $reviewStatus != 0) {
                        //Get old activities with stepId = 5002 and specific topicStatus
                        $approvedActivities = ActivityTable::getDepartmentApprovedActivity(5002, $review);

                        if(count($approvedActivities) > 0) {   //From second time
                            if($reviewStatus == 2) {
                                //Create new record for reviewer who request learner modify register
                                $review->setReviewStatus(0);
                                $iteration = $lastestIteration + 1;
                                $review->setIteration($iteration);
                                $reviewResult = ReviewTable::create($review);

                                if($reviewResult) {
                                    $successReviewIds[] = $reviewResult;

                                    //Create activity
                                    $activity->setRequestedSupervisorId($officerId);
                                    $actResult = ActivityTable::createActivity($activity);
                                    if($actResult) {
                                        ActivityTable::updateActivityTopic($actResult, $reviews['topicId']);

                                        //queue email to ready to be send
                                        Middleware::queueEmail($officerId,$reviews['topicId'],4);
                                        
                                        //Call background sending mail application
                                        //Middleware::activeCurl($api);
                                    }

                                    continue;
                                } else {
                                    $errorCount += 1;
                                    break;
                                }
                            } else { continue; }
                        } 
                        // else {    //First time
                        //     if(!$updated) {
                        //         $ret = array(
                        //             'success' => false,
                        //             'message' => Constant::notUpdated
                        //         );
                        //         return $ret;
                        //     } else {
                        //         continue;
                        //     }
                        // }
                    }
                    //Officer has not review yet and topic is not changed, return error
                    // else {
                    //     if(!$updated) { //No change
                    //         $ret = array(
                    //             'success' => false,
                    //             'message' => Constant::notUpdated
                    //         );
                    //         return $ret;
                    //     } else {
                    //         continue;
                    //     }
                    // }
                }
            }
        }

        //Hanlde if unsuccessfully
        if($errorCount > 0) {
            foreach($successReviewIds as $reviewId) {
                ReviewTable:deleteById($reviewId);
            }

            //Reset topic's' field
            $topic['expertiseOfficerIds'] = null;
            TopicTable::updateById($reviews['topicId'], $topic);

            $ret = array(
                'success' => false,
                'message' => Constant::failed
            );
            return $ret;
        } else {
            $ret = array(
                'success' => true,
                'message' => Constant::success
            );
            return $ret;
        }
    }
}

