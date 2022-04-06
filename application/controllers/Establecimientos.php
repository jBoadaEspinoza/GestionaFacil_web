<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Establecimientos extends CI_Controller {

	public function __construct() {
        parent::__construct();
		$this->load->library('session');
		$this->load->model('establecimientos_model');
        $this->load->model('usuarios_model');    
    }
	
	public function abrir_cerrar()
	{
        $user=$_SESSION["user"];
        $abierto=intval($this->input->post("abierto"));
        $actualizado=$this->establecimientos_model->abrir_cerrar($abierto,$user["business_id"]);
        
        $_SESSION["user"]["business_open"]=$abierto;
        echo json_encode(array("abierto"=>$abierto,"msg"=>($abierto==0) ?  " Establecimiento cerrado" : "Establecimiento abierto"));
	}
    public function abrir_cambiar_contrasenha(){
        $user=$_SESSION["user"];
        $title="Cambiar contraseña";
        $dc_templates_extranet_establecimientos_cambiar_contrasenha["hola"]="hola";
        $template=$this->load->view('templates/extranet/establecimientos/cambiar_contrasenha',$dc_templates_extranet_establecimientos_cambiar_contrasenha,true);;
		echo json_encode(array("title"=>$title,"template"=>$template));
    }
    public function guardar_nueva_contrasenha(){
        $user=$_SESSION["user"];
        $actual=md5($this->input->post("actual"));
        if(strlen($this->input->post("nueva"))<8){
            echo json_encode(array("success"=>false,"msg_id"=>3,"msg"=>"La contraseña ingresada debe tener como minimo 8 caracteres"));
            return;
        }
        $nueva=md5($this->input->post("nueva"));
        $repetir=md5($this->input->post("repetir"));
        $business_id=$user["business_id"];
        $user_name=$user["user_name"];
        
        $user_password_old=$actual;
        $objUsuario=$this->usuarios_model->is_password_correct($business_id,$user_name,$user_password_old);
        
        if($objUsuario["success"]){
            if($nueva==$repetir){
                $user_passwor_new=$nueva;
                $objUsuario=$this->usuarios_model->change_password($business_id,$user_name,$user_password_old,$user_passwor_new);
                $this->session->unset_userdata('user');
                echo json_encode(array("success"=>true,"msg"=>$objUsuario["msg"],"redirect"=>base_url()));
                return;
            }
            echo json_encode(array("success"=>false,"msg_id"=>2,"msg"=>"Contraseña diferente"));
            return;
        }else{
            echo json_encode(array("success"=>false,"msg_id"=>1,"msg"=>"La contraseña ingresada es incorrecta"));
            return;
        }
    }
}
