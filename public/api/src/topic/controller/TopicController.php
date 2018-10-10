<?php
namespace topic\controller;

use core\model\ActivityTable;
use core\model\DocumentTable;
use core\model\AttachmentTable;
use core\model\FacultyTable;
use core\model\LearnerTable;
use core\model\OutOfficerTable;
use core\model\QuotaTable;
use core\model\Topic;
use core\model\TopicChange;
use core\model\TopicChangeTable;
use core\model\TopicHistoryTable;
use core\model\TopicTable;
use core\model\OfficerTable;
use core\utility\Paging;
use DateTime;
use core\utility\Constant;
use core\utility\Middleware;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/utility/Middleware.php';
require_once 'src/core/model/TopicTable.php';
require_once 'src/core/model/QuotaTable.php';
require_once 'src/core/model/Topic.php';
require_once 'src/core/model/TopicChange.php';
require_once 'src/core/model/TopicChangeTable.php';
require_once 'src/core/model/TopicHistoryTable.php';
require_once 'src/core/model/ActivityTable.php';
require_once 'src/core/model/DocumentTable.php';
require_once 'src/core/model/AttachmentTable.php';
require_once 'src/core/model/FacultyTable.php';
require_once 'src/core/model/OfficerTable.php';
require_once 'src/core/model/OutOfficerTable.php';
require_once 'src/core/model/LearnerTable.php';
require_once 'src/core/utility/Paging.php';

/**
 * TopicController
 */
class TopicController
{

    /**
     * API
     * getTopic()
     *
     * HOW-TO-DO: des
     */
    public function getTopic()
    {
        $facultyId = $_SESSION['facultyId'];
        $option = Paging::normalizeOption($_GET);
        $result = TopicTable::get($option, $facultyId);
        $result = Paging::genNextPrev($option, $result);

        http_response_code(200);
        echo json_encode($result);
    }
    /**
    * API
    * learnerGetTopic()
    *
    * HOW-TO-DO: des
    * @param $param
    * @internal param $id
    */
    public function getLearnerTopics($param)
    {
        $learnerId = $param['id'];

        switch ($_SESSION['role']) {
            case 2:
                break;
            default:
                http_response_code(403);
                echo json_encode(array(
                    'message' => Constant::notPermissionText
                ));
                return;
        }

        $option = Paging::normalizeOption($_GET);
        $result = TopicTable::getLearnerTopic($option, $learnerId);

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * officerGetTopic()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function getOfficerTopics($param)
    {
        $officerId = $param['id'];

        $facultyId = OfficerTable::getFacultyIdOf($officerId);
        if(!$facultyId){
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['officer']
            ));
            return;
        }

        switch ($_SESSION['role']) {
            case 3:
            case 5:
            case 6:
                break;
            default:
                http_response_code(403);
                echo json_encode(array(
                    'message' => Constant::notPermissionText
                ));
                return;
        }

        $option = Paging::normalizeOption($_GET);
        $result = array();
        $result['mainTopic'] = TopicTable::getOfficerTopic($option, $officerId);
        $result['topicChange'] = TopicTable::getOfficerTopicChange($officerId);
        //$result = Paging::genNextPrev($option, $result);

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * getTopicById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function getTopicById($param)
    {
        $id = $param['id'];

        $result = TopicTable::getById($id);

        if ($result !== null) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['topic']
            ));
        }
    }

    /**
     * API
     * learnerRegisterTopic()
     *
     * HOW-TO-DO: des
     */
    public function adminImportLearnerCodeTopic()
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

        $topicType = 0;
        $learnerCodes = array();
        $newIter = "0";
        if (property_exists($obj, 'topicType')) $topicType = $obj->topicType;
        if (property_exists($obj, 'data')) $learnerCodes = $obj->data;
        if (property_exists($obj, 'newIter')) $newIter = $obj->newIter;


        if($topicType == 0) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['topicType']) . Constant::isRequiredText
            ));
            return;
        }

        if($topicType < 1 || $topicType > 3) {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['topicType']) . Constant::invalidText
            ));
            return;
        }

        if (!is_array($learnerCodes) || count($learnerCodes) == 0) {
            http_response_code(400);
            echo json_encode(array(
                'message' => Constant::emptyList
            ));
            return;
        }

        if ($newIter == "1") {
          //Reset dinh muc cho giang vien
          TopicTable::newIter($topicType);
        }

        $ret = array();
        foreach ($learnerCodes as $learnerCode) {
            $learner = LearnerTable::getIdByLearnerCode($learnerCode);
            if ($learner == null) {
                $ret[] = $learnerCode;
                continue;
            } else {
                if($topicType != $learner['learnerType']){
                    $ret[] = $learnerCode;
                    continue;
                }

                //check existing topic
                $existedTopic = TopicTable::getTopicByLearnerId($learner['id']);
                if($existedTopic != null) { //register at second time
                    //check old topic status
                    $topicStatus = $existedTopic['topicStatus'];
                    if($topicStatus != 0 && $topicStatus != 1 && $topicStatus != 3) {
                        //learner with topic in proceed cannot register new one
                        $ret[] = $learnerCode;
                        continue;
                    }
                }

                //get departmentId of learner's training program
                $departmentId = LearnerTable::getDepartmentIdOf($learner['id']);
                $topicId = TopicTable::initializeTopic($departmentId, $learner);
                if (!$topicId) {
                    $ret[] = $learnerCode;
                    continue;
                } else {
                    //queue email to ready to be send
                    Middleware::queueEmail($learner['id'],$topicId,2);
                }
            }
        }

        $api = 'api/register-topic-email';
        //Call background sending mail application
        //Middleware::activeCurl($api);

        http_response_code(200);
        echo json_encode(array(
            'error' => $ret
        ));
    }

    /**
     * API
     * learnerAddTopic()
     *
     * HOW-TO-DO: des
     * @param $topicId
     * @param $requestedId
     * @param $data
     * @return array
     */
    public static function learnerRegisterTopic($topicId, $requestedId, $data)
    {
        $ret = array();

        switch ($_SESSION['role']) {
            case 2:
                $learnerId = $_SESSION['uid'];
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $tp = array();
        if (property_exists($data, 'vietnameseTopicTitle')) $tp['vietnameseTopicTitle'] = $data->vietnameseTopicTitle;
        if (property_exists($data, 'mainSupervisorId')) $tp['mainSupervisorId'] = $data->mainSupervisorId;
        if (property_exists($data, 'isEnglish')) $tp['isEnglish'] = $data->isEnglish;
        if (property_exists($data, 'description')) $tp['description'] = $data->description;
        if (property_exists($data, 'englishTopicTitle')) $tp['englishTopicTitle'] = $data->englishTopicTitle;
        if (property_exists($data, 'coSupervisorIds')) $tp['coSupervisorIds'] = $data->coSupervisorIds;
        if (property_exists($data, 'outOfficerIds')) $tp['outOfficerIds'] = $data->outOfficerIds;
        if (property_exists($data, 'tags')) $tp['tags'] = $data->tags;
        if (property_exists($data, 'referenceUrl')) $tp['referenceUrl'] = $data->referenceUrl;
        if (property_exists($data, 'registerUrl')) $tp['registerUrl'] = $data->registerUrl;
        if ($requestedId) $tp['requestedSupervisorId'] = $requestedId;

        $topic = new Topic($tp);

        if (!isset($tp['vietnameseTopicTitle'])) {
            $ret['message'] = ucfirst(Constant::columnNames['VietnameseTopicTitle']) . Constant::isRequiredText;
            $ret['success'] = false;
            return $ret;
        } elseif (!isset($tp['isEnglish'])) {
            $ret['message'] = ucfirst(Constant::columnNames['isEnglish']) . Constant::isRequiredText;
            $ret['success'] = false;
            return $ret;
        } elseif (!isset($tp['description'])) {
            $ret['message'] = ucfirst(Constant::columnNames['description']) . Constant::isRequiredText;
            $ret['success'] = false;
            return $ret;
        } else {
            foreach ($tp as $key => $value) {
                $action = 'check' . ucfirst($key);
                if (!$topic->$action()) {
                    $ret['message'] = ucfirst(Constant::columnNames[$key]) . Constant::invalidText;
                    $ret['success'] = false;
                    return $ret;
                }
            }

            if(isset($tp['mainSupervisorId'])) {
                $msFacultyId = OfficerTable::getFacultyIdOf($tp['mainSupervisorId']);
                if ($msFacultyId == null) {
                    $ret['message'] = ucfirst(Constant::columnNames['mainSupervisorId']) . Constant::notExistedText;
                    $ret['success'] = false;
                    return $ret;
                }
            }

            $topicResult = TopicTable::getTopicStatus($topicId);

            if($topicResult === null) {
                $ret['message'] = ucfirst(Constant::objectNames['learner']) . ' không đủ điều kiện đăng ký đề tài';
                $ret['success'] = false;
                return $ret;
            }

            if($topicResult['topicStatus'] != 100) {
                $ret['message'] = ucfirst(Constant::objectNames['learner']) . ' đã đăng ký một đề tài trong đợt này';
                $ret['success'] = false;
                return $ret;
            }

            // Valid now, add
            $tp['topicStatus'] = 101;   //update topic status
            $result = TopicTable::updateById($topicId, $tp);

            if ($result) {
                $ret['outOfficerIds'] = $tp['outOfficerIds'];
                $ret['success'] = true;

                //queue email to ready to be send
                Middleware::queueEmail($requestedId,$topicId,3);

                $api = 'api/approve-topic-email';
                //Call background sending mail application
                //Middleware::activeCurl($api);
            }
            else {
                $ret['message'] = 'Không thay đổi';
                $ret['success'] = false;
            }

            return $ret;
        }
    }

    /**
     * API
     * learnerAddRequestChange()
     *
     * HOW-TO-DO: des
     * @param $topicId
     * @param $requestedId
     * @param $data
     * @return array
     */
    public static function learnerAddRequestChange($topicId, $requestedId, $data)
    {
        $ret = array();
        $facultyId = $_SESSION['facultyId'];

        switch ($_SESSION['role']) {
            case 2:
                $learnerId = $_SESSION['uid'];
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $topicChange = TopicChangeTable::getById($topicId);
        if($topicChange) {
            $existedTopicChange = true;
        } else {
            $existedTopicChange = false;
        }

        $tpc = array();
        $tp = array();
        if(property_exists($data, 'vietnameseTopicTitle'))
            $tpc['vietnameseTopicTitle'] = $data->vietnameseTopicTitle;
        if(property_exists($data, 'mainSupervisorId'))
            $tpc['mainSupervisorId'] = $data->mainSupervisorId;
        if(property_exists($data, 'isEnglish'))
            $tpc['isEnglish'] = $data->isEnglish;
        if(property_exists($data, 'description'))
            $tpc['description'] = $data->description;
        if(property_exists($data, 'englishTopicTitle'))
            $tpc['englishTopicTitle'] = $data->englishTopicTitle;
        if(property_exists($data, 'coSupervisorIds'))
            $tpc['coSupervisorIds'] = $data->coSupervisorIds;
        if(property_exists($data, 'tags'))
            $tpc['tags'] = $data->tags;
        if(property_exists($data, 'registerUrl'))
            $tpc['registerUrl'] = $data->registerUrl;
        if ($requestedId) {
            $tpc['requestedSupervisorId'] = $requestedId;
        } else { $tpc['requestedSupervisorId'] = $requestedId; }
        property_exists($data, 'outOfficerIds') ?
            $tp['outOfficerIds'] = $data->outOfficerIds : null;

        $topicChange = new TopicChange($tpc);

        foreach ($tpc as $key => $value) {
            if(isset($tpc[$key])){
                $action = 'check' . ucfirst($key);
                if (!$topicChange->$action()) {
                    $ret['message'] = ucfirst(Constant::columnNames[$key]) . Constant::invalidText;
                    $ret['success'] = false;
                    return $ret;
                }
            }
        }

        if(isset($tpc['mainSupervisorId'])) {
            $msFacultyId = OfficerTable::getFacultyIdOf($tpc['mainSupervisorId']);
            if ($msFacultyId == null) {
                $ret['message'] = ucfirst(Constant::columnNames['mainSupervisorId']) . Constant::notExistedText;
                $ret['success'] = false;
                return $ret;
            }
        } else {
            // Valid now, add
            $tp['topicStatus'] = 890;   //update topic status
            //Update when topicChange record existed
            //Create when topicChange record not exist
            if($existedTopicChange) {
                $tpcResult = TopicChangeTable::updateById($topicId, $tpc);
            } else {
                $tpcObj = new TopicChange($tpc);
                $tpcResult = TopicChangeTable::insert($topicId, $tpcObj);
            }

            if ($tpcResult == true || (is_string($tpcResult) && strlen($tpcResult) == 32)) {
                TopicTable::updateById($topicId, $tp);
                $ret['success'] = true;
                $ret['outOfficerIds'] = $tp['outOfficerIds'];

                //queue email to ready to be send
                Middleware::queueEmail($requestedId,$topicId,3);

                $api = 'api/approve-topic-email';
                //Call background sending mail application
                //Middleware::activeCurl($api);
            } else {
                $ret['success'] = false;
                $ret['message'] = "Không thay đổi hoặc không tạo được yêu cầu mới";
            }
            return $ret;
        }
    }

    /**
     * API
     * learnerAddRequest()
     *
     * HOW-TO-DO: des
     * @param $stepId
     * @param $topicId
     * @param $data
     * @param $requestedId
     * @return array
     */
    public static function learnerAddRequest($stepId, $topicId, $data, $requestedId)
    {
        $ret = array();

        switch ($_SESSION['role']) {
            case 2:
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $oldTopic = TopicTable::getTopicStatus($topicId);

        if($stepId != 103 && $oldTopic['topicType'] == 1){  //student can cancel topic
            $ret['message'] = ucfirst(Constant::objectNames['learner']) . ' là sinh viên không thể thực hiện tác vụ này';
            $ret['success'] = false;
            return $ret;
        }

        if($oldTopic['topicStatus'] != 888 && $oldTopic['topicStatus'] != 889) {
            $ret['message'] = ucfirst(Constant::objectNames['learner']) . ' không thể tạo yêu cầu mới hiện tại';
            $ret['success'] = false;
            return $ret;
        }

        $tpc = array();

        $topicChange = TopicChangeTable::getById($topicId);
        if($topicChange) {
            $existedTopicChange = true;
        } else {
            $existedTopicChange = false;
        }

        switch($stepId){
            case 102: //Learner add request to delay topic
                if (property_exists($data, 'delayDuration')) $tpc['delayDuration'] = $data->delayDuration;
                if (!isset($tpc['delayDuration']) || $tpc['delayDuration'] == null) {
                    $ret['message'] = ucfirst(Constant::columnNames['delayDuration']) . Constant::isRequiredText;
                    $ret['success'] = false;
                    return $ret;
                } else {
                    if (!is_int($tpc['delayDuration'])) {
                        $ret['message'] = ucfirst(Constant::columnNames['delayDuration']) . Constant::invalidText;
                        $ret['success'] = false;
                        return $ret;
                    }
                }

                //Update when topicChange record existed
                //Create when topicChange record not exist
                if($existedTopicChange) {
                    $tcpResult = TopicChangeTable::updateById($topicId, $tpc);
                } else {
                    $tpc['requestedSupervisorId'] = $oldTopic['requestedSupervisorId'];
                    $tpcObj = new TopicChange($tpc);
                    $tpcResult = TopicChangeTable::insert($topicId, $tpcObj);
                }

                if ($tcpResult == true || (is_string($tpcResult) && strlen($tpcResult) == 32)) {
                    TopicTable::updateStatus($topicId, 893);
                    $ret['success'] = true;

                    //queue email to ready to be send
                    Middleware::queueEmail($requestedId,$topicId,3);

                    $api = 'api/approve-topic-email';
                    //Call background sending mail application
                    //Middleware::activeCurl($api);
                } else {
                    $ret['success'] = false;
                    $ret['message'] = "Không thay đổi hoặc không tạo được yêu cầu mới";
                }
                break;
            case 103: //Learner add request to cancel topic
                if (property_exists($data, 'cancelReason')) $tpc['cancelReason'] = $data->cancelReason;
                if (!isset($tpc['cancelReason']) || $tpc['cancelReason'] == null) {
                    $ret['message'] = ucfirst(Constant::columnNames['cancelReason']) . Constant::isRequiredText;
                    $ret['success'] = false;
                    return $ret;
                } else {
                    if (!is_string($tpc['cancelReason'])) {
                        $ret['message'] = ucfirst(Constant::columnNames['cancelReason']) . Constant::invalidText;
                        $ret['success'] = false;
                        return $ret;
                    }
                }

                //Update when topicChange record existed
                //Create when topicChange record not exist
                if($existedTopicChange) {
                    $tcpResult = TopicChangeTable::updateById($topicId, $tpc);
                } else {
                    $tpc['requestedSupervisorId'] = $oldTopic['requestedSupervisorId'];
                    $tpcObj = new TopicChange($tpc);
                    $tpcResult = TopicChangeTable::insert($topicId, $tpcObj);
                }

                if ($tcpResult == true || (is_string($tpcResult) && strlen($tpcResult) == 32)) {
                    TopicTable::updateStatus($topicId, 200);
                    $ret['success'] = true;

                    //queue email to ready to be send
                    Middleware::queueEmail($requestedId,$topicId,3);

                    $api = 'api/approve-topic-email';
                    //Call background sending mail application
                    //Middleware::activeCurl($api);
                } else {
                    $ret['success'] = false;
                    $ret['message'] = "Không thay đổi hoặc không tạo được yêu cầu mới";
                }
                break;
            case 104: //Learner add request to pause topic
                if (property_exists($data, 'startPauseDate')) $tpc['startPauseDate'] = $data->startPauseDate;
                if (property_exists($data, 'pauseDuration')) $tpc['pauseDuration'] = $data->pauseDuration;
                $topicChange = new TopicChange($tpc);
                if (!isset($tpc['startPauseDate']) || $tpc['startPauseDate'] == null) {
                    $ret['message'] = ucfirst(Constant::columnNames['startPauseDate']) . Constant::isRequiredText;
                    $ret['success'] = false;
                    return $ret;
                } elseif (!isset($tpc['pauseDuration']) || $tpc['pauseDuration'] == null) {
                    $ret['message'] = ucfirst(Constant::columnNames['pauseDuration']) . Constant::isRequiredText;
                    $ret['success'] = false;
                    return $ret;
                } else {
                    foreach ($tpc as $key => $value) {
                        $action = 'check' . ucfirst($key);
                        if (!$topicChange->$action()) {
                            $ret['message'] = ucfirst(Constant::columnNames[$key]) . Constant::invalidText;
                            $ret['success'] = false;
                            return $ret;
                        }
                    }
                }

                //Update when topicChange record existed
                //Create when topicChange record not exist
                if($existedTopicChange) {
                    $tcpResult = TopicChangeTable::updateById($topicId, $tpc);
                } else {
                    $tpc['requestedSupervisorId'] = $oldTopic['requestedSupervisorId'];
                    $tpcObj = new TopicChange($tpc);
                    $tpcResult = TopicChangeTable::insert($topicId, $tpcObj);
                }

                if ($tcpResult == true || (is_string($tpcResult) && strlen($tpcResult) == 32)) {
                    TopicTable::updateStatus($topicId, 300);
                    $ret['success'] = true;

                    //queue email to ready to be send
                    Middleware::queueEmail($requestedId,$topicId,3);

                    $api = 'api/approve-topic-email';
                    //Call background sending mail application
                    //Middleware::activeCurl($api);
                } else {
                    $ret['success'] = false;
                    $ret['message'] = "Không thay đổi hoặc không tạo được yêu cầu mới";
                }
                break;
            default:
                $ret['message'] = ucfirst(Constant::columnNames['stepId']) . Constant::unknownText;
                $ret['success'] = false;
                break;
        }

        return $ret;
    }

    /**
     * API
     * learnerRegisterProtectTopic()
     *
     * HOW-TO-DO: des
     * @param $topicId
     * @param $requestedId
     * @return array
     */
    public static function learnerRegisterProtectTopic($topicId, $requestedId)
    {
        $ret = array();

        switch ($_SESSION['role']) {
            case 2:
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $oldTopic = TopicTable::getTopicStatus($topicId);

        if($oldTopic['topicStatus'] != 666) {
            $ret['message'] = ucfirst(Constant::objectNames['learner']) . ' không thể đăng kí bảo vệ đề tài này hiện tại';
            $ret['success'] = false;
            return $ret;
        }

        TopicTable::updateStatus($topicId, 667);
        $ret['success'] = true;

        //queue email to ready to be send
        Middleware::queueEmail($requestedId,$topicId,3);

        $api = 'api/approve-topic-email';
        //Call background sending mail application
        //Middleware::activeCurl($api);

        return $ret;
    }

    /**
     * API
     * learnerRegisterSeminar()
     *
     * HOW-TO-DO: des
     * @param $topicId
     * @param $requestedId
     * @return array
     */
    public static function learnerRegisterSeminar($topicId, $requestedId)
    {
        $ret = array();

        switch ($_SESSION['role']) {
            case 2:
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $oldTopic = TopicTable::getTopicStatus($topicId);

        if($oldTopic['topicStatus'] != 897) {
            $ret['message'] = ucfirst(Constant::objectNames['learner']) . ' không thể đăng kí báo cáo tiến độ cho đề tài này hiện tại';
            $ret['success'] = false;
            return $ret;
        }

        TopicTable::updateStatus($topicId, 898);
        $ret['success'] = true;

        //queue email to ready to be send
        Middleware::queueEmail($requestedId,$topicId,3);

        $api = 'api/approve-topic-email';
        //Call background sending mail application
        //Middleware::activeCurl($api);

        return $ret;
    }

    /**
     * API
     * lecturerAcceptRequest()
     *
     * HOW-TO-DO: des
     * @param $topicId
     * @param $supervisorIds
     * @param $stepId
     * @return array
     */
    public static function lecturerAcceptRequest($topicId, $supervisorIds, $stepId)
    {
        $ret = array();

        $requestedId = $supervisorIds['requestedSupervisorId'];
        $newSupervisorIds = $supervisorIds['newSupervisorIds'];

        switch ($_SESSION['role']) {
            case 3:
            case 6:
                if($requestedId != $_SESSION['uid']) {
                    $ret['message'] = Constant::notPermissionText;
                    $ret['success'] = false;
                    return $ret;
                }
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $topic = TopicTable::getTopicStatus($topicId);

        switch ($stepId){
            case 4001:   //Lecturer accept 'register topic' request
            case 4011: //Lecturer accept 'change' request
                $topicType = $topic['topicType'];
                if($topicType == 1) { //Student
                    $ofAction = 'get' . ucfirst('numberOfStudent');
                    $qAction = 'get' . ucfirst('maxStudent');
                } elseif($topicType == 2) { //Graduated
                    $ofAction = 'get' . ucfirst('numberOfGraduated');
                    $qAction = 'get' . ucfirst('maxGraduated');
                } else { //Researcher
                    $ofAction = 'get' . ucfirst('numberOfReseacher');
                    $qAction = 'get' . ucfirst('maxReseacher');
                }

                $maxLearner = 10;
                $errorCount = 0;
                $newOfficers = array();
                $quotas = array();
                //Check quota of supervisors
                foreach($newSupervisorIds as $id){
                    if($id != ''){
                        $newOfficers[$id] = OfficerTable::getById($id);
                        if(!$newOfficers[$id]) {
                            $ret['success'] = false;
                            $ret['supervisorId'] = $id;
                            $ret['message'] = Constant::notFoundText . Constant::objectNames['officer'];
                            $errorCount += 1;
                            break;
                        }

                        $degreeId = $newOfficers[$id]->getDegreeId();
                        $numberLeaner = $newOfficers[$id]->$ofAction();

                        $quotas[$id] = self::getActiveQuotaByDegree($degreeId);
                        $maxLearner = $quotas[$id]->$qAction();

                        //Check quota of register request
                        if($stepId == 4001) {
                            if($numberLeaner >= $maxLearner) {
                                $ret['success'] = false;
                                $ret['supervisorId'] = $id;
                                $ret['message'] = Constant::outOfMaxQuota . Constant::objectNames['quotas'];
                                $errorCount += 1;
                                break;
                            }
                        }
                    }
                }

                //Out of quota
                if($errorCount > 0) {
                    return $ret;
                }

                if($stepId == 4001){    //register topic request
                    if($topic['topicStatus'] == 101){
                        $tp = array();

                        //Update department of topic where the requested supervisor is working
                        //Set department of officer for topic has type = 1
                        if($topic['topicType'] == 1) {  //Khoa luan
                            $tp['departmentId'] = OfficerTable::getDepartmentIdOf($_SESSION['uid']);
                            $tp['topicStatus'] = 102;
                        } else {    //Luan van
                            $tp['topicStatus'] = 104;
                        }

                        $result = TopicTable::updateById($topicId, $tp);
                        if ($result) {
                            $ret['success'] = true;
                            $ret['maxLearner'] = $maxLearner;
                        } else {
                            $ret['success'] = false;
                        }
                        return $ret;
                    }
                } else {    //change topic request
                    if($topic['topicStatus'] == 890){
                        $tp = array();

                        //Update department of topic where the requested supervisor is working
                        //Set department of officer for topic has type = 1
                        if($topic['topicType'] == 1) {  //Khoa luan
                            $tp['departmentId'] = OfficerTable::getDepartmentIdOf($_SESSION['uid']);
                            $tp['topicStatus'] = 891;
                        } else {    //Luan van
                            $tp['topicStatus'] = 896;
                        }

                        $result = TopicTable::updateById($topicId, $tp);
                        if ($result) {
                            $ret['success'] = true;
                            $ret['maxLearner'] = $maxLearner;
                        } else {
                            $ret['success'] = false;
                        }
                        return $ret;
                    }
                }

                $result = TopicTable::updateById($topicId, $tp);
                if ($result) {
                    $ret['success'] = true;
                    $ret['maxLearner'] = $maxLearner;
                } else {
                    $ret['success'] = false;
                }
                return $ret;

                break;
            case 4021: //Lecturer accept 'delay' request
                if($topic['topicStatus'] == 893){
                    $result = TopicTable::updateStatus($topicId, 894);
                    if ($result) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 4031: //Lecturer accept 'cancel topic' request
                if($topic['topicStatus'] == 200){
                    $result = TopicTable::updateStatus($topicId, 201);
                    if ($result) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 4041: //Lecturer accept 'pause topic' request
                if($topic['topicStatus'] == 300){
                    $result = TopicTable::updateStatus($topicId, 301);
                    if ($result) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 4051: //Lecturer accept 'register protecting' request
                if($topic['topicStatus'] == 667){
                    $result = TopicTable::updateStatus($topicId, 668);
                    if ($result) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 4061: //Lecturer accept 'register seminar' request
                if($topic['topicStatus'] == 898){
                    $result = TopicTable::updateStatus($topicId, 899);
                    if ($result) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            default:
                $ret['message'] = ucfirst(Constant::columnNames['stepId']) . Constant::unknownText;
                $ret['success'] = false;
                return $ret;
        }

        //False
        $ret['message'] = ucfirst(Constant::objectNames['lecturer']) . Constant::notChangeTopicStatus;
        $ret['success'] = false;
        return $ret;
    }

    /**
     * API
     * lecturerDenyRequest()
     *
     * HOW-TO-DO: des
     *
     * @param $topicId
     * @param $requestedId
     * @param $stepId
     * @return array
     */
    public static function lecturerDenyRequest($topicId, $requestedId, $stepId)
    {
        $ret = array();

        if(!isset($requestedId) || $requestedId == null) {
            $ret['message'] = ucfirst(Constant::columnNames['requestedSupervisorId']) . Constant::isRequiredText;
            $ret['success'] = false;
            return $ret;
        }

        switch ($_SESSION['role']) {
            case 3:
            case 6:
                if($requestedId != $_SESSION['uid']) {
                    $ret['message'] = Constant::notPermissionText;
                    $ret['success'] = false;
                    return $ret;
                }
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $topic = TopicTable::getTopicStatus($topicId);

        switch ($stepId){
            case 4000: //Lecture deny register topic request
                if($topic['topicStatus'] == 101){
                    if (TopicTable::updateStatus($topicId, 100)) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 4010: //Lecture deny request change topic
                if($topic['topicStatus'] == 890){
                    if (TopicTable::updateStatus($topicId, 889)) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 4020: //Lecture deny request delay topic
                if($topic['topicStatus'] == 893){
                    if (TopicTable::updateStatus($topicId, 888)) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 4030: //Lecture deny request cancel topic
                if($topic['topicStatus'] == 200){
                    if (TopicTable::updateStatus($topicId, 888)) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 4040: //Lecture deny request pause topic
                if($topic['topicStatus'] == 300){
                    if (TopicTable::updateStatus($topicId, 888)) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 4050: //Lecture deny request protect topic
                if($topic['topicStatus'] == 667){
                    $nextStatus = 888;
                    if($topic['topicType'] == 2) $nextStatus = 900;
                    if (TopicTable::updateStatus($topicId, $nextStatus)) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 4060: //Lecture deny request register seminar
                if($topic['topicStatus'] == 898){
                    if (TopicTable::updateStatus($topicId, 897)) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            default:
                $ret['message'] = ucfirst(Constant::columnNames['stepId']) . Constant::unknownText;
                $ret['success'] = false;
                return $ret;
        }

        $ret['message'] = ucfirst(Constant::objectNames['lecturer']) . Constant::notChangeTopicStatus;
        $ret['success'] = false;
        return $ret;
    }

    /**
     * API
     * lectureUpdateTopic()
     *
     * HOW-TO-DO: des
     * @param $topicId
     * @param $requestedId
     * @param $data
     * @return array
     */
    public static function lecturerUpdateTopic($topicId, $requestedId, $data)
    {
        $ret = array();

        if(!isset($requestedId) || $requestedId == null){
            $ret['message'] = ucfirst(Constant::columnNames['requestedSupervisorId']) . Constant::isRequiredText;
            $ret['success'] = false;
            return $ret;
        }

        switch ($_SESSION['role']) {
            case 3:
            case 6:
                if($requestedId != $_SESSION['uid']) {
                    $ret['message'] = Constant::notPermissionText;
                    $ret['success'] = false;
                    return $ret;
                }
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $tp = array();
        if (property_exists($data, 'vietnameseTopicTitle')) $tp['vietnameseTopicTitle'] = $data->vietnameseTopicTitle;
        if (property_exists($data, 'englishTopicTitle')) $tp['englishTopicTitle'] = $data->englishTopicTitle;
        if (property_exists($data, 'isEnglish')) $tp['isEnglish'] = $data->isEnglish;
        if (property_exists($data, 'description')) $tp['description'] = $data->description;
        if (property_exists($data, 'tags')) $tp['tags'] = $data->tags;
        if (property_exists($data, 'referenceUrl')) $tp['referenceUrl'] = $data->referenceUrl;
        $topic = new Topic($tp);

        if (isset($tp['vietnameseTopicTitle']) && $tp['vietnameseTopicTitle'] == '') {
            $ret['message'] = ucfirst(Constant::columnNames['vietnameseTopicTitle']) . Constant::isRequiredText;
            $ret['success'] = false;
            return $ret;
        }

        foreach ($tp as $key => $value) {
            $action = 'check' . ucfirst($key);
            if (!$topic->$action()) {
                $ret['message'] = ucfirst(Constant::columnNames[$key]) . Constant::invalidText;
                $ret['success'] = false;
                return $ret;
            }
        }

        $topicResult = TopicTable::getTopicStatus($topicId);

        if(!$topicResult || $topicResult['topicStatus'] != 101) {
            $ret['message'] = ucfirst(Constant::objectNames['learner']) . ' không thể sửa đổi đề tài này hiện tại';
            $ret['success'] = false;
            return $ret;
        }

        $result = TopicTable::updateById($topicId, $tp);

        if ($result) {
            $ret['success'] = true;
        } else {
            $ret['message'] = 'Không thay đổi';
            $ret['success'] = false;
        }

        return $ret;
    }

    /**
     * API
     * adminAcceptRequest()
     *
     * HOW-TO-DO: des
     * @param $topicId
     * @param $stepId
     * @param $oldTopic
     * @return array
     */
    public static function adminAcceptRequest($topicId, $stepId, $oldTopic)
    {
        $ret = array();

        $facultyId = $_SESSION['facultyId'];
        $facultyTopicId = TopicTable::getFacultyIdOf($topicId);

        if($facultyId != $facultyTopicId){
            $ret['message'] = ucfirst(Constant::objectNames['topic']) . " này không thuộc quyền quản lý của Khoa";
            $ret['success'] = false;
            return $ret;
        }

        switch ($_SESSION['role']) {
            case 1:
            case 4:
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $topic = TopicTable::getTopicStatus($topicId);

        switch ($stepId){
            case 200:   //Admin accept 'register topic' request
                if($topic['topicStatus'] == 102){
                    $result = TopicTable::updateStatus($topicId, 103);
                    if ($result) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 2011: //Admin accept 'change' request
                if($topic['topicStatus'] == 891){
                    $result = TopicTable::updateStatus($topicId, 892);
                    if ($result) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 2021: //Admin accept 'delay' request
                if($topic['topicStatus'] == 894){
                    $result = TopicTable::updateStatus($topicId, 895);
                    if ($result) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 2031: //Admin accept 'cancel topic' request
                if($topic['topicStatus'] == 201){
                    $result = TopicTable::updateStatus($topicId, 202);
                    if ($result) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 2041: //Admin accept 'pause topic' request
                if($topic['topicStatus'] == 301){
                    $result = TopicTable::updateStatus($topicId, 302);
                    if ($result) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 2049: //Admin undo confirming learner submit thesis document
                if($topic['topicStatus'] == 669){
                    $result = TopicTable::updateStatus($topicId, 668);
                    if ($result) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 2051: //Admin confirms learner submit thesis document
                if($topic['topicStatus'] == 668){
                    $result = TopicTable::updateStatus($topicId, 669);
                    if ($result) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 2052: //Admin accept 'register topic' request
                if($topic['topicStatus'] == 669){
                    $result = TopicTable::updateStatus($topicId, 670);
                    if ($result) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 2081: //Admin confirms topic protected successfully
                if($topic['topicStatus'] == 700){
                    $result = TopicTable::updateStatus($topicId, 2);
                    if ($result) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 305: //University accept 'register topic' request
                if($topic['topicStatus'] == 670){
                    $result = TopicTable::updateStatus($topicId, 700);
                    if ($result) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 2090: //Admin grant permission to register seminar
                if($topic['topicType'] == 2) {
                    if($topic['topicStatus'] == 888){
                        $result = TopicTable::updateStatus($topicId, 897);
                        if ($result) {
                            $ret['success'] = true;
                            $learnerId = $oldTopic->getLearnerId();
                            //queue email to ready to be send
                            Middleware::queueEmail($learnerId,$topicId,7);
                        } else {
                            $ret['success'] = false;
                        }
                        return $ret;
                    }
                }
                break;
            default:
                $ret['message'] = ucfirst(Constant::columnNames['stepId']) . Constant::unknownText;
                $ret['success'] = false;
                return $ret;
        }

        //False
        $ret['message'] = ucfirst(Constant::objectNames['admin']) . Constant::notChangeTopicStatus;
        $ret['success'] = false;
        return $ret;
    }

    /**
     * API
     * adminDenyRequest()
     *
     * HOW-TO-DO: des
     *
     * @param $topicId
     * @param $stepId
     * @return array
     */
    public static function adminDenyRequest($topicId, $stepId)
    {
        $ret = array();

        $facultyId = $_SESSION['facultyId'];
        $facultyTopicId = TopicTable::getFacultyIdOf($topicId);

        if($facultyId != $facultyTopicId){
            $ret['message'] = ucfirst(Constant::objectNames['topic']) . " này không thuộc quyền quản lý của Khoa";
            $ret['success'] = false;
            return $ret;
        }

        switch ($_SESSION['role']) {
            case 1:
            case 4:
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $topic = TopicTable::getTopicStatus($topicId);

        switch ($stepId){
            case 2010: //Admin deny register topic request
                switch($topic['topicStatus']){
                    case 891:
                        $nextStatus = 889;
                        break;
                    case 892:
                        $nextStatus = 888;
                        break;
                    default:
                        $ret['message'] = ucfirst(Constant::objectNames['admin']) . Constant::notChangeTopicStatus;
                        $ret['success'] = false;
                        return $ret;
                }

                if (TopicTable::updateStatus($topicId, $nextStatus)) {
                    $ret['success'] = true;
                } else {
                    $ret['success'] = false;
                }
                return $ret;
                break;
            case 2020: //Admin deny request delay topic
                if($topic['topicStatus'] == 894 || $topic['topicStatus'] == 895){
                    if (TopicTable::updateStatus($topicId, 888)) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 2030: //Admin deny request cancel topic
                if($topic['topicStatus'] == 201 || $topic['topicStatus'] == 202){
                    if (TopicTable::updateStatus($topicId, 888)) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 2040: //Admin deny request pause topic
                if($topic['topicStatus'] == 301 || $topic['topicStatus'] == 302){
                    if (TopicTable::updateStatus($topicId, 888)) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 2050: //Admin deny request protecting topic
                if($topic['topicStatus'] >= 667 && $topic['topicStatus'] <= 670){
                    $nextStatus = 888;
                    if($topic['topicType'] == 2) $nextStatus = 900;
                    if (TopicTable::updateStatus($topicId, $nextStatus)) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 2080: //Admin confirm topic protected unsuccessfully
                if($topic['topicStatus'] == 700){
                    if (TopicTable::updateStatus($topicId, 0)) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 2082: //Admin change topic status when out of deadline
                if($topic['topicStatus'] > 2){
                    if (TopicTable::updateStatus($topicId, 3)) {
                        $ret['success'] = true;
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 2100: //Admin change topic status to allow edit topic
                $nextStatus = $topic['topicStatus'];
                switch($topic['topicStatus']) {
                    case 102:
                        $nextStatus = 100;
                        break;
                    case 891:
                        $nextStatus = 889;
                        break;
                    default:
                        break;
                }
                if (TopicTable::updateStatus($topicId, $nextStatus)) {
                    $ret['success'] = true;
                } else {
                    $ret['message'] = ucfirst(Constant::objectNames['admin']) . Constant::notChangeTopicStatus;
                    $ret['success'] = false;
                }
                return $ret;
                break;
            default:
                $ret['message'] = ucfirst(Constant::columnNames['stepId']) . Constant::unknownText;
                $ret['success'] = false;
                return $ret;
        }

        $ret['message'] = ucfirst(Constant::objectNames['admin']) . Constant::notChangeTopicStatus;
        $ret['success'] = false;
        return $ret;
    }

    /**
     * API
     * adminCloseSeminar()
     *
     * HOW-TO-DO: des
     *
     * @param $stepId
     * @param $topicId
     * @param $checkedTopicIds
     * @return array
     */
    public static function adminCloseSeminar($stepId, $topicId, $checkedTopicIds)
    {
        $ret = array();
        $facultyId = $_SESSION['facultyId'];
        $facultyTopicId = TopicTable::getFacultyIdOf($topicId);

        if($facultyId != $facultyTopicId){
            $ret['message'] = ucfirst(Constant::objectNames['topic']) . " này không thuộc quyền quản lý của Khoa";
            $ret['success'] = false;
            return $ret;
        }

        switch ($_SESSION['role']) {
            case 1:
            case 4:
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $topic = TopicTable::getTopicStatus($topicId);

        switch($stepId) {
            case 2091:  //check and uncheck seminar
                //Uncompleted seminar
                $nextTopicStatus = 897;
                //Completed seminar
                if(in_array($topicId, $checkedTopicIds)) {
                    if($topic['topicStatus'] >= 899) {
                        $nextTopicStatus = 900;
                    }
                }
                if($topic['topicStatus'] >= 898) {
                    $result = TopicTable::updateStatus($topicId, $nextTopicStatus);
                    if ($result) {
                        $ret['success'] = true;
                    } else {
                        $ret['message'] = ucfirst(Constant::objectNames['admin']) . Constant::notChangeTopicStatus;
                        $ret['success'] = false;
                    }
                } else {
                    $ret['success'] = true;
                }
                break;
            case 2092:  //close seminar
                if($topic['topicStatus'] >= 897 && $topic['topicStatus'] < 900) {
                    $result = TopicTable::updateStatus($topicId, 888);
                    if ($result) {
                        $ret['success'] = true;
                    } else {
                        $ret['message'] = ucfirst(Constant::objectNames['admin']) . Constant::notChangeTopicStatus;
                        $ret['success'] = false;
                    }
                } else {
                    $ret['success'] = true;
                }
                break;
            default:
                break;
        }

        return $ret;
    }

    /**
     * API
     * adminUpdateSupervisorIds()
     *
     * HOW-TO-DO: des
     * @param $stepId
     * @param $topicId
     * @param $data
     * @return array
     */
    public static function adminUpdateSupervisorIds($stepId, $topicId, $data){
        $ret = array();

        $facultyId = $_SESSION['facultyId'];
        $facultyTopicId = TopicTable::getFacultyIdOf($topicId);

        if($facultyId != $facultyTopicId){
            $ret['message'] = ucfirst(Constant::objectNames['topic']) . " này không thuộc quyền quản lý của Khoa";
            $ret['success'] = false;
            return $ret;
        }

        $topicStatus = TopicTable::getTopicStatus($topicId);

        if($stepId == 210){
            if($topicStatus['topicStatus'] < 101 || $topicStatus['topicStatus'] > 102){
                $ret['message'] = ucfirst(Constant::objectNames['admin']) . " không thể cập nhật giảng viên cho đề tài này";
                $ret['success'] = false;
                return $ret;
            }
        } else {
            if($topicStatus['topicStatus'] < 889 || $topicStatus['topicStatus'] > 891){
                $ret['message'] = ucfirst(Constant::objectNames['admin']) . " không thể cập nhật giảng viên cho yêu cầu này";
                $ret['success'] = false;
                return $ret;
            }
        }

        switch ($_SESSION['role']) {
            case 1:
            case 4:
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $tp = array();
        if (property_exists($data, 'mainSupervisorId')) $tp['mainSupervisorId'] = $data->mainSupervisorId;
        if (property_exists($data, 'coSupervisorIds')) $tp['coSupervisorIds'] = $data->coSupervisorIds;
        $topic = new Topic($tp);

        if (isset($tp['mainSupervisorId']) && $tp['mainSupervisorId'] == null) {
            $ret['message'] =  ucfirst(Constant::columnNames['mainSupervisorId']) . Constant::notEmptyText;
            $ret['success'] = false;
            return $ret;
        }  elseif (isset($tp['coSupervisorIds']) && $tp['coSupervisorIds'] == null) {
            $ret['message'] =  ucfirst(Constant::columnNames['coSupervisorIds']) . Constant::notEmptyText;
            $ret['success'] = false;
            return $ret;
        } else {
            foreach ($tp as $key => $value) {
                $action = 'check' . ucfirst($key);
                if (!$topic->$action()) {
                    $ret['message'] = ucfirst(Constant::columnNames[$key]) . Constant::invalidText;
                    $ret['success'] = false;
                    return $ret;
                }
            }
        }

        if(isset($tp['mainSupervisorId']) && $tp['mainSupervisorId'] != null) {
            $msFacultyId = OfficerTable::getFacultyIdOf($tp['mainSupervisorId']);
            if ($msFacultyId !== $facultyId) {
                $ret['message'] = "Giảng viên chính không công tác tại Khoa";
                $ret['success'] = false;
                return $ret;
            }
        }

        //reset $outOfficerIds
        if($stepId == 210){
            $result = TopicTable::updateById($topicId, $tp);
        } else {
            $result = TopicChangeTable::updateById($topicId, $tp);
        }

        $ret['success'] = true;
        return $ret;
    }

    /**
     * API
     * adminUpdateRequestDelayTopic()
     *
     * HOW-TO-DO: des
     * @param $topicId
     * @param $data
     * @return array
     */
    public static function adminUpdateRequestDelayTopic($topicId, $data)
    {
        $ret = array();
        $facultyId = $_SESSION['facultyId'];
        $facultyTopicId = TopicTable::getFacultyIdOf($topicId);

        if($facultyId != $facultyTopicId){
            $ret['message'] = ucfirst(Constant::objectNames['topic']) . " này không thuộc quyền quản lý của Khoa";
            $ret['success'] = false;
            return $ret;
        }

        switch ($_SESSION['role']) {
            case 1:
            case 4:
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $oldTopic = TopicTable::getTopicStatus($topicId);

        $tpc = array();
        if (property_exists($data, 'delayDuration')) $tpc['delayDuration'] = $data->delayDuration;

        if (!isset($tpc['delayDuration']) || $tpc['delayDuration'] == null) {
            $ret['message'] = ucfirst(Constant::columnNames['delayDuration']) . Constant::isRequiredText;
            $ret['success'] = false;
            return $ret;
        } else {
            if (!is_int($tpc['delayDuration'])) {
                $ret['message'] = ucfirst(Constant::columnNames['delayDuration']) . Constant::invalidText;
                $ret['success'] = false;
                return $ret;
            }
        }

        if($oldTopic['topicStatus'] != 894) {
            $ret['message'] = ucfirst(Constant::objectNames['admin']) . ' không thể sửa đổi yêu cầu này hiện tại';
            $ret['success'] = false;
            return $ret;
        }

        //update
        TopicChangeTable::updateById($topicId, $tpc);
        $ret['success'] = true;
        return $ret;
    }

    /**
     * API
     * adminUpdateRequestPauseTopic()
     *
     * HOW-TO-DO: des
     * @param $topicId
     * @param $data
     * @return array
     */
    public static function adminUpdateRequestPauseTopic($topicId, $data)
    {
        $ret = array();
        $facultyId = $_SESSION['facultyId'];
        $facultyTopicId = TopicTable::getFacultyIdOf($topicId);

        if($facultyId != $facultyTopicId){
            $ret['message'] = ucfirst(Constant::objectNames['topic']) . " này không thuộc quyền quản lý của Khoa";
            $ret['success'] = false;
            return $ret;
        }

        switch ($_SESSION['role']) {
            case 1:
            case 4:
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $oldTopic = TopicTable::getTopicStatus($topicId);

        $tpc = array();
        if (property_exists($data, 'startPauseDate')) $tpc['startPauseDate'] = $data->startPauseDate;
        if (property_exists($data, 'pauseDuration')) $tpc['pauseDuration'] = $data->pauseDuration;
        $topicChange = new TopicChange($tpc);

        if (isset($tpc['startPauseDate']) && $tpc['startPauseDate'] == null) {
            $ret['message'] = ucfirst(Constant::columnNames['startPauseDate']) . Constant::notEmptyText;
            $ret['success'] = false;
            return $ret;
        } elseif (isset($tpc['pauseDuration']) && $tpc['pauseDuration'] == null) {
            $ret['message'] = ucfirst(Constant::columnNames['pauseDuration']) . Constant::notEmptyText;
            $ret['success'] = false;
            return $ret;
        } else {
            foreach ($tpc as $key => $value) {
                $action = 'check' . ucfirst($key);
                if (!$topicChange->$action()) {
                    $ret['message'] = ucfirst(Constant::columnNames[$key]) . Constant::invalidText;
                    $ret['success'] = false;
                    return $ret;
                }
            }
        }

        if($oldTopic['topicStatus'] != 301) {
            $ret['message'] = ucfirst(Constant::objectNames['admin']) . ' không thể sửa đổi yêu cầu này hiện tại';
            $ret['success'] = false;
            return $ret;
        }

        //update
        TopicChangeTable::updateById($topicId, $tpc);
        $ret['success'] = true;
        return $ret;
    }

    /**
     * API
     * universityAcceptRegisterTopic()
     *
     * HOW-TO-DO: des
     * @param $topicId
     * @param $data
     * @return array
     */
    public static function universityAcceptRegisterTopic($topicId, $data)
    {
        $ret = array();

        $facultyId = $_SESSION['facultyId'];
        $facultyTopicId = TopicTable::getFacultyIdOf($topicId);

        if($facultyId != $facultyTopicId){
            $ret['message'] = ucfirst(Constant::objectNames['topic']) . " này không thuộc quyền quản lý của Khoa";
            $ret['success'] = false;
            return $ret;
        }

        switch ($_SESSION['role']) {
            case 1:
            case 4:
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $tp = array();
        $tp['topicStatus'] = 888;
        if (property_exists($data, 'startDate')) $tp['startDate'] = $data->startDate;
        if (property_exists($data, 'defaultDeadlineDate')) $tp['defaultDeadlineDate'] = $data->defaultDeadlineDate;
        if (property_exists($data, 'deadlineDate')) $tp['deadlineDate'] = $data->deadlineDate;
        $topic = new Topic($tp);

        foreach ($tp as $key => $value) {
            $action = 'check' . ucfirst($key);
            if (!$topic->$action()) {
                $ret['message'] = ucfirst(Constant::columnNames[$key]) . Constant::invalidText;
                $ret['success'] = false;
                return $ret;
            }
        }

        $topic = TopicTable::getTopicStatus($topicId);

        if($topic['topicStatus'] >= 103){
            $result = TopicTable::updateById($topicId, $tp);

            if ($result) {
                $ret['success'] = true;
            } else {
                $ret['success'] = false;
            }

            return $ret;
        } else {
            $ret['message'] = ucfirst(Constant::objectNames['admin']) . Constant::notChangeTopicStatus;
            $ret['success'] = false;
            return $ret;
        }
    }

    /**
     * API
     * universityAcceptRequestChange()
     *
     * HOW-TO-DO: des
     * @param $topicId
     * @param $oldTopic Topic
     * @return array
     */
    public static function universityAcceptRequestChange($topicId, $oldTopic)
    {
        $ret = array();

        $facultyId = $_SESSION['facultyId'];
        $facultyTopicId = TopicTable::getFacultyIdOf($topicId);

        if($facultyId != $facultyTopicId){
            $ret['message'] = ucfirst(Constant::objectNames['topic']) . " này không thuộc quyền quản lý của Khoa";
            $ret['success'] = false;
            return $ret;
        }

        switch ($_SESSION['role']) {
            case 1:
            case 4:
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        if($oldTopic->getTopicStatus() != 892){
            $ret['message'] = ucfirst(Constant::objectNames['admin']) . Constant::notChangeTopicStatus;
            $ret['success'] = false;
            return $ret;
        }

        $newTopic = TopicChangeTable::getById($topicId);

        if(!$newTopic){
            $ret['message'] = Constant::notFoundText . ucfirst(Constant::columnNames['topicChange']);
            $ret['success'] = false;
            return $ret;
        }

        $tpc = array();
        $otp = array();
        $ntp = array();

        // Convert object to array
        $newTopicArray = json_decode(json_encode($newTopic), true);
        $diffArray = ['startPauseDate', 'pauseDuration', 'delayDuration', 'cancelReason'];
        $hasChange = false;
        foreach($newTopicArray as $key => $value){
            if(!in_array($key, $diffArray) && $value != null){
                if($key != 'id') { $hasChange = true; }

                $action = 'get' . ucfirst($key);
                if($key == 'coSupervisorIds') {
                    $coSupervisorIdsChange = $newTopic->$action();
                    $otp[$key] = $oldTopic->$action();

                    $newCoIds = array();
                    $newCoIds = explode(',', $coSupervisorIdsChange);

                    $oldCoIds = array();
                    $oldCoIds = explode(',', $otp[$key]);

                    $ntp[$key] = null;

                    if($oldCoIds[1] && $newCoIds[0] == 'del') {  //Remove first co supervisor
                        $ntp[$key] = $oldCoIds[1];
                    }
                    else if($newCoIds[0] != 'del' && $newCoIds[0] != '') { //Change first co supervisor
                        $ntp[$key] = $newCoIds[0];
                    }
                    //Other $ntp[$key] = null

                    if ($newCoIds[1] == 'del') { //Remove second co supervisor
                        $ntp[$key] = ($ntp[$key] == null) ? ($oldCoIds[0] ? $oldCoIds[0] : null)
                                                        : ($newCoIds[0] && $newCoIds[0] != del && $newCoIds[0] != '' ? $newCoIds[0] : null);
                    }
                    else if($newCoIds[1] != 'del' && $newCoIds[1] != '') { //Change second co supervisor
                        $ntp[$key] = ($ntp[$key] == null) ? ($oldCoIds[0] && $newCoIds[0] != 'del' ? ($oldCoIds[0] . ',' . $newCoIds[1]) : $newCoIds[1])
                                                        : ($oldCoIds[1] ? $newCoIds[1] : ($ntp[$key] . ',' . $newCoIds[1]));
                    }
                    else if($newCoIds[1] === '') {
                        $ntp[$key] = ($ntp[$key] == null) ? $otp[$key] : ($ntp[$key] . ',' . $oldCoIds[1]);
                    }
                } else {
                    if($key == 'requestedSupervisorId'){
                        //Update new departmentId for topic
                        $department = OfficerTable::getDepartmentIdOf($value);
                        $ntp['departmentId'] = $department;
                    }

                    $ntp[$key] = $newTopic->$action();
                    $otp[$key] = $oldTopic->$action();
                }
            }

            else if(in_array($key, $diffArray) && $value != null) {
                $tpc[$key] = $newTopic->$action();
            }
        }

        //Update topic status
        $ntp['topicStatus'] = 888;
        $ntp['outOfficerIds'] = null;

        $otp['id'] = $topicId;
        $result = true;
        //Save old information of topic
        if($hasChange) {
            $topic = new Topic($otp);
            $topicChange = new TopicChange($tpc);
            $result = TopicHistoryTable::create($topic, $topicChange);
        }

        if($result){
            $ret['newTopic'] = $ntp;
            $ret['success'] = true;
            $ret['historyId'] = $result;
        } else {
            $ret['success'] = false;
            $ret['message'] = "Lưu lịch sử đề tài không thành công";
        }

        return $ret;
    }

    /**
     * API
     * universityAcceptRequest()
     *
     * HOW-TO-DO: des
     * @param $topicId
     * @param $stepId
     * @return array
     */
    public static function universityAcceptRequest($topicId, $stepId)
    {
        $ret = array();

        $facultyId = $_SESSION['facultyId'];
        $facultyTopicId = TopicTable::getFacultyIdOf($topicId);

        if($facultyId != $facultyTopicId){
            $ret['message'] = ucfirst(Constant::objectNames['topic']) . " này không thuộc quyền quản lý của Khoa";
            $ret['success'] = false;
            return $ret;
        }

        switch ($_SESSION['role']) {
            case 1:
            case 4:
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $topic = TopicTable::getById($topicId);
        $topicStatus = $topic->getTopicStatus();
        $topicChange = TopicChangeTable::getById($topicId);

        //Array
        $topicHistory = array();
        $topicHistory['id'] = $topicId;
        $topicHistory['deadlineDate'] = $topic->getDeadlineDate();

        self::setTimeZone();

        switch ($stepId){
            case 302: //University accept request delay topic
                if($topicStatus == 895){
                    //Set new deadlineDate
                    $tp = array();
                    $delayDuration = $topicChange->getDelayDuration();
                    $deadlineDate = new DateTime($topic->getDeadlineDate());

                    if($delayDuration == null) {
                        $ret['success'] = false;
                        $ret['message'] = ucfirst(Constant::columnNames['delayDuration']) . Constant::invalidText;
                        return $ret;
                    }

                    $newDeadlineDate = $deadlineDate->modify('+' . $delayDuration . ' months');

                    $tp['topicStatus'] = 888;
                    $tp['deadlineDate'] = $newDeadlineDate->format('Y-m-d H:i:s');
                    $result = TopicTable::updateById($topicId, $tp);
                    if ($result) {
                        $topicHistory['deadlineDate'] = $topic->getDeadlineDate();
                        $tpObj = new Topic($topicHistory);
                        $tpcObj = new TopicChange($topicHistory);
                        $historyRet = TopicHistoryTable::create($tpObj, $tpcObj);

                        if($historyRet){
                            $ret['success'] = true;
                            $ret['historyId'] = $historyRet;
                        } else {
                            $ret['success'] = false;
                            $ret['message'] = "Lưu lịch sử đề tài không thành công";
                        }
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 303: //University accept request cancel topic
                if($topicStatus == 202){
                    $result = TopicTable::updateStatus($topicId, 1);
                    if ($result) {
                        $topicHistory['deadlineDate'] = null;
                        $topicHistory['cancelReason'] = $topicChange->getCancelReason();
                        $tpObj = new Topic($topicHistory);
                        $tpcObj = new TopicChange($topicHistory);
                        $historyRet = TopicHistoryTable::create($tpObj, $tpcObj);

                        if($historyRet){
                            $ret['success'] = true;
                            $ret['historyId'] = $historyRet;
                        } else {
                            $ret['success'] = false;
                            $ret['message'] = "Lưu lịch sử đề tài không thành công";
                        }
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            case 304:   //University accept request pause
                if($topicStatus == 302){
                    //Set new deadlineDate
                    $tp = array();
                    $startPauseDate = new DateTime($topicChange->getStartPauseDate());
                    $pauseDuration = $topicChange->getPauseDuration();
                    $deadlineDate = new DateTime($topic->getDeadlineDate());

                    if($pauseDuration == null) {
                        $ret['success'] = false;
                        $ret['message'] = ucfirst(Constant::columnNames['pauseDuration']) . Constant::invalidText;
                        return $ret;
                    }

                    $newDeadlineDate = $deadlineDate->modify('+' . $pauseDuration . ' months');
                    $today = new DateTime();

                    if($startPauseDate <= $today){
                        //update topic status
                        $tp['topicStatus'] = 303;
                    } else {
                        $tp['topicStatus'] = 887;
                    }

                    $tp['deadlineDate'] = $newDeadlineDate->format('Y-m-d H:i:s');
                    $result = TopicTable::updateById($topicId, $tp);
                    if ($result) {
                        $topicHistory['deadlineDate'] = $topic->getDeadlineDate();
                        $topicHistory['startPauseDate'] = $topicChange->getStartPauseDate();
                        $topicHistory['pauseDuration'] = $topicChange->getPauseDuration();
                        $tpObj = new Topic($topicHistory);
                        $tpcObj = new TopicChange($topicHistory);
                        $historyRet = TopicHistoryTable::create($tpObj, $tpcObj);

                        if($historyRet){
                            $ret['success'] = true;
                            $ret['historyId'] = $historyRet;
                        } else {
                            $ret['success'] = false;
                            $ret['message'] = "Lưu lịch sử đề tài không thành công";
                        }
                    } else {
                        $ret['success'] = false;
                    }
                    return $ret;
                }
                break;
            default:
                $ret['message'] = ucfirst(Constant::columnNames['stepId']) . Constant::unknownText;
                $ret['success'] = false;
                return $ret;
        }

        //False
        $ret['message'] = ucfirst(Constant::objectNames['admin']) . Constant::notChangeTopicStatus;
        $ret['success'] = false;
        return $ret;
    }

    /**
     * API
     * universityAcceptTopic()
     *
     * HOW-TO-DO: des
     * @param $param
     */
    public static function adminDeleteTopic($param)
    {
        $id = $param['id'];
        $facultyId = $_SESSION['facultyId'];
        $facultyTopicId = TopicTable::getFacultyIdOf($id);

        if($facultyId != $facultyTopicId){
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['topic']
            ));
            return;
        }

        switch($_SESSION['role']) {
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

        $topic = TopicTable::getById($id);
        $topicStatus = $topic->getTopicStatus();
        $referenceUrl = $topic->getReferenceUrl();
        $registerUrl = $topic->getRegisterUrl();

        echo $referenceUrl;

        if($topicStatus == 891){
            http_response_code(403);
            echo json_encode(array(
                'message' => 'Không thể xóa đề tài đang chờ phê duyệt chỉnh sửa'
            ));
            return;
        }

        if($topicStatus == 888){
            http_response_code(403);
            echo json_encode(array(
                'message' => 'Đề tài đang trong quá trình thực hiện. Vui lòng đánh dấu đề tài quá hạn trước'
            ));
            return;
        }

        if($topicStatus == 700){
            http_response_code(403);
            echo json_encode(array(
                'message' => 'Đề tài đang trong thời gian bảo vệ. Không thể xóa'
            ));
            return;
        }

        //Get active quota
        $quota = self::getActiveQuotaVersion();
        if(!$quota){
            http_response_code(400);
            echo json_encode(array(
                'message' => "Không có phiên bản định mức nào đang có hiệu lực"
            ));
            return;
        }

        //Save all activityIds
        $activityIds = TopicTable::getActivitiesByTopicId($id);

        //Delete topic, its topics_changes & activities_topics records
        $topicResult = TopicTable::deleteById($id);

        if ($topicResult['rowCount']) {
            $errorCount = 0;
            //Delete all activities safety
            foreach($activityIds as $actId){
                //Save documentId
                $documentId = ActivityTable::getDocumentIdOf($actId);

                //Delete activity
                $actResult = ActivityTable::deleteById($actId);

                //Delete document & its attachment
                if($actResult['rowCount']){
                    if($documentId['0'] != null){
                        //Remove attachment from server
                        $attachment = AttachmentTable::getByDocumentId($documentId['0']);
                        $attmUrl = $_SERVER['DOCUMENT_ROOT'] . $attachment->getUrl();
                        if(file_exists($attmUrl)) {
                            //chmod($attmUrl, 0755);    //Change the file permissions if allowed
                            unlink($attmUrl);
                        }

                        DocumentTable::deleteById($documentId['0']);
                    }
                } else {
                    $errorCount += 1;
                    break;
                }
            }

            if($errorCount == 0) {
                //Remove related attachments: register & reference
                $referenceUrl = $_SERVER['DOCUMENT_ROOT'] . $referenceUrl;
                if(file_exists($referenceUrl)) {
                    //chmod($attmUrl, 0755);    //Change the file permissions if allowed
                    unlink($referenceUrl);
                }
                $registerUrl = $_SERVER['DOCUMENT_ROOT'] . $registerUrl;
                if(file_exists($registerUrl)) {
                    //chmod($attmUrl, 0755);    //Change the file permissions if allowed
                    unlink($registerUrl);
                }
            }

            if($topicStatus >= 102) {
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

                //Revert quota
                $mainSupervisorId = $topic->getMainSupervisorId();
                $coSupervisorIds = explode(',', $topic->getCoSupervisorIds());
                $outSupervisorIds = explode(',', $topic->getOutOfficerIds());

                //Get factor
                $mainFactorAction = 'getMain' . $factor;
                $coFactorAction = 'getCo' . $factor;
                $mainFactor = $quota->$mainFactorAction();
                $coFactor = $quota->$coFactorAction();

                //Count number of co out officers
                $numCoOutOfficers = 0;
                for ($i = 0; $i < count($outOfficerIds); $i++) {
                    if ($i > 0 && $outOfficerIds[$i] != '') {
                        $numCoOutOfficers += 1;
                    }
                }

                //count number of co supervisors
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
            }

            //END
            http_response_code(204);
        } else {
            if($topicResult['data']['1'] && $topicResult['data']['2']){
                http_response_code(400);
                echo json_encode(array(
                    'message' => 'Xóa đề tài không thành công'
                ));
                return;
            } else {
                http_response_code(404);
                echo json_encode(array(
                    'message' => Constant::notFoundText . ucfirst(Constant::objectNames['topic'])
                ));
                return;
            }
        }
    }


    /**
     * API
     * adminManageRequestChangeSession()
     *
     * HOW-TO-DO: des
     */
    public static function adminManageRequestChangeSession()
    {
        $facultyId = $_SESSION['facultyId'];

        switch($_SESSION['role']) {
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
        $objs = json_decode($json);

        if (!is_array($objs) || count($objs) == 0) {
            http_response_code(400);
            echo json_encode(array(
                'message' => Constant::emptyList
            ));
            return;
        }

        $ret = array();

        foreach ($objs as $obj) {
            $stepId = property_exists($obj, 'stepId') ? intval($obj->stepId) : null;
            $topicId = property_exists($obj, 'topicId') ? $obj->topicId : null;

            if($stepId == null){
                $ret[] = array(
                    'topicId' => $topicId,
                    'error' => ucfirst(Constant::columnNames['stepId']) . Constant::isRequiredText
                );
                continue;
            }

            if($topicId == null) {
                $ret[] = array(
                    'topicId' => $topicId,
                    'error' => ucfirst(Constant::columnNames['topicId']) . Constant::isRequiredText
                );
                continue;
            }

            $facultyTopicId = TopicTable::getFacultyIdOf($topicId);
            if($facultyId != $facultyTopicId){
                $ret[] = array(
                    'topicId' => $topicId,
                    'error' => ucfirst(Constant::objectNames['topic']) . " không thuộc quyền quản lý của Khoa"
                );
                continue;
            }

            //Check Topic existing
            $topic = TopicTable::getById($topicId);
            if(!$topic) {
                $ret[] = array(
                    'topicId' => $topicId,
                    'error' =>  Constant::notFoundText . ucfirst(Constant::objectNames['topic'])
                );
                continue;
            }

            //Get current topic status
            $topicStatus = $topic->getTopicStatus();

            switch ($stepId){
                case 211:
                    if($topicStatus == 888){
                        $result = TopicTable::updateStatus($topicId, 889);
                        if (!$result) {
                            $ret[] = array(
                                'topicId' => $topicId,
                                'error' => ucfirst(Constant::objectNames['admin']) . Constant::notChangeTopicStatus
                            );
                        } else {
                            //queue email to ready to be send
                            Middleware::queueEmail($topic->getLearnerId(),$topicId,5);
                        }
                    } else {
                        $ret[] = array(
                            'topicId' => $topicId,
                            'error' => 'Đề tài chưa đủ điều kiện đăng kí thay đổi'
                        );
                    }
                    break;
                case 2012:
                    if($topicStatus >= 888 && $topicStatus <= 889){
                        //learner not change topic
                        TopicTable::updateStatus($topicId, 888);
                    } else {
                        $ret[] = array(
                            'topicId' => $topicId,
                            'error' => ucfirst(Constant::objectNames['admin']) . Constant::notChangeTopicStatus
                        );
                    }
                    break;
                default:
                    $ret[] = array(
                        'topicId' => $topicId,
                        'error' => ucfirst(Constant::columnNames['stepId']) . Constant::unknownText
                    );
                    continue;
            }
        }

        $api = 'api/change-topic-email';
        //Call background sending mail application
        //Middleware::activeCurl($api);

        http_response_code(200);
        echo json_encode($ret);
    }

    /**
     * API
     * adminManageRequestProtectSession()
     *
     * HOW-TO-DO: des
     */
    public static function adminManageRequestProtectSession()
    {
        $facultyId = $_SESSION['facultyId'];

        switch($_SESSION['role']) {
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
        $objs = json_decode($json);

        if (!is_array($objs) || count($objs) == 0) {
            http_response_code(400);
            echo json_encode(array(
                'message' => Constant::emptyList
            ));
            return;
        }

        $ret = array();

        foreach ($objs as $obj) {
            $stepId = property_exists($obj, 'stepId') ? intval($obj->stepId) : null;
            $topicId = property_exists($obj, 'topicId') ? $obj->topicId : null;

            if($stepId == null){
                $ret[] = array(
                    'topicId' => $topicId,
                    'error' => ucfirst(Constant::columnNames['stepId']) . Constant::isRequiredText
                );
                continue;
            }

            if($topicId == null) {
                $ret[] = array(
                    'topicId' => $topicId,
                    'error' => ucfirst(Constant::columnNames['topicId']) . Constant::isRequiredText
                );
                continue;
            }

            $facultyTopicId = TopicTable::getFacultyIdOf($topicId);
            if($facultyId != $facultyTopicId){
                $ret[] = array(
                    'topicId' => $topicId,
                    'error' => ucfirst(Constant::objectNames['topic']) . " không thuộc quyền quản lý của Khoa"
                );
                continue;
            }

            //Check Topic existing
            $topic = TopicTable::getById($topicId);
            if(!$topic) {
                $ret[] = array(
                    'topicId' => $topicId,
                    'error' => Constant::notFoundText . ucfirst(Constant::objectNames['topic'])
                );
                continue;
            }

            //Get current topic status
            $topicStatus = $topic->getTopicStatus();
            $topicType = $topic->getTopicType();

            $topicStatusCondition = 888;
            if($topicType != 1) {   //graduated learner
                $topicStatusCondition = 900;
            }

            switch ($stepId){
                case 2053:  //Admin open session
                    if($topicStatus == $topicStatusCondition){
                        $nextTopicStatus = 666;
                        if($topicType == 1) { //students do not need register and be approved by supervisor
                            $nextTopicStatus = 668;
                        }
                        $result = TopicTable::updateStatus($topicId, $nextTopicStatus);
                        if (!$result) {
                            $ret[] = array(
                                'topicId' => $topicId,
                                'error' => ucfirst(Constant::objectNames['admin']) . Constant::notChangeTopicStatus
                            );
                        } else {
                            //queue email to ready to be send
                            Middleware::queueEmail($topic->getLearnerId(),$topicId,6);
                        }
                    } else {
                        $ret[] = array(
                            'topicId' => $topicId,
                            'error' => ucfirst(Constant::objectNames['admin']) . Constant::notChangeTopicStatus
                        );
                    }


                    break;
                case 2054: //Admin close session
                    if($topicStatus == $topicStatusCondition || ($topicStatus >= 666 && $topicStatus <= 668)){
                        TopicTable::updateStatus($topicId, $topicStatusCondition);
                    }
                    // } else {
                    //     $ret[] = array(
                    //         'topicId' => $topicId,
                    //         'error' => ucfirst(Constant::objectNames['admin']) . Constant::notChangeTopicStatus
                    //     );
                    // }
                    break;
                default:
                    $ret[] = array(
                        'topicId' => $topicId,
                        'error' => ucfirst(Constant::columnNames['stepId']) . Constant::unknownText
                    );
                    continue;
            }
        }

        $api = 'api/protect-topic-email';
        //Call background sending mail application
        //Middleware::activeCurl($api);

        http_response_code(200);
        echo json_encode($ret);
    }

    /**
     * API
     * changeTopicStatusRequestPause()
     *
     * HOW-TO-DO: des
     */
    public static function changeTopicStatusRequestPause(){
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

        foreach ($objs as $obj) {
            $topicId = property_exists($obj, 'topicId') ? $obj->topicId : null;

            if(!isset($topicId) || $topicId == null ){
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['topicId']) . Constant::isRequiredText
                );
                continue;
            }

            $topic = TopicTable::getTopicStatus($topicId);
            if(!$topic){
                $ret[] = array(
                    'topicId' => $topicId,
                    'error' => Constant::notFoundText . ucfirst(Constant::objectNames['topic'])
                );
                continue;
            }

            self::setTimeZone();
            $today = new DateTime();
            $topicChange = TopicChangeTable::getById($topicId);
            $startPauseDate = new DateTime($topicChange->getStartPauseDate());
            $pauseDuration = '+' . $topicChange->getPauseDuration() . ' days';

            switch ($topic['topicStatus']) {
                case 887:
                    $startDiff = $startPauseDate <= $today;
                    //Pause topic now
                    if($startDiff){
                        $result = TopicTable::updateStatus($topicId, 303);
                        if (!$result){
                            $ret[] = array(
                                'topicId' => $topicId,
                                'error' => 'Không thể sửa đổi trạng thái đề tài này hiện tại'
                            );
                        }
                    } else {
                        $ret[] = array(
                            'topicId' => $topicId,
                            'error' => 'Đề tài chưa trong thời gian tạm hoãn thực hiện'
                        );
                    }
                    break;
                case 303:
                    //End pause topic session
                    $endPauseDate = $startPauseDate->modify($pauseDuration);
                    $endDiff = $endPauseDate <= $today;
                    if($endDiff){
                        $result = TopicTable::updateStatus($topicId, 888);
                        if (!$result){
                            $ret[] = array(
                                'topicId' => $topicId,
                                'error' => 'Không thể sửa đổi trạng thái đề tài này hiện tại'
                            );
                        }
                    } else {
                        $ret[] = array(
                            'topicId' => $topicId,
                            'error' => 'Đề tài đang trong thời gian tạm hoãn thực hiện'
                        );
                    }
                    break;
                default:
                    $ret[] = array(
                        'topicId' => $topicId,
                        'error' => Constant::missingText
                    );
                    continue;
            }
        }

        http_response_code(200);
        echo json_encode($ret);
    }

    /**
     * API
     * learnerUpdateRequestChangeById()
     *
     * HOW-TO-DO: des
     * @param $param
     */
    public static function learnerUpdateRequestChangeById($param){
        $id = $param['id'];

        $facultyId = $_SESSION['facultyId'];
        $facultyTopicId = TopicTable::getFacultyIdOf($id);

        if($facultyTopicId != $facultyId){
            http_response_code(404);
            echo json_encode(array(
                'message' => ucfirst(Constant::objectNames['topic']) . " này không thuộc quyền quản lý của Khoa"
            ));
            return;
        }

        $topicResult = TopicTable::getById($id);

        switch ($_SESSION['role']) {
            case 2:
                if($_SESSION['uid'] != $topicResult->getLearnerId()){
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
        $data = json_decode($json);

        $tpc = array();
        if (property_exists($data, 'vietnameseTopicTitle')) $tpc['vietnameseTopicTitle'] = $data->vietnameseTopicTitle;
        if (property_exists($data, 'mainSupervisorId')) $tpc['mainSupervisorId'] = $data->mainSupervisorId;
        if (property_exists($data, 'isEnglish')) $tpc['isEnglish'] = $data->isEnglish;
        if (property_exists($data, 'description')) $tpc['description'] = $data->description;
        if (property_exists($data, 'englishTopicTitle')) $tpc['englishTopicTitle'] = $data->englishTopicTitle;
        if (property_exists($data, 'coSupervisorIds')) $tpc['coSupervisorIds'] = $data->coSupervisorIds;
        if (property_exists($data, 'requestedSupervisorId')) $tpc['requestedSupervisorId'] = $data->requestedSupervisorId;
        if (property_exists($data, 'tags')) $tpc['tags'] = $data->tags;
        if (property_exists($data, 'startPauseDate')) $tpc['startPauseDate'] = $data->startPauseDate;
        if (property_exists($data, 'pauseDuration')) $tpc['pauseDuration'] = $data->pauseDuration;
        if (property_exists($data, 'delayDuration')) $tpc['delayDuration'] = $data->delayDuration;
        $request = new TopicChange($tpc);

        $status = $topicResult->getTopicStatus();
        if(isset($tpc['startPauseDate']) || isset($tpc['pauseDuration'])){
            if($status > 302){
                http_response_code(403);
                echo json_encode(array(
                    'message' => ucfirst(Constant::objectNames['learner']) . ' không thể sửa đổi yêu cầu tạm hoãn'
                ));
                return;
            }
        } elseif(isset($tpc['delayDuration'])){
            if($status > 895){
                http_response_code(403);
                echo json_encode(array(
                    'message' => ucfirst(Constant::objectNames['learner']) . ' không thể sửa đổi yêu cầu gia hạn'
                ));
                return;
            }
        } else {
            if($status > 892){
                http_response_code(403);
                echo json_encode(array(
                    'message' => ucfirst(Constant::objectNames['learner']) . ' không thể sửa đổi yêu cầu cập nhật đề tài'
                ));
                return;
            }
        }

        if (isset($tpc['vietnameseTopicTitle']) && $tpc['vietnameseTopicTitle'] == null) {
            http_response_code(400);
            echo json_encode(array(
                "message" => ucfirst(Constant::columnNames['vietnameseTopicTitle']) . Constant::notEmptyText
            ));
            return;
        } elseif (isset($tpc['description']) && $tpc['description'] == null) {
            http_response_code(400);
            echo json_encode(array(
                "message" => ucfirst(Constant::columnNames['description']) . Constant::notEmptyText
            ));
            return;
        } else {
            foreach ($tpc as $key => $value) {
                $action = 'check' . ucfirst($key);
                if (!$request->$action()) {
                    http_response_code(400);
                    echo json_encode(array(
                        "message" => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                    ));
                    return;
                }
            }
        }

        if (property_exists($data, 'outOfficerIds'))
        {
            $tp = array();
            $tp['outOfficerIds'] = $data->outOfficerIds;
            $topic = new Topic($tp);

            if (!$topic->checkOutOfficerIds()) {
                http_response_code(400);
                echo json_encode(array(
                    "message" => ucfirst(Constant::columnNames['outOfficerIds']) . Constant::invalidText
                ));
                return;
            }
            TopicTable::updateById($id, $tp);
        }

        if(isset($tpc['mainSupervisorId'])) {
            $msFacultyId = OfficerTable::getFacultyIdOf($tpc['mainSupervisorId']);
            if ($msFacultyId == null) {
                http_response_code(404);
                echo json_encode(array(
                    "message" => "Giảng viên chính không công tác tại Khoa"
                ));
                return;
            }
        }

        TopicChangeTable::updateById($id, $tpc);
        http_response_code(200);
        echo json_encode(array(
            "message" => Constant::updated
        ));
    }

    /**
     * API
     * getRequestChangeById()
     *
     * HOW-TO-DO: des
     * @param $param
     */
    public static function getRequestChangeById($param){
        $id = $param['id'];

        $facultyId = $_SESSION['facultyId'];
        $facultyTopicId = TopicTable::getFacultyIdOf($id);

        if($facultyTopicId != $facultyId){
            http_response_code(404);
            echo json_encode(array(
                'message' => ucfirst(Constant::objectNames['topic']) . " này không thuộc quyền quản lý của Khoa"
            ));
            return;
        }

        if($_SESSION['role'] == 0){
            http_response_code(403);
            echo json_encode(array(
                'message' => Constant::notPermissionText
            ));
            return;
        }

        $result = TopicChangeTable::getById($id);

        if ($result !== null) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . ucfirst(Constant::objectNames['topic'])
            ));
        }
    }

    /**
     * API
     * departmentAcceptRequest()
     *
     * HOW-TO-DO: des
     * @param $topicId
     * @param $stepId
     * @return array
     */
    public static function departmentAcceptRequest($topicId, $stepId)
    {
        $ret = array();
        switch ($_SESSION['role']) {
            case 6:
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $topic = TopicTable::getTopicStatus($topicId);
        if($topic['topicType'] != 2) {
            $ret['message'] = ucfirst(Constant::objectNames['topic']) . " không phải đề tài luận văn";
            $ret['success'] = false;
            return $ret;
        }

        switch ($stepId){
            case 5001: //Head of department accept request
                if($topic['topicStatus'] == 104){
                    $result = TopicTable::updateStatus($topicId, 102);
                } else {
                    $result = TopicTable::updateStatus($topicId, 891);
                }

                if ($result) {
                    $ret['success'] = true;
                } else {
                    $ret['success'] = false;
                }
                return $ret;
                break;
            case 5002: //Head of department wonder request
                if($topic['topicStatus'] == 104){
                    $result = TopicTable::updateStatus($topicId, 100);
                } else {
                    $result = TopicTable::updateStatus($topicId, 889);
                }

                if ($result) {
                    $ret['success'] = true;
                } else {
                    $ret['success'] = false;
                }
                return $ret;
                break;
            default:
                $ret['message'] = ucfirst(Constant::columnNames['stepId']) . Constant::unknownText;
                $ret['success'] = false;
                return $ret;
        }

        //False
        $ret['message'] = ucfirst(Constant::objectNames['lecturer']) . Constant::notChangeTopicStatus;
        $ret['success'] = false;
        return $ret;
    }

    /**
     * API
     * departmentDenyRequest()
     *
     * HOW-TO-DO: des
     * @param $topicId
     * @param $stepId
     * @return array
     */
    public static function departmentDenyRequest($topicId, $stepId)
    {
        $ret = array();
        switch ($_SESSION['role']) {
            case 6:
                break;
            default:
                $ret['message'] = Constant::notPermissionText;
                $ret['success'] = false;
                return $ret;
        }

        $topic = TopicTable::getTopicStatus($topicId);
        if($topic['topicType'] != 2) {
            $ret['message'] = ucfirst(Constant::objectNames['topic']) . " không phải đề tài luận văn";
            $ret['success'] = false;
            return $ret;
        }

        switch ($stepId){
            case 5000: //Head of department deny request
                if($topic['topicStatus'] == 104){
                    //reset topic
                    $result = TopicTable::updateStatus($topicId, 100);
                } else {
                    $result = TopicTable::updateStatus($topicId, 889);
                }

                if ($result) {
                    $ret['success'] = true;
                } else {
                    $ret['success'] = false;
                }
                return $ret;
                break;
            default:
                $ret['message'] = ucfirst(Constant::columnNames['stepId']) . Constant::unknownText;
                $ret['success'] = false;
                return $ret;
        }

        //False
        $ret['message'] = ucfirst(Constant::objectNames['lecturer']) . Constant::notChangeTopicStatus;
        $ret['success'] = false;
        return $ret;
    }

    /**
     * API
     * deleteRequestChangeById()
     *
     * HOW-TO-DO: des
     * @param $param
     */
    public static function deleteRequestChangeById($param){
        $id = $param['id'];

        $facultyId = $_SESSION['facultyId'];
        $facultyTopicId = TopicTable::getFacultyIdOf($id);

        if($facultyTopicId != $facultyId){
            http_response_code(404);
            echo json_encode(array(
                'message' => ucfirst(Constant::objectNames['topic']) . " này không thuộc quyền quản lý của Khoa"
            ));
            return;
        }

        $topic = TopicTable::getById($id);

        switch ($_SESSION['role']) {
            case 1:
            case 4:
                break;
            case 2:
                if($_SESSION['uid'] != $topic->getLearnerId()){
                    http_response_code(403);
                    echo json_encode(array(
                        'message' => Constant::notPermissionText
                    ));
                    return;
                }
                break;
            case 3:
            case 6:
                if($_SESSION['uid'] != $topic->getRequestedSupervisorId()){
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

        $topicStatus = $topic->getTopicStatus();
        $role = $_SESSION['role'];

        if($role == 2){
            switch($topicStatus) {
                case 888:
                case 889:
                case 890: //request change
                case 893: //request-pause
                case 200: //request cancel
                case 300: //request delay
                    break;
                default:
                    http_response_code(403);
                    echo json_encode(array(
                        'message' => ucfirst(Constant::objectNames['learner']) . ' không thể xóa yêu cầu này hiện tại'
                    ));
                    return;
            }
        }

        $result = TopicChangeTable::deleteById($id);
        if($result){
            $tp = array();
            $tp['topicStatus'] = ($topicStatus == 890) ? 889 : 888;
            $tp['outOfficerIds'] = null;
            TopicTable::updateById($id, $tp);
            http_response_code(204);
        } else {
            http_response_code(403);
            echo json_encode(array(
                'message' => 'Không thể xóa yêu cầu'
            ));
            return;
        }
    }

    private function setTimeZone(){
        date_default_timezone_set('Asia/Ho_Chi_Minh');
    }

    private function getActiveQuotaByDegree($degreeId) {
        $option = array();
        $option['degreeId'] = $degreeId;
        $option['isActive'] = 1;
        $result = QuotaTable::getByDegreeId($option);

        return $result;
    }

    private function getActiveQuotaVersion() {
        $result = QuotaTable::getActiveQuotaVersion();
        return $result;
    }
}
