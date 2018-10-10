<?php
namespace faculty\controller;
use core\model\AccountTable;
use core\model\DegreeTable;
use core\model\Department;
use core\model\DepartmentTable;
use core\model\Learner;
use core\model\LearnerTable;
use core\model\Officer;
use core\model\OfficerTable;
use core\model\OutOfficer;
use core\model\OutOfficerTable;
use core\model\TrainingCourseTable;
use core\utility\Middleware;
use core\utility\Paging;
use core\utility\UUID;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/utility/Middleware.php';
require_once 'src/core/model/LearnerTable.php';
require_once 'src/core/model/Learner.php';
require_once 'src/core/model/OfficerTable.php';
require_once 'src/core/model/Officer.php';
require_once 'src/core/model/OutOfficerTable.php';
require_once 'src/core/model/OutOfficer.php';
require_once 'src/core/model/AccountTable.php';
require_once 'src/core/model/DepartmentTable.php';
require_once 'src/core/model/DegreeTable.php';
require_once 'src/core/model/TrainingCourseTable.php';
require_once 'src/core/utility/Paging.php';
/**
 * FacultyAdminController
 */
class FacultyAdminController
{
    /**
     * API
     * addOfficerAdmin()
     *
     * HOW-TO-DO: des
     */
    public function addOfficerAdmin()
    {
        switch ($_SESSION['role']) {
            case 1:
                $facultyId = $_SESSION['uid'];
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

        $usernameRegex = '/^[a-zA-Z0-9_.-]{2,50}$/';
        $passwordRegex = '/^[\x20-\x7F]{6,}$/'; // more than 6 ASCII chars
        $vnuMailRegex = '/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@vnu.edu.vn$/i';

        $username = property_exists($obj, 'username') ? $obj->username : null;
        $password = property_exists($obj, 'password') ? $obj->password : null;
        $vnuMail  = property_exists($obj, 'vnuMail')  ? $obj->vnuMail : null;

        $ofc = array();
        if (property_exists($obj, 'officerCode')) $ofc['officerCode'] = $obj->officerCode;
        if (property_exists($obj, 'fullname')) $ofc['fullname'] = $obj->fullname;
        if (property_exists($obj, 'degreeId')) $ofc['degreeId'] = $obj->degreeId;
        if (property_exists($obj, 'departmentId')) $ofc['departmentId'] = $obj->departmentId;
        $officer = new Officer($ofc);

        if ($username === null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['username']) . Constant::isRequiredText
            ));
            return;
        } elseif (preg_match($usernameRegex, $username) != 1) {
            http_response_code(400);
            echo json_encode(array(
                'message' =>  ucfirst(Constant::columnNames['username']) . Constant::isRequiredText
            ));
            return;
        } elseif ($password === null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['password']) . Constant::isRequiredText
            ));
            return;
        } elseif (preg_match($passwordRegex, $password) != 1) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['password']) . Constant::invalidText
            ));
            return;
        } elseif ($vnuMail === null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['vnuMail']) . Constant::isRequiredText
            ));
            return;
        } elseif (preg_match($vnuMailRegex, $vnuMail) != 1) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['vnuMail']) . Constant::invalidText
            ));
            return;
        } elseif (!isset($ofc['fullname'])) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['fullname']) . Constant::objectNames['officer'] . Constant::isRequiredText
            ));
            return;
        } elseif (!isset($ofc['departmentId'])) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['departmentId']) . Constant::isRequiredText
            ));
            return;
        } else {
            foreach ($ofc as $key => $value) {
                $action = 'check' . ucfirst($key);
                if (!$officer->$action()) {
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                    ));
                    return;
                }
            }

            if (isset($ofc['degreeId']) && !DegreeTable::checkDegree($ofc['degreeId'])) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::objectNames['degree']) . Constant::notExistedText
                ));
                return;
            }

            $dFacultyId = DepartmentTable::getFacultyIdOf($ofc['departmentId']);

            if ($dFacultyId == null) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::objectNames['department']) . Constant::notExistedText
                ));
                return;
            } elseif ($dFacultyId != $facultyId) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::objectNames['department']) . " không thuộc quyền quản lý của Khoa"
                ));
                return;
            }

            // Valid now, create officer
            // Encrypt password
            $password = hash('sha512', $password);
            $result = AccountTable::createAccount($username, $password, $vnuMail, 4);

            if ($result === false) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => 'Tên tài khoản hoặc VNU mail ' . Constant::isExistedText
                ));
            } else {
                $officer->setId($result);
                OfficerTable::insert($officer);

                http_response_code(200);
                echo json_encode(array(
                    'uid' => $result
                ));
            }
        }
    }

    /**
     * API
     * setOfficerAdmin()
     *
     * HOW-TO-DO: des
     */
    public function setOfficerAdmin()
    {
        // TODO setOfficerAdmin
        // Your code here

        http_response_code(200);
        echo json_encode(array(
            'message' => 'API is not depressed'
        ));
    }

    /**
     * API
     * removeOfficerAdmin()
     *
     * HOW-TO-DO: des
     */
    public function removeOfficerAdmin()
    {
        // TODO removeOfficerAdmin
        // Your code here

        http_response_code(200);
        echo json_encode(array(
            'message' => 'API is not depressed'
        ));
    }

    /**
     * API
     * adminGetOfficers()
     *
     */
    public function adminGetOfficers()
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

        $option = Paging::normalizeOption($_GET);
        $result = OfficerTable::adminGet($option, $facultyId);
        //$result = Paging::genNextPrev($option, $result);

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * adminGetOfficerById()
     *
     * @param $param
     * @internal param $id
     */
    public function adminGetOfficerById($param)
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

        $id = $param['id'];

        $facultyOfficerId = OfficerTable::getFacultyIdOf($id);

        if($facultyId !== $facultyOfficerId) {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['officer']
            ));
            return;
        }

        $result = OfficerTable::adminGetById($id);

        if ($result !== null) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['officer']
            ));
        }
    }

    /**
     * API
     * adminRemoveOfficer()
     *
     * @param $param
     */
    public function adminRemoveOfficer($param)
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

        $id = $param['id'];

        $facultyOfficerId = OfficerTable::getFacultyIdOf($id);

        if($facultyId !== $facultyOfficerId) {
            http_response_code(400);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['officer']
            ));
            return;
        }

        $officer = OfficerTable::getById($id);
        $result = OfficerTable::deleteById($id);

        if ($result['rowCount']) {
            $accountResult = AccountTable::deleteById($id);
            if(!$accountResult) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => Constant::cannotDelete . Constant::objectNames['account']
                ));
                OfficerTable::backUpOfficer($officer);
            } else {
                http_response_code(204);
            }
        } else {
            if($result['data']['1'] && $result['data']['2']){
                http_response_code(400);
                echo json_encode(array(
                    'message' => Constant::cannotDelete . Constant::objectNames['officer']
                ));
            } else {
                http_response_code(404);
                echo json_encode(array(
                    'message' => Constant::notFoundText . Constant::objectNames['officer']
                ));
            }

        }
    }

    /**
     * API
     * importOfficer()
     *
     * HOW-TO-DO: des
     */
    public function importOfficer()
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

        if (!is_array($obj) || count($obj) == 0) {
            http_response_code(400);
            echo json_encode(array(
                'message' => Constant::emptyList
            ));
            return;
        }

        $usernameRegex = '/^[a-zA-Z0-9_.-]{2,50}$/';
        $passwordRegex = '/^[\x20-\x7F]{6,}$/'; // more than 6 ASCII chars
        $vnuMailRegex = '/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@vnu.edu.vn$/i';

        $ret = array();

        foreach ($obj as $lecture) {
            $username = property_exists($lecture, 'username') ? $lecture->username : null;
            $password = property_exists($lecture, 'password') ? $lecture->password : null;
            $vnuMail  = property_exists($lecture, 'vnuMail')  ? $lecture->vnuMail : null;
            $role = property_exists($lecture, 'role')  ? $lecture->role : null;

            $ofc = array();
            if (property_exists($lecture, 'officerCode')) $ofc['officerCode'] = $lecture->officerCode;
            if (property_exists($lecture, 'fullname')) $ofc['fullname'] = $lecture->fullname;
            if (property_exists($lecture, 'degreeId')) $ofc['degreeId'] = $lecture->degreeId;
            if (property_exists($lecture, 'departmentId')) $ofc['departmentId'] = $lecture->departmentId;
            $officer = new Officer($ofc);

            if ($username === null) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['username']) . Constant::isRequiredText
                );
                continue;
            } elseif (preg_match($usernameRegex, $username) != 1) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['username']) . Constant::invalidText
                );
                continue;
            } elseif ($password === null) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['password']) . Constant::isRequiredText
                );
                continue;
            }  elseif ($role === null) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['role']) . Constant::isRequiredText
                );
                continue;
            }  elseif (preg_match($passwordRegex, $password) != 1) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['password']) . Constant::invalidText
                );
                continue;
            } elseif ($vnuMail === null) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['vnuMail']) . Constant::isRequiredText
                );
                continue;
            } elseif (preg_match($vnuMailRegex, $vnuMail) != 1) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['vnuMail']) . Constant::invalidText
                );
                continue;
            } elseif (!isset($ofc['fullname'])) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['fullname']) . Constant::objectNames['officer'] . Constant::isRequiredText
                );
                continue;
            } elseif(!isset($ofc['departmentId'])){
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['departmentId']) . Constant::isRequiredText
                );
                continue;
            } elseif (!isset($ofc['officerCode'])) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['officerCode']) . Constant::isRequiredText
                );
                continue;
            }

            foreach ($ofc as $key => $value) {
                $action = 'check' . ucfirst($key);
                if (!$officer->$action()) {
                    $ret[] = array(
                        'error' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                    );
                    continue;
                }
            }

            if(isset($ofc['degreeId'])){
                if (!DegreeTable::checkDegree($ofc['degreeId'])) {
                    $ret[] = array(
                        'error' => ucfirst(Constant::objectNames['degree']) . Constant::notExistedText
                    );
                    continue;
                }
            }

            $dFacultyId = DepartmentTable::getFacultyIdOf($ofc['departmentId']);

            if ($dFacultyId == null) {
                $ret[] = array(
                    'error' => ucfirst(Constant::objectNames['department']) . Constant::notExistedText
                );
                continue;
            } elseif ($dFacultyId != $facultyId) {
                $ret[] = array(
                    'error' => ucfirst(Constant::objectNames['department']) . " không thuộc quyền quản lý của Khoa"
                );
                continue;
            }

            // Valid now, create officer
            // Encrypt password
            $password = hash('sha512', $password);
            $result = AccountTable::createAccount($username, $password, $vnuMail, $role);

            if ($result === false) {
                $ret[] = array(
                    'error' => "Tên tài khoản hoặc VNU mail " . Constant::isExistedText
                );
                continue;
            } else {
                $officer->setId($result);
                $ofcResult = OfficerTable::insert($officer);

                if($ofcResult === false){
                    AccountTable::deleteById($result);

                    $ret[] = array(
                        'error' => ucfirst(Constant::columnNames['officerCode']) . Constant::isExistedText
                    );
                    continue;
                } else {
                    //queue email to ready to be send
                    Middleware::queueEmail($result,null,1);
                }
            }
        }

        $api = 'api/set-password-email';
        //Call background sending mail application
        //Middleware::activeCurl($api);

        http_response_code(200);
        echo json_encode($ret);
    }

    /**
     * API
     * adminAddOutOfficer()
     *
     * HOW-TO-DO: des
     */
    public function adminAddOutOfficer()
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

        if (!is_array($obj) || count($obj) == 0) {
            http_response_code(400);
            echo json_encode(array(
                'message' => Constant::emptyList
            ));
            return;
        }

        $ret = array();

        foreach($obj as $officer){
            $dep = array();
            $ofc = array();

            if (property_exists($officer, 'departmentName')) $dep['name'] = $officer->departmentName;
            $dep['type'] = 3;

            if (property_exists($officer, 'outOfficerId')) $outOfficerId = $officer->outOfficerId;
            if (property_exists($officer, 'departmentId')) $ofc['departmentId'] = $officer->departmentId;
            if (property_exists($officer, 'officerCode')) $ofc['officerCode'] = $officer->officerCode;
            if (property_exists($officer, 'fullname')) $ofc['fullname'] = $officer->fullname;
            if (property_exists($officer, 'degreeId')) $ofc['degreeId'] = $officer->degreeId;

            if(!isset($outOfficerId) || $outOfficerId == null){
                $ret[] = array(
                    'error' => "Mã giảng viên ngoài " . Constant::isRequiredText
                );
                continue;
            } elseif(!isset($ofc['fullname']) || $ofc['fullname'] == null){
                $ret[] = array(
                    'outOfficerId' => $outOfficerId,
                    'error' => ucfirst(Constant::columnNames['fullname']) . Constant::objectNames['officer'] . Constant::isRequiredText
                );
                continue;
            } elseif(!isset($ofc['officerCode']) || $ofc['officerCode'] == null){
                $ret[] = array(
                    'outOfficerId' => $outOfficerId,
                    'error' => ucfirst(Constant::columnNames['officerCode']) . Constant::isRequiredText
                );
                continue;
            } elseif(!isset($ofc['degreeId']) || $ofc['degreeId'] == null){
                $ret[] = array(
                    'outOfficerId' => $outOfficerId,
                    'error' => ucfirst(Constant::columnNames['degreeId']) . Constant::isRequiredText
                );
                continue;
            }

            //Create new department if departmentId not set
            if(isset($ofc['departmentId'])) {
                $dFacultyId = DepartmentTable::getFacultyIdOf($ofc['departmentId']);
                if ($dFacultyId == null) {
                    $ret[] = array(
                        'outOfficerId' => $outOfficerId,
                        'error' => ucfirst(Constant::objectNames['department']) . Constant::notExistedText
                    );
                    continue;
                } elseif ($dFacultyId != $facultyId) {
                    $ret[] = array(
                        'outOfficerId' => $outOfficerId,
                        'error' => ucfirst(Constant::objectNames['department']) . " không thuộc quyền quản lý của Khoa"
                    );
                    continue;
                }
            } else {
                $department = new Department($dep);
                if (!isset($dep['name']) || $dep['name'] == null) {
                    $ret[] = array(
                        'outOfficerId' => $outOfficerId,
                        'error' => ucfirst(Constant::columnNames['departmentName']) . Constant::isRequiredText
                    );
                    continue;
                } else {
                    foreach ($dep as $key => $value) {
                        $action = 'check' . ucfirst($key);
                        if (!$department->$action()) {
                            $ret[] = array(
                                'outOfficerId' => $outOfficerId,
                                'error' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                            );
                            continue;
                        }
                    }
                }

                //Check existed department
                $checkDepartment = DepartmentTable::getDepartmentIdByName($dep['name'], $facultyId);
                if($checkDepartment === false) {
                    //add new department to current faculty
                    $department->setFacultyId($facultyId);
                    $depResult = DepartmentTable::addDepartment($department);

                    if ($depResult === false) {
                        $ret[] = array(
                            'outOfficerId' => $outOfficerId,
                            'error' => "Tạo mới " . Constant::objectNames['department'] . Constant::failed
                        );
                        continue;
                    } else {
                        $ofc['departmentId'] = $depResult;
                    }
                } else {
                    $ofc['departmentId'] = $checkDepartment;
                }
            }

            //Add new out officer account
            $lecturer = new Officer($ofc);

            foreach ($ofc as $key => $value) {
                $action = 'check' . ucfirst($key);
                if (!$lecturer->$action()) {
                    $ret[] = array(
                        'outOfficerId' => $outOfficerId,
                        'error' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                    );
                    continue;
                }
            }

            if(isset($ofc['degreeId'])){
                if (!DegreeTable::checkDegree($ofc['degreeId'])) {
                    $ret[] = array(
                        'outOfficerId' => $outOfficerId,
                        'error' => ucfirst(Constant::objectNames['degree']) . Constant::notExistedText
                    );
                    continue;
                }
            }

            // Valid now, create officer

            //Auto set up out-officer's account
            $id = UUID::v4();
            $username = 'gvn_';
            for($i = 0; $i < 5; $i++) {
                $username = $username . $id[rand(0, strlen($id) - 1)];
            }
            // Encrypt password
            $password = hash('sha512', '12345678');
            $vnuMail = $username . '@vnu.edu.vn';
            $role = 5;
            $accResult = AccountTable::createAccount($username, $password, $vnuMail, $role);

            if ($accResult === false) {
                DepartmentTable::deleteById($ofc['departmentId']);
                $ret[] = array(
                    'outOfficerId' => $outOfficerId,
                    'error' => "Tên tài khoản hoặc VNU mail đã tồn tại"
                );
                continue;
            } else {
                $lecturer->setId($accResult);
                $ofcResult = OfficerTable::insert($lecturer);

                if($ofcResult === false){
                    if(!property_exists($officer, 'departmentId')){
                        DepartmentTable::deleteById($ofc['departmentId']);
                    }
                    AccountTable::deleteById($accResult);

                    $ret[] = array(
                        'outOfficerId' => $outOfficerId,
                        'error' => ucfirst(Constant::columnNames['officerCode']) . Constant::isExistedText
                    );
                    continue;
                }
            }
        }

        http_response_code(200);
        echo json_encode($ret);
    }

    /**
     * API
     * adminRemoveOutOfficer()
     *
     * HOW-TO-DO: des
     */
    public function adminRemoveOutOfficer(){
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

        if (!is_array($obj) || count($obj) == 0) {
            http_response_code(400);
            echo json_encode(array(
                'message' => Constant::emptyList
            ));
            return;
        }

        $ret = array();

        foreach($obj as $outOfficerId){
            if(!is_int($outOfficerId)){
                $ret[] = array(
                    'outOfficerId' => $outOfficerId,
                    'error' => ucfirst(Constant::columnNames['outOfficerId']) . Constant::invalidText
                );
                continue;
            } else {
                $offResult = OutOfficerTable::deleteById($outOfficerId);

                if($offResult == false){
                    $ret[] = array(
                        'outOfficerId' => $outOfficerId,
                        'error' => Constant::notFoundText . Constant::objectNames['outOfficer']
                    );
                    continue;
                }
            }
        }

        http_response_code(200);
        echo json_encode($ret);
    }

    /**
     * API
     * adminUpdateOfficerById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function adminUpdateOfficerById($param)
    {
        $id = $param['id'];
        $facultyId = OfficerTable::getFacultyIdOf($id);

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

        $usernameRegex = '/^[a-zA-Z0-9_.-]{2,50}$/';
        $vnuMailRegex = '/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@vnu.edu.vn$/i';

        $json = file_get_contents('php://input');
        $obj = json_decode($json);

        $act = array();
        $act['vnuMail'] = property_exists($obj, 'vnuMail') ? $obj->vnuMail : null;
        $act['username'] = property_exists($obj, 'username') ? $obj->username : null;
        $act['role'] = property_exists($obj, 'role') ? $obj->role : null;

        $ofc = array();
        if (property_exists($obj, 'officerCode')) $ofc['officerCode'] = $obj->officerCode;
        if (property_exists($obj, 'fullname')) $ofc['fullname'] = $obj->fullname;
        if (property_exists($obj, 'degreeId')) $ofc['degreeId'] = $obj->degreeId;
        if (property_exists($obj, 'departmentId')) $ofc['departmentId'] = $obj->departmentId;
        $officer = new Officer($ofc);

        if($act['username'] != null && (preg_match($usernameRegex, $act['username']) != 1
            || strlen($act['username']) >= 50)) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['username']) . Constant::invalidText
            ));
            return;
        }

        if ($act['vnuMail'] != null && (preg_match($vnuMailRegex, $act['vnuMail']) != 1
            || strlen($act['vnuMail']) >= 255)) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['vnuMail']) . Constant::invalidText
            ));
            return;
        }

        if($act['role'] != null && (!is_int($act['role']) || $act['role'] < 3 || $act['role'] > 6)) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['role']) . Constant::invalidText
            ));
            return;
        }

        foreach ($ofc as $key => $value) {
            $action = 'check' . ucfirst($key);
            if (!$officer->$action()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                ));
                return;
            }
        }

        if (isset($ofc['degreeId'])) {
            if (!DegreeTable::checkDegree($ofc['degreeId'])) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::objectNames['degree']) . Constant::notExistedText
                ));
                return;
            }
        }

        if (isset($ofc['departmentId'])) {
            $dFacultyId = DepartmentTable::getFacultyIdOf($ofc['departmentId']);

            if ($dFacultyId == null) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::objectNames['department']) . Constant::notExistedText
                ));
                return;
            }
        }

        // valid now
        AccountTable::updateAccountBasicInfo($id, $act);
        OfficerTable::updateById($id, $ofc);

        http_response_code(200);
        echo json_encode(array(
            'message' => Constant::success
        ));
        return;
    }

    /**
     * API
     * setNormLecture()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function setNormLecture($param)
    {
        $id = $param['id'];
        $facultyId = OfficerTable::getFacultyIdOf($id);

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
            case 5:
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

        $ofc = array();
        if (property_exists($obj, 'numberOfStudent')) $ofc['numberOfStudent'] = $obj->numberOfStudent;
        if (property_exists($obj, 'numberOfResearcher')) $ofc['numberOfResearcher'] = $obj->numberOfResearcher;
        if (property_exists($obj, 'numberOfGraduatedStudent')) $ofc['numberOfGraduatedStudent'] = $obj->numberOfGraduatedStudent;

        $officer = new Officer($ofc);

        foreach ($ofc as $key => $value) {
            $action = 'check' . ucfirst($key);
            if (!$officer->$action()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                ));
                return;
            }
        }

        $result = OfficerTable::updateById($id, $ofc);

        if ($result) {
            http_response_code(200);
            echo json_encode(array(
                'message' => Constant::success
            ));
        } else {
            http_response_code(400);
            echo json_encode(array(
                'message' => 'Not updated'
            ));
        }
    }

    /**
     * API
     * importLearner()
     *
     * HOW-TO-DO: des
     */
    public function importLearner()
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

        if (!is_array($obj) || count($obj) == 0) {
            http_response_code(400);
            echo json_encode(array(
                'message' => Constant::emptyList
            ));
            return;
        }

        $usernameRegex = '/^[a-zA-Z0-9_.-]{2,50}$/';
        $passwordRegex = '/^[\x20-\x7F]{6,}$/'; // more than 6 ASCII chars
        $vnuMailRegex = '/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@vnu.edu.vn$/i';

        $ret = array();

        foreach ($obj as $person) {
            $username = property_exists($person, 'username') ? $person->username : null;
            $password = property_exists($person, 'password') ? $person->password : null;
            $vnuMail  = property_exists($person, 'vnuMail')  ? $person->vnuMail : null;

            $lrn = array();
            if (property_exists($person, 'learnerCode')) $lrn['learnerCode'] = $person->learnerCode;
            if (property_exists($person, 'fullname')) $lrn['fullname'] = $person->fullname;
            if (property_exists($person, 'trainingCourseId')) $lrn['trainingCourseId'] = $person->trainingCourseId;
            $learner = new Learner($lrn);

            if ($username === null) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['username']) . Constant::isRequiredText
                );
                continue;
            } elseif (preg_match($usernameRegex, $username) != 1) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['username']) . Constant::invalidText
                );
                continue;
            } elseif ($password === null) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['password']) . Constant::isRequiredText
                );
                continue;
            } elseif (preg_match($passwordRegex, $password) != 1) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['password']) . Constant::invalidText
                );
                continue;
            } elseif ($vnuMail === null) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['vnuMail']) . Constant::isRequiredText
                );
                continue;
            } elseif (preg_match($vnuMailRegex, $vnuMail) != 1) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['vnuMail']) . Constant::invalidText
                );
                continue;
            } elseif (!isset($lrn['fullname']) || $lrn['fullname'] == null) {
                $ret[] = array(
                    'error' =>  ucfirst(Constant::columnNames['fullname']) . Constant::objectNames['learner'] . Constant::isRequiredText
                );
                continue;
            } elseif (!isset($lrn['trainingCourseId'])) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['trainingCourseId']) . Constant::isRequiredText
                );
                continue;
            }

            foreach ($lrn as $key => $value) {
                $action = 'check' . ucfirst($key);
                if (!$learner->$action()) {
                    $ret[] = array(
                        'error' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                    );
                    continue 2;
                }
            }

            $cFacultyId = TrainingCourseTable::getFacultyIdOf($lrn['trainingCourseId']);

            if ($cFacultyId == null) {
                $ret[] = array(
                    'error' => ucfirst(Constant::objectNames['trainingCourse']) . Constant::notExistedText
                );
                continue;
            } elseif ($cFacultyId != $facultyId) {
                $ret[] = array(
                    'error' => ucfirst(Constant::objectNames['trainingCourse']) . " không thuộc quyền quản lý của Khoa"
                );
                continue;
            }

            $trainingLevel = TrainingCourseTable::getLevelByCourseId($lrn['trainingCourseId']);
            $learner->setLearnerType($trainingLevel);

            // Valid now, create learner
            // Encrypt password
            $password = hash('sha512', $password);
            $result = AccountTable::createAccount($username, $password, $vnuMail, 2);

            if ($result === false) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['username']) . ' hoặc ' 
                                . ucfirst(Constant::columnNames['vnuMail']) . Constant::isExistedText
                );
                continue;
            } else {
                $learner->setId($result);
                $learnerRet = LearnerTable::insert($learner);

                if($learnerRet != null){
                    $ret[] = array(
                        'uid' => $result
                    );

                    //queue email to ready to be send
                    Middleware::queueEmail($result,null,1);
                } else {
                    AccountTable::deleteById($result);
                    $ret[] = array(
                        'error' => ucfirst(Constant::columnNames['learnerCode']) . Constant::isExistedText
                    );
                }
            }
        }

        $api = 'api/set-password-email';
        //Call background sending mail application
        //Middleware::activeCurl($api);

        http_response_code(200);
        echo json_encode($ret);
    }

    /**
     * API
     * updateLearnerAsAdmin()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function adminUpdateLearnerById($param)
    {
        $id = $param['id'];
        $facultyId = LearnerTable::getFacultyIdOf($id);

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

        $usernameRegex = '/^[a-zA-Z0-9_.-]{2,50}$/';
        $vnuMailRegex = '/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@vnu.edu.vn$/i';

        $json = file_get_contents('php://input');
        $obj = json_decode($json);

        $act = array();
        $act['vnuMail'] = property_exists($obj, 'vnuMail') ? $obj->vnuMail : null;
        $act['username'] = property_exists($obj, 'username') ? $obj->username : null;

        $lrn = array();
        if (property_exists($obj, 'learnerCode')) $lrn['learnerCode'] = $obj->learnerCode;
        if (property_exists($obj, 'fullname')) $lrn['fullname'] = $obj->fullname;
        if (property_exists($obj, 'trainingCourseId')) $lrn['trainingCourseId'] = $obj->trainingCourseId;
        $learner = new Learner($lrn);

        if($act['username'] != null && (preg_match($usernameRegex, $act['username']) != 1
            || strlen($act['username']) >= 50)) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['username']) . Constant::invalidText
            ));
            return;
        }

        if ($act['vnuMail'] == null && (preg_match($vnuMailRegex, $act['vnuMail']) != 1
            || strlen($act['vnuMail']) >= 255)) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['vnuMail']) . Constant::invalidText
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

        if (isset($lrn['classId'])) {
            $dFacultyId = TrainingCourseTable::getFacultyIdOf($lrn['trainingCourseId']);

            if ($dFacultyId == null) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::objectNames['trainingCourse']) . Constant::notExistedText
                ));
                return;
            }
        }

        $trainingLevel = TrainingCourseTable::getLevelByCourseId($lrn['trainingCourseId']);
        $lrn['learnerType'] = $trainingLevel;

        // valid now
        AccountTable::updateAccountBasicInfo($id, $act);
        LearnerTable::updateById($id, $lrn);

        http_response_code(200);
        echo json_encode(array(
            'message' => Constant::success
        ));
        return;
    }

    /**
     * API
     * adminGetLearner()
     *
     */
    public function adminGetLearners()
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

        $option = Paging::normalizeOption($_GET);
        $result = LearnerTable::adminGet($option, $facultyId);
        $result = Paging::genNextPrev($option, $result);

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * adminGetLearnerById()
     *
     * @param $param
     * @internal param $id
     */
    public function adminGetLearnerById($param)
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

        $id = $param['id'];

        $facultyLearnerId = LearnerTable::getFacultyIdOf($id);

        if($facultyId !== $facultyLearnerId){
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['learner']
            ));
            return;
        }

        $result = LearnerTable::adminGetById($id);

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
     * adminRemoveLearner()
     *
     * @param $param
     */
    public function adminRemoveLearner($param)
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

        $id = $param['id'];

        $facultyLearnerId = LearnerTable::getFacultyIdOf($id);

        if($facultyId !== $facultyLearnerId){
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['learner']
            ));
            return;
        }

        $learner = LearnerTable::getById($id);
        $result = LearnerTable::deleteById($id);

        if ($result['rowCount']) {
            $accountResult = AccountTable::deleteById($id);
            if(!$accountResult) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => Constant::cannotDelete . Constant::objectNames['account']
                ));
                LearnerTable::backUpLearner($learner);
            } else {
                http_response_code(204);
            }
        } else {
            if($result['data']['1'] && $result['data']['2']){
                http_response_code(400);
                echo json_encode(array(
                    'message' => Constant::cannotDelete . Constant::objectNames['learner']
                ));
            } else {
                http_response_code(404);
                echo json_encode(array(
                    'message' => Constant::notFoundText . Constant::objectNames['learner']
                ));
            }

        }
    }
}
