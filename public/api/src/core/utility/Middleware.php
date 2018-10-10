<?php
/**
 * Created by PhpStorm.
 * User: Can
 * Date: 18-Nov-16
 * Time: 11:10 PM
 */

namespace core\utility;

use core\model\KnowledgeAreaTable;
use core\model\OfficerTable;
use core\model\LearnerTable;
use core\model\MailTable;
use core\model\Mail;

require_once 'src/core/model/OfficerTable.php';
require_once 'src/core/model/LearnerTable.php';
require_once 'src/core/model/Mail.php';
require_once 'src/core/model/MailTable.php';
require_once 'src/core/model/KnowledgeAreaTable.php';

class Middleware
{
    /**
     * @param $officerId
     * @param $facultyId
     * @return bool
     */
    public static function isOfficerBelongToFaculty($officerId, $facultyId)
    {
        return OfficerTable::getFacultyIdOf($officerId) == $facultyId;
    }

    public static function getFacultyIdByAccount() {
        switch ($_SESSION['role']) {
            case 1:
                return $_SESSION['uid'];
            case 2:
                return LearnerTable::getFacultyIdOf($_SESSION['uid']);
            case 3:
            case 4:
            case 5:
            case 6:
                return OfficerTable::getFacultyIdOf($_SESSION['uid']);
            default:
                return null;
        }
    }

    public static function queueEmail($receiverId, $topicId, $type) {
        $mail = new Mail(null);
        $mail->setReceiverId($receiverId);
        $mail->setTopicId($topicId);
        $mail->setType($type);

        MailTable::insert($mail);
    }

    // public static function activeCurl($api) {
    //     // check if  is CURL is installed or not?
    //     if (!function_exists('curl_init')) {
    //         die('Sorry Curl is not installed!');
    //     }

    //     $server_name = $_SERVER['SERVER_NAME'];
    //     $server_port = $_SERVER['SERVER_PORT'];
    //     $server_protocol = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ) ? 'https://' : 'http://';
    //     $url = $server_protocol . $server_name . ':' . $server_port . '/mailer/' . $api;
        
    //     //$response = \Httpful\Request::get($url)->send();
    //     exec("C:\Users\MSI\Desktop\helo.php > /dev/null 2>&1 & ", $output, $return);
    // }
}
