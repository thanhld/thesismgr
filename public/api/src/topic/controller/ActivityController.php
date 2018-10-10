<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2/27/2017
 * Time: 08:38 PM
 */

namespace topic\controller;

use core\model\Activity;
use core\model\ActivityTable;
use core\model\AttachmentTable;
use core\model\DocumentTable;
use core\model\OfficerTable;
use core\model\OutOfficerTable;
use core\model\QuotaTable;
use core\model\StepTable;
use core\model\Topic;
use core\model\TopicChangeTable;
use core\model\TopicHistoryTable;
use core\model\TopicTable;
use topic\controller\TopicController;
use department\controller\DepartmentTopicController;
use core\utility\Constant;
use core\utility\Middleware;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/utility/Middleware.php';
require_once 'src/core/model/Activity.php';
require_once 'src/core/model/ActivityTable.php';
require_once 'src/core/model/AttachmentTable.php';
require_once 'src/core/model/DocumentTable.php';
require_once 'src/core/model/StepTable.php';
require_once 'src/core/model/TopicTable.php';
require_once 'src/core/model/QuotaTable.php';
require_once 'src/core/model/TopicChangeTable.php';
require_once 'src/core/model/OutOfficerTable.php';
require_once 'src/department/controller/DepartmentTopicController.php';
require_once 'src/topic/controller/TopicController.php';

class ActivityController
{
    /**
     * API
     * getTopicActivities()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function getTopicActivities($param)
    {
        $id = $param['topicId'];

        $learnerId = TopicTable::getLearnerIdOf($id);

        if(!$learnerId) {
            http_response_code(404);
            echo json_encode(array(
                'message' =>  Constant::notFoundText . ucfirst(Constant::objectNames['topic'])
            ));
            return;
        }

        $result = ActivityTable::getActivities($id);

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * createActivity()
     *
     * HOW-TO-DO: des
     */
    public function createActivity()
    {
        $facultyId = $_SESSION['facultyId'];

        $json = file_get_contents('php://input');
        $objs = json_decode($json);

        if (!is_array($objs) || count($objs) == 0) {
           http_response_code(400);
            echo json_encode(array(
                'message' => Constant::emptyList
            ));
            return;
        }

        $ret = array();

        //true when sendmail action is needed
        $needSendingMail = false;

        foreach ($objs as $obj) {
            $stepId = property_exists($obj, 'stepId') ? intval($obj->stepId) : null;
            $topicId = property_exists($obj, 'topicId') ? $obj->topicId : null;

            $act = array();
            $tp = array();
            $expertiseOfficerIds = null;
            $data = null;
            if (property_exists($obj, 'documentId')) $act['documentId'] = $obj->documentId;
            if (property_exists($obj, 'requestedSupervisorId')) $act['requestedSupervisorId'] = $obj->requestedSupervisorId;
            if (property_exists($obj, 'expertiseOfficerIds')) $expertiseOfficerIds = $obj->expertiseOfficerIds;
            if (property_exists($obj, 'newSupervisorIds')) {
                if($obj->newSupervisorIds == null){
                    $newSupervisorIds = array();
                } else {
                    $newSupervisorIds = explode(',', $obj->newSupervisorIds);
                }
            }
            if (property_exists($obj, 'oldSupervisorIds')) {
                if($obj->oldSupervisorIds == null) {
                    $oldSupervisorIds = array();
                } else {
                    $oldSupervisorIds = explode(',', $obj->oldSupervisorIds);                    
                }
            }
            if (property_exists($obj, 'data')) {
                $data = $obj->data;
            }
            $act['accountId'] = $_SESSION['uid'];

            if($stepId == null){
                $ret[] = array(
                    'topicId' => $topicId,
                    'error' => ucfirst(Constant::objectNames['step']) . Constant::isRequiredText
                );
                continue;
            } else { $act['stepId'] = $stepId; }

            $step = StepTable::getById($stepId);
            if(!$step){
                $ret[] = array(
                    'topicId' => $topicId,
                    'error' => Constant::notFoundText . Constant::objectNames['step']
                );
                continue;
            }

            if($topicId == null) {
                $ret[] = array(
                    'topicId' => $topicId,
                    'error' => ucfirst(Constant::objectNames['topic']) . Constant::isRequiredText
                );
                continue;
            } else { $tp['topicId'] = $topicId; }

            //Check Topic existing
            $oldTopic = TopicTable::getById($topicId);
            if(!$oldTopic) {
                $ret[] = array(
                    'topicId' => $topicId,
                    'error' => Constant::notFoundText . ucfirst(Constant::objectNames['topic'])
                );
                continue;
            }

            //Get current topic status
            $oldStatus = $oldTopic->getTopicStatus();

            //status of editing topic
            $editTopicSuccess = false;

            $unknownStepId = false;

            $firstNumOfStepId = (strlen((string)$stepId) == 3) ? intval($stepId/100) : intval($stepId/1000);

            //TEMP variable
            $notDepartment = true;
            switch($firstNumOfStepId) {
                case 5:
                    /* Parameter:
                        - old topic
                        - stepId
                        - activity
                        - data
                    */
                    $departmentRet = self::departmentCreateActivity($stepId, $oldTopic, $act, $data, $expertiseOfficerIds);
                    if($departmentRet) {
                        $ret[] = $departmentRet;
                    }
                    $notDepartment = false;
                    continue;
                default:
                    break;
            }
            
            if($notDepartment) {
                //HANDLE DATA
                if($data){
                    if(count((array)$data) == 0){
                        $ret[] = array(
                            'topicId' => $topicId,
                            'error' => 'Trường dữ liệu gửi lên không hợp lệ'
                        );
                        continue;
                    }

                    //Handle activity for each stepCode
                    switch($stepId){
                        case 100: //Learner register topic
                            if(!$act['requestedSupervisorId']){
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => ucfirst(Constant::columnNames['requestedSupervisorId']) . Constant::isRequiredText
                                );
                                continue;
                            }
                            $registerRet = TopicController::learnerRegisterTopic($topicId,$act['requestedSupervisorId'],$data);
                            $editTopicSuccess = $registerRet['success'];
                            if(!$editTopicSuccess) {
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => $registerRet['message']
                                );
                                continue;
                            } else {
                                $newOutOfficerIds = $registerRet['outOfficerIds'];
                            }
                            break;
                        case 101: //Learner request change topic
                            if(!$act['requestedSupervisorId']){
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => ucfirst(Constant::columnNames['requestedSupervisorId']) . Constant::isRequiredText
                                );
                                continue;
                            }
                            $learnerChangeRet = TopicController::learnerAddRequestChange($topicId,$act['requestedSupervisorId'],$data);
                            $editTopicSuccess = $learnerChangeRet['success'];
                            if(!$editTopicSuccess) {
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => $learnerChangeRet['message']
                                );
                                continue;
                            } else {
                                $newOutOfficerIds = $learnerChangeRet['outOfficerIds'];
                            }
                            break;
                        case 102:
                        case 103:
                        case 104: //Learner request pause topic
                            $learnerChangeRet = TopicController::learnerAddRequest($stepId, $topicId, $data, $oldTopic->getMainSupervisorId());
                            $editTopicSuccess = $learnerChangeRet['success'];
                            if(!$editTopicSuccess) {
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => $learnerChangeRet['message']
                                );
                                continue;
                            }
                            break;
                        case 300:
                            //University accept topic
                            $changeRet = TopicController::universityAcceptRegisterTopic($topicId, $data);
                            $editTopicSuccess = $changeRet['success'];
                            if(!$editTopicSuccess){
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => $changeRet['message']
                                );
                                continue;
                            }
                            break;
                        case 210:
                        case 212:
                            //Admin update supervisorIds for topic
                            $updateIds = TopicController::adminUpdateSupervisorIds($stepId, $topicId, $data);
                            $editTopicSuccess = $updateIds['success'];
                            if(!$editTopicSuccess){
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => $updateIds['message']
                                );
                                continue;
                            }
                            break;
                        case 2022:
                            //admin update request delay deadline topic
                            $adminChangeRet = TopicController::adminUpdateRequestDelayTopic($topicId, $data);
                            $editTopicSuccess = $adminChangeRet['success'];
                            if(!$editTopicSuccess) {
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => $adminChangeRet['message']
                                );
                                continue;
                            }
                            break;
                        case 2042:
                            //admin update request pause topic
                            $adminChangeRet = TopicController::adminUpdateRequestPauseTopic($topicId, $data);
                            $editTopicSuccess = $adminChangeRet['success'];
                            if(!$editTopicSuccess) {
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => $adminChangeRet['message']
                                );
                                continue;
                            }
                            break;
                        case 4002:
                            //Lecturer update topic
                            $updateResult = TopicController::lecturerUpdateTopic($topicId, $act['requestedSupervisorId'], $data);
                            $editTopicSuccess = $updateResult['success'];
                            if(!$editTopicSuccess){
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => $updateResult['message']
                                );
                                continue;
                            }
                            break;
                        default:
                            $unknownStepId = true;
                            break;
                    }
                } else {
                    switch($stepId){
                        case 105: //Learner register protecting topic
                            $learnerProtectRet = TopicController::learnerRegisterProtectTopic($topicId, $oldTopic->getMainSupervisorId());
                            $editTopicSuccess = $learnerProtectRet['success'];
                            if(!$editTopicSuccess) {
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => $learnerProtectRet['message']
                                );
                                continue;
                            }
                            break;
                        case 106: //Learner register seminar
                            $learnerSeminarRet = TopicController::learnerRegisterSeminar($topicId, $oldTopic->getMainSupervisorId());
                            $editTopicSuccess = $learnerSeminarRet['success'];
                            if(!$editTopicSuccess) {
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => $learnerSeminarRet['message']
                                );
                                continue;
                            }
                            break;
                        case 301:
                            //University accept topic request
                            $uniAcceptResult = TopicController::universityAcceptRequestChange($topicId, $oldTopic);
                            $editTopicSuccess = $uniAcceptResult['success'];
                            if($editTopicSuccess == false){
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => $uniAcceptResult['message']
                                );
                                continue;
                            } else {
                                $newTopic = $uniAcceptResult['newTopic'];
                                $topicHistoryId = $uniAcceptResult['historyId'];
                            }
                            break;
                        case 302:
                        case 303:
                        case 304:
                            //University accept request pause
                            $uniAcceptResult = TopicController::universityAcceptRequest($topicId, $stepId);
                            $editTopicSuccess = $uniAcceptResult['success'];
                            if(!$editTopicSuccess){
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => $uniAcceptResult['message']
                                );
                                continue;
                            } else {
                                $topicHistoryId = $uniAcceptResult['historyId'];
                            }
                            break;
                        //Admin accept topic request
                        case 200:
                        case 2011:
                        case 2021:
                        case 2031:
                        case 2041:
                        case 2049:
                        case 2051:
                        case 2052:
                        case 2081:
                        case 305:   //University accept register protect topic
                        case 2090:  //Admin grant permission to register seminar
                            if($stepId == 200 || $stepId == 2011) {
                                if($oldTopic->getOutOfficerIds() != null){
                                    $ret[] = array(
                                        'topicId' => $topicId,
                                        'error' => "Đề tài đang chờ chuyên viên phê duyệt giảng viên ngoài"
                                    );
                                    continue;
                                }
                            }

                            $adminAcceptResult = TopicController::adminAcceptRequest($topicId, $stepId, $oldTopic);
                            $editTopicSuccess = $adminAcceptResult['success'];
                            if($editTopicSuccess == false){
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => $adminAcceptResult['message']
                                );
                                continue;
                            } else {
                                if($stepId == 2090) {
                                    $needSendingMail = true;
                                }
                            }
                            break;
                        case 2091: //Admin close seminar
                        case 2092:
                            $checkedTopicIds = property_exists($obj, 'checkedTopicIds') ? $obj->checkedTopicIds : null;
                            $checkedTopicIds = explode(',', $checkedTopicIds);
                            $adminCloseSeminarRet = TopicController::adminCloseSeminar($stepId, $topicId, $checkedTopicIds);
                            $editTopicSuccess = $adminCloseSeminarRet['success'];
                            if($editTopicSuccess == false) {
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => $adminCloseSeminarRet['message']
                                );
                                continue;
                            }
                            break;
                        //Supervisor accept topic request
                        case 4001:
                        case 4011:
                        case 4021:
                        case 4031:
                        case 4041:
                        case 4051:
                        case 4061:
                            if(!$act['requestedSupervisorId']){
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => ucfirst(Constant::columnNames['requestedSupervisorId']) . Constant::isRequiredText
                                );
                                continue;
                            } elseif(!isset($newSupervisorIds)){
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => ucfirst(Constant::columnNames['newSupervisorIds']) . Constant::isRequiredText
                                );
                                continue;
                            } elseif(!isset($oldSupervisorIds)){
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => ucfirst(Constant::columnNames['oldSupervisorIds']) . Constant::isRequiredText
                                );
                                continue;
                            }

                            $supervisorIds = array();
                            $supervisorIds['requestedSupervisorId'] = $act['requestedSupervisorId'];
                            $supervisorIds['newSupervisorIds'] = $newSupervisorIds;
                            $supervisorIds['oldSupervisorIds'] = $oldSupervisorIds;

                            $lecturerAcceptResult = TopicController::lecturerAcceptRequest($topicId, $supervisorIds, $stepId);
                            $editTopicSuccess = $lecturerAcceptResult['success'];
                            $maxLearnerQuota = $lecturerAcceptResult['maxLearner'];
                            if($editTopicSuccess == false)
                            {
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'supervisorId' => $lecturerAcceptResult['supervisorId'],
                                    'error' => $lecturerAcceptResult['message']
                                );
                                continue;
                            }
                            break;
                        //Admin deny request
                        case 2010:
                        case 2020:
                        case 2030:
                        case 2040:
                        case 2050:
                        case 2080:
                        case 2082:
                        case 2100:
                            if($stepId == 2010){
                                if(!isset($newSupervisorIds)){
                                    $ret[] = array(
                                        'topicId' => $topicId,
                                        'error' => ucfirst(Constant::columnNames['newSupervisorIds']) . Constant::isRequiredText
                                    );
                                    continue;
                                } elseif(!isset($oldSupervisorIds)){
                                    $ret[] = array(
                                        'topicId' => $topicId,
                                        'error' => ucfirst(Constant::columnNames['oldSupervisorIds']) . Constant::isRequiredText
                                    );
                                    continue;
                                }
                            }

                            $adminDenyResult = TopicController::adminDenyRequest($topicId, $stepId);
                            $editTopicSuccess = $adminDenyResult['success'];
                            if(!$editTopicSuccess) {
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => $adminDenyResult['message']
                                );
                                continue;
                            }
                            break;
                        //Supervisor deny request
                        case 4000:
                        case 4010:
                        case 4020:
                        case 4030:
                        case 4040:
                        case 4050:
                        case 4060:
                            $lecturerDenyResult = TopicController::lecturerDenyRequest($topicId, $act['requestedSupervisorId'], $stepId);
                            $editTopicSuccess = $lecturerDenyResult['success'];
                            if(!$editTopicSuccess) {
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => $lecturerDenyResult['message']
                                );
                                continue;
                            }
                            break;
                        default:
                            $unknownStepId = true;
                            break;
                    }
                }

                if($unknownStepId == true){
                    $ret[] = array(
                        'topicId' => $topicId,
                        'error' => ucfirst(Constant::objectNames['step']) . Constant::unknownText . " hoặc thiếu trường dữ liệu"
                    );
                    continue;
                }

                //Check valid value for activity
                $activity = new Activity($act);
                foreach ($act as $key => $value) {
                    $action = 'check' . ucfirst($key);
                    if (!$activity->$action()) {
                        $ret[] = array(
                            'topicId' => $topicId,
                            'error' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                        );
                        continue;
                    }
                }

                //CREATE ACTION
                if($editTopicSuccess) {
                    $actResult = ActivityTable::createActivity($activity);

                    if (!$actResult) {
                        //RESET TOPIC IF CREATE ACTION UNSUCCESSFULLY
                        switch ($stepId){
                            case 100: //Learner register topic
                                //Delete out officer records
                                $outOfficerIds = explode(',', $newOutOfficerIds);
                                foreach($outOfficerIds as $oofId){
                                    OutOfficerTable::deleteById($oofId);
                                }
                                $resetTopic = self::resetTopic();
                                break;
                            case 101: //Learner request change topic
                                TopicChangeTable::deleteById($topicId);
                                //Delete out officer records
                                $outOfficerIds = explode(',', $newOutOfficerIds);
                                foreach($outOfficerIds as $oofId){
                                    OutOfficerTable::deleteById($oofId);
                                }
                                $resetTopic = self::resetCommonTopicInfo($oldStatus, null, null);
                                $resetTopic['outOfficerIds'] = null;
                                break;
                            case 103: //Learner request cancel topic
                            case 102: //Learner request delay topic
                            case 104: //Learner request pause topic
                                TopicChangeTable::deleteById($topicId);
                                $resetTopic = self::resetCommonTopicInfo($oldStatus, null, null);
                                break;
                            case 105: //Learner request protecting topic
                            case 106:
                                $resetTopic = self::resetCommonTopicInfo($oldStatus, null, null);
                                break;
                            case 210: //Admin update supervisorIds for topic
                                $resetTopic = array();
                                $resetTopic['mainSupervisorId'] = $oldTopic->getMainSupervisorId();
                                $resetTopic['coSupervisorIds'] = $oldTopic->getCoSupervisorIds();
                                break;
                            case 212: //Admin update supervisorIds for topic change
                                $resetTopicChange = array();
                                $resetTopicChange['mainSupervisorId'] = null;
                                $resetTopicChange['coSupervisorIds'] = null;
                                break;
                            case 300: //University accept request register
                                $resetTopic = array();
                                $resetTopic['startDate'] = null;
                                $resetTopic['defaultDeadlineDate'] = null;
                                $resetTopic['deadlineDate'] = null;
                                $resetTopic['topicStatus'] = $oldStatus;
                                break;
                            case 301: //University accept request change
                                //Delete topic history
                                TopicHistoryTable::deleteById($topicHistoryId);
                                TopicTable::updateById($topicId, $oldTopic);
                                break;
                            case 303: //University accept request cancel
                            case 302: //University accept request delay
                            case 304: //University accept request pause
                                TopicHistoryTable::deleteById($topicHistoryId);
                                $resetTopic = self::resetCommonTopicInfo($oldStatus, null, $oldTopic->getDeadlineDate());
                                break;
                            case 305: //University accept request protecting topic
                                $resetTopic = array();
                                $resetTopic['topicStatus'] = $oldStatus;
                                break;
                            case 2022: //Admin update request delay
                                $topicChange = TopicChangeTable::getById($topicId);
                                $resetTopicChange = array();
                                $resetTopicChange['delayDuration'] = $topicChange->getDelayDuration();
                                break;
                            case 2042: //Admin update request pause
                                $topicChange = TopicChangeTable::getById($topicId);
                                $resetTopicChange = array();
                                $resetTopicChange['startPauseDate'] = $topicChange->getStartPauseDate();
                                $resetTopicChange['pauseDuration'] = $topicChange->getPauseDuration();
                                break;
                            case 2090:
                            case 2091: //Admin change topic status in seminar
                            case 2100: //Admin change topic status to 'can edit'
                                $resetTopic = self::resetCommonTopicInfo($oldStatus, null, null);
                                break;
                            default:  //Admin, Lecturer accept/deny request
                                $resetTopic = self::resetCommonTopicInfo($oldStatus, null, null);
                                break;
                        }

                        //remove document & attachent
                        if($stepId >= 300 && $stepId <= 305) {
                            if(isset($act['documentId'])){
                                //Remove uploaded attament from server
                                $oldAttm = AttachmentTable::getByDocumentId($act['documentId']);
                                $oldAttmUrl = $oldAttm->getUrl();
                                if(file_exists($oldAttmUrl)) {
                                    //chmod($oldAttmUrl, 0755);    //Change the file permissions if allowed
                                    unlink($oldAttmUrl);
                                }

                                //Remove document record from table
                                DocumentTable::deleteById($act['documentId']);
                            }
                        }

                        if(isset($resetTopic)) { TopicTable::updateById($topicId, $resetTopic); }
                        if(isset($resetTopicChange)) { TopicChangeTable::updateById($topicId, $resetTopicChange); }

                        $ret[] = array(
                            'topicId' => $topicId,
                            'error' => "Tạo hoạt động mới không thành công"
                        );
                        continue;
                    } else {
                        $removedActivity = false;

                        //HANDLE DENY ACTION AFTER ACTIVITY CREATED SUCCESSFULLY
                        switch($stepId){
                            case 210:   //Admin change supervisor
                            case 212:   //Admin change supervisor
                                //Delete all records on out_officers table
                                $outOfficerIds = $oldTopic->getOutOfficerIds();
                                $outOfficerIds = explode(',', $outOfficerIds);
                                foreach($outOfficerIds as $id){
                                    if($id != ''){
                                        OutOfficerTable::deleteById($id);
                                    }
                                }
                                $resetTopic = array();
                                $resetTopic['outOfficerIds'] = null;
                                TopicTable::updateById($topicId, $resetTopic);
                                break;
                            case 2010:  //Admin deny request change
                            case 2020:  //Admin deny request delay
                            case 2030:  //Admin deny request cancel
                            case 2040:  //Admin deny request pause
                                if($stepId == 2010){
                                    if(count($newSupervisorIds) != 0 || count($oldSupervisorIds) != 0) {
                                        $quota = self::getActiveQuotaVersion();
                                        if(!$quota){
                                            $ret[] = array(
                                                'topicId' => $topicId,
                                                'error' => "Không có phiên bản định mức nào đang có hiệu lực"
                                            );
                                            //Reset topic status
                                            $resetTopic = self::resetCommonTopicInfo($oldStatus, null, null);
                                            TopicTable::updateById($topicId, $resetTopic);
                                            //Delete activity
                                            ActivityTable::deleteById($actResult);
                                            $removedActivity = true;
                                        }
                                        else {
                                            $topicType = $oldTopic->getTopicType();
                                            if ($topicType == 1) { //Student
                                                $numLearner = 'numberOfStudent';
                                                $factorLearner = 'FactorStudent';
                                            } elseif ($topicType == 2) { //Graduated
                                                $numLearner = 'numberOfGraduated';
                                                $factorLearner = 'FactorGraduated';
                                            } else { //Researcher
                                                $numLearner = 'numberOfResearcher';
                                                $factorLearner = 'FactorResearcher';
                                            }

                                            $coSupervisorIds = explode(',', $oldTopic->getCoSupervisorIds());
                                            $mainSupervisorId = $oldTopic->getMainSupervisorId();
                                            $outOfficerIds = explode(',', $oldTopic->getOutOfficerIds());

                                            //get factor
                                            $mainFactorAction = 'getMain' . $factorLearner;
                                            $coFactorAction = 'getCo' . $factorLearner;
                                            $mainFactor = $quota->$mainFactorAction();
                                            $coFactor = $quota->$coFactorAction();

                                            $numCoSupervisors = 0;
                                            foreach($coSupervisorIds as $id){
                                                if($id != ''){
                                                    $numCoSupervisors += 1;
                                                }
                                            }

                                            //Reduce - revert quota
                                            $topicChange = TopicChangeTable::getById($topicId);
                                            $tpcCoSupervisorIds = explode(',', $topicChange->getCoSupervisorIds());
                                            $numTpcCoSupervisors = 0;
                                            for($i = 0; $i < count($outOfficerIds); $i++){
                                                if($i > 0 && $outOfficerIds[$i] != ''){
                                                    $numTpcCoSupervisors += 1;
                                                }
                                            }
                                            
                                            //CASE: no out officers
                                            if($numTpcCoSupervisors == 0) {
                                                foreach ($tpcCoSupervisorIds as $id) {
                                                    if ($id != ''){
                                                        if($id != 'del') {    //change co supervisor with specificed ids
                                                            $numTpcCoSupervisors += 1;
                                                        } 
                                                    } else {    //no change co supervisor
                                                        $numTpcCoSupervisors += 1;
                                                    }
                                                }
                                            }

                                            //CASE: has out officers
                                            else {
                                                $outOffIndex = 1;

                                                foreach($tpcCoSupervisorIds as $id) {
                                                    if($id != ''){
                                                        if($id != 'del' && $outOfficerIds[$outOffIndex] == '') {    //change co supervisor with specificed ids
                                                            $numTpcCoSupervisors += 1;
                                                        }
                                                    } else {
                                                        if($outOfficerIds[$outOffIndex] == '') {  //replace co supervisor with new out officer
                                                            $numTpcCoSupervisors += 1;
                                                        }
                                                    }

                                                    $outOffIndex += 1;
                                                }                                       
                                            }

                                            $totalOldSupervisors = $mainFactor + (($numTpcCoSupervisors == 0 && !$topicChange->getCoSupervisorIds())
                                                                    ? ($numCoSupervisors * $coFactor) //case no change co supervisor
                                                                    : ($numTpcCoSupervisors * $coFactor));

                                            //action: get number of learners
                                            $action = 'get' . ucfirst($numLearner);
                                            
                                            $oldPos = 0;
                                            foreach($oldSupervisorIds as $id){
                                                if($id != ''){
                                                    $newOfficer = OfficerTable::getById($id);
                                                    if($newOfficer) {
                                                        $oldNumber = $newOfficer->$action();
                                                        $newNumber = array();

                                                        if($oldPos == 0){
                                                            $factor = $mainFactor;
                                                        } else {
                                                            $factor = $coFactor;
                                                        }

                                                        $newNumber[$numLearner] = $oldNumber
                                                            - number_format(doubleval($factor/$totalOldSupervisors), 2, '.', '');
                                                        $newNumber[$numLearner] = ($newNumber[$numLearner] < 0) ? 0 : $newNumber[$numLearner];
                                                        OfficerTable::updateById($id, $newNumber);
                                                    }
                                                }

                                                $oldPos += 1;
                                            }

                                            //Increase number of learners
                                            $totalNewSupervisors = ((!$mainSupervisorId) ? 0 : $mainFactor) + ($numCoSupervisors * $coFactor);
                                            $newPos = 0;
                                            foreach ($newSupervisorIds as $id) {
                                                if($id != ''){
                                                    $newOfficers = OfficerTable::getById($id);
                                                    if($newOfficers) {
                                                        $oldNumber = $newOfficers->$action();
                                                        $newNumber = array();
                                                        if($newPos == 0){
                                                            $factor = $mainFactor;
                                                        } else {
                                                            $factor = $coFactor;
                                                        }

                                                        $newNumber[$numLearner] = $oldNumber
                                                            + number_format(doubleval($factor/$totalNewSupervisors), 2, '.', '');
                                                        OfficerTable::updateById($id, $newNumber);
                                                    }
                                                }

                                                $newPos += 1;
                                            }
                                        }
                                    }

                                    if(!$removedActivity) {
                                        //Delete out officer records
                                        $outOfficerIds = explode(',', $oldTopic->getOutOfficerIds());
                                        foreach ($outOfficerIds as $oofId) {
                                            OutOfficerTable::deleteById($oofId);
                                        }
                                        $resetTopic = array();
                                        $resetTopic['outOfficerIds'] = null;
                                        TopicTable::updateById($topicId, $resetTopic);
                                    }
                                }

                                //Delete topic change
                                if(!$removedActivity) {
                                    TopicChangeTable::deleteById($topicId);
                                }
                                break;
                            case 2080:  //Admin check topic is completed successfully
                            case 2081:  //Admin check topic is completed unsuccessfully
                                if(!self::revertQuota($oldTopic)){
                                    $ret[] = array(
                                        'topicId' => $topicId,
                                        'error' => "Không có phiên bản định mức nào đang có hiệu lực"
                                    );
                                    //Reset topic status
                                    $resetTopic = self::resetCommonTopicInfo($oldStatus, null, null);
                                    TopicTable::updateById($topicId, $resetTopic);
                                    //Delete activity
                                    ActivityTable::deleteById($actResult);
                                    $removedActivity = true;
                                }
                                break;
                            case 2082: //Admin change topic status when out of deadline
                                $topicStatus = $oldTopic->getTopicStatus();
                                if($topicStatus >= 102){
                                    if(!self::revertQuota($oldTopic)){
                                        $ret[] = array(
                                            'topicId' => $topicId,
                                            'error' => "Không có phiên bản định mức nào đang có hiệu lực"
                                        );
                                        //Reset topic status
                                        $resetTopic = self::resetCommonTopicInfo($oldStatus, null, null);
                                        TopicTable::updateById($topicId, $resetTopic);
                                        //Delete activity
                                        ActivityTable::deleteById($actResult);
                                        $removedActivity = true;
                                        break;
                                    }
                                }
                                break;
                            case 4001: //Lecturer accept request register
                            case 4011: //Lecturer accept request change
                                if(count($newSupervisorIds) != 0 || count($oldSupervisorIds) != 0){
                                    $quota = self::getActiveQuotaVersion();
                                    $topicType = $oldTopic->getTopicType();
                                    if ($topicType == 1) { //Student
                                        $numLearner = 'numberOfStudent';
                                        $factorLearner = 'FactorStudent';
                                    } elseif ($topicType == 2) { //Graduated
                                        $numLearner = 'numberOfGraduated';
                                        $factorLearner = 'FactorGraduated';
                                    } else { //Researcher
                                        $numLearner = 'numberOfResearcher';
                                        $factorLearner = 'FactorResearcher';
                                    }

                                    if(!$quota){
                                        $ret[] = array(
                                            'topicId' => $topicId,
                                            'error' => "Không có phiên bản định mức nào đang có hiệu lực"
                                        );
                                        //Reset topic status
                                        $resetTopic = self::resetCommonTopicInfo($oldStatus, null, null);
                                        TopicTable::updateById($topicId, $resetTopic);
                                        //Delete activity
                                        ActivityTable::deleteById($actResult);
                                        $removedActivity = true;
                                    }
                                    else {
                                        $coSupervisorIds = explode(',', $oldTopic->getCoSupervisorIds());
                                        $mainSupervisorId = $oldTopic->getMainSupervisorId();
                                        $outOfficerIds = explode(',', $oldTopic->getOutOfficerIds());

                                        //get factor
                                        $mainFactorAction = 'getMain' . $factorLearner;
                                        $coFactorAction = 'getCo' . $factorLearner;
                                        $mainFactor = $quota->$mainFactorAction();
                                        $coFactor = $quota->$coFactorAction();

                                        $action = 'get' . ucfirst($numLearner);
                                        
                                        //Count number of co out officers
                                        $numCoOutOfficers = 0;
                                        for ($i = 0; $i < count($outOfficerIds); $i++) {
                                            if ($i > 0 && $outOfficerIds[$i] != '') {
                                                $numCoOutOfficers += 1;
                                            }
                                        }

                                        //Count number of co supervisors
                                        $numCoSupervisors = 0;
                                        foreach ($coSupervisorIds as $id) {
                                            if ($id != '') {
                                                $numCoSupervisors += 1;
                                            }
                                        }

                                        //LECTURER ACCEPT TOPIC REQUEST REGISTER
                                        if ($stepId == 4001) {
                                            $totalSupervisors = $mainFactor + ($numCoSupervisors * $coFactor) + ($numCoOutOfficers * $coFactor);
                                            //Check out of quota
                                            $outOfQuota = false;
                                            $newOfficerQuotas = array();
                                            $newPos = 0;
                                            foreach ($newSupervisorIds as $id) {
                                                if ($id != '') {
                                                    $newOfficer = OfficerTable::getById($id);
                                                    if($newOfficer) {
                                                        $oldNumber = $newOfficer->$action();
                                                        if($newPos == 0){
                                                            $factor = $mainFactor;
                                                        } else {
                                                            $factor = $coFactor;
                                                        }

                                                        $newOfficerQuotas[$id] = $oldNumber
                                                            + number_format(doubleval($factor/$totalSupervisors), 2, '.', '');
                                                        if ($newOfficerQuotas[$id] > doubleval($maxLearnerQuota)) {
                                                            $ret[] = array(
                                                                'topicId' => $topicId,
                                                                'supervisorId' => $id,
                                                                'error' => Constant::outOfMaxQuota . Constant::objectNames['quotas']
                                                            );
                                                            $outOfQuota = true;
                                                        }
                                                    }
                                                }

                                                if ($outOfQuota) {
                                                    //Reset topic status
                                                    $resetTopic = self::resetCommonTopicInfo($oldStatus, null, null);
                                                    TopicTable::updateById($topicId, $resetTopic);
                                                    //Delete activity
                                                    ActivityTable::deleteById($actResult);
                                                    $removedActivity = true;
                                                    break;
                                                }

                                                $newPos += 1;
                                            }

                                            //Increase number of learner
                                            if (!$outOfQuota) {
                                                foreach ($newSupervisorIds as $id) {
                                                    if ($id != '') {
                                                        $newNumber = array();
                                                        $newNumber[$numLearner] = $newOfficerQuotas[$id];
                                                        OfficerTable::updateById($id, $newNumber);
                                                    }
                                                }
                                            }
                                        }

                                        //LECTURER ACCEPT TOPIC REQUEST CHANGE
                                        else { //StepId = 4011
                                            $totalOldSupervisors = ((!$mainSupervisorId) ? 0 : $mainFactor) + ($numCoSupervisors * $coFactor);
                                            $oldOfficerQuotas = array();
                                            //Reduce number of learners
                                            $oldPos = 0;
                                            foreach ($oldSupervisorIds as $id) {
                                                if ($id != '') {
                                                    $oldOfficer = OfficerTable::getById($id);
                                                    if($oldOfficer) {
                                                        $oldOfficerQuotas[$id] = $oldOfficer->$action();
                                                        $newNumber = array();

                                                        if($oldPos == 0){
                                                            $factor = $mainFactor;
                                                        } else {
                                                            $factor = $coFactor;
                                                        }

                                                        $newNumber[$numLearner] = $oldOfficerQuotas[$id]
                                                            - number_format(doubleval($factor/$totalOldSupervisors), 2, '.', '');
                                                        $newNumber[$numLearner] = ($newNumber[$numLearner] < 0) ? 0 : $newNumber[$numLearner];
                                                        OfficerTable::updateById($id, $newNumber);
                                                    }
                                                }
                                                $oldPos += 1;
                                            }

                                            //Increase new quota for supervisors
                                            $topicChange = TopicChangeTable::getById($topicId);
                                            $tpcCoSupervisorIds = explode(',', $topicChange->getCoSupervisorIds());
                                            $numTpcCoSupervisors = 0;
                                            for ($i = 0; $i < count($outOfficerIds); $i++) {
                                                if ($i > 0 && $outOfficerIds[$i] != '') {
                                                    $numTpcCoSupervisors += 1;
                                                }
                                            }

                                            //CASE: no out officers
                                            if($numTpcCoSupervisors == 0) {
                                                foreach ($tpcCoSupervisorIds as $id) {
                                                    if ($id != ''){
                                                        if($id != 'del') {    //change co supervisor with specificed ids
                                                            $numTpcCoSupervisors += 1;
                                                        } 
                                                    } else {    //no change co supervisor
                                                        $numTpcCoSupervisors += 1;
                                                    }
                                                }
                                            }

                                            //CASE: has out officers
                                            else {
                                                $outOffIndex = 1;

                                                foreach($tpcCoSupervisorIds as $id) {
                                                    if($id != ''){
                                                        if($id != 'del' && $outOfficerIds[$outOffIndex] == '') {    //change co supervisor with specificed ids
                                                            $numTpcCoSupervisors += 1;
                                                        }
                                                    } else {
                                                        if($outOfficerIds[$outOffIndex] == '') {  //replace co supervisor with new out officer
                                                            $numTpcCoSupervisors += 1;
                                                        }
                                                    }

                                                    $outOffIndex += 1;
                                                }                                       
                                            }

                                            $totalNewSupervisors = $mainFactor + (($numTpcCoSupervisors == 0 && !$topicChange->getCoSupervisorIds()) 
                                                                ? ($numCoSupervisors * $coFactor) //case no change co supervisor
                                                                : ($numTpcCoSupervisors * $coFactor));

                                            //Check out of quota
                                            $outOfQuota = false;
                                            $newOfficerQuotas = array();
                                            $newPos = 0;
                                            foreach ($newSupervisorIds as $id) {
                                                if ($id != '') {
                                                    $action = 'get' . ucfirst($numLearner);
                                                    $newOfficer = OfficerTable::getById($id);
                                                    if($newOfficer) {
                                                        $oldNumber = $newOfficer->$action();
                                                        if($newPos == 0) {
                                                            $factor = $mainFactor;
                                                        } else {
                                                            $factor = $coFactor;
                                                        }

                                                        $newOfficerQuotas[$id] = $oldNumber + number_format(doubleval($factor/$totalNewSupervisors), 2, '.', '');
                                                        if ($newOfficerQuotas[$id] > doubleval($maxLearnerQuota)) {
                                                            $ret[] = array(
                                                                'topicId' => $topicId,
                                                                'supervisorId' => $id,
                                                                'error' => Constant::outOfMaxQuota . Constant::objectNames['quotas']
                                                            );
                                                            $outOfQuota = true;
                                                        }
                                                    }
                                                }

                                                if ($outOfQuota) {
                                                    //Revert number of learner
                                                    foreach ($oldSupervisorIds as $ofid) {
                                                        $revertNumber = array();
                                                        $revertNumber[$numLearner] = $oldOfficerQuotas[$ofid];
                                                        OfficerTable::updateById($ofid, $revertNumber);
                                                    }

                                                    //Reset topic status
                                                    $resetTopic = self::resetCommonTopicInfo($oldStatus, null, null);
                                                    TopicTable::updateById($topicId, $resetTopic);
                                                    //Delete activity
                                                    ActivityTable::deleteById($actResult);
                                                    $removedActivity = true;
                                                    break;
                                                }

                                                $newPos += 1;
                                            }

                                            //Increase number of learner
                                            if (!$outOfQuota) {
                                                foreach ($newSupervisorIds as $id) {
                                                    if ($id != '') {
                                                        $newNumber = array();
                                                        $newNumber[$numLearner] = $newOfficerQuotas[$id];
                                                        OfficerTable::updateById($id, $newNumber);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                break;
                            case 4000:  //Lecturer deny request register
                            case 4010:  //Lecturer deny request change
                            case 4020:  //Lecturer deny request delay
                            case 4030:  //Lecturer deny request cancel
                            case 4040:  //Lecturer deny request pause
                                //Delete out officer records
                                $outOfficerIds = explode(',', $oldTopic->getOutOfficerIds());
                                foreach($outOfficerIds as $oofId){
                                    OutOfficerTable::deleteById($oofId);
                                }
                                if($stepId == 4000){
                                    $resetTopic = self::resetTopic();
                                    TopicTable::updateById($topicId, $resetTopic);
                                } elseif($stepId == 4010){
                                    TopicChangeTable::deleteById($topicId);
                                    $resetTopic = array();
                                    $resetTopic['outOfficerIds'] = null;
                                    TopicTable::updateById($topicId, $resetTopic);
                                } else {
                                    TopicChangeTable::deleteById($topicId);
                                }
                                break;
                            case 301:
                                //Update new topic
                                TopicTable::updateById($topicId, $newTopic);
                                TopicChangeTable::deleteById($topicId);
                                break;
                            case 302:
                            case 303: //University accept request cancel topic
                            case 304:
                                if($stepId == 303){
                                    if(!self::revertQuota($oldTopic)){
                                        $ret[] = array(
                                            'topicId' => $topicId,
                                            'error' => "Không có phiên bản định mức nào đang có hiệu lực"
                                        );
                                        //Reset topic status
                                        $resetTopic = self::resetCommonTopicInfo($oldStatus, null, null);
                                        TopicTable::updateById($topicId, $resetTopic);
                                        //Delete activity
                                        ActivityTable::deleteById($actResult);
                                        $removedActivity = true;
                                    } else {
                                        TopicChangeTable::deleteById($topicId);
                                    }
                                } else {
                                    TopicChangeTable::deleteById($topicId);
                                }
                                break;
                            default:
                                break;
                        }

                        //Update activities_topics table
                        if(!$removedActivity){
                            if($stepId >= 301 && $stepId <= 304) {
                                //Update topic histories
                                if(isset($topicHistoryId) || $topicHistoryId !== null) {
                                    TopicHistoryTable::updateActivityId($topicHistoryId, $actResult);
                                }
                            }

                            ActivityTable::updateActivityTopic($actResult, $topicId);
                        }
                    }
                }
            }
        }

        if($needSendingMail == true) {
            $api = 'api/seminar-topic-email';
            //Call background sending mail application
            //Middleware::activeCurl($api);
        }

        http_response_code(200);
        echo json_encode($ret);
    }

    /* HEAD OF DEPARTMENT CREATE ACTIVITY
    * departmentCreateActivity()
    */
    private function departmentCreateActivity($stepId, $oldTopic, $act, $data, $expertiseOfficerIds) {
        //Get current topic status
        $oldStatus = $oldTopic->getTopicStatus();
        $topicId = $oldTopic->getId();
        
        //status of editing topic
        $editTopicSuccess = false;
        $unknownStepId = false;

        $ret = null;

        //CREATE ACTVITIY
        //Check valid value for activity
        $activity = new Activity($act);
        foreach ($act as $key => $value) {
            $action = 'check' . ucfirst($key);
            if (!$activity->$action()) {
                $ret = array(
                    'topicId' => $topicId,
                    'error' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                );
                return $ret;
            }
        }

        //HANDLE DATA
        if($data){
            if(count((array)$data) == 0){
                $ret = array(
                    'topicId' => $topicId,
                    'error' => 'Trường dữ liệu gửi lên không hợp lệ'
                );
                return $ret;
            }

            //Handle activity for each stepCode
            switch($stepId){
                default:
                    $unknownStepId = true;
                    break;
            }
        } else {
            switch($stepId){
                case 5000:
                    $departmentDenyResult = TopicController::departmentDenyRequest($topicId, $stepId);
                    $editTopicSuccess = $departmentDenyResult['success'];
                    if($editTopicSuccess == false)
                    {
                        $ret = array(
                            'topicId' => $topicId,
                            'error' => $departmentDenyResult['message']
                        );
                        return $ret;
                    }
                    break;
                case 5001:
                case 5002:
                    $departmentAcceptResult = TopicController::departmentAcceptRequest($topicId, $stepId);
                    $editTopicSuccess = $departmentAcceptResult['success'];
                    if($editTopicSuccess == false)
                    {
                        $ret = array(
                            'topicId' => $topicId,
                            'error' => $departmentAcceptResult['message']
                        );
                        return $ret;
                    }
                    break;
                case 5003:
                    if(!isset($expertiseOfficerIds) || $expertiseOfficerIds == null) {
                        $ret = array(
                            'topicId' => $topicId,
                            'error' => ucfirst(Constant::columnNames['expertiseOfficerIds']) . Constant::isRequiredText
                        );
                        return $ret;
                    }
                    
                    if($oldStatus != 104 && $oldStatus != 896) {
                        $ret = array(
                            'topicId' => $topicId,
                            'error' => 'Trưởng bộ môn không thể sửa đổi trạng thái đề tài này hiện tại'
                        );
                        return $ret;
                    }

                    $departmentAcceptResult = DepartmentTopicController::assignReviewOfficers($oldTopic, $expertiseOfficerIds, $activity);
                    $editTopicSuccess = $departmentAcceptResult['success'];
                    if($editTopicSuccess == false)
                    {
                        $ret = array(
                            'topicId' => $topicId,
                            'error' => $departmentAcceptResult['message']
                        );
                        return $ret;
                    }
                    break;
                default:
                    $unknownStepId = true;
                    break;
            }
        }

        if($unknownStepId == true){
            $ret = array(
                'topicId' => $topicId,
                'error' => ucfirst(Constant::objectNames['step']) . Constant::unknownText . " hoặc thiếu trường dữ liệu"
            );
            return $ret;
        }

        //CREATE ACTION
        if($editTopicSuccess) {
            if($stepId != 5003) {   //Case create 1 activity
                $actResult = ActivityTable::createActivity($activity);

                if (!$actResult) {
                    //RESET TOPIC IF CREATE ACTION UNSUCCESSFULLY
                    switch ($stepId){
                        case 5000:
                        case 5001:
                        case 5002:
                            $resetTopic = self::resetCommonTopicInfo($oldStatus, null, null);
                            TopicTable::updateById($topicId, $resetTopic);
                            //Delete activity
                            ActivityTable::deleteById($actResult);
                            break;

                    }

                    $ret = array(
                        'topicId' => $topicId,
                        'error' => Constant::failed
                    );
                    return $ret;
                }

                else {
                    $removedActivity = false;
                    
                    //HANDLE DENY ACTION AFTER ACTIVITY CREATED SUCCESSFULLY
                    switch ($stepId){
                        case 5000: //deny
                            //revert supervisors's quota
                            if(!self::revertQuota($oldTopic)){
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => "Không có phiên bản định mức nào đang có hiệu lực"
                                );
                                //Reset topic status
                                $resetTopic = self::resetCommonTopicInfo($oldStatus, null, null);
                                TopicTable::updateById($topicId, $resetTopic);
                                //Delete activity
                                ActivityTable::deleteById($actResult);
                                $removedActivity = true;
                            } else {
                                if($oldStatus == 104) {
                                    //Reset all topic registered data
                                    $resetTopic = self::resetTopic();
                                    TopicTable::updateById($topicId, $resetTopic);
                                }
                            }

                            break;
                        case 5001: //accepted
                            $resetTopic = array();
                            $resetTopic['expertiseOfficerIds'] = null;
                            TopicTable::updateById($topicId, $resetTopic);
                            break;
                        case 5002: //wonder
                            //revert supervisors's quota
                            if(!self::revertQuota($oldTopic)){
                                $ret[] = array(
                                    'topicId' => $topicId,
                                    'error' => "Không có phiên bản định mức nào đang có hiệu lực"
                                );
                                //Reset topic status
                                $resetTopic = self::resetCommonTopicInfo($oldStatus, null, null);
                                TopicTable::updateById($topicId, $resetTopic);
                                //Delete activity
                                ActivityTable::deleteById($actResult);
                                $removedActivity = true;
                            }

                            break;
                        default:
                            break;
                    }

                    //Update activities_topics table
                    if(!$removedActivity){
                        ActivityTable::updateActivityTopic($actResult, $topicId);
                    }
                }
            }
        }

        return $ret;
    }

    private function resetTopic(){
        //Reset all topic info to default
        $resetTopic = array();
        $resetTopic['topicStatus'] = 100;
        $resetTopic['vietnameseTopicTitle'] = null;
        $resetTopic['englishTopicTitle'] = null;
        $resetTopic['isEnglish'] = 0;
        $resetTopic['description'] = null;
        $resetTopic['tags'] = null;
        $resetTopic['referenceUrl'] = null;
        $resetTopic['registerUrl'] = null;
        $resetTopic['mainSupervisorId'] = null;
        $resetTopic['coSupervisorIds'] = null;
        $resetTopic['requestedSupervisorId'] = null;
        $resetTopic['expertiseOfficerIds'] = null;
        $resetTopic['outOfficerIds'] = null;

        return $resetTopic;
    }

    private function resetCommonTopicInfo($status, $outOfficerIds, $deadlineDate){
        $resetTopic = array();
        if($status) { $resetTopic['topicStatus'] = $status; }
        if($outOfficerIds){ $resetTopic['outOfficerIds'] = $outOfficerIds; }
        if($deadlineDate){ $resetTopic['deadlineDate'] = $deadlineDate; }
        return $resetTopic;
    }

    private function getActiveQuotaVersion() {
        $result = QuotaTable::getActiveQuotaVersion();
        return $result;
    }

    /**
     * @param $topic Topic
     * @return bool
     */
    private function revertQuota($topic){
        //Get quota
        $quota = self::getActiveQuotaVersion();

        if(!$quota){ return false; }

        $topicType = $topic->getTopicType();
        if ($topicType == 1) { //Student
            $numLearner = 'numberOfStudent';
            $factor = 'FactorStudent';
        } elseif ($topicType == 2) { //Graduated
            $numLearner = 'numberOfGraduated';
            $factor = 'FactorGraduated';
        } else { //Researcher
            $numLearner = 'numberOfResearcher';
            $factor = 'FactorResearcher';
        }

        //get factor
        $mainFactorAction = 'getMain' . $factor;
        $coFactorAction = 'getCo' . $factor;
        $mainFactor = $quota->$mainFactorAction();
        $coFactor = $quota->$coFactorAction();

        //Revert quota
        $mainSupervisorId = $topic->getMainSupervisorId();
        $coSupervisorIds = explode(',', $topic->getCoSupervisorIds());
        $outSupervisorIds = explode(',', $topic->getOutOfficerIds());
        
        //Count number of co out officers
        $numCoOutOfficers = 0;
        for ($i = 0; $i < count($outSupervisorIds); $i++) {
            if ($i > 0 && $outSupervisorIds[$i] != '') {
                $numCoOutOfficers += 1;
            }
        }
        
        //Count number co officers
        $numCoOfficers = 0;
        foreach($coSupervisorIds as $id) {
            if($id != ''){
                $numCoOfficers += 1;
            }
        }

        $totalSupervisors = $mainFactor + ($numCoOfficers * $coFactor) + ($numCoOutOfficers * $coFactor);

        $action = 'get' . ucfirst($numLearner);

        //quota of main supervisor
        $mainSupervisor = OfficerTable::getById($mainSupervisorId);
        if($mainSupervisor) {
            $mainQuota = array();
            $mainQuota[$numLearner] = $mainSupervisor->$action() - number_format(doubleval($mainFactor / $totalSupervisors), 2, '.', '');
            $mainQuota[$numLearner] = ($mainQuota[$numLearner] < 0) ? 0 : $mainQuota[$numLearner];
            OfficerTable::updateById($mainSupervisorId, $mainQuota);
        }
        

        //quota of co supervisors
        foreach ($coSupervisorIds as $id) {
            if($id != ''){
                $coSupervisor = OfficerTable::getById($id);
                if($coSupervisor) {
                    $coQuota = array();
                    $coQuota[$numLearner] = $coSupervisor->$action() - number_format(doubleval($coFactor / $totalSupervisors), 2, '.', '');
                    $coQuota[$numLearner] = ($coQuota[$numLearner] < 0) ? 0 : $coQuota[$numLearner];
                    OfficerTable::updateById($id, $coQuota);
                }
            }
        }

        return true;
    }
}
