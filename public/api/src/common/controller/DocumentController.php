<?php

namespace common\controller;

use core\model\Attachment;
use core\model\AttachmentTable;
use core\model\Document;
use core\model\DocumentTable;
use core\model\LearnerTable;
use core\utility\UUID;
use core\utility\Constant;
use core\utility\Paging;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/Document.php';
require_once 'src/core/model/DocumentTable.php';
require_once 'src/core/model/LearnerTable.php';
require_once 'src/core/model/Attachment.php';
require_once 'src/core/model/AttachmentTable.php';
require_once 'src/core/utility/UUID.php';
require_once 'src/core/utility/Paging.php';
require_once 'src/core/model/Topic.php';

/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2/27/2017
 * Time: 10:54 PM
 */
class DocumentController
{
    /**
     * API
     * uploadAttachment
     *
     * HOW-TO-DO: des
     */
    public function uploadAttachment()
    {
        if($_SESSION['role'] != 0 && $_SESSION['role'] != 5){
            $facultyId = $_SESSION['facultyId'];
        } else {
            http_response_code(403);
            echo json_encode(array(
                'message' => Constant::notPermissionText
            ));
            return;
        }

        //print_r($_FILES);

        if($_FILES){
            $server_path = "/uploads/totrinh/" . date('Y') . '/';
            $target_path = $_SERVER['DOCUMENT_ROOT'] . $server_path;
            if(!is_dir($target_path)){
                //Directory does not exist, so lets create it.
                mkdir($target_path, 0777, true);
            }

            //handle filename
            $strings = explode('.', $_FILES['uploadFile']['name']);
            $extension = $strings[count($strings)-1];   //get the last extension
            $fileId = UUID::v4();
            $filename = $fileId . '.' . $extension;
            $target_path = $target_path . $filename;
            
            header('Content-type: application/json;charset=utf-8');

            if(move_uploaded_file($_FILES['uploadFile']['tmp_name'], $target_path)) {
                http_response_code(200);
                echo json_encode(array(
                    'name' => $filename,
                    'url' => $server_path . $filename
                ));
                return;
            } else {
                http_response_code(400);
                echo json_encode(array(
                    'message' => "Tải tệp không thành công"
                ));
                return;
            }
        }

        http_response_code(404);
        echo json_encode(array(
            'message' => Constant::notFoundText . "tệp"
        ));
        return;
    }

    /**
     * API
     * learnerUploadRegisterAttachment
     *
     * HOW-TO-DO: des
     */
    public function learnerUploadRegisterAttachment()
    {
        if($_SESSION['role'] != 2){
            http_response_code(403);
            echo json_encode(array(
                'message' => Constant::notPermissionText
            ));
            return;
        } else {
            $uid = $_SESSION['uid'];
            $learner = LearnerTable::getById($uid);
        }

        if(!$learner) {
            http_response_code(403);
            echo json_encode(array(
                'message' => Constant::notPermissionText
            ));
            return;
        } else {
            $fullname = $learner->getFullname();
            //Standardlize name file
            $fullname = self::file_name_standardization($fullname);

            if($_FILES){

                $file_size = $_FILES['uploadRegisterAttm']['size'];
                if($file_size > 10 * 1048576) { //10MB
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => "Dung lượng tệp vượt quá 10 MB"
                    ));
                    return;
                }

                $server_path = "/uploads/dangki/luanvan/" . date('Y') . '/';
                $target_path = $_SERVER['DOCUMENT_ROOT'] . $server_path;
                if(!is_dir($target_path)){
                    //Directory does not exist, so lets create it.
                    mkdir($target_path, 0777, true);
                }

                //handle filename
                $strings = explode('.', $_FILES['uploadRegisterAttm']['name']);
                $extension = $strings[count($strings)-1];

                $filename = $fullname . '.' . $extension;
                $target_path = $target_path . $filename;

                //Remove old avatar from server
                if(file_exists($target_path)) {
                    //chmod($target_path, 0755);    //Change the file permissions if allowed
                    if(!unlink($target_path)){
                        http_response_code(400);
                        echo json_encode(array(
                            'message' => "Thay đổi tệp đăng kí luận văn không thành công"
                        ));
                        return;
                    }
                }

                header('Content-type: application/json;charset=utf-8');

                if(move_uploaded_file($_FILES['uploadRegisterAttm']['tmp_name'], $target_path)) {
                    http_response_code(200);
                    echo json_encode(array(
                        'name' => $filename,
                        'url' => $server_path . $filename
                    ));
                    return;
                } else{
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => "Tải tệp không thành công"
                    ));
                    return;
                }
            }

            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . "tệp"
            ));
            return;
        }
    }

    public function adminGetDocument()
    {
        $facultyId = $_SESSION['facultyId'];

        switch($_SESSION['role']){
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

        $result = DocumentTable::adminGet($facultyId);

        http_response_code(200);
        echo json_encode($result);
    }

    public function getTopicsByDocumentId($param) {
        $documentId = $param['id'];
        $facultyId = $_SESSION['facultyId'];

        switch($_SESSION['role']){
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

        $option = Paging::normalizeOption($_GET);
        $result = DocumentTable::getTopics($option, $documentId, $facultyId);
        $result = Paging::genNextPrev($option, $result);

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * addDocument()
     *
     * HOW-TO-DO: des
     */
    public function addDocument()
    {
        if($_SESSION['role'] != 0 && $_SESSION['role'] != 5){
            $facultyId = $_SESSION['facultyId'];
        } else {
            http_response_code(403);
            echo json_encode(array(
                'message' => Constant::notPermissionText
            ));
            return;
        }

        $json = file_get_contents('php://input');
        $obj = json_decode($json);

        $dc = array();
        $attm = array();
        $dc['facultyId'] = $facultyId;
        if (property_exists($obj, 'documentCode')) $dc['documentCode'] = $obj->documentCode;
        if (property_exists($obj, 'createdDate')) $dc['createdDate'] = $obj->createdDate;
        if (property_exists($obj, 'name')) $attm['name'] = $obj->name;
        if (property_exists($obj, 'url')) $attm['url'] = $obj->url;
        $document = new Document($dc);

        if(!isset($dc['documentCode']) || $dc['documentCode'] == null){
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['documentCode']) . Constant::isRequiredText
            ));
            return;
        }

        if(!isset($dc['createdDate']) || $dc['createdDate'] == null){
            http_response_code(400);
            echo json_encode(array(
                'message' => "Ngày ra quyết định " . Constant::isRequiredText
            ));
            return;
        }

        if(isset($attm['name']) && $attm['name'] == null){
            http_response_code(400);
            echo json_encode(array(
                'message' => "Tên tệp đính kèm " . Constant::notEmptyText
            ));
            return;
        }

        if(isset($attm['url']) && $attm['url'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Đường dẫn cho tệp đính kèm " . Constant::notEmptyText
            ));
            return;
        }

        foreach ($dc as $key => $value) {
            $action = 'check' . ucfirst($key);
            if (!$document->$action()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                ));
                return;
            }
        }

        //Create document first
        $docResult = DocumentTable::addDocument($document);

        if($docResult){
            //Create attachment if name & url are set
            if(isset($attm['name']) && isset($attm['url'])) {
                $attm['documentId'] = $docResult;
                $attachment = new Attachment($attm);

                foreach ($attm as $key => $value) {
                    $action = 'check' . ucfirst($key);
                    if (!$attachment->$action()) {
                        http_response_code(400);
                        echo json_encode(array(
                            'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                        ));

                        //Delete document record if attachment record is saved unsuccessfully
                        DocumentTable::deleteById($docResult);
                        return;
                    }
                }

                //Now create new attachment record with documentId
                $attmResult = AttachmentTable::addAttachment($attachment);

                if($attmResult) {
                    http_response_code(201);
                    echo json_encode(array(
                        'documentId' => $docResult,
                        'attachmentId' => $attmResult
                    ));
                } else {
                    //Remove uploaded attachment
                    if(file_exists($attm['url'])) {
                        //chmod($attm['url'], 0755);    //Change the file permissions if allowed
                        unlink($attm['url']);
                    }
                    //Delete document record if attachment record is saved unsuccessfully
                    DocumentTable::deleteById($docResult);
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => "Lưu tệp đính kèm không thành công"
                    ));
                    return;
                }
            }

            //Only create document
            else {
                http_response_code(201);
                echo json_encode(array(
                    'documentId' => $docResult,
                ));
                return;
            }
        } else {
            //Remove uploaded attachment
            if(file_exists($attm['url'])) {
                //chmod($attm['url'], 0755);    //Change the file permissions if allowed
                unlink($attm['url']);
            }
            http_response_code(400);
            echo json_encode(array(
                'message' => "Lưu văn bản không thành công"
            ));
            return;
        }
    }

    /**
     * API
     * adminUpdateDocumentById()
     *
     * HOW-TO-DO: des
     */
    public function adminUpdateDocumentById($param)
    {
        $id = $param['id'];
        $facultyId = $_SESSION['facultyId'];

        $facultyDocumentId = DocumentTable::getFacultyIdOf($id);

        if($facultyDocumentId != $facultyId){
            http_response_code(400);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['document']
            ));
            return;
        }

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

        $dc = array();
        $attm = array();
        $dc['facultyId'] = $facultyId;
        if (property_exists($obj, 'documentCode')) $dc['documentCode'] = $obj->documentCode;
        if (property_exists($obj, 'createdDate')) $dc['createdDate'] = $obj->createdDate;
        if (property_exists($obj, 'name')) $attm['name'] = $obj->name;
        if (property_exists($obj, 'url')) $attm['url'] = $obj->url;
        $document = new Document($dc);

        if(isset($dc['documentCode']) && $dc['documentCode'] == null){
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['documentCode']) . Constant::notEmptyText
            ));
            return;
        }

        foreach ($dc as $key => $value) {
            $action = 'check' . ucfirst($key);
            if (!$document->$action()) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                ));
                return;
            }
        }

        //Update attachment
        if(isset($attm['name']) && isset($attm['url'])) {
            if ($attm['name'] != null && $attm['url'] != null) {
                $attm['documentId'] = $id;
                $attachment = new Attachment($attm);

                foreach ($attm as $key => $value) {
                    $action = 'check' . ucfirst($key);
                    if (!$attachment->$action()) {
                        http_response_code(400);
                        echo json_encode(array(
                            'message' => ucfirst(Constant::columnNames[$key]) . " tệp đính kèm " . Constant::invalidText
                        ));
                        return;
                    }
                }

                //Delete old attachment on server
                $oldAttm = AttachmentTable::getByDocumentId($id);
                $oldAttmUrl = '';
                if($oldAttm) { $oldAttmUrl = $_SERVER['DOCUMENT_ROOT'] . $oldAttm->getUrl(); }

                if(file_exists($oldAttmUrl)) {
                    //chmod($oldAttmUrl, 0755);    //Change the file permissions if allowed
                    if(!unlink($oldAttmUrl)) {
                        http_response_code(400);
                        echo json_encode(array(
                            'message' => "Không thể xóa văn bản công văn cũ"
                        ));
                        return;
                    }
                }

                //Remove old attachment
                AttachmentTable::deleteByDocumentId($id);

                //Create new attachment
                AttachmentTable::addAttachment($attachment);
            } else {
                //Remove old attachment
                AttachmentTable::deleteByDocumentId($id);
            }
        }

        //Update document
        DocumentTable::updateById($id, $dc);
        http_response_code(200);
        echo json_encode(array(
            'message' => Constant::updated
        ));
        return;
    }

    private function file_name_standardization($name)
    {
        /* Standardize
        * Convert accented letters to standard (no accent)
        * Remove redundant spacing
        * Replace ' ' by '_'
        * Remove redundant '_'
        * Lowercase all characters
        */
        $unicode = array(
            'a'=>'å|ä|á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'A'=>'Å|Ä|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'd'=>'đ',
            'D'=>'Đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
            'o'=>'ö|ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'O'=>'Ö|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
            ' ' =>'?|(|)|[|]|{|}|#|%|-|<|>|,|:|;|.|&|–|/|'
        );
        foreach($unicode as $standard => $accent) {
            $arr = explode("|", $accent);
            $name = str_replace($arr, $standard, $name);
        }
        $name = preg_replace('/\s+/', '_', trim($name));
        $name = preg_replace('/_+/', '_', $name);
        return strtolower($name);
    }
}
