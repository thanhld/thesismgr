<?php
namespace superuser\controller;

use core\model\AccountTable;
use core\model\Department;
use core\model\DepartmentTable;
use core\model\Faculty;
use core\model\FacultyTable;
use core\utility\UUID;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/AccountTable.php';
require_once 'src/core/model/FacultyTable.php';
require_once 'src/core/model/Faculty.php';
require_once 'src/core/model/DepartmentTable.php';
require_once 'src/core/model/Department.php';
require_once 'src/core/utility/UUID.php';

/**
 * AccountManagerController
 */
class AccountManagerController
{
    /**
     * API
     * createFaculty()
     *
     * HOW-TO-DO: get faculty information from POST data
     * create new faculty
     */
    public function createFaculty()
    {
        if ($_SESSION['role'] != 0) {
            http_response_code(403);
            echo json_encode(array(
                'message' => Constant::notPermissionText
            ));
            return;
        }

        $usernameRegex = '/^[a-zA-Z0-9_.-]{2,50}$/';
        $passwordRegex = '/^[\x20-\x7F]{6,}$/'; // more than 6 ASCII chars
        $vnuMailRegex = '/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@vnu.edu.vn$/i';

        $json = file_get_contents('php://input');
        $obj = json_decode($json);


        $username = property_exists($obj, 'username') ? $obj->username : null;
        $password = property_exists($obj, 'password') ? $obj->password : null;
        $vnuMail  = property_exists($obj, 'vnuMail')  ? $obj->vnuMail : null;

        $fct = array();
        if (property_exists($obj, 'name')) $fct['name'] = $obj->name;
        if (property_exists($obj, 'shortName')) $fct['shortName'] = $obj->shortName;
        if (property_exists($obj, 'phone')) $fct['phone'] = $obj->phone;
        if (property_exists($obj, 'website')) $fct['website'] = $obj->website;
        if (property_exists($obj, 'location')) $fct['location'] = $obj->location;
        $faculty = new Faculty($fct);

        if ($username === null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['username']) . Constant::isRequiredText
            ));
        } elseif (preg_match($usernameRegex, $username) != 1) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['username']) . Constant::invalidText
            ));
        } elseif ($password === null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['password']) . Constant::isRequiredText
            ));
        } elseif (preg_match($passwordRegex, $password) != 1) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['password']) . Constant::invalidText
            ));
        } elseif ($vnuMail === null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['vnuMail']) . Constant::isRequiredText
            ));
        } elseif (preg_match($vnuMailRegex, $vnuMail) != 1) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['vnuMail']) . Constant::invalidText
            ));
        } elseif (!isset($fct['name'])) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['name']) . Constant::isRequiredText
            ));
        } else {
            foreach ($fct as $key => $value) {
                $action = 'check' . ucfirst($key);
                if (!$faculty->$action()) {
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                    ));
                    return;
                }
            }

            // Valid now, create faculty
            // Encrypt password
            $password = hash('sha512', $password);
            $result = AccountTable::createAccount($username, $password, $vnuMail, 1);

            if ($result === false) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames['username']) . ' hoặc ' 
                                . ucfirst(Constant::columnNames['vnuMail']) . Constant::isExistedText
                ));
                return;
            } else {
                $faculty->setId($result);
                FacultyTable::insert($faculty);

                //Create faculty's office
                $fof = array();
                $fof['id'] = UUID::v4();
                $fof['name'] = "Văn phòng khoa " . $fct['name'];
                $fof['facultyId'] = $result;
                $fof['type'] = 4;
                $facultyOffice = new Department($fof);
                $fofResult = DepartmentTable::addDepartment($facultyOffice);

                if(!$fofResult){
                    FacultyTable::deleteById($result);
                    AccountTable::deleteById($result);
                    json_encode(array(
                        'message' => "Không thể tạo Văn phòng Khoa mới"
                    ));
                }

                http_response_code(201);
                echo json_encode(array(
                    'uid' => $result
                ));
            }
        }
    }

    /**
     * API
     * disableFacultyById()
     *
     * @param $param array require id
     *
     * HOW-TO-DO: delete the faculty with the id in param
     */
    public function deleteFacultyById($param)
    {
        if ($_SESSION['role'] !== 0) {
            http_response_code(403);
            echo json_encode(array(
                'message' => Constant::notPermissionText
            ));
            return;
        }

        $id = $param['id'];

        $faculty = FacultyTable::getById($id);
        $result = FacultyTable::deleteById($id);

        if ($result['rowCount']) {
            $accountResult = AccountTable::deleteById($id);
            if(!$accountResult){
                http_response_code(400);
                echo json_encode(array(
                    'message' => "Không thể xóa tài khoản Khoa"
                ));
                FacultyTable::insert($faculty);
            } else {
                http_response_code(204);
            }
        } else {

            if($result['data']['1'] && $result['data']['2']){
                http_response_code(400);
                echo json_encode(array(
                    'message' => "Không thể xóa Khoa này"
                ));
            } else {
                http_response_code(404);
                echo json_encode(array(
                    'message' => ucfirst(Constant::objectNames['faculty']) . Constant::notFoundText
                ));
            }

        }
    }
}
