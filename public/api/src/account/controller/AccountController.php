<?php
namespace account\controller;

use core\model\Account;
use core\model\AccountTable;
use core\utility\MailHelper;
use core\utility\TokenGenerator;
use core\utility\Middleware;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/AccountTable.php';
require_once 'src/core/model/Account.php';
require_once 'src/core/utility/TokenGenerator.php';
require_once 'src/core/utility/MailHelper.php';
require_once 'src/core/utility/Middleware.php';

/**
 * AccountController
 */
class AccountController
{
    /**
     * API
     * login()
     *
     * HOW-TO-DO: get username and password from POST data
     * check login in if failed echo failed message
     */
    public function login()
    {
        $usernameRegex = '/^[a-zA-Z0-9_.-]{2,50}$/';
        $passwordRegex = '/^[\x20-\x7F]{6,}$/'; // more than 6 printable ASCII chars

        $json = file_get_contents('php://input');
        $obj = json_decode($json);

        $username = property_exists($obj, 'username') ? $obj->username : null;
        $password = property_exists($obj, 'password') ? $obj->password : null;

        // check valid password and username
        // avoid sql injection
        if (preg_match($usernameRegex, $username) == 1
            && preg_match($passwordRegex, $password) == 1
        ) {
            // Encrypt password
            $password = hash('sha512', $password);

            $result = AccountTable::getUserInfoByLogin($username, $password);
            if ($result != null) {
                $_SESSION['username'] = $result->getUsername();
                $_SESSION['uid'] = $result->getUid();
                $_SESSION['role'] = $result->getRole();
                $_SESSION['vnuMail'] = $result->getVnuMail();
                $_SESSION['facultyId'] = Middleware::getFacultyIdByAccount();
                $result->setFacultyId($_SESSION['facultyId']);

                http_response_code(200);
                echo json_encode($result);
            } else {
                http_response_code(400);
                echo json_encode(array('message' => 'Sai tên hoặc mật khẩu đăng nhập'));
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                'message' => 'Tên hoặc mật khẩu đăng nhập không hợp lệ'
            ));
        }
    }

    /**
     * API
     * loadAuthAccount()
     *
     * HOW-TO-DO: load all auth info of the logged in account
     */
    public function loadAuthAccount()
    {
        http_response_code(200);
        echo json_encode(new Account($_SESSION));
    }

    /**
     * API
     * logout()
     *
     * HOW-TO-DO: end the session
     */
    public function logout()
    {
        // remove all session variables
        session_unset();

        // destroy the session
        session_destroy();

        http_response_code(204);
    }

    /**
     * API
     * changePassword()
     *
     * HOW-TO-DO: change password of the current account
     */
    public function changePassword()
    {
        $passwordRegex = '/^[\x20-\x7F]{6,}$/';

        $json = file_get_contents('php://input');
        $obj = json_decode($json);

        $oldPassword = property_exists($obj, 'oldPassword') ? $obj->oldPassword : null;
        $newPassword = property_exists($obj, 'newPassword') ? $obj->newPassword : null;

        // check valid password and username
        // avoid sql injection
        if (preg_match($passwordRegex, $newPassword) == 1) {
            $uid = $_SESSION['uid'];

            // Encrypt password
            $oldPassword = hash('sha512', $oldPassword);
            $newPassword = hash('sha512', $newPassword);

            $success = AccountTable::changePassword($uid, $oldPassword, $newPassword);
            if ($success) {
                http_response_code(200);
                echo json_encode(array(
                    'message' => Constant::updated
                ));
            } else {
                http_response_code(400);
                echo json_encode(array(
                    'message' => 'Sai mật khẩu cũ'
                ));
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                'message' => 'Mật khẩu mới không hợp lệ'
            ));
        }
    }

    /**
     * API
     * setPassword()
     *
     * HOW-TO-DO: set password of the current account by token
     */
    public function setPassword()
    {
        $passwordRegex = '/^[\x20-\x7F]{6,}$/';

        $json = file_get_contents('php://input');
        $obj = json_decode($json);

        $uid = property_exists($obj, 'uid') ? $obj->uid : null;
        $password = property_exists($obj, 'password') ? $obj->password : null;
        $securityToken = property_exists($obj, 'securityToken') ? $obj->securityToken : null;

        // check valid password
        // avoid sql injection
        if (preg_match($passwordRegex, $password) == 1) {
            // Encrypt password
            $password = hash('sha512', $password);

            $success = AccountTable::changePasswordByToken($uid, $password, $securityToken);
            if ($success) {
                http_response_code(200);
                echo json_encode(array(
                    'message' => Constant::success
                ));
            } else {
                http_response_code(400);
                echo json_encode(array(
                    'message' => 'Sai UID hoặc mã bảo mật'
                ));
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                'message' => 'Mật khẩu không hợp lệ'
            ));
        }
    }

    /**
     * API
     * forgotPassword()
     *
     * HOW-TO-DO: send recover password email
     */
    public function forgotPassword()
    {
        $vnuMailRegex = '/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@vnu.edu.vn$/i';

        $json = file_get_contents('php://input');
        $obj = json_decode($json);

        $vnuMail = property_exists($obj, 'vnuMail') ? $obj->vnuMail : null;

        // check valid password and username
        // avoid sql injection
        if (preg_match($vnuMailRegex, $vnuMail) == 1) {
            $uid = AccountTable::checkByVnuMail($vnuMail);
            if ($uid != false) {
                $token = TokenGenerator::generate();
                AccountTable::setToken($uid, $token);
                $result = MailHelper::sendResetPasswordEmail($vnuMail, $uid, $token);

                if($result){
                    http_response_code(200);
                    echo json_encode(array(
                        'message' => Constant::success
                    ));
                } else {
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => Constant::failed
                    ));
                }
            } else {
                http_response_code(400);
                echo json_encode(array(
                    'message' => 'VNU mail không tồn tại'
                ));
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                'message' => 'VNU mail không hợp lệ'
            ));
        }
    }
}
