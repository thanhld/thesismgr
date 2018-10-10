<?php
namespace faculty\controller;

use core\model\LearnerTable;
use core\model\OfficerTable;
use core\model\TrainingArea;
use core\model\TrainingAreaTable;
use core\utility\Middleware;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/OfficerTable.php';
require_once 'src/core/model/LearnerTable.php';
require_once 'src/core/model/TrainingAreaTable.php';
require_once 'src/core/model/TrainingArea.php';
require_once 'src/core/utility/Middleware.php';
require_once 'src/core/utility/Paging.php';


/**
 * TrainingAreaController
 */
class TrainingAreaController
{
    /**
     * API
     * getTrainingArea()
     *
     * HOW-TO-DO: des
     */
    public function getTrainingArea()
    {
        $facultyId = $_SESSION['facultyId'];

        //$option = Paging::normalizeOption($_GET);
        $result = TrainingAreaTable::get($facultyId);
        //$result = Paging::genNextPrev($option, $result);

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * addClass()
     *
     * HOW-TO-DO: des
     */
    public function addTrainingArea()
    {
        switch ($_SESSION['role']) {
            case 1:
                $facultyId = $_SESSION['uid'];
                break;
            case 4:
                $facultyId = OfficerTable::getFacultyIdOf($_SESSION['uid']);
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

        $fct = array();
        if (property_exists($obj, 'areaCode')) $fct['areaCode'] = $obj->areaCode;
        if (property_exists($obj, 'name')) $fct['name'] = $obj->name;
        $trainingArea = new TrainingArea($fct);

        if (!isset($fct['areaCode']) || $fct['areaCode'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' =>  ucfirst(Constant::objectNames['areaCode']) . Constant::isRequiredText
            ));
            return;
        } elseif (!isset($fct['name']) || $fct['name'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Tên " . ucfirst(Constant::objectNames['trainingArea']) . Constant::isRequiredText
            ));
            return;
        } else {
            foreach ($fct as $key => $value) {
                $action = 'check' . ucfirst($key);
                if (!$trainingArea->$action()) {
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                    ));
                    return;
                }
            }
        }

        // Valid now, add training area
        $trainingArea->setFacultyId($facultyId);
        $result = TrainingAreaTable::addTrainingArea($trainingArea);

        if ($result === false) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::objectNames['areaCode']) . Constant::isExistedText
            ));
            return;
        } else {
            http_response_code(201);
            echo json_encode(array(
                'id' => $result
            ));
        }
    }

    /**
     * API
     * getClassById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function getTrainingAreaById($param)
    {
        switch ($_SESSION['role']) {
            case 1:
                $facultyId = $_SESSION['uid'];
                break;
            case 2:
                $facultyId = LearnerTable::getFacultyIdOf($_SESSION['uid']);
                break;
            case 3:
            case 4:
            case 5:
            case 6:
                $facultyId = OfficerTable::getFacultyIdOf($_SESSION['uid']);
                break;
            default:
                $facultyId = null;
                break;
        }

        $id = $param['id'];
        $result = TrainingAreaTable::getById($id, $facultyId);

        if ($result !== null) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['trainingArea']
            ));
        }
    }

    /**
     * API
     * updateTrainingAreaById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function updateTrainingAreaById($param)
    {
        $id = $param['id'];
        $facultyId = TrainingAreaTable::getFacultyIdOf($id);

        if ($facultyId == null) {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['trainingArea']
            ));
            return;
        }

        switch ($_SESSION['role']) {
            case 1:
                if ($_SESSION['uid'] != $facultyId) {
                    http_response_code(403);
                    echo json_encode(array(
                        'message' => Constant::notPermissionText
                    ));
                    return;
                }
                break;
            case 4:
                if (!Middleware::isOfficerBelongToFaculty($_SESSION['uid'], $facultyId)) {
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

        $tra = array();
        if (property_exists($obj, 'areaCode')) $tra['areaCode'] = $obj->areaCode;
        if (property_exists($obj, 'name')) $tra['name'] = $obj->name;
        $trainingArea = new TrainingArea($tra);

        if (isset($tra['areaCode']) && $tra['areaCode'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::objectNames['areaCode']) . Constant::notEmptyText
            ));
            return;
        }

        if (isset($tra['name']) && $tra['name'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Tên " . ucfirst(Constant::objectNames['trainingArea']) . Constant::notEmptyText
            ));
            return;
        }

        foreach ($tra as $key => $value) {
            $action = 'check' . ucfirst($key);
            if (!$trainingArea->$action()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                ));
                return;
            }
        }

        TrainingAreaTable::updateById($id, $tra);
        http_response_code(200);
        echo json_encode(array(
            'message' => Constant::success
        ));
    }

    /**
     * API
     * deleteTrainingAreaById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function deleteTrainingAreaById($param)
    {
        $id = $param['id'];
        $facultyId = TrainingAreaTable::getFacultyIdOf($id);

        switch ($_SESSION['role']) {
            case 1:
                if ($_SESSION['uid'] != $facultyId) {
                    http_response_code(403);
                    echo json_encode(array(
                        'message' => Constant::notPermissionText
                    ));
                    return;
                }
                break;
            case 4:
                if (!Middleware::isOfficerBelongToFaculty($_SESSION['uid'], $facultyId)) {
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

        $result = TrainingAreaTable::deleteById($id);

        if ($result['rowCount']) {
            http_response_code(204);
        } else {
            if($result['data']['1'] && $result['data']['2']){
                http_response_code(400);
                echo json_encode(array(
                    'message' => Constant::cannotDelete . Constant::objectNames['trainingArea']
                ));
            } else {
                http_response_code(404);
                echo json_encode(array(
                    'message' => Constant::notFoundText . Constant::objectNames['trainingArea']
                ));
            }
        }
    }
}
