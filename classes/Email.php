<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'core/init.php';

class Email {

    private $_PHPMailer;
    private static $_email;

    public function __construct()
    {
        $this->_PHPMailer = new PHPMailer();
        $this->_PHPMailer->IsSMTP();
        $this->_PHPMailer->SMTPAuth = true;
        $this->_PHPMailer->Host = Config::get('email/smtp_host');
        $this->_PHPMailer->Port = Config::get('email/smtp_port');
        $this->_PHPMailer->Username = Config::get('email/username');
        $this->_PHPMailer->Password = Config::get('email/password');
        $this->_PHPMailer->SetFrom(Config::get('email/email'), Config::get('email/email_name'));
        $this->_PHPMailer->SMTPDebug = SMTP::DEBUG_SERVER;
        $this->_PHPMailer->Debugoutput = function($str, $level) {  
            $log = '/var/www/log/emails.log';
            $date = date('Y-m-d H:i:s');
            $message = "[$date][$level]: $str \n";
            file_put_contents($log, $message, FILE_APPEND | LOCK_EX);
        };
    }

    public static function getInstance()
    {
        if(!isset(self::$_email)) {
            self::$_email = new Email();
        }
        return self::$_email;
    }

    public function sendEmail($to, $subject, $template, $variables = array())
    {
        $this->_PHPMailer->clearAddresses();
        $this->_PHPMailer->clearAllRecipients();
        $this->_PHPMailer->clearAttachments();
        $this->_PHPMailer->AddEmbeddedImage('email_templates/header-email.png', 'header_logo');

        $email = file_get_contents('email_templates/'.$template.'.html');
        foreach($variables as $key => $value)
        {
            $email = str_replace('{'.$key.'}', $value, $email);
        }

        $this->_PHPMailer->AddAddress($to);
        $this->_PHPMailer->Subject = $subject;
        $this->_PHPMailer->MsgHTML($email);
        $this->_PHPMailer->AltBody = strip_tags($email);
        $this->_PHPMailer->Send();
    }

    public function sendEmailWithAttachments($to, $subject, $template, $variables = array(), $attachments = array())
    {
        $this->_PHPMailer->clearAddresses();
        $this->_PHPMailer->clearAllRecipients();
        $this->_PHPMailer->clearAttachments();
        $this->_PHPMailer->AddEmbeddedImage('email_templates/header-email.png', 'header_logo');
        foreach($attachments as $attachment)
        {
            $this->_PHPMailer->AddAttachment($attachment);
        }

        $email = file_get_contents('email_templates/'.$template.'.html');
        foreach($variables as $key => $value)
        {
            $email = str_replace('{'.$key.'}', $value, $email);
        }

        $this->_PHPMailer->AddAddress($to);
        $this->_PHPMailer->Subject = $subject;
        $this->_PHPMailer->MsgHTML($email);
        $this->_PHPMailer->AltBody = strip_tags($email);
        $this->_PHPMailer->Send();
    }
}