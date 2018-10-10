<?php
/**
 * Created by PhpStorm.
 * User: catcan
 * Date: 11/27/16
 * Time: 10:34 AM
 */

namespace core\utility;

use PHPMailer;

require_once('src/core/PHPMailer/PHPMailerAutoload.php');


class MailHelper
{
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
        '<a href="">Truy cập : %s/set-password/%s/%s </a> để tạo mật khẩu mới!';
    /**
     * @return string
     */
    public static function getServerHost(){
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
    public static function sendResetPasswordEmail($to = '', $uid = '', $token = '') {
        $mail = self::getPHPMailer();
        $mail->addAddress($to);
        $mail->Subject = 'Thiết lập lại mật khẩu';
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
    public static function sendEmail($to = '', $subject = '', $body = '') {
        $mail = self::getPHPMailer();

        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $body;
        return $mail->send();
    }

    /**
     * @return PHPMailer
     */
    private static function getPHPMailer()
    {
        $mail = new PHPMailer();

        $mail->isSMTP(); // Set mailer to use SMTP
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
        // $mail->SMTPDebug = 2;
        // Ask for HTML-friendly debug output
        // $mail->Debugoutput = 'html';

        $mail->setFrom(self::MAIL_USERNAME, self::MAIL_NAME);
        $mail->addReplyTo(self::MAIL_USERNAME, self::MAIL_NAME);

        $mail->isHTML(true); // Set email format to HTML
        $mail->CharSet = self::MAIL_CHARSET;

        return $mail;
    }
}