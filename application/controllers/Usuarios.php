<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {

	public function __construct() {
        parent::__construct();
		$this->load->library('session');
		$this->load->model('usuarios_model');
        $this->load->model('roles_model');
        $this->load->model('documentosTipos_model');
        $this->load->model('personas_model');
        $this->load->model('permisosRoles_model');
        $this->load->model('permisosUsuarios_model');
    }
	public function permisos(){
        $rol_id=$this->input->post('rol_id');
        $objPermisosRoles=$this->permisosRoles_model->get(array("rol_id"=>$rol_id));
        $permisos=$objPermisosRoles["data"];
        $template='';
        if(count($permisos)>0){
            $template.= '<fieldset class="row scheduler-border">';
            $template.= '<legend class="scheduler-border">Permisos</legend>';
            $template.= '<div class="col-12 d-flex flex-nowrap">';
            foreach($permisos as $index=>$p){
                $template.= '<div class="form-check">';
                $template.= '<input class="form-check-input" type="checkbox" value="'.$p["id"].'" name="permiso['.$index.'][id]">';
                $template.= '<label class="form-check-label">';
                $template.=    $p["denominacion"];
                $template.= '</label>';
                $template.= '</div>';
                $template.= '&nbsp;';
                $template.= '&nbsp;';
                $template.= '&nbsp;';
                $template.= '&nbsp;';
            }
            $template.= '</div>';
            $template.= '</fieldset>';
        }
        echo json_encode(array("template"=>$template));
    }
	public function abrir_nuevo()
	{
        $user=$_SESSION["user"];
        $title="Nueva usuario";
        $documentosTipos=$this->documentosTipos_model->get(array());
        $roles=$this->roles_model->get(array());
        $objPermisosRoles=$this->permisosRoles_model->get(array("rol_id"=>$roles["data"][0]["id"]));
        $dc_templates_extranet_usuarios_nuevo["user"]=$user;
        $dc_templates_extranet_usuarios_nuevo["roles"]=$roles["data"];
        $dc_templates_extranet_usuarios_nuevo["permisos"]=$objPermisosRoles["data"];
        $dc_templates_extranet_usuarios_nuevo["documentosTipos"]=$documentosTipos["data"];
        $template=$this->load->view('templates/extranet/usuarios/nuevo',$dc_templates_extranet_usuarios_nuevo,true);;
		echo json_encode(array("title"=>$title,"template"=>$template));
	}
    
    public function abrir_ver(){
        
    }
     public function abrir_editar_permisos(){
        $user=$_SESSION["user"];
        $id=$this->input->post('id');
        $objUsuario=$this->usuarios_model->get(array("id"=>$id));
         if(!$objUsuario["success"]){
             echo json_encode(array("success"=>false,"msg"=>"error"));
             return;
         }
        $usuario=$objUsuario["data"][0];
        $title="Editar usuario - ".md5($usuario["id"]);
        $documentosTipos=$this->documentosTipos_model->get(array());
        $roles=$this->roles_model->get(array());
        $objPermisosRoles=$this->permisosRoles_model->get(array("rol_id"=>$usuario["rol_id"]));
        $objPermisosUsuarios=$this->permisosUsuarios_model->get(array("usuario_id"=>$usuario["id"]));
        $dc_templates_extranet_usuarios_editar_permisos["user"]=$user;
        $dc_templates_extranet_usuarios_editar_permisos["usuario"]=$usuario;
        $dc_templates_extranet_usuarios_editar_permisos["roles"]=$roles["data"];
        $dc_templates_extranet_usuarios_editar_permisos["permisos"]=$objPermisosRoles["data"];
        $dc_templates_extranet_usuarios_editar_permisos["permisos_segun_usuario"]=$objPermisosUsuarios["data"];
        $dc_templates_extranet_usuarios_editar_permisos["documentosTipos"]=$documentosTipos["data"];
         $template=$this->load->view('templates/extranet/usuarios/editar_permisos',$dc_templates_extranet_usuarios_editar_permisos,true);;
	 	echo json_encode(array("title"=>$title,"template"=>$template));
     }
     public function guardar_editar_permisos(){
        $id=$this->input->post('id');
                
        if(isset($_POST["permiso"])){
            $permisos=$this->input->post('permiso');
            $deleted=$this->permisosUsuarios_model->delete($id);
            foreach($permisos as $index=>$p){
                $inserted=$this->permisosUsuarios_model->insert($p["id"],$id);
            }
        }
        echo json_encode(array("success"=>true,"msg"=>"registro actualizado con exito"));
     }
      public function guardar_editar(){
         $id=$this->input->post('id');
         $documento_tipo_id=$this->input->post('documento_tipo');
         $documento_numero=$this->input->post('documento_numero');
         $nombres=$this->input->post('nombres');
         $apellidos=$this->input->post('apellidos');
         $rol_id=$this->input->post('rol');
         $establecimiento_id=$this->input->post('establecimiento_id');
         $objPersona=$this->personas_model->get(array("documento_tipo_id"=>$documento_tipo_id,"documento_numero"=>$documento_numero));
         if($objPersona["success"]){
            $persona_id=$objPersona["data"][0]["id"];
         }else{
            $objPersona=$this->personas_model->insert($documento_tipo_id,$documento_numero,$nombres,$apellidos);
            $persona_id=$objPersona["id"];
         }
         
 
         $objUsuarios=$this->usuarios_model->insert($nombre_usuario,$clave_acceso,$persona_id,$rol_id,$establecimiento_id);
         if(isset($_POST["permiso"])){
             $permisos=$this->input->post('permiso');
             foreach($permisos as $index=>$p){
                 $inserted=$this->permisosUsuarios_model->insert($p["id"],$objUsuarios["id"]);
             }
         }
         echo json_encode($objUsuarios);
       
         $actualizado=$this->categorias_model->actualizar(
             $id,
             $denominacion_por_unidad,
             $denominacion_por_grupo,
             $descripcion,
            $url,
            $establecimiento_id
        );

         echo json_encode($actualizado);
     }
    public function guardar_nuevo()
    {
        $nombre_usuario=$this->input->post('nombre_usuario');
        $clave_acceso=$this->input->post('clave_acceso');
        $documento_tipo_id=$this->input->post('documento_tipo');
        $documento_numero=$this->input->post('documento_numero');
        $nombres=$this->input->post('nombres');
        $apellidos=$this->input->post('apellidos');
        $rol_id=$this->input->post('rol');
        $establecimiento_id=$this->input->post('establecimiento_id');
        $objPersonas=$this->personas_model->insert($documento_tipo_id,$documento_numero,$nombres,$apellidos);
        $persona_id=$objPersonas["id"];

        $objUsuarios=$this->usuarios_model->insert($nombre_usuario,$clave_acceso,$persona_id,$rol_id,$establecimiento_id);
        if(isset($_POST["permiso"])){
            $permisos=$this->input->post('permiso');
            foreach($permisos as $index=>$p){
                $inserted=$this->permisosUsuarios_model->insert($p["id"],$objUsuarios["id"]);
            }
        }
        echo json_encode($objUsuarios);
    }
}