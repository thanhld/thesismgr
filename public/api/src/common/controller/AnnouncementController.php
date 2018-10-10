<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 3/11/2017
 * Time: 08:33 PM
 */

namespace common\controller;

use core\model\Announcement;
use core\model\AnnouncementTable;
use core\model\Attachment;
use core\model\AttachmentTable;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/utility/Paging.php';
require_once 'src/core/model/Announcement.php';
require_once 'src/core/model/AnnouncementTable.php';
require_once 'src/core/model/Attachment.php';
require_once 'src/core/model/AttachmentTable.php';

class AnnouncementController
{
    const announcementText = 'thông báo';
    /**
     * API
     * getAnnouncement()
     *
     * HOW-TO-DO: des
     */
    public function getAnnouncement()
    {
        $facultyId = $_SESSION['facultyId'];

        if($facultyId){
            $result = AnnouncementTable::getByFacultyId($facultyId);
        } else {
            $result = AnnouncementTable::get();
        }

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * adminGetAnnouncement()
     *
     * HOW-TO-DO: des
     */
    public function adminGetAnnouncement()
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

        $result = AnnouncementTable::adminGet($facultyId);

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * getAnnouncementById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function getAnnouncementById($param)
    {
        $id = $param['id'];
        $facultyId = $_SESSION['facultyId'];

        if($facultyId) {
            $facultyAnnouncementId = AnnouncementTable::getFacultyIdOf($id);
            if($facultyId !== $facultyAnnouncementId) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => Constant::notFoundText . self::announcementText
                ));
                return;
            }
            $result = AnnouncementTable::getById($id);
        } else {
            $result = AnnouncementTable::getById($id);
        }

        if ($result !== null) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . self::announcementText
            ));
        }
    }

    /**
     * API
     * adminCreateAnnouncementById()
     *
     * HOW-TO-DO: des
     */
    public function adminCreateAnnouncement()
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

        $an = array();
        $attm = array();
        if (property_exists($obj, 'title')) $an['title'] = $obj->title;
        if (property_exists($obj, 'tags')) $an['tags'] = $obj->tags;
        if (property_exists($obj, 'content')) $an['content'] = $obj->content;
        if (property_exists($obj, 'showDate')) $an['showDate'] = $obj->showDate;
        if (property_exists($obj, 'hideDate')) $an['hideDate'] = $obj->hideDate;
        if (property_exists($obj, 'name')) $attm['name'] = $obj->name;
        if (property_exists($obj, 'url')) $attm['url'] = $obj->url;

        $an['facultyId'] = $facultyId;
        $announcement = new Announcement($an);

        if(!isset($an['title']) || $an['title'] == null){
            http_response_code(400);
            echo json_encode(array(
                'error' => ucfirst(Constant::columnNames['title']) . Constant::isRequiredText
            ));
            return;
        } elseif(!isset($an['showDate']) || $an['showDate'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'error' => ucfirst(Constant::columnNames['showDate']) . Constant::isRequiredText
            ));
            return;
        } elseif(!isset($an['hideDate']) || $an['hideDate'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'error' => ucfirst(Constant::columnNames['hideDate']) . Constant::isRequiredText
            ));
            return;
        } else {
            foreach ($an as $key => $value) {
                $action = 'check' . ucfirst($key);
                if (!$announcement->$action()) {
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                    ));
                    return;
                }
            }
        }

        $anResult = AnnouncementTable::adminAddAnnouncement($announcement);

        if($anResult){

            if(isset($attm['name']) && isset($attm['url'])){
                $attm['announcementId'] = $anResult;
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

                $attmResult = AttachmentTable::addAttachment($attachment);

                if($attmResult) {
                    http_response_code(201);
                    echo json_encode(array(
                        'announcementId' => $anResult,
                        'attachmentId' => $attmResult
                    ));
                } else {
                    AnnouncementTable::deleteById($anResult);
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => "Không thể tải lên tệp đính kèm"
                    ));
                    return;
                }
            } else {
                http_response_code(201);
                echo json_encode(array(
                    'announcementId' => $anResult,
                ));
                return;
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Thông báo không được tạo thành công"
            ));
            return;
        }
    }

    /**
     * API
     * adminUpdateAnnouncementById()
     *
     * HOW-TO-DO: des
     * @param $param
     */
    public function adminUpdateAnnouncementById($param)
    {
        $facultyId = $_SESSION['facultyId'];
        $id = $param['id'];

        $facultyAnnouncementId = AnnouncementTable::getFacultyIdOf($id);

        if($facultyAnnouncementId != $facultyId){
            http_response_code(400);
            echo json_encode(array(
                'message' => Constant::notFoundText . self::announcementText
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

        $an = array();
        $attm = array();
        if (property_exists($obj, 'title')) $an['title'] = $obj->title;
        if (property_exists($obj, 'tags')) $an['tags'] = $obj->tags;
        if (property_exists($obj, 'content')) $an['content'] = $obj->content;
        if (property_exists($obj, 'showDate')) $an['showDate'] = $obj->showDate;
        if (property_exists($obj, 'hideDate')) $an['hideDate'] = $obj->hideDate;
        if (property_exists($obj, 'name')) $attm['name'] = $obj->name;
        if (property_exists($obj, 'url')) $attm['url'] = $obj->url;

        $an['facultyId'] = $facultyId;
        $announcement = new Announcement($an);

        if(isset($an['title']) && $an['title'] == null){
            http_response_code(400);
            echo json_encode(array(
                'error' => ucfirst(Constant::columnNames['title']) . Constant::notEmptyText
            ));
            return;
        } elseif(isset($an['showDate']) && $an['showDate'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'error' => ucfirst(Constant::columnNames['showDate']) . Constant::notEmptyText
            ));
            return;
        } elseif(isset($an['hideDate']) && $an['hideDate'] == null) {
            http_response_code(400);
            echo json_encode(array(
                'error' => ucfirst(Constant::columnNames['hideDate']) . Constant::notEmptyText
            ));
            return;
        } else {
            foreach ($an as $key => $value) {
                $action = 'check' . ucfirst($key);
                if (!$announcement->$action()) {
                   http_response_code(400);
                    echo json_encode(array(
                        'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                    ));
                    return;
                }
            }
        }

        //Update attachment
        if(isset($attm['name']) && isset($attm['url'])) {
            if ($attm['name'] != null && $attm['url'] != null) {
                $attm['announcementId'] = $id;
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
                $oldAttm = AttachmentTable::getByAnnouncementId($id);
                $oldAttmUrl = $oldAttm->getUrl();

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
                AttachmentTable::deleteByAnnouncementId($id);
                //Create new attachment
                AttachmentTable::addAttachment($attachment);
            } else {
                //Remove old attachment
                AttachmentTable::deleteByAnnouncementId($id);
            }
        }

        //Update announcement
        AnnouncementTable::updateById($id, $an);
        http_response_code(200);
        echo json_encode(array(
            'message' => Constant::updated
        ));
        return;
    }

    /**
     * API
     * adminDeleteAnnouncement()
     *
     * HOW-TO-DO: des
     */
    public function adminDeleteAnnouncement(){
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

        if (!is_array($obj) || count($obj) == 0) {
            http_response_code(400);
            echo json_encode(array(
                'message' => Constant::emptyList
            ));
            return;
        }

        $ret = array();

        foreach($obj as $announcementId){
            $facultyAnnouncementId = AnnouncementTable::getFacultyIdOf($announcementId);

            if($facultyAnnouncementId != $facultyId){
                $ret[] = array(
                    'id' => $announcementId,
                    'error' => Constant::notFoundText . self::announcementText
                );
                continue;
            }

            if(!is_int($announcementId)){
                $ret[] = array(
                    'outOfficerId' => $announcementId,
                    'error' => ucfirst(Constant::columnNames['announcementId']) . Constant::invalidText
                );
                continue;
            } else {
                //Remove all attachment
                AttachmentTable::deleteByAnnouncementId($announcementId);

                //Remove announcement
                $offResult = AnnouncementTable::deleteById($announcementId);
                if($offResult['rowCount'] == 0){
                    $ret[] = array(
                        'id' => $announcementId,
                        'error' => "Không thể xóa thông báo"
                    );
                    continue;
                }
            }
        }

        http_response_code(200);
        echo json_encode($ret);
    }
}