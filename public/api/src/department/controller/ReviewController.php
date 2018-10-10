<?php
namespace department\controller;

use core\model\Review;
use core\model\ReviewTable;
use core\utility\Middleware;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/ReviewTable.php';
require_once 'src/core/model/Review.php';
require_once 'src/core/utility/Middleware.php';
require_once 'src/core/utility/Paging.php';

class ReviewController
{
    /**
     * @param $param
     */
    public function deleteReviewById($param) {
        $id = $param['id'];
        
        switch ($_SESSION['role']) {
            case 6:
                break;
            default:
                http_response_code(403);
                echo json_encode(array(
                    'message' => Constant::notPermissionText
                ));
                return;
        }

        $review = ReviewTable::getById($id);
        if(!$review) {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['review']
            ));
            return;
        }

        $headDepartmentId = $review->getDepartmentSuperId();
        if($_SESSION['uid'] != $headDepartmentId) {
            http_response_code(403);
            echo json_encode(array(
                'message' => ucfirst(Constant::objectNames['review']) . " không thuộc quyền quản lý của bộ môn"
            ));
            return;
        }

        $result = ReviewTable::deleteById($id);
        if ($result['rowCount']) {
            http_response_code(204);
        } else {
            if($result['data']['1'] && $result['data']['2']){
                http_response_code(400);
                echo json_encode(array(
                    'message' => Constant::cannotDelete . Constant::objectNames['review']
                ));
            } else {
                http_response_code(404);
                echo json_encode(array(
                    'message' => Constant::notFoundText . Constant::objectNames['review']
                ));
            }
        }
    }
}