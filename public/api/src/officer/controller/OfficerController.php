<?php
namespace officer\controller;

use core\model\AreaOfficerTable;
use core\model\KnowledgeArea;
use core\model\KnowledgeAreaTable;
use core\model\Officer;
use core\model\OfficerTable;
use core\model\OutOfficerTable;
use core\model\Attachment;
use core\model\AttachmentTable;
use core\model\OutOfficer;
use core\utility\Paging;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/Officer.php';
require_once 'src/core/model/OfficerTable.php';
require_once 'src/core/model/OutOfficerTable.php';
require_once 'src/core/model/OutOfficer.php';
require_once 'src/core/model/AreaOfficerTable.php';
require_once 'src/core/model/KnowledgeAreaTable.php';
require_once 'src/core/model/Attachment.php';
require_once 'src/core/model/AttachmentTable.php';
require_once 'src/core/utility/Paging.php';

/**
 * OfficerController
 */
class OfficerController
{
    /**
     * API
     * getOfficer()
     *
     * HOW-TO-DO: des
     */
    public function getOfficer()
    {
        $option = Paging::normalizeOption($_GET);
        $result = OfficerTable::get($option);
        //$result = Paging::genNextPrev($option, $result);

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * getOfficerById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function getOfficerById($param)
    {
        $id = $param['id'];
        $result = OfficerTable::getById($id);

        if ($result !== null) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['officer']
            ));
        }
    }

    /**
     * API
     * getOutOfficer()
     *
     * HOW-TO-DO: des
     */
    public function getOutOfficer()
    {
        $result = OutOfficerTable::get();

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * getOutOfficerById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function getOutOfficerById($param)
    {
        $id = $param['id'];

        $result = new OutOfficer(OutOfficerTable::getById($id));

        if ($result !== null) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['outOfficer']
            ));
        }
    }

    /**
     * API
     * addOutOfficer()
     *
     * HOW-TO-DO: des
     */
    public function addOutOfficer()
    {
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

        $json = file_get_contents('php://input');
        $obj = json_decode($json);

        if (!is_array($obj) || count($obj) == 0) {
            http_response_code(400);
            echo json_encode(array(
                'message' => Constant::emptyList
            ));
            return;
        }

        $ret = array();
        foreach($obj as $officer){
            $oofc = array();

            if (property_exists($officer, 'fullname')) $oofc['fullname'] = $officer->fullname;
            if (property_exists($officer, 'degreeId')) $oofc['degreeId'] = $officer->degreeId;
            if (property_exists($officer, 'departmentName')) $oofc['departmentName'] = $officer->departmentName;
            $outOfficer = new OutOfficer($oofc);

            if(!isset($oofc['fullname']) || $oofc['fullname'] == null){
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['fullname']) . ' ' . Constant::objectNames['outOfficer'] . Constant::isRequiredText
                );
                continue;
            } elseif(!isset($oofc['departmentName']) || $oofc['departmentName'] == null) {
                $ret[] = array(
                    'error' => ucfirst(Constant::columnNames['departmentName']) . Constant::isRequiredText
                );
                continue;
            } else {
                foreach ($oofc as $key => $value) {
                    $action = 'check' . ucfirst($key);
                    if (!$outOfficer->$action()) {
                        $ret[] = array(
                            'error' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                        );
                        continue;
                    }
                }
            }

            $checkOutOfficer = OutOfficerTable::checkOutOfficerExisted($oofc);
            if($checkOutOfficer){
                $ret[] = array(
                    'error' => ucfirst(Constant::objectNames['outOfficer']) . " đã tồn tại"
                );
                continue;
            }

            $oofcResult = OutOfficerTable::addOutOfficer($outOfficer);

            if($oofcResult == false){
                $ret[] = array(
                    'error' => "Lưu thông tin " . Constant::objectNames['outOfficer'] . " không thành công"
                );
                continue;
            } else {
                $ret[] = array(
                    'id' => $oofcResult
                );
                continue;
            }
        }

        http_response_code(200);
        echo json_encode($ret);
    }

    /**
     * API
     * updateOfficerById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function updateOfficerById($param)
    {
        $id = $param['id'];

        switch ($_SESSION['role']) {
            case 3:
            case 4:
            case 5:
            case 6:
                if ($_SESSION['uid'] != $id){
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

        $ofc = array();
        if (property_exists($obj, 'fullname')) $ofc['fullname'] = $obj->fullname;
        if (property_exists($obj, 'otherEmail')) $ofc['otherEmail'] = $obj->otherEmail;
        if (property_exists($obj, 'phone')) $ofc['phone'] = $obj->phone;
        if (property_exists($obj, 'website')) $ofc['website'] = $obj->website;
        if (property_exists($obj, 'address')) $ofc['address'] = $obj->address;
        if (property_exists($obj, 'description')) $ofc['description'] = $obj->description;
        
        $officer = new Officer($ofc);

        if(isset($ofc['fullname']) && $ofc['fullname'] == null){
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['fullname']) . ' ' . Constant::objectNames['officer'] . Constant::isRequiredText
            ));
            return;
        }

        foreach ($ofc as $key => $value) {
            $action = 'check' . ucfirst($key);
            if (!$officer->$action()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                ));
                return;
            }
        }

        OfficerTable::updateById($id, $ofc);
        http_response_code(200);
        echo json_encode(array(
            'message' => Constant::success
        ));
    }

    /**
     * API
     * getOfficerAreas()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function getOfficerAreas($param)
    {
        $id = $param['id'];

        $departmentId = OfficerTable::getDepartmentIdOf($id);

        if ($departmentId == null) {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['officer']
            ));
            return;
        }

        $result = AreaOfficerTable::getOfficerAreas($id);

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * updateOfficerAreas()
     *
     * HOW-TO-DO: des
     */
    public function updateOfficerAreas()
    {
        $facultyOfficerId = $_SESSION['facultyId'];

        switch ($_SESSION['role']) {
            case 3:
            case 4:
            case 5:
            case 6:
                $officerId = $_SESSION['uid'];
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

        if (!is_array($obj) || count($obj) == 0) {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::emptyList
            ));
            return;
        }

        $ret = array();
        $ret['error'] = array();
        $ret['data'] = array();
        foreach ($obj as $areaId) {
            if(is_int($areaId)){
                $facultyAreaId = KnowledgeAreaTable::getFacultyIdOf($areaId);

                if ($facultyAreaId !== $facultyOfficerId) {
                    $ret['error'][] = $areaId;
                } else {
                    if (AreaOfficerTable::updateOfficerAreas($officerId, $areaId)) {
                        $ret['data'][] = $areaId;
                    } else {
                        $ret['error'][] = $areaId;
                    }
                }
            }
            else {
                continue;
            }
        }

        http_response_code(200);
        echo json_encode($ret);
    }

    /**
     * API
     * deleteOfficerArea()
     *
     * HOW-TO-DO: des
     * @param $param
     */
    public function deleteOfficerArea($param)
    {
        $areaId = $param['areaId'];
        $facultyAreaId = KnowledgeAreaTable::getFacultyIdOf($areaId);
        $facultyOfficerId = $_SESSION['facultyId'];

        if($facultyAreaId !== $facultyOfficerId){
            http_response_code(400);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['knowledgeArea']
            ));
            return;
        }

        switch ($_SESSION['role']) {
            case 3:
            case 4:
            case 5:
            case 6:
                $officerId = $_SESSION['uid'];
                break;
            default:
                http_response_code(403);
                echo json_encode(array(
                    'message' => Constant::notPermissionText
                ));
                return;
        }

        $result = AreaOfficerTable::deleteByOfficerIdAndAreaId($officerId, $areaId);

        if ($result) {
            http_response_code(204);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => ucfirst(Constant::objectNames['officer']) . " không có lĩnh vực nghiên cứu này"
            ));
        }
    }

    /**
     * API
     * uploadOfficerAvatar()
     *
     * HOW-TO-DO: des
     */
    public function uploadOfficerAvatar() {
        switch ($_SESSION['role']) {
            case 3:
            case 4:
            case 5:
            case 6:
                $officerId = $_SESSION['uid'];
                break;
            default:
                http_response_code(403);
                echo json_encode(array(
                    'message' => Constant::notPermissionText
                ));
                return;
        }

        if($_FILES){
            $target_path = $_SERVER['DOCUMENT_ROOT'] . "/uploads/officer_avatars/";
            if(!is_dir($target_path)){
                //Directory does not exist, so lets create it.
                mkdir($target_path, 0753, true);
            }

            //handle filename
            $strings = explode('.', $_FILES['uploadAvatar']['name']);
            $extension = $strings[count($strings)-1];
            $filename = $officerId . '.' . $extension;
            $target_path = $target_path . $filename;

            //Remove old avatar from server
            if(file_exists($target_path)) {
                //chmod($target_path, 0755);    //Change the file permissions if allowed
                if(!unlink($target_path)){
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => "Thay đổi ảnh đại diện cũ không thành công"
                    ));
                    return;
                }
            }

            header('Content-type: application/json;charset=utf-8');

            //Now, upload new avatar to server
            if(move_uploaded_file($_FILES['uploadAvatar']['tmp_name'], $target_path)) {
                $ofc = array();
                $ofc['avatarUrl'] = "/uploads/officer_avatars/" . $filename;
                
                //Update officer avatar url
                OfficerTable::updateById($officerId, $ofc);
                http_response_code(200);
                echo json_encode(array(
                    'message' => Constant::success
                ));

                return;
            } else{
                http_response_code(400);
                echo json_encode(array(
                    'message' => "Thay đổi ảnh đại diện mới không thành công"
                ));
                return;
            }
        }

        http_response_code(404);
        echo json_encode(array(
            'message' => Constant::notFoundText . " ảnh đại diện"
        ));
        return;
    }

    /**
     * API
     * removeOfficerAvatar()
     *
     * HOW-TO-DO: des
     */
    public function removeOfficerAvatar() {
        switch ($_SESSION['role']) {
            case 3:
            case 4:
            case 5:
            case 6:
                $officerId = $_SESSION['uid'];
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

        $ofc = array();
        if (property_exists($obj, 'avatarUrl')) $ofc['avatarUrl'] = $obj->avatarUrl;

        if(isset($ofc['avatarUrl']) && $ofc['avatarUrl'] != null) {

            $target_path = $_SERVER['DOCUMENT_ROOT'] . $ofc['avatarUrl'];

            if(file_exists($target_path)) {
                //chmod($target_path, 0755);    //Change the file permissions if allowed
                if(!unlink($target_path)){
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => "Xóa ảnh đại diện không thành công"
                    ));
                    return;
                } else {
                    $ofc['avatarUrl'] = null;
                    //Update officer avatar url
                    OfficerTable::updateById($officerId, $ofc);
                    http_response_code(200);
                    echo json_encode(array(
                        'message' => Constant::success
                    ));
                    return;
                }
            } else {
                http_response_code(404);
                echo json_encode(array(
                    'message' => "Ảnh đại diện không còn lưu trên server"
                ));
                return;
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['avatarUrl']) . ' ' . Constant::objectNames['officer'] . Constant::isRequiredText
            ));
            return;
        }
    }
}
