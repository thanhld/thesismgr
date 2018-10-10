<?php
namespace faculty\controller;
use core\model\AreaOfficerTable;
use core\model\KnowledgeArea;
use core\model\KnowledgeAreaTable;
use core\model\LearnerTable;
use core\model\OfficerTable;
use core\utility\Middleware;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/OfficerTable.php';
require_once 'src/core/model/LearnerTable.php';
require_once 'src/core/model/KnowledgeAreaTable.php';
require_once 'src/core/model/KnowledgeArea.php';
require_once 'src/core/model/AreaOfficerTable.php';
require_once 'src/core/utility/Middleware.php';
require_once 'src/core/utility/Paging.php';

/**
 * KnowledgeAreaController
 */
class KnowledgeAreaController
{

    /**
     * API
     * getKnowledgeArea()
     *
     * HOW-TO-DO: des
     */
    public function getKnowledgeArea()
    {
        $facultyId = $_SESSION['facultyId'];
        $result = KnowledgeAreaTable::get($facultyId);
        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * addKnowledgeArea()
     *
     * HOW-TO-DO: des
     */
    public function addKnowledgeArea()
    {
        $facultyId = $_SESSION['facultyId'];

        switch ($_SESSION['role']) {
            case 1:
            case 4:
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
        if (property_exists($obj, 'name')) $fct['name'] = $obj->name;
        if (property_exists($obj, 'parentId')) $fct['parentId'] = $obj->parentId;
        $knowledgeArea = new KnowledgeArea($fct);

        if (!isset($fct['name']) || $fct['name'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Tên " . ucfirst(Constant::objectNames['knowledgeArea']) . Constant::isRequiredText
            ));
            return;
        } else {
            foreach ($fct as $key => $value) {
                $action = 'check' . ucfirst($key);
                if (!$knowledgeArea->$action()) {
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                    ));
                    return;
                }
            }

            if ($fct['parentId'] !== null) {
                $parent = KnowledgeAreaTable::checkParentExisted($fct['parentId'], $facultyId);
                if (!$parent) {
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => ucfirst(Constant::objectNames['knowledgeArea']) . " cha " . Constant::notExistedText
                    ));
                    return;
                }
            }

            // Valid now, add knowledge area
            $knowledgeArea->setFacultyId($facultyId);
            $result = KnowledgeAreaTable::addKnowledgeArea($knowledgeArea);

            if ($result === false) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => "Tạo " . Constant::objectNames['knowledgeArea'] . Constant::failed
                ));
                return;
            } else {
                http_response_code(201);
                echo json_encode(array(
                    'id' => $result
                ));
            }
        }
    }

    /**
     * API
     * getKnowledgeAreaById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function getKnowledgeAreaById($param)
    {
        $id = $param['id'];

        $result = new KnowledgeArea(KnowledgeAreaTable::getById($id));

        if ($result !== null) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['knowledgeArea']
            ));
        }
    }

    /**
     * API
     * updateKnowledgeAreaById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function updateKnowledgeAreaById($param)
    {
        $id = $param['id'];
        $facultyId = KnowledgeAreaTable::getFacultyIdOf($id);

        if ($facultyId == null){
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['knowledgeArea']
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

        $fct = array();
        if (property_exists($obj, 'name')) $fct['name'] = $obj->name;
        if (property_exists($obj, 'parentId')) $fct['parentId'] = $obj->parentId;
        $knowledgeArea = new KnowledgeArea($fct);

        if (isset($fct['name']) && $fct['name'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Tên " . ucfirst(Constant::objectNames['knowledgeArea']) . Constant::notEmptyText
            ));
            return;
        }

        foreach ($fct as $key => $value) {
            $action = 'check' . ucfirst($key);
            if (!$knowledgeArea->$action()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                ));
                return;
            }
        }

        if ($fct['parentId'] !== null) {
            $check = KnowledgeAreaTable::checkParent($id, $fct['parentId']);

            if ($check !== true) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => $check
                ));
                return;
            }
        }

        KnowledgeAreaTable::updateById($id, $fct);
        http_response_code(200);
        echo json_encode(array(
            'message' => Constant::success
        ));
    }

    /**
     * API
     * deleteKnowledgeAreaById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function deleteKnowledgeAreaById($param)
    {
        $kaId = $param['id'];

        $areaFacultyId = KnowledgeAreaTable::getFacultyIdOf($kaId);

        $facultyId = $_SESSION['facultyId'];

        if($areaFacultyId != $facultyId){
            http_response_code(403);
            echo json_encode(array(
                'message' => Constant::notPermissionText
            ));
            return;
        }

        $currentKArea = new KnowledgeArea(KnowledgeAreaTable::getById($kaId));
        $childKAreas = KnowledgeAreaTable::getChildren($currentKArea->getId());

        $updated = 0;   //number of areas area updated successfully
        for($id=0; $id < $childKAreas['count']; $id++){
            $childArea = $childKAreas['data'][$id];

            $fct = array();
            $fct['parentId'] = $currentKArea->getParentId();

            if(KnowledgeAreaTable::updateById($childArea->getId(), $fct)){
                $updated += 1;
            }
        }

        if($updated == $childKAreas['count']){
            $result = KnowledgeAreaTable::deleteById($kaId);

            if ($result['rowCount']) {
                http_response_code(204);
            } else {
                if($result['data']['1'] && $result['data']['2']){
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => Constant::cannotDelete . Constant::objectNames['knowledgeArea']
                    ));
                } else {
                    http_response_code(404);
                    echo json_encode(array(
                        'message' => Constant::notFoundText . Constant::objectNames['knowledgeArea']
                    ));
                }
            }
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::cannotDelete . Constant::objectNames['knowledgeArea']
            ));
        }
    }

    /**
     * API
     * getAreaOfficers()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function getAreaOfficers($param)
    {
        $id = $param['id'];
        $facultyId = KnowledgeAreaTable::getFacultyIdOf($id);

        if ($facultyId == null){
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['knowledgeArea']
            ));
            return;
        }

        $result = AreaOfficerTable::getAreaOfficers($id);

        http_response_code(200);
        echo json_encode($result);
    }
}
