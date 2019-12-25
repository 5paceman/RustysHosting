<?php
require_once 'core/init.php';

class Email {

    private $_PHPMailer;
    private static $_email;

    public function __construct()
    {
        $this->_PHPMailer = new PHPMailer();
        $this->_PHPMailer->IsSMTP();
        $this->_PHPMailer->Host = Config::get('email/smtp_host');
        $this->_PHPMailer->Port = Config::get('email/smtp_port');
        $this->_PHPMailer->Username = Config::get('email/username');
        $this->_PHPMailer->Password = Config::get('email/password');
    }

    public static function getInstance()
    {
        if(!isset(self::$_email)) {
            self::$_email = new Email();
        }
        return self::$_email;
    }

}