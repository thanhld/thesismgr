<?php
/**
 * Created by PhpStorm.
 * User: catcan
 * Date: 11/27/16
 * Time: 10:34 AM
 */

namespace core;

use PHPMailer;

require_once('src/core/PHPMailer/PHPMailerAutoload.php');


class MailHelper
{
    public function __construct() {}

    /**
     * Mail config
     */
    const MAIL_HOST = 'smtp.gmail.com';
    const MAIL_PORT = 587;
    const MAIL_USERNAME = 'uet.thesismgr@gmail.com';
    const MAIL_PASSWORD = 'uet.thesismgruet.thesismgr';
    const MAIL_NAME = 'UET Thesis Management System';
    const MAIL_CHARSET = 'utf-8';
    const MAIL_ENCRYPTION = 'tls';

    /**
     * Mail templates
     */
    const MAIL_RESET_PASSWORD_TEMPLATE = /** @lang text */
        '<a href="">Vui lòng truy cập liên kết: %s/set-password/%s/%s </a> để thiết lập lại mật khẩu!';

    /**
     * @return string
     */
    private function getServerHost(){
        $server_name = $_SERVER['SERVER_NAME'];
        $server_port = $_SERVER['SERVER_PORT'];
        $server_protocol = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ) ? 'https://' : 'http://';
        return $server_protocol . $server_name . ':' . $server_port;
    }

    /**
     * @param string $to
     * @param string $uid
     * @param string $token
     * @return bool
     */
    public function sendSetPasswordEmail($to = '', $uid = '', $username = '', $token = '') {
        $body = 'Tài khoản của bạn đã được tạo mới với tên đăng nhập là <b>' . $username 
        . '.</b><br/><a href=""> Vui lòng truy cập liên kết: %s/set-password/%s/%s </a> để thiết lập mật khẩu!';

        $mail = self::getPHPMailer();

        $mail->addAddress($to);
        $mail->Subject = 'Tạo mật khẩu cho tài khoản mới';
        $mail->Body = sprintf($body, self::getServerHost(), $uid, $token);
        return $mail->send();
    }

    /**
     * @param string $to
     * @return bool
     */
    public function sendAnnounceRegisterTopicEmail($to = '') {
        $body = 'Đợt đăng kí đề tài đã được mở. </br>Những học viên nhận được mail thông báo này đã đủ điều kiện đăng ký. </br>Yêu cầu học viên truy cập vào hệ thống: %s để đăng ký đề tài.';
        $mail = self::getPHPMailer();

        $mail->addAddress($to);
        $mail->Subject = 'Thông báo: Mở đợt đăng ký đề tài';
        $mail->Body = sprintf($body, self::getServerHost());
        return $mail->send();
    }

    /**
     * @param string $to
     * @return bool
     */
     public function sendAnnounceChangeTopicEmail($to = '') {
        $body = 'Đợt chỉnh sửa đề tài đã được mở. </br>Học viên truy cập vào hệ thống: %s để thực hiện chỉnh sửa đề tài.';
        $mail = self::getPHPMailer();

        $mail->addAddress($to);
        $mail->Subject = 'Thông báo: Mở đợt chỉnh sửa đề tài';
        $mail->Body = sprintf($body, self::getServerHost());
        return $mail->send();
    }

    /**
     * @param string $to
     * @return bool
     */
     public function sendAnnounceProtectTopicEmail($to = '') {
        $body = 'Đợt đăng kí bảo vệ đề tài <b>(chỉ yêu cầu với học viên cao học)</b> đã được mở. </br>Những học viên nhận được mail thông báo này đã đủ điều kiện bảo vệ đề tài. </br>Yêu cầu học viên truy cập vào hệ thống: %s để thực hiện đăng ký (áp dụng đối với học viên cao học) hoặc kiểm tra tiến độ bảo vệ đề tài.';
        $mail = self::getPHPMailer();

        $mail->addAddress($to);
        $mail->Subject = 'Thông báo: Mở đợt đăng ký bảo vệ đề tài';
        $mail->Body = sprintf($body, self::getServerHost());
        return $mail->send();
    }

    /**
     * @param string $to
     * @return bool
     */
     public function sendAnnounceSeminarTopicEmail($to = '') {
        $body = 'Đợt thảo luận đề tài (seminar) đã được mở. </br>Yêu cầu học viên truy cập vào hệ thống: %s để đăng ký.';
        $mail = self::getPHPMailer();

        $mail->addAddress($to);
        $mail->Subject = 'Thông báo: Mở đợt đăng ký seminar';
        $mail->Body = sprintf($body, self::getServerHost());
        return $mail->send();
    }

    /**
     * @param string $to
     * @param string $topicName
     * @param string $learnerName
     * @return bool
     */
    public function sendAnnounceApproveTopicEmail($to = '', $topicName = '', $learnerName = '') {
        $body = 'Phê duyệt yêu cầu cho đề tài: <b><i>"' . $topicName . '</i></b> - học viên: ' . $learnerName . '"</br>. Giảng viên vui lòng truy cập vào hệ thống: %s để thực hiện xác nhận.';
        $mail = self::getPHPMailer();

        $mail->addAddress($to);
        $mail->Subject = 'Thông báo: Phê duyệt yêu cầu đề tài';
        $mail->Body = sprintf($body, self::getServerHost());
        return $mail->send();
    }

    /**
     * @param string $to
     * @param string $uid
     * @param string $token
     * @return bool
     */
    public function sendAnnounceReviewTopicEmail($to = '', $topicName = '', $learnerName = '') {
        $body = 'Yêu cầu phản biện đề tài: <b><i>"' . $topicName . '</i></b> - học viên: ' . $learnerName . '"</br>. Giảng viên vui lòng truy cập vào hệ thống: %s để tiến hành nhận xét đề tài.';
        $mail = self::getPHPMailer();

        $mail->addAddress($to);
        $mail->Subject = 'Thông báo: Yêu cầu phản biện đề tài';
        $mail->Body = sprintf($body, self::getServerHost());
        return $mail->send();
    }

    /**
     * @param string $to
     * @param string $uid
     * @param string $token
     * @return bool
     */
    public function sendResetPasswordEmail($to = '', $uid = '', $token = '') {
        $mail = self::getPHPMailer();
        $mail->addAddress($to);
        $mail->Subject = 'Thiết lập mật khẩu mới';
        $mail->Body = sprintf(self::MAIL_RESET_PASSWORD_TEMPLATE, self::getServerHost(), $uid, $token);

        //send the message, check for errors
        if (!$mail->send()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $body
     * @return bool
     */
    public function sendEmail($to = '', $subject = '', $body = '') {
        $mail = self::getPHPMailer();

        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $body;
        return $mail->send();
    }

    /**
     * @return PHPMailer
     */
    private function getPHPMailer()
    {
        $mail = new PHPMailer();

        $mail->isSMTP(false); // Set mailer to use SMTP
        $mail->Host = self::MAIL_HOST; // Specify main and backup SMTP servers
        $mail->Port = self::MAIL_PORT; // TCP port to connect to
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = self::MAIL_USERNAME; // SMTP username
        $mail->Password = self::MAIL_PASSWORD; // SMTP password
        $mail->SMTPSecure = self::MAIL_ENCRYPTION; // Enable encryption

        // Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        //$mail->SMTPDebug = 2;
        // Ask for HTML-friendly debug output
        //$mail->Debugoutput = 'html';

        $mail->setFrom(self::MAIL_USERNAME, self::MAIL_NAME);
        $mail->addReplyTo(self::MAIL_USERNAME, self::MAIL_NAME);

        $mail->isHTML(true); // Set email format to HTML
        $mail->CharSet = self::MAIL_CHARSET;
        return $mail;
    }

    public function __destruct() {}
}