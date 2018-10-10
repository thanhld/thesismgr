<?php
namespace faculty\controller;
use core\model\Department;
use core\model\DepartmentTable;
use core\model\LearnerTable;
use core\model\OfficerTable;
use core\utility\Middleware;
use core\utility\Paging;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/OfficerTable.php';
require_once 'src/core/model/LearnerTable.php';
require_once 'src/core/model/DepartmentTable.php';
require_once 'src/core/model/Department.php';
require_once 'src/core/utility/Middleware.php';
require_once 'src/core/utility/Paging.php';

/**
 * DepartmentController
 */
class DepartmentController
{

    /**
     * API
     * getDepartment()
     *
     * HOW-TO-DO: des
     */
    public function getDepartment()
    {
        $option = Paging::normalizeOption($_GET);
        $result = DepartmentTable::get($option);
        //$result = Paging::genNextPrev($option, $result);

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * addDepartment()
     *
     * HOW-TO-DO: des
     */
    public function addDepartment()
    {
        $facultyId = $_SESSION['facultyId'];

        $json = file_get_contents('php://input');
        $obj = json_decode($json);

        $fct = array();
        if (property_exists($obj, 'name')) $fct['name'] = $obj->name;
        if (property_exists($obj, 'type')) $fct['type'] = $obj->type;
        if (property_exists($obj, 'address')) $fct['address'] = $obj->office;
        if (property_exists($obj, 'phone')) $fct['phone'] = $obj->phone;
        if (property_exists($obj, 'website')) $fct['website'] = $obj->website;
        $department = new Department($fct);

        if (!isset($fct['name']) || $fct['name'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames["departmentName"]) . Constant::isRequiredText
            ));
            return;
        } elseif (!isset($fct['type']) || $fct['type'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Loại " . Constant::objectNames["department"] . Constant::isRequiredText
            ));
            return;
        } else {
            if($fct['type'] == 4){
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::objectNames["facultyOfficer"]) . Constant::isExistedText
                ));
                return;
            }

            foreach ($fct as $key => $value) {
                $action = 'check' . ucfirst($key);
                if (!$department->$action()) {
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                    ));
                    return;
                }
            }
        }

        /* if (isset($fct['deanId'])) {
            $deanFacultyId = OfficerTable::getFacultyIdOf($fct['deanId']);

            if ($deanFacultyId == null) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => 'Dean is not existed'
                ));
                return;
            } elseif ($deanFacultyId != $facultyId) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => 'Dean is officer of other faculty'
                ));
                return;

            }
        } */

        // Valid now, add department
        $department->setFacultyId($facultyId);
        $result = DepartmentTable::addDepartment($department);

        if ($result === false) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Tạo mới " . Constant::objectNames['department'] . Constant::failed
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
     * getDepartmentById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function getDepartmentById($param)
    {
        $id = $param['id'];

        $result = DepartmentTable::getById($id);

        if ($result !== null) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['department']
            ));
        }
    }

    /**
     * API
     * updateDepartmentById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function updateDepartmentById($param)
    {
        $id = $param['id'];
        $facultyId = DepartmentTable::getFacultyIdOf($id);

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
        if (property_exists($obj, 'type')) $fct['type'] = $obj->type;
        if (property_exists($obj, 'address')) $fct['address'] = $obj->address;
        if (property_exists($obj, 'phone')) $fct['phone'] = $obj->phone;
        if (property_exists($obj, 'website')) $fct['website'] = $obj->website;
        $department = new Department($fct);

        if (isset($fct['name']) && $fct['name'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames["departmentName"]) . Constant::notEmptyText
            ));
            return;
        }

        if (isset($fct['type']) && $fct['type'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Loại " . Constant::objectNames["department"] . Constant::notEmptyText
            ));
            return;
        }

        foreach ($fct as $key => $value) {
            $action = 'check' . ucfirst($key);
            if (!$department->$action()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                ));
                return;
            }
        }

        $oldDepartment = DepartmentTable::getById($id, $facultyId);
        $type = $oldDepartment->getType();

        if($type != 4 && $fct['type'] == 4){
            http_response_code(400);
            echo json_encode(array(
                'message' => "Văn phòng Khoa " . Constant::isExistedText
            ));
            return;
        }

        DepartmentTable::updateById($id, $fct);

        http_response_code(200);
        echo json_encode(array(
            'message' => Constant::success
        ));
    }

    /**
     * API
     * disableDepartmentById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function deleteDepartmentById($param)
    {
        $id = $param['id'];
        $facultyId = DepartmentTable::getFacultyIdOf($id);
        $department = DepartmentTable::getById($id, $facultyId);

        $type = $department->getType();
        if($type == 4) {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::cannotDelete . Constant::objectNames['facultyOfficer']
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

        $result = DepartmentTable::deleteById($id);

        if ($result['rowCount']) {
            http_response_code(204);
        } else {
            //if($result['data']['1'] && $result['data']['2']){
            http_response_code(400);
            echo json_encode(array(
                'message' => Constant::cannotDelete . Constant::objectNames['department']
            ));
        }
    }
}
