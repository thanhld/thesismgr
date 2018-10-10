<?php
namespace superuser\controller;

use core\model\Degree;
use core\model\DegreeTable;
use core\model\TrainingType;
use core\model\TrainingTypeTable;
use core\model\TrainingLevel;
use core\model\TrainingLevelTable;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/Degree.php';
require_once 'src/core/model/DegreeTable.php';
require_once 'src/core/model/TrainingType.php';
require_once 'src/core/model/TrainingTypeTable.php';
require_once 'src/core/model/TrainingLevel.php';
require_once 'src/core/model/TrainingLevelTable.php';

/**
 * DictManagerController
 */
class DictManagerController
{
    /**
     * API
     * getDegree()
     *
     * HOW-TO-DO: des
     */
    public function getDegree()
    {
        $result = DegreeTable::get();
        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * addDegree()
     *
     * HOW-TO-DO: des
     */
    public function addDegree()
    {
        switch ($_SESSION['role']) {
            case 0:
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

        $degree = new Degree($fct);

        if (!isset($fct['name']) || $fct['name'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Tên " . Constant::objectNames['degree'] . Constant::isRequiredText
            ));
            return;
        } else foreach ($fct as $key => $value) {
            $action = 'check' . ucfirst($key);
            if (!$degree->$action()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                ));
                return;
            }
        }

