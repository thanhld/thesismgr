<?php
namespace core;

use PDO;
use PDOException;

require_once './../../api/src/core/model/PDOData.php';
require_once 'Mail.php';

/**
 * Class to interact with mail table
 */
class MailTable
{
    public function __construct() {}

    /**
     * @param $type
     * @return array|null
     */
    public function get($type) {
        $db = new model\PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT * FROM dict_mails
                WHERE status = 0 AND type = :type;'
            );

            $stmt->bindParam(':type', $type, PDO::PARAM_STR);

            $stmt->execute();
            $ret = array();
            while ($result = $stmt->fetch()) {
                $ret[] = new Mail($result);
            }

            return $ret;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $db->disconnect();
        return null;
    }

    /**
     * @param $id
     * @return array|null
     */
    public function checkSentMail($id) {
        $db = new model\PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'UPDATE dict_mails SET status = 1 WHERE id = :id'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

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
     * @return array|null
     */
    public function deleteById($id) {
        $db = new model\PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'DELETE FROM dict_mails WHERE id = :id AND status = 1;'
            );

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            $ret = array();
            $ret['data'] = array();
            $ret['data'] = $stmt->errorInfo();
            $ret['rowCount'] = $stmt->rowCount();
            return $ret;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return false;
    }

    public function __destruct() {}
}