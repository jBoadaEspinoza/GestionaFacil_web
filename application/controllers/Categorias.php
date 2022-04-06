<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categorias extends CI_Controller {

	public function __construct() {
        parent::__construct();
		$this->load->library('session');
		$this->load->model('categorias_model');
    }
	
	public function abrir_nuevo()
	{
        $user=$_SESSION["user"];
        $title="Nueva categoria";
        $dc_templates_extranet_categorias_nuevo["user"]=$user;
        $template=$this->load->view('templates/extranet/categorias/nuevo',$dc_templates_extranet_categorias_nuevo,true);;
		echo json_encode(array("title"=>$title,"template"=>$template));
	}
    public function abrir_ver(){
        $user=$_SESSION["user"];
        $categoria_id=$this->input->post('id');
        
        $filtros=array("id"=>$categoria_id);
        $objCategoria=$this->categorias_model->get($filtros);
        
        if(!$objCategoria["success"]){
            echo json_encode(array("success"=>false,"msg"=>"error"));
            return;
        }
        
        $categoria=$objCategoria["data"][0];
       
        $title="Ver categoria - ".md5($categoria["id"]);
        $dc_templates_extranet_categorias_ver["user"]=$user;
        $dc_templates_extranet_categorias_ver["categoria"]=$categoria;
        $template=$this->load->view('templates/extranet/categorias/ver',$dc_templates_extranet_categorias_ver,true);;
		echo json_encode(array("title"=>$title,"template"=>$template));
    }
    public function abrir_editar(){
        $user=$_SESSION["user"];
        $categoria_id=$this->input->post('id');
        
        $filtros=array("id"=>$categoria_id);
        $objCategoria=$this->categorias_model->get($filtros);
        
        if(!$objCategoria["success"]){
            echo json_encode(array("success"=>false,"msg"=>"error"));
            return;
        }
        
        $categoria=$objCategoria["data"][0];
       
        $title="Editar categoria - ".md5($categoria["id"]);
        $dc_templates_extranet_categorias_editar["user"]=$user;
        $dc_templates_extranet_categorias_editar["categoria"]=$categoria;
        $template=$this->load->view('templates/extranet/categorias/editar',$dc_templates_extranet_categorias_editar,true);;
		echo json_encode(array("title"=>$title,"template"=>$template));
    }
    public function guardar_editar(){
        $id=$this->input->post('id');
        $denominacion_por_unidad=$this->input->post('denominacion_por_unidad');
        $denominacion_por_grupo=$this->input->post('denominacion_por_grupo');
        $descripcion=$this->input->post('descripcion');
        $imagen_url_loaded=$this->input->post('imagen_url_loaded');
        $imagen_url_to_change=$this->input->post('imagen_url_to_change'); 
        $imagen_url_state=$this->input->post('imagen_url_state');
        $establecimiento_id=$this->input->post('establecimiento_id');
        $url=($imagen_url_state=="changed") ? $imagen_url_to_change : $imagen_url_loaded;
       
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
        $denominacion_por_unidad=$this->input->post('denominacion_por_unidad');
        $denominacion_por_grupo=$this->input->post('denominacion_por_grupo');
        $descripcion=$this->input->post('descripcion');
        $url=$this->input->post('url');
        $establecimiento_id=$this->input->post('establecimiento_id');

        $objCategorias=$this->categorias_model->insert(
            $denominacion_por_unidad,
            $denominacion_por_grupo,
            $descripcion,
            $url,
            $establecimiento_id
        );

        echo json_encode($objCategorias);
    }
    public function eliminar(){
        $id=$this->input->post('id');
        $eliminado=$this->categorias_model->delete($id);
        echo json_encode($eliminado);
    }
}
