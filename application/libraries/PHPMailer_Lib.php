<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PHPMailer_Lib
{
    public function __construct(){
        log_message('Debug', 'PHPMailer class is loaded.');
    }

    public function load(){
        // Include PHPMailer library files
        require_once APPPATH. 'third_party/PHPMailer/class.phpmailer.php';
        require_once APPPATH. 'third_party/PHPMailer/class.phpmaileroauth.php';
        require_once APPPATH. 'third_party/PHPMailer/class.phpmaileroauthgoogle.php';
        require_once APPPATH. 'third_party/PHPMailer/class.pop3.php';
        require_once APPPATH. 'third_party/PHPMailer/class.smtp.php';
        $mail = new PHPMailer();
        return $mail;
    }
}