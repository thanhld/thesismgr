<?php
namespace core\model;

use core\utility\UUID;
use PDO;
use PDOException;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/Account.php';
require_once 'src/core/utility/UUID.php';

/**
 * Class to interact with accounts table
 */
class AccountTable
{
    /**
     * getUserInfoByLogin($username = '', $password = '')
     * @param string $username
     * @param string $password
     * HOW-TO-DO: check if this username and password is matched or not
     * @return Account
     * associative array (info of matched account, if not matched return empty associative array)
     */
    public static function getUserInfoByLogin($username = '', $password = '')
    {
        $db = new PDOData(); $conn = $db->connect();try {
            $db = new PDOData();
            $conn = $db->connect();
            $stmt = $conn->prepare(
                'SELECT uid, username, vnuMail, role FROM accounts WHERE username = :username AND password = :password;'
            );

            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new Account($result);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return null;
    }

    /**
     * changePassword($uid = '', $oldPassword = '', $newPassword = '')
     * @param string $uid
     * @param string $oldPassword
     * @param string $newPassword
     * HOW-TO-DO: change password of this uid if oldPassword matched
     * @return bool
     * boolean (true if oldPassword matched with account, otherwise false)
     */
    public static function changePassword($uid = '', $oldPassword = '', $newPassword = '')
    {
        $db = new PDOData(); $conn = $db->connect();try {
            $db = new PDOData();
            $conn = $db->connect();
            $stmt = $conn->prepare(
                'UPDATE accounts SET password = :newPassword WHERE uid = :uid AND password = :oldPassword;'
            );

            $stmt->bindParam(':newPassword', $newPassword, PDO::PARAM_STR);
            $stmt->bindParam(':oldPassword', $oldPassword, PDO::PARAM_STR);
            $stmt->bindParam(':uid', $uid, PDO::PARAM_STR);

            return $stmt->execute() && $stmt->rowCount() != 0;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    /**
     * @param string $uid
     * @param string $password
     * @param string $token
     * @return bool
     */
    public static function changePasswordByToken($uid = '', $password = '', $token = '')
    {
        $db = new PDOData(); $conn = $db->connect();try {
            $db = new PDOData();
            $conn = $db->connect();
            $stmt = $conn->prepare(
                'UPDATE accounts SET password = :password, securityToken = NULL
                WHERE uid = :uid AND securityToken = :token;'
            );

            $stmt->bindParam(':uid', $uid, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);

            return $stmt->execute() && $stmt->rowCount() != 0;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    /**
     * createAccount($username, $password, $role)
     * @param $username
     * @param $password
     * @param $vnuMail
     * @param $role
     * HOW-TO-DO: change password of this uid if oldPassword matched
     * @return bool|string
     * false if username or vnuMail existed
     * otherwise return new uid
     */
    public static function createAccount($username, $password, $vnuMail, $role)
    {
        $uid = UUID::v4();
        $db = new PDOData(); $conn = $db->connect();
        try {
            $db = new PDOData();
            $conn = $db->connect();
            $stmt = $conn->prepare(
                'INSERT IGNORE INTO accounts(uid, username, password, vnuMail, role)
                VALUES (:uid, :username, :password, :vnuMail, :role); '
            );

            $stmt->bindParam(':uid', $uid, PDO::PARAM_STR);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':vnuMail', $vnuMail, PDO::PARAM_STR);
            $stmt->bindParam(':role', $role, PDO::PARAM_INT);

            if ($stmt->execute() && $stmt->rowCount() != 0) {
                return $uid;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public static function deleteById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'DELETE FROM accounts WHERE uid = :id;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();
            return $stmt->rowCount() !== 0;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    /* public static function disableById($id)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'UPDATE accounts SET status = 0 WHERE uid = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();
            return $stmt->rowCount() !== 0;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    } */

    /**
     * @param string $uid
     * @param null $token
     * @return bool
     */
    public static function setToken($uid = '', $token = null)
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare(
                'UPDATE accounts SET securityToken = :securityToken WHERE uid = :uid'
            );

            $stmt->bindParam(':uid', $uid, PDO::PARAM_STR);
            if ($token != null) {
                $stmt->bindParam(':securityToken', $token, PDO::PARAM_STR);
            } else {
                $stmt->bindParam(':securityToken', $token, PDO::PARAM_NULL);
            }

            $stmt->execute();
            return $stmt->rowCount() !== 0;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    /**
     * @param string $vnuMail
     * @return bool
     * HOW-TO-DO: check if this email is existed on the system or not
     */
    public static function checkByVnuMail($vnuMail = '')
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare('SELECT uid FROM accounts WHERE vnuMail = :vnuMail;');

            $stmt->bindParam(':vnuMail', $vnuMail, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch();
            if ($result == false) {
                return false;
            } else {
                return $result['uid'];
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    /**
     * @param string $username
     * @return bool
     * HOW-TO-DO: check if this username is existed on the system or not
     */
    public static function checkByUsername($username = '')
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare('SELECT uid FROM accounts WHERE username = :username;');

            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch();
            if ($result == false) {
                return false;
            } else {
                return $result['uid'];
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    /**
     * @param string $id , string $vnuMail
     * @param string $vnuMail
     * @return bool HOW-TO-DO: update vnuMail for account
     * HOW-TO-DO: update vnuMail for account
     */
    public static function updateVnuMail($id = '', $vnuMail = ''){
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare('UPDATE accounts SET vnuMail = :vnuMail WHERE uid = :id');

            $stmt->bindParam(':vnuMail', $vnuMail, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt->rowCount() !== 0;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    /**
     * @param string $id
     * @param $act
     * @return bool HOW-TO-DO: update vnuMail & username for account
     * HOW-TO-DO: update vnuMail & username for account
     */
    public static function updateAccountBasicInfo($id = '', $act){
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare('UPDATE accounts 
                      SET username = :username, vnuMail = :vnuMail, role = :role
                      WHERE uid = :id');

            $stmt->bindParam(':username', $act['username'], PDO::PARAM_STR);
            $stmt->bindParam(':vnuMail', $act['vnuMail'], PDO::PARAM_STR);
            $stmt->bindParam(':role', $act['role'], PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt->rowCount() !== 0;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    /**
     * @param string $id
     * @return int
     * HOW-TO-DO: get role of user
     */
    public static function getRoleUser($id = '')
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare('SELECT role FROM accounts WHERE uid = :id;');

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch();
            if ($result == false) {
                return false;
            } else {
                return $result['role'];
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }
    // // verifyEmail($username = '', $tokenhash = '')
    // // INPUT: username and tokenhash of the account
    // // HOW-TO-DO: verify email with the tokenhash and username
    // // OUTPUT: bool (success or not)
    // public static function verifyEmail($username = '', $tokenhash = '') {
    //     $db = new PDOData(); $conn = $db->connect();try {
    //         
    //         $stmt = $conn->prepare('UPDATE accounts SET activate = 1 WHERE username = :username AND tokenhash = :tokenhash;');

    //         $stmt->bindParam(':username', $username);
    //         $stmt->bindParam(':tokenhash', $tokenhash);

    //         $stmt->execute();

    //         return $stmt->rowCount() != 0;
    //     } catch(PDOException $e) {
    //         echo Constant::connectionText . $e->getMessage();
    //     }
    //     $db->disconnect();
    // }
}
