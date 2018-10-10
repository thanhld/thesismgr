<?php
namespace faculty\controller;

use core\model\DepartmentTable;
use core\model\OfficerTable;
use core\model\TrainingAreaTable;
use core\model\TrainingLevelTable;
use core\model\TrainingProgram;
use core\model\TrainingProgramTable;
use core\model\TrainingTypeTable;
use core\utility\Middleware;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/OfficerTable.php';
require_once 'src/core/model/DepartmentTable.php';
require_once 'src/core/model/TrainingProgramTable.php';
require_once 'src/core/model/TrainingProgram.php';
require_once 'src/core/model/TrainingAreaTable.php';
require_once 'src/core/model/TrainingTypeTable.php';
require_once 'src/core/model/TrainingLevelTable.php';
require_once 'src/core/utility/Middleware.php';
require_once 'src/core/utility/Paging.php';

/**
 * ProgramController
 */
class TrainingProgramController
{

    /**
     * API
     * getTrainingProgram()
     *
     * HOW-TO-DO: des
     */
    public function getTrainingProgram()
    {
        $facultyId = $_SESSION['facultyId'];

        //$option = Paging::normalizeOption($_GET);
        $result = TrainingProgramTable::get($facultyId);
        //$result = Paging::genNextPrev($option, $result);

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * addTrainingProgram()
     *
     * HOW-TO-DO: des
     */
    public function addTrainingProgram()
    {
        switch ($_SESSION['role']) {
            case 1:
                $facultyId = $_SESSION['uid'];
                break;
            case 4:
                $facultyId = $_SESSION['facultyId'];
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

        $prg = array();
        if (property_exists($obj, 'departmentId')) $prg['departmentId'] = $obj->departmentId;
        if (property_exists($obj, 'trainingAreasId')) $prg['trainingAreasId'] = $obj->trainingAreasId;
        if (property_exists($obj, 'trainingLevelsId')) $prg['trainingLevelsId'] = $obj->trainingLevelsId;
        if (property_exists($obj, 'trainingTypesId')) $prg['trainingTypesId'] = $obj->trainingTypesId;
        if (property_exists($obj, 'programCode')) $prg['programCode'] = $obj->programCode;
        if (property_exists($obj, 'name')) $prg['name'] = $obj->name;
        if (property_exists($obj, 'vietnameseThesisTitle')) $prg['vietnameseThesisTitle'] = $obj->vietnameseThesisTitle;
        if (property_exists($obj, 'englishThesisTitle')) $prg['englishThesisTitle'] = $obj->englishThesisTitle;
        if (property_exists($obj, 'startTime')) $prg['startTime'] = $obj->startTime;
        if (property_exists($obj, 'trainingDuration')) $prg['trainingDuration'] = $obj->trainingDuration;
        if (property_exists($obj, 'isInUse')) $prg['isInUse'] = $obj->isInUse;
        if (property_exists($obj, 'thesisNormalizedFactor')) $prg['thesisNormalizedFactor'] = $obj->thesisNormalizedFactor;
        $program = new TrainingProgram($prg);

        if (!isset($prg['departmentId']) || $prg['departmentId'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::objectNames['department']) . Constant::isRequiredText
            ));
            return;
        } elseif (!isset($prg['trainingAreasId']) || $prg['trainingAreasId'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['trainingAreasId']) . Constant::isRequiredText
            ));
            return;
        } elseif (!isset($prg['trainingLevelsId']) || $prg['trainingLevelsId'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['trainingLevelsId']) . Constant::isRequiredText
            ));
            return;
        } elseif (!isset($prg['trainingTypesId']) || $prg['trainingTypesId'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['trainingTypesId']) . Constant::isRequiredText
            ));
            return;
        } elseif (!isset($prg['programCode']) || $prg['programCode'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['programCode']) . Constant::isRequiredText
            ));
            return;
        } elseif (!isset($prg['name']) || $prg['name'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Tên " . ucfirst(Constant::objectNames['trainingProgram']) . Constant::isRequiredText
            ));
            return;
        } elseif (!isset($prg['vietnameseThesisTitle']) || $prg['vietnameseThesisTitle'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['vietnameseThesisTitle']) . Constant::isRequiredText
            ));
            return;
        } elseif (!isset($prg['trainingDuration']) || $prg['trainingDuration'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['trainingDuration']) . Constant::isRequiredText
            ));
            return;
        } elseif (!isset($prg['isInUse']) || $prg['isInUse'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Trạng thái chương trình đào tạo " . Constant::isRequiredText
            ));
            return;
        } else {
            foreach ($prg as $key => $value) {
                $action = 'check' . ucfirst($key);
                if (!$program->$action()) {
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                    ));
                    return;
                }
            }

            $pFacultyId = DepartmentTable::getFacultyIdOf($prg['departmentId']);
            if ($pFacultyId == null) {
                http_response_code(400);
                echo json_encode(array(
                    'error' => ucfirst(Constant::objectNames['department']) . Constant::notExistedText
                ));
                return;
            } elseif ($pFacultyId != $facultyId) {
                http_response_code(400);
                echo json_encode(array(
                    'error' => ucfirst(Constant::objectNames['department']) . " không thuộc quyền quản lý của Khoa"
                ));
                return;
            }

            $traFacultyId = TrainingAreaTable::getFacultyIdOf($prg['trainingAreasId']);
            if ($traFacultyId == null) {
                http_response_code(400);
                echo json_encode(array(
                    'error' => ucfirst(Constant::objectNames['trainingArea']) . Constant::notExistedText
                ));
                return;
            } elseif ($traFacultyId != $facultyId) {
                http_response_code(400);
                echo json_encode(array(
                    'error' => ucfirst(Constant::objectNames['trainingArea']) . " không thuộc quyền quản lý của Khoa"
                ));
                return;
            }

            $trainingType = TrainingTypeTable::getById($prg['trainingTypesId']);
            if(!$trainingType){
                http_response_code(400);
                echo json_encode(array(
                    'error' => ucfirst(Constant::objectNames['trainingType']) . Constant::notExistedText
                ));
                return;
            }

            $trainingLevel = TrainingLevelTable::getById($prg['trainingLevelsId']);
            if(!$trainingLevel){
                http_response_code(400);
                echo json_encode(array(
                    'error' => ucfirst(Constant::objectNames['trainingLevel']) . Constant::notExistedText
                ));
                return;
            }

            $result = TrainingProgramTable::addProgram($program);

            if ($result === false) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames['programCode']) . Constant::isExistedText
                ));
                return;
            } else {
                http_response_code(201);
                echo json_encode(array(
                    'id' => $result
                ));
            }
        }
    }

    /**
     * API
     * getTrainingProgramById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function getTrainingProgramById($param)
    {
        $facultyId = $_SESSION['facultyId'];

        $id = $param['id'];

        $result = TrainingProgramTable::getById($id, $facultyId);

        if ($result !== null) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['trainingProgram']
            ));
        }
    }

    /**
     * API
     * updateTrainingProgramById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function updateTrainingProgramById($param)
    {
        $id = $param['id'];
        $facultyId = TrainingProgramTable::getFacultyIdOf($id);

        if ($facultyId == null) {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['trainingProgram']
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

        $json = file_get_contents('php://input');
        $obj = json_decode($json);

        $prg = array();
        if (property_exists($obj, 'departmentId')) $prg['departmentId'] = $obj->departmentId;
        if (property_exists($obj, 'trainingAreasId')) $prg['trainingAreasId'] = $obj->trainingAreasId;
        if (property_exists($obj, 'trainingLevelsId')) $prg['trainingLevelsId'] = $obj->trainingLevelsId;
        if (property_exists($obj, 'trainingTypesId')) $prg['trainingTypesId'] = $obj->trainingTypesId;
        if (property_exists($obj, 'programCode')) $prg['programCode'] = $obj->programCode;
        if (property_exists($obj, 'name')) $prg['name'] = $obj->name;
        if (property_exists($obj, 'vietnameseThesisTitle')) $prg['vietnameseThesisTitle'] = $obj->vietnameseThesisTitle;
        if (property_exists($obj, 'englishThesisTitle')) $prg['englishThesisTitle'] = $obj->englishThesisTitle;
        if (property_exists($obj, 'startTime')) $prg['startTime'] = $obj->startTime;
        if (property_exists($obj, 'trainingDuration')) $prg['trainingDuration'] = $obj->trainingDuration;
        if (property_exists($obj, 'isInUse')) $prg['isInUse'] = $obj->isInUse;
        if (property_exists($obj, 'thesisNormalizedFactor')) $prg['thesisNormalizedFactor'] = $obj->thesisNormalizedFactor;
        $program = new TrainingProgram($prg);

        if (isset($prg['departmentId']) && $prg['departmentId'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::objectNames['department']) . Constant::notEmptyText
            ));
            return;
        }
        if (isset($prg['trainingAreasId']) && $prg['trainingAreasId'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' =>ucfirst(Constant::objectNames['trainingArea']) . Constant::notEmptyText
            ));
            return;
        }
        if (isset($prg['trainingLevelsId']) && $prg['trainingLevelsId'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::objectNames['trainingLevel']) . Constant::notEmptyText
            ));
            return;
        }
        if (isset($prg['trainingTypesId']) && $prg['trainingTypesId'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' =>  ucfirst(Constant::objectNames['trainingType']) . Constant::notEmptyText
            ));
            return;
        }
        if (isset($prg['programCode']) && $prg['programCode'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['programCode']) . Constant::notEmptyText
            ));
            return;
        }
        if (isset($prg['name']) && $prg['name'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Tên " . ucfirst(Constant::objectNames['trainingProgram']) . Constant::notEmptyText
            ));
            return;
        }
        if (isset($prg['vietnameseThesisTitle']) && $prg['vietnameseThesisTitle'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['vietnameseThesisTitle']) . Constant::notEmptyText
            ));
            return;
        }
        if (isset($prg['trainingDuration']) && $prg['trainingDuration'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['trainingDuration']) . Constant::notEmptyText
            ));
            return;
        }
        if (isset($prg['isInUse']) && $prg['isInUse'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Trạng thái chương trình đào tạo " . Constant::notEmptyText
            ));
            return;
        }

        foreach ($prg as $key => $value) {
            $action = 'check' . ucfirst($key);
            if (!$program->$action()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                ));
                return;
            }
        }

        if(isset($prg['departmentId'])){
            $pFacultyId = DepartmentTable::getFacultyIdOf($prg['departmentId']);

            if ($pFacultyId == null) {
                http_response_code(400);
                echo json_encode(array(
                    'error' => ucfirst(Constant::objectNames['department']) . Constant::notExistedText
                ));
                return;
            } elseif ($pFacultyId != $facultyId) {
                http_response_code(400);
                echo json_encode(array(
                    'error' => ucfirst(Constant::objectNames['department']) . " không thuộc quyền quản lý của Khoa"
                ));
                return;
            }
        }

        if(isset($prg['trainingAreasId'])){
            $traFacultyId = TrainingAreaTable::getFacultyIdOf($prg['trainingAreasId']);

            if ($traFacultyId == null) {
                http_response_code(400);
                echo json_encode(array(
                    'error' => ucfirst(Constant::objectNames['trainingArea']) . Constant::notExistedText
                ));
                return;
            } elseif ($traFacultyId != $facultyId) {
                http_response_code(400);
                echo json_encode(array(
                    'error' => ucfirst(Constant::objectNames['trainingArea']) . " không thuộc quyền quản lý của Khoa"
                ));
                return;
            }
        }

        if(isset($prg['trainingTypesId'])){
            $trainingType = TrainingTypeTable::getById($prg['trainingTypesId']);

            if(!$trainingType){
                http_response_code(400);
                echo json_encode(array(
                    'error' => ucfirst(Constant::objectNames['trainingType']) . Constant::notExistedText
                ));
                return;
            }
        }

        if(isset($prg['trainingLevelsId'])){
            $trainingLevel = TrainingLevelTable::getById($prg['trainingLevelsId']);

            if(!$trainingLevel){
                http_response_code(400);
                echo json_encode(array(
                    'error' => ucfirst(Constant::objectNames['trainingLevel']) . Constant::notExistedText
                ));
                return;
            }
        }

        $result = TrainingProgramTable::updateById($id, $prg);

        if ($result['rowCount']) {
            http_response_code(200);
            echo json_encode(array(
                'message' => Constant::success
            ));
        } else {
            if($result['data']['1'] && $result['data']['2']){
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames['programCode']) . Constant::isExistedText
                ));
            } else {
               http_response_code(200);
                echo json_encode(array(
                    'message' => Constant::success
                ));
            }
        }
    }

    /**
     * API
     * deleteTrainingProgramById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function deleteTrainingProgramById($param)
    {
        $id = $param['id'];
        $facultyId = TrainingProgramTable::getFacultyIdOf($id);

        if ($facultyId == null) {
            http_response_code(404);
            echo json_encode(array(
                'message' => 'Program not found'
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

        $result = TrainingProgramTable::deleteById($id);

        if ($result['rowCount']) {
            http_response_code(204);
        } else {
            if($result['data']['1'] && $result['data']['2']){
                http_response_code(400);
                echo json_encode(array(
                    'message' => Constant::cannotDelete . Constant::objectNames['trainingProgram']
                ));
            } else {
                http_response_code(404);
                echo json_encode(array(
                    'message' => Constant::notFoundText . Constant::objectNames['trainingProgram']
                ));
            }
        }
    }
}
