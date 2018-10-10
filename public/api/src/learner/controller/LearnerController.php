<?php
namespace learner\controller;
use core\model\Learner;
use core\model\LearnerTable;
use core\utility\Paging;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/LearnerTable.php';
require_once 'src/core/model/Learner.php';
require_once 'src/core/utility/Paging.php';

/**
 * LearnerController
 */
class LearnerController
{
    /**
     * API
     * getLearnerById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function getLearnerById($param)
    {
        $id = $param['id'];

        switch ($_SESSION['role']) {
            case 2:
                if ($_SESSION['uid'] != $id){
                    http_response_code(403);
                    echo json_encode(array(
                        'message' => Constant::notPermissionText
                    ));
                    return;
                }
                break;
            default:
                http_response_code(403);
                echo json_encode(array(
                    'message' => Constant::notPermissionText
                ));
                return;
        }

        $result = LearnerTable::getById($id);

        if ($result !== null) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['learner']
            ));
        }
    }

    /**
     * API
     * updateLearnerById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function updateLearnerById($param)
    {
        $id = $param['id'];

        switch ($_SESSION['role']) {
            case 2:
                if ($_SESSION['uid'] != $id){
                    http_response_code(403);
                    echo json_encode(array(
                        'message' => Constant::notPermissionText
                    ));
                    return;
                }
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

        $lrn = array();
        if (property_exists($obj, 'fullname')) $lrn['fullname'] = $obj->fullname;
        if (property_exists($obj, 'otherEmail')) $lrn['otherEmail'] = $obj->otherEmail;
        if (property_exists($obj, 'avatarUrl')) $lrn['avatarUrl'] = $obj->avatarUrl;
        if (property_exists($obj, 'phone')) $lrn['phone'] = $obj->phone;
        if (property_exists($obj, 'gpa')) $lrn['gpa'] = $obj->gpa;
        if (property_exists($obj, 'description')) $lrn['description'] = $obj->description;

        $learner = new Learner($lrn);

        if(isset($lrn['fullname']) && $lrn['fullname'] == null){
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['fullname']) . Constant::objectNames['learner'] . Constant::notEmptyText
            ));
            return;
        }

        foreach ($lrn as $key => $value) {
            $action = 'check' . ucfirst($key);
            if (!$learner->$action()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                ));
                return;
            }
        }

        LearnerTable::updateById($id, $lrn);
        http_response_code(200);
        echo json_encode(array(
            'message' => Constant::success
        ));
    }
}
