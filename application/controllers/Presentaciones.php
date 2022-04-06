<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Presentaciones extends CI_Controller {

	public function __construct() {
        parent::__construct();
		$this->load->library('session');
		$this->load->model('articulos_model');
        $this->load->model('productos_model');
        $this->load->model('categorias_model');
        $this->load->model('presentaciones_model');
        $this->load->model('preposiciones_model');
        $this->load->model('insumosPrincipalesMarcas_model');
    }
	
	public function abrir_nuevo()
	{
        $user=$_SESSION["user"];
        $title="Nueva presentacion";
        $dc_templates_extranet_presentaciones_nuevo["user"]=$user;
        $template=$this->load->view('templates/extranet/presentaciones/nuevo',$dc_templates_extranet_presentaciones_nuevo,true);
		echo json_encode(array("title"=>$title,"template"=>$template));
	}
    public function abrir_editar(){
        $user=$_SESSION["user"];

        $presentacion_id=$this->input->post('id');
        
        $filtros=array("id"=>$presentacion_id);
        $objPresentaciones=$this->presentaciones_model->get($filtros);
        $presentacion=$objPresentaciones["data"][0];
       
        $title="Editar presentacion - ".$presentacion["denominacion"];
        $dc_templates_extranet_presentaciones_editar["user"]=$user;
        $dc_templates_extranet_presentaciones_editar["presentacion"]=$presentacion;
        $template=$this->load->view('templates/extranet/presentaciones/editar',$dc_templates_extranet_presentaciones_editar,true);;
		echo json_encode(array("title"=>$title,"template"=>$template));
    }
    public function guardar_editar(){
        $id=$this->input->post('id');
        $denominacion=$this->input->post('denominacion');
        $establecimiento_id=$this->input->post('establecimiento_id');
               
        $actualizado=$this->presentaciones_model->actualizar(
            $id,
            $denominacion,
            $establecimiento_id
        );
        echo json_encode($actualizado);
    }
    public function guardar_nuevo()
    {
        $denominacion=$this->input->post('denominacion');
        $establecimiento_id=$this->input->post('establecimiento_id');

        $objPresentaciones=$this->presentaciones_model->insert(
            $denominacion,
            $establecimiento_id
        );

        echo json_encode($objPresentaciones);
    }
    public function eliminar(){
        $id=$this->input->post('id');
        $eliminado=$this->presentaciones_model->delete($id);
        echo json_encode($eliminado);
    }
}
