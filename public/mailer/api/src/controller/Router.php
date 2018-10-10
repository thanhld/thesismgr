<?php
namespace controller;

class Router
{

    public static function proc()
    {
        // Explode the URI
        $requestURI = explode('/', strtolower(strtok($_SERVER['REQUEST_URI'],'?')));
        $scriptName = explode('/', strtolower($_SERVER['SCRIPT_NAME']));
        $commandArray = array_diff_assoc($requestURI, $scriptName);

        $commandArray = array_values($commandArray);

        if (count($commandArray) != 0) {
            return self::api($commandArray);
        } else {
            return array();
        }
    }

    private static function api($commandArray) {
        $ret = array();
        $ret['actionName'] = '';
        $ret['parameters'] = array();

        /*****************************
         * ROUTE for sending set password email
         * special first command array
         ****************************/
        // Send set password email. email type = 1
        // api/set-password-email GET
        if ($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            if($commandArray[0] == 'set-password-email') {
                $ret['actionName'] = 'sendSetPasswordEmail';
            }

            //Announcement email for learners
            else if($commandArray[0] == 'register-topic-email') {
                $ret['actionName'] = 'sendAnnounceRegisterTopicEmail';
            }
            else if($commandArray[0] == 'change-topic-email') {
                $ret['actionName'] = 'sendAnnounceChangeTopicEmail';
            }
            else if($commandArray[0] == 'protect-topic-email') {
                $ret['actionName'] = 'sendAnnounceProtectTopicEmail';
            }
            else if($commandArray[0] == 'seminar-topic-email') {
                $ret['actionName'] = 'sendAnnounceSeminarTopicEmail';
            }

            //Email for supervisors
            else if($commandArray[0] == 'approve-topic-email') {
                $ret['actionName'] = 'sendAnnounceApproveTopicEmail';
            }
            else if($commandArray[0] == 'review-topic-email') {
                $ret['actionName'] = 'sendAnnounceReviewTopicEmail';
            }

            // not match
            else {
                return array();
            }
        }

        // not match
        else {
            return array();
        }

        return $ret;
    }
}