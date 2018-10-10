<?php
namespace officer\controller;

use DateTime;
use core\utility\Constant;
use core\model\Review;
use core\model\ReviewTable;
use core\model\OfficerTable;
use core\model\TopicTable;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/Review.php';
require_once 'src/core/model/ReviewTable.php';
require_once 'src/core/model/OfficerTable.php';
require_once 'src/core/model/TopicTable.php';

/**
 * TopicController
 */
class TopicController
{
    /**
     * API
     * officerReviewTopic()
     *
     * HOW-TO-DO: des
     */
    public function officerReviewTopic() {
        switch ($_SESSION['role']) {
            case 3:
            case 6:
                $officerId = $_SESSION['uid'];
                break;
            default:
                http_response_code(403);
                echo json_encode(array(
                    'message' => Constant::notPermissionText
                ));
                return;
        }

        $json = file_get_contents('php://input');
        $obj = json_decode($json);

        $reviews = array();
        if (property_exists($obj, 'topicId')) $reviews['topicId'] = $obj->topicId;
        if (property_exists($obj, 'topicStatus')) $reviews['topicStatus'] = $obj->topicStatus;
        if (property_exists($obj, 'content')) $reviews['content'] = $obj->content;
        if (property_exists($obj, 'reviewStatus')) $reviews['reviewStatus'] = $obj->reviewStatus;
        if (property_exists($obj, 'iteration')) $reviews['iteration'] = $obj->iteration;
        if(isset($reviews['reviewStatus'])) $reviews['officerId'] = $officerId;

        if(!isset($reviews['topicStatus']) || $reviews['topicStatus'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['topicStatus']) . Constant::isRequiredText
            ));
            return;
        }

        if(!isset($reviews['topicId']) || $reviews['topicId'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['topicId']) . Constant::isRequiredText
            ));
            return;
        }

        if(!isset($reviews['reviewStatus'])) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['reviewStatus']) . Constant::isRequiredText
            ));
            return;
        }

        if(!isset($reviews['content']) || $reviews['content'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Ý kiến nhận xét " . Constant::isRequiredText
            ));
            return;
        }

        if(!isset($reviews['iteration'])) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Thứ tự của lần nhận xét " . Constant::isRequiredText
            ));
            return;
        }

        $review = new Review($reviews);
        foreach ($reviews as $key => $value) {
            $action = 'check' . ucfirst($key);
            if (!$review->$action()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                ));
                return;
            }
        }

        //Update review's content & status
        ReviewTable::update($reviews);
        http_response_code(200);
        echo json_encode(array(
            'message' => Constant::success
        ));
    }
}