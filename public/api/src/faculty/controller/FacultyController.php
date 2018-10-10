<?php
namespace faculty\controller;

use core\model\AccountTable;
use core\model\Faculty;
use core\model\FacultyTable;
use core\utility\Middleware;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/AccountTable.php';
require_once 'src/core/model/FacultyTable.php';
require_once 'src/core/model/Faculty.php';
require_once 'src/core/utility/Middleware.php';
require_once 'src/core/utility/Paging.php';


/**
 * FacultyController
 */
class FacultyController
{
    /**
     *
     */
    public function getFaculty()
    {
        if ($_SESSION['role'] !== 0) {
            http_response_code(403);
            echo json_encode(array(
                'message' => Constant::notPermissionText
            ));
            return;
        }

        $result = FacultyTable::get();

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * @param $param array require id
     */
    public function getFacultyById($param)
    {
        $id = $param['id'];

        switch ($_SESSION['role']) {
            case 0:
                break;
            case 1:
                if ($_SESSION['uid'] != $id) {
                    http_response_code(403);
                    echo json_encode(array(
                        'message' => Constant::notPermissionText
                    ));
                    return;
                }
                break;
            case 4:
                if (!Middleware::isOfficerBelongToFaculty($_SESSION['uid'], $id)) {
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

        $result = FacultyTable::getById($id);

        if ($result !== null) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['faculty']
            ));
        }
    }

    /**
     * @param $param array require id
     */
    public function updateFacultyById($param)
    {
        $id = $param['id'];

        switch ($_SESSION['role']) {
            case 0:
                break;
            case 1:
                if ($_SESSION['uid'] != $id) {
                    http_response_code(403);
                    echo json_encode(array(
                        'message' => Constant::notPermissionText
                    ));
                    return;
                }
                break;
            case 4:
                if (!Middleware::isOfficerBelongToFaculty($_SESSION['uid'], $id)) {
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

        $usernameRegex = '/^[a-zA-Z0-9_.-]{2,50}$/';
        $vnuMailRegex = '/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@vnu.edu.vn$/i';

        $json = file_get_contents('php://input');
        $obj = json_decode($json);

        if (property_exists($obj, 'name') && $obj->name == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Tên " . ucfirst(Constant::objectNames['faculty']) . Constant::isRequiredText
            ));
            return;
        }

        $act = array();
        $act['vnuMail'] = property_exists($obj, 'vnuMail') ? $obj->vnuMail : null;
        $act['username'] = property_exists($obj, 'username') ? $obj->username : null;
        $act['role'] = 1;

        $fct = array();
        if (property_exists($obj, 'name')) $fct['name'] = $obj->name;
        if (property_exists($obj, 'shortName')) $fct['shortName'] = $obj->shortName;
        if (property_exists($obj, 'phone')) $fct['phone'] = $obj->phone;
        if (property_exists($obj, 'website')) $fct['website'] = $obj->website;
        if (property_exists($obj, 'address')) $fct['address'] = $obj->address;

        $faculty = new Faculty($fct);

        if($act['username'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' =>  ucfirst(Constant::columnNames['username']) . Constant::isRequiredText
            ));
            return;
        } elseif (preg_match($usernameRegex, $act['username']) != 1
                    || strlen($act['username']) >= 50) {
            http_response_code(400);
            echo json_encode(array(
                'message' =>  ucfirst(Constant::columnNames['username']) . Constant::invalidText
            ));
            return;
        }

        if ($act['vnuMail'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' =>  ucfirst(Constant::columnNames['vnuMail']) . Constant::isRequiredText
            ));
            return;
        } elseif (preg_match($vnuMailRegex, $act['vnuMail']) != 1
                    || strlen($act['vnuMail']) >= 255) {
            http_response_code(400);
            echo json_encode(array(
                'message' =>  ucfirst(Constant::columnNames['vnuMail']) . Constant::invalidText
            ));
            return;
        }

        foreach ($fct as $key => $value) {
            $action = 'check'. ucfirst($key);
            if (!$faculty->$action()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                ));
                return;
            }
        }

        AccountTable::updateAccountBasicInfo($id, $act);
        FacultyTable::updateById($id, $fct);
        http_response_code(200);
        echo json_encode(array(
            'message' => Constant::success
        ));
        return;
    }
}
