<?php
namespace core;

use PDO;
use PDOException;

require_once './../../api/src/core/model/PDOData.php';
require_once 'Account.php';

/**
 * Class to interact with accounts table
 */
class AccountTable
{
    /**
     * @param string $uid
     * @param null $token
     * @return bool
     */
    public static function setToken($uid = '', $token = null)
    {
        $db = new model\PDOData(); $conn = $db->connect();
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
     * @param string $id
     * @return int
     * HOW-TO-DO: get of user
     */
    public static function getById($id = '')
    {
        $db = new model\PDOData(); $conn = $db->connect();
        try {
            
            $stmt = $conn->prepare('SELECT * FROM accounts WHERE uid = :id;');

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetch();

            if ($result == false) {
                return false;
            } else {
                return new Account($result);
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }
}
