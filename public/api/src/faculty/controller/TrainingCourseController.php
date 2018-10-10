<?php
namespace faculty\controller;
use core\model\LearnerTable;
use core\model\OfficerTable;
use core\model\TrainingProgramTable;
use core\model\TrainingCourse;
use core\model\TrainingCourseTable;
use core\utility\Middleware;
use core\utility\Paging;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/OfficerTable.php';
require_once 'src/core/model/LearnerTable.php';
require_once 'src/core/model/TrainingCourseTable.php';
require_once 'src/core/model/TrainingProgramTable.php';
require_once 'src/core/model/TrainingCourse.php';
require_once 'src/core/utility/Middleware.php';
require_once 'src/core/utility/Paging.php';

/**
 * TrainingCourseController
 */
class TrainingCourseController
{
    /**
     * API
     * getTrainingCourse()
     *
     * HOW-TO-DO: des
     */
    public function getTrainingCourse()
    {
        $facultyId = $_SESSION['facultyId'];

        $option = Paging::normalizeOption($_GET);
        $result = TrainingCourseTable::get($option, $facultyId);
        //$result = Paging::genNextPrev($option, $result);

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * addTrainingCourse()
     *
     * HOW-TO-DO: des
     */
    public function addTrainingCourse()
    {
        switch ($_SESSION['role']) {
            case 1:
                $facultyId = $_SESSION['uid'];
                break;
            case 4:
                $facultyId = OfficerTable::getFacultyIdOf($_SESSION['uid']);
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

        $trc = array();
        if (property_exists($obj, 'trainingProgramId')) $trc['trainingProgramId'] = $obj->trainingProgramId;
        if (property_exists($obj, 'courseCode')) $trc['courseCode'] = $obj->courseCode;
        if (property_exists($obj, 'courseName')) $trc['courseName'] = $obj->courseName;
        if (property_exists($obj, 'admissionYear')) $trc['admissionYear'] = $obj->admissionYear;
        if (property_exists($obj, 'isCompleted')) $trc['isCompleted'] = $obj->isCompleted;
        $trainingCourse = new TrainingCourse($trc);

        if (!isset($trc['trainingProgramId'])) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::objectNames['trainingProgram']) . Constant::isRequiredText
            ));
            return;
        } elseif (!isset($trc['courseCode']) || $trc['courseCode'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['courseCode']) . Constant::isRequiredText
            ));
            return;
        } elseif (!isset($trc['courseName']) || $trc['courseName'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['courseName']) . Constant::isRequiredText
            ));
            return;
        } elseif (!isset($trc['admissionYear']) || $trc['admissionYear'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['admissionYear']) . Constant::isRequiredText
            ));
            return;
        } elseif (!isset($trc['isCompleted'])) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['isCompleted']) . Constant::isRequiredText
            ));
            return;
        } else {
            foreach ($trc as $key => $value) {
                $action = 'check' . ucfirst($key);
                if (!$trainingCourse->$action()) {
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                    ));
                    return;
                }
            }

            $pFacultyId = TrainingProgramTable::getFacultyIdOf($trc['trainingProgramId']);
            if ($facultyId !== $pFacultyId) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::objectNames['trainingProgram']) . " không thuộc quyền quản lý của Khoa"
                ));
                return;
            }

            // Valid now, add trainingCourse
            $result = TrainingCourseTable::addTrainingCourse($trainingCourse);

            if($result === false){
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames['courseCode']) . Constant::isExistedText
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
     * getTrainingCourseById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function getTrainingCourseById($param)
    {
        switch ($_SESSION['role']) {
            case 1:
                $facultyId = $_SESSION['uid'];
                break;
            case 2:
                $facultyId = LearnerTable::getFacultyIdOf($_SESSION['uid']);
                break;
            case 3:
            case 4:
            case 5:
            case 6:
                $facultyId = OfficerTable::getFacultyIdOf($_SESSION['uid']);
                break;
            default:
                $facultyId = null;
                break;
        }

        $id = $param['id'];

        $result = TrainingCourseTable::getById($id, $facultyId);

        if ($result !== null) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['trainingCourse']
            ));
        }
    }

    /**
     * API
     * updateTrainingCourseById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function updateTrainingCourseById($param)
    {
        $id = $param['id'];
        $facultyId = TrainingCourseTable::getFacultyIdOf($id);

        if ($facultyId == null) {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['trainingCourse']
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

        $trc = array();
        if (property_exists($obj, 'courseCode')) $trc['courseCode'] = $obj->courseCode;
        if (property_exists($obj, 'courseName')) $trc['courseName'] = $obj->courseName;
        if (property_exists($obj, 'admissionYear')) $trc['admissionYear'] = $obj->admissionYear;
        if (property_exists($obj, 'isCompleted')) $trc['isCompleted'] = $obj->isCompleted;
        $trainingCourse = new TrainingCourse($trc);

        if (isset($trc['courseCode']) && $trc['courseCode'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['courseCode']) . Constant::notEmptyText
            ));
            return;
        }

        if (isset($trc['courseName']) && $trc['courseName'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['courseName']) . Constant::notEmptyText
            ));
            return;
        }

        if (isset($trc['admissionYear']) && $trc['admissionYear'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['admissionYear']) . Constant::notEmptyText
            ));
            return;
        }

        foreach ($trc as $key => $value) {
            $action = 'check' . ucfirst($key);
            if (!$trainingCourse->$action()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                ));
                return;
            }
        }

        TrainingCourseTable::updateById($id, $trc);
        http_response_code(200);
        echo json_encode(array(
            'message' => Constant::success
        ));
    }

    /**
     * API
     * deleteTrainingCourseById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function deleteTrainingCourseById($param)
    {
        $id = $param['id'];
        $facultyId = TrainingCourseTable::getFacultyIdOf($id);

        if ($facultyId == null) {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['trainingCourse']
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

        $result = TrainingCourseTable::deleteById($id);

        if ($result['rowCount']) {
            http_response_code(204);
        } else {
            if($result['data']['1'] && $result['data']['2']){
                http_response_code(400);
                echo json_encode(array(
                    'message' => Constant::cannotDelete . Constant::objectNames['trainingCourse']
                ));
            } else {
                http_response_code(404);
                echo json_encode(array(
                    'message' => Constant::notFoundText . Constant::objectNames['trainingCourse']
                ));
            }
        }
    }
}