        $result = DegreeTable::addDegree($degree);
        if ($result === false) {
            http_response_code(400);
            echo json_encode(array(
                'message' => 'Tạo mới ' . Constant::objectNames['degree'] . Constant::failed
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
     * updateDegreeById()
     *
     * HOW-TO-DO: des
     * @param $param
     */
    public function updateDegreeById($param)
    {
        $id = $param['id'];
        switch ($_SESSION['role']) {
            case 0:
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
        $degree = new Degree($fct);
        foreach ($fct as $key => $value) {
            $action = 'check' . ucfirst($key);
            if (!$degree->$action()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                ));
                return;
            }
        }
        if (isset($fct['name']) && $fct['name'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => Constant::notEmptyText . " tên " . Constant::objectNames['degree']
            ));
            return;
        }

        DegreeTable::updateById($id, $fct);
        http_response_code(200);
        echo json_encode(array(
            'message' => Constant::success
        ));
    }

    /**
     * API
     * deleteDegreeById()
     *
     * HOW-TO-DO: des
     * @param $param
     */
    public function deleteDegreeById($param)
    {
        $id = $param['id'];
        switch ($_SESSION['role']) {
            case 0:
                break;
            default:
                http_response_code(403);
                echo json_encode(array(
                    'message' => Constant::notPermissionText
                ));
                return;
        }
        $result = DegreeTable::deleteById($id);
        if ($result['rowCount']) {
            http_response_code(204);
        } else {
            if($result['data']['1'] && $result['data']['2']){
                http_response_code(400);
                echo json_encode(array(
                    'message' => Constant::cannotDelete . Constant::objectNames['degree']
                ));
            } else {
                http_response_code(404);
                echo json_encode(array(
                    'message' => Constant::notFoundText . Constant::objectNames['degree']
                ));
            }
        }
    }

    /**
     * API
     * getTrainingType()
     *
     * HOW-TO-DO: des
     */
    public function getTrainingType()
    {
        $result = TrainingTypeTable::get();

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * addTrainingType()
     *
     * HOW-TO-DO: des
     */
    public function addTrainingType()
    {
        switch ($_SESSION['role']) {
            case 0:
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

        $try = array();
        if (property_exists($obj, 'name')) $try['name'] = $obj->name;
        $trainingType = new TrainingType($try);

        if (!isset($try['name']) || $try['name'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Tên " . Constant::objectNames['trainingType'] . Constant::isRequiredText
            ));
            return;
        } else {
            if (!$trainingType->checkName()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => "Tên " . Constant::objectNames['trainingType'] . Constant::invalidText
                ));
                return;
            }
        }

        $result = TrainingTypeTable::addTrainingType($trainingType);

        if ($result === false) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Tạo mới " . Constant::objectNames['trainingType'] . " không thành công"
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
     * getTrainingTypeById()
     *
     * HOW-TO-DO: des
     * @param $param
     */
    public function getTrainingTypeById($param)
    {
        $id = $param['id'];
        $result = TrainingTypeTable::getById($id);

        if ($result !== null) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['trainingType']
            ));
        }
    }

    /**
     * API
     * updateTrainingTypeById()
     *
     * HOW-TO-DO: des
     * @param $param
     */
    public function updateTrainingTypeById($param)
    {
        $id = $param['id'];

        switch ($_SESSION['role']) {
            case 0:
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

        $try = array();
        if (property_exists($obj, 'name')) $try['name'] = $obj->name;
        $trainingType = new TrainingType($try);

        if (isset($try['name']) && $try['name'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Tên " . Constant::objectNames['trainingType'] . Constant::notEmptyText
            ));
            return;
        } else {
            if (!$trainingType->checkName()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => "Tên " . Constant::objectNames['trainingType'] . Constant::invalidText
                ));
                return;
            }
        }

        TrainingTypeTable::updateById($id, $trainingType);
        http_response_code(200);
        echo json_encode(array(
            'message' => Constant::success
        ));
    }

    /**
     * API
     * deleteTrainingTypeById()
     *
     * HOW-TO-DO: des
     * @param $param
     */
    public function deleteTrainingTypeById($param)
    {
        $id = $param['id'];

        $result = TrainingTypeTable::deleteById($id);

        if ($result['rowCount']) {
            http_response_code(204);
        } else {
            if($result['data']['1'] && $result['data']['2']){
                http_response_code(400);
                echo json_encode(array(
                    'message' => Constant::cannotDelete . Constant::objectNames['trainingType']
                ));
            } else {
                http_response_code(404);
                echo json_encode(array(
                    'message' => Constant::notFoundText . Constant::objectNames['trainingType']
                ));
            }
        }
    }

    /**
     * API
     * getTrainingLevel()
     *
     * HOW-TO-DO: des
     */
    public function getTrainingLevel()
    {
        $result = TrainingLevelTable::get();
        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * addTrainingLevel()
     *
     * HOW-TO-DO: des
     */
    public function addTrainingLevel()
    {
        switch ($_SESSION['role']) {
            case 0:
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

        $trl = array();
        if (property_exists($obj, 'name')) $trl['name'] = $obj->name;
        if (property_exists($obj, 'levelType')) $trl['levelType'] = $obj->levelType;
        $trainingLevel = new TrainingLevel($trl);

        if (!isset($trl['name']) || $trl['name'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Tên " . Constant::objectNames['trainingLevel'] . Constant::isRequiredText
            ));
            return;
        } else if (!isset($trl['levelType']) || $trl['levelType'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['levelType']) . Constant::isRequiredText
            ));
            return;
        } else {
            if (!$trainingLevel->checkName()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => "Tên " . Constant::objectNames['trainingLevel'] . Constant::invalidText
                ));
                return;
            } else if (!$trainingLevel->checkLevelType()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames['levelType']) . Constant::invalidText
                ));
                return;
            }
        }

        $result = TrainingLevelTable::addTrainingLevel($trainingLevel);

        if ($result === false) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Tạo mới " . Constant::objectNames['trainingLevel'] . " không thành công"
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
     * getTrainingLevelById()
     *
     * HOW-TO-DO: des
     * @param $param
     */
    public function getTrainingLevelById($param)
    {
        $id = $param['id'];
        $result = TrainingLevelTable::getById($id);

        if ($result !== null) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['trainingLevel']
            ));
        }
    }

    /**
     * API
     * updateTrainingLevelById()
     *
     * HOW-TO-DO: des
     * @param $param
     */
    public function updateTrainingLevelById($param)
    {
        $id = $param['id'];

        switch ($_SESSION['role']) {
            case 0:
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

        $try = array();
        if (property_exists($obj, 'name')) $try['name'] = $obj->name;
        if (property_exists($obj, 'levelType')) $try['levelType'] = $obj->levelType;
        $trainingLevel = new TrainingLevel($try);

        if (isset($try['name']) && $try['name'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Tên " . Constant::objectNames['trainingLevel'] . Constant::notEmptyText
            ));
            return;
        } else if (isset($try['levelType']) && $try['levelType'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['levelType']) . Constant::notEmptyText
            ));
            return;
        } else {
            if (!$trainingLevel->checkName()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => "Tên " . Constant::objectNames['trainingLevel'] . Constant::invalidText
                ));
                return;
            } else if (!$trainingLevel->checkLevelType()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames['levelType']) . Constant::invalidText
                ));
                return;
            }
        }

        TrainingLevelTable::updateById($id, $trainingLevel);
        http_response_code(200);
        echo json_encode(array(
            'message' => Constant::success
        ));
    }

    /**
     * API
     * deleteTrainingLevelById()
     *
     * HOW-TO-DO: des
     * @param $param
     */
    public function deleteTrainingLevelById($param)
    {
        $id = $param['id'];

        $result = TrainingLevelTable::deleteById($id);

        if ($result['rowCount']) {
            http_response_code(204);
        } else {
            if($result['data']['1'] && $result['data']['2']){
                http_response_code(400);
                echo json_encode(array(
                    'message' => Constant::cannotDelete . Constant::objectNames['trainingLevel']
                ));
            } else {
                http_response_code(404);
                echo json_encode(array(
                    'message' => Constant::notFoundText . Constant::objectNames['trainingLevel']
                ));
            }
        }
    }
}
