<?php
namespace core\model;

use PDOException;
use PDO;
use core\utility\DBUtility;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/PDOData.php';
require_once 'src/core/model/Step.php';
require_once 'src/core/utility/DBUtility.php';

class StepTable
{

    public static function get()
    {
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare('SELECT id, stepName, stepCode FROM dict_steps;');

            $stmt->execute();
            $ret = array();
            $ret['data'] = array();
            while ($result = $stmt->fetch()) {
                $ret['data'][] = new Step($result);
            }

            return $ret;
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return null;
    }

    /**
     * @param $id
     * @return null
     */
    public static function getById($id){
        $db = new PDOData(); $conn = $db->connect();
        try {
            $stmt = $conn->prepare(
                'SELECT id, stepName, stepCode
                  FROM dict_steps
                  WHERE id = :id;');

            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result !== false) {
                return new Step($result);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            echo Constant::connectionText . $e->getMessage();
        }
        $db->disconnect();
        return null;
    }
}
