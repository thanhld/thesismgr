<?php

namespace controller;

use core\MailTable;
use core\TopicTable;
use core\MailHelper;
use core\TokenGenerator;
use core\AccountTable;

require_once 'src/core/MailTable.php';
require_once 'src/core/TopicTable.php';
require_once 'src/core/AccountTable.php';
require_once 'src/core/MailHelper.php';
require_once 'src/core/TokenGenerator.php';

class MailController {
    public function __construct() {}

    /*
    *
    */
    public function sendSetPasswordEmail() {
       // Mail table object
       $mailTable = new MailTable();
       $mailHelper = new MailHelper();

       //get list of unsent mails
       $mails = $mailTable->get(1);

       foreach($mails as $mail) {
           $receiverId = $mail->getReceiverId();

           $user = AccountTable::getById($receiverId);

           if(!$user) continue;

           //Get user's vnuMail
           $receiverEmail = $user->getVnuMail();
           //Get username
           $username = $user->getUsername();

           //Create security token
           $token = TokenGenerator::generate();
           AccountTable::setToken($receiverId, $token);

           $result = $mailHelper->sendSetPasswordEmail($receiverEmail, $receiverId, $username, $token);

           if($result) {
                $id = $mail->getId();
            
                //change mail status
                MailTable::checkSentMail($id);
    
                //remove record
                $mailTable->deleteById($id);
           }
       }
    }

    /*
    *
    */
    public function sendAnnounceRegisterTopicEmail() {
       // Mail table object
       $mailTable = new MailTable();
       $mailHelper = new MailHelper();

       $mails = $mailTable->get(2);

       foreach($mails as $mail) {
           $receiverId = $mail->getReceiverId();

           $user = AccountTable::getById($receiverId);

           if(!$user) continue;

           //Get user's vnuMail
           $receiverEmail = $user->getVnuMail();

           $result = $mailHelper->sendAnnounceRegisterTopicEmail($receiverEmail);

           if($result) {
                $id = $mail->getId();
            
                //change mail status
                MailTable::checkSentMail($id);

                //remove record
                $mailTable->deleteById($id);
            }
       }
    }

    /*
    *
    */
    public function sendAnnounceChangeTopicEmail() {
 
        // Mail table object
        $mailTable = new MailTable();
        $mailHelper = new MailHelper();
 
        $mails = $mailTable->get(5);
 
        foreach($mails as $mail) {
            $receiverId = $mail->getReceiverId();
 
            $user = AccountTable::getById($receiverId);
 
            if(!$user) continue;
 
            //Get user's vnuMail
            $receiverEmail = $user->getVnuMail();
 
            $result = $mailHelper->sendAnnounceChangeTopicEmail($receiverEmail);
 
            if($result) {
                $id = $mail->getId();
            
                //change mail status
                MailTable::checkSentMail($id);
    
                //remove record
                $mailTable->deleteById($id);
           }
        }
     }

     /*
    *
    */
    public function sendAnnounceProtectTopicEmail() {
        // Mail table object
        $mailTable = new MailTable();
        $mailHelper = new MailHelper();
 
        $mails = $mailTable->get(6);
 
        foreach($mails as $mail) {
            $receiverId = $mail->getReceiverId();
 
            $user = AccountTable::getById($receiverId);
 
            if(!$user) continue;
 
            //Get user's vnuMail
            $receiverEmail = $user->getVnuMail();
 
            $result = $mailHelper->sendAnnounceProtectTopicEmail($receiverEmail);
 
            if($result) {
                $id = $mail->getId();
            
                //change mail status
                MailTable::checkSentMail($id);
    
                //remove record
                $mailTable->deleteById($id);
           }
        }
     }

    /*
    *
    */
    public function sendAnnounceSeminarTopicEmail() {
        // Mail table object
        $mailTable = new MailTable();
        $mailHelper = new MailHelper();
 
        $mails = $mailTable->get(7);
 
        foreach($mails as $mail) {
            $receiverId = $mail->getReceiverId();
 
            $user = AccountTable::getById($receiverId);
 
            if(!$user) continue;
 
            //Get user's vnuMail
            $receiverEmail = $user->getVnuMail();
 
            $result = $mailHelper->sendAnnounceSeminarTopicEmail($receiverEmail);
 
            if($result) {
                $id = $mail->getId();
            
                //change mail status
                MailTable::checkSentMail($id);
    
                //remove record
                $mailTable->deleteById($id);
           }
        }
     }

    /*
    *
    */
    public function sendAnnounceApproveTopicEmail() {
       // Mail table object
       $mailTable = new MailTable();
       $mailHelper = new MailHelper();

       $mails = $mailTable->get(3);

       foreach($mails as $mail) {
           $receiverId = $mail->getReceiverId();
           $topicId = $mail->getTopicId();

           $user = AccountTable::getById($receiverId);

           if(!$user) continue;

           $topic = TopicTable::getById($topicId);

           if(!$topic) continue;

           //Get user's vnuMail
           $receiverEmail = $user->getVnuMail();

           //Get topic's info
           $topicName = $topic['vietnameseTopicTitle'];
           $learnerName = $topic['learnerName'];

           $result = $mailHelper->sendAnnounceApproveTopicEmail($receiverEmail, $topicName, $learnerName);

           if($result) {
            $id = $mail->getId();
        
            //change mail status
            MailTable::checkSentMail($id);

            //remove record
            $mailTable->deleteById($id);
       }
       }
    }

    /*
    *
    */
    public function sendAnnounceReviewTopicEmail() {

       // Mail table object
       $mailTable = new MailTable();
       $mailHelper = new MailHelper();

       $mails = $mailTable->get(4);

       foreach($mails as $mail) {
           $receiverId = $mail->getReceiverId();
           $topicId = $mail->getTopicId();

           $user = AccountTable::getById($receiverId);

           if(!$user) continue;

           $topic = TopicTable::getById($topicId);

           if(!$topic) continue;

           //Get user's vnuMail
           $receiverEmail = $user->getVnuMail();

           //Get topic's info
           $topicName = $topic['vietnameseTopicTitle'];
           $learnerName = $topic['learnerName'];

           $result = $mailHelper->sendAnnounceReviewTopicEmail($receiverEmail, $topicName, $learnerName);

           if($result) {
                $id = $mail->getId();
            
                //change mail status
                MailTable::checkSentMail($id);

                //remove record
                $mailTable->deleteById($id);
            }
       }
    }

    public function __destruct() {}
}

?>