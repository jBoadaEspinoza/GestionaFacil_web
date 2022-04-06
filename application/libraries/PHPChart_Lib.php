<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PHPChart_Lib
{
    public function __construct(){
        log_message('Debug', 'PHPChart class is loaded.');
    }

    public function create($data,$target){
        // Include PHPMailer library files
        require_once APPPATH. 'third_party/PHPChart/conf.php';
        $phpchart = new C_PhpChartX($data,$target);
        return $phpchart;
    }
}