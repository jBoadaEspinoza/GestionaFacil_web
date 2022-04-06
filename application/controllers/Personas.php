<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Personas extends CI_Controller {

	public function __construct() {
        parent::__construct();
		$this->load->library('session');
		$this->load->model('usuarios_model');
        $this->load->model('roles_model');
        $this->load->model('documentosTipos_model');
        $this->load->model('personas_model');
    }
	
	public function busqueda_por_dni()
	{
        $dni=$this->input->post("dni");

        $data=APIS::getDNI($dni);
        echo json_encode($data);
	}
    public function busqueda_por_documento(){
        $documento_numero=$this->input->post('documento_numero');
        $documento_tipo_id=$this->input->post('documento_tipo');

        $objPersonas=$this->personas_model->get(array("documento_tipo_id"=>$documento_tipo_id,"documento_numero"=>$documento_numero));
        if(count($objPersonas["data"])==0 ){
            if($documento_tipo_id==1){
                $dni=$documento_numero;
                $data=APIS::getDNI($dni);
                
                if(!$data["success"]){
                    echo json_encode(array("success"=>false,"msg_id"=>2,"msg"=>$data["msg"]));
                    return;
                }
                echo json_encode(array("success"=>true,"return"=>"from_dni","nombres"=>strtolower($data["nombres"]),"apellidos"=>strtolower($data["apellidoPaterno"].' '.$data["apellidoMaterno"]),"celular_postal"=>"51","celular_numero"=>"","correo_electronico"=>""));
                return;
            }
            if($documento_tipo_id==2){
                $ruc=$documento_numero;
                $data=APIS::getRUC($ruc);
                
                if(!$data["success"]){
                    echo json_encode(array("success"=>false,"msg_id"=>2,"msg"=>$data["msg"]));
                    return;
                }
                echo json_encode(array("success"=>true,"return"=>"from_ruc","razon_social"=>$data["razonSocial"],"direccion"=>$data["direccion"],"estado"=>$data["estado"],"condicion"=>$data["condicion"],"celular_postal"=>"51","celular_numero"=>"","correo_electronico"=>""));
                return;
            }
            echo json_encode(array("success"=>false,"data"=>$documento_tipo_id,"msg_id"=>1,"msg"=>"no hay registro"));
            return;
        }
        $personas=$objPersonas["data"][0];
        if($personas["documento_tipo_id"]==2){
            echo json_encode(array("success"=>true,"return"=>"from_bd","razon_social"=>$personas["razon_social"],"direccion"=>$personas["direccion"],"celular_postal"=>$personas["celular_postal"]=="" ? "51" : $personas["celular_postal"] ,"celular_numero"=>$personas["celular_numero"],"correo_electronico"=>$personas["correo_electronico"]));     
        }else{
            echo json_encode(array("success"=>true,"return"=>"from_bd","nombres"=>$personas["nombres"],"apellidos"=>$personas["apellidos"],"celular_postal"=>$personas["celular_postal"]=="" ? "51" : $personas["celular_postal"] ,"celular_numero"=>$personas["celular_numero"],"correo_electronico"=>$personas["correo_electronico"])); 
        }
        

    }
}