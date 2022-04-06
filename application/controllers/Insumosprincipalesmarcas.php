<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class InsumosPrincipalesMarcas extends CI_Controller {

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
        $tipos=APIS::getInsumosTipos();
        $title="Nuevo insumo principal o marca";
        $dc_templates_extranet_insumos_principales_marcas_nuevo["user"]=$user;
        $dc_templates_extranet_insumos_principales_marcas_nuevo["tipos"]=$tipos;
        $template=$this->load->view('templates/extranet/insumos_principales_marcas/nuevo',$dc_templates_extranet_insumos_principales_marcas_nuevo,true);
		echo json_encode(array("title"=>$title,"template"=>$template));
	}
    public function abrir_editar(){
        $user=$_SESSION["user"];

        $insumoPrincipalMarca_id=$this->input->post('id');
        
        $filtros=array("id"=>$insumoPrincipalMarca_id);
        $objInsumosPrincipalesMarcas=$this->insumosPrincipalesMarcas_model->get($filtros);
        $insumoPrincipalMarca=$objInsumosPrincipalesMarcas["data"][0];
        $tipos=APIS::getInsumosTipos();
        
        $title="Editar insumo principal marca - ".$insumoPrincipalMarca["denominacion"];
        $dc_templates_extranet_insumos_principales_marcas_editar["user"]=$user;
        $dc_templates_extranet_insumos_principales_marcas_editar["tipos"]=$tipos;
        $dc_templates_extranet_insumos_principales_marcas_editar["insumoPrincipalMarca"]=$insumoPrincipalMarca;
        $template=$this->load->view('templates/extranet/insumos_principales_marcas/editar',$dc_templates_extranet_insumos_principales_marcas_editar,true);;
		echo json_encode(array("title"=>$title,"template"=>$template));
    }
    public function guardar_editar(){
        $id=$this->input->post('id');
        $denominacion=$this->input->post('denominacion');
        $tipo_id=$this->input->post('tipo');
        $establecimiento_id=$this->input->post('establecimiento_id');
               
        $actualizado=$this->insumosPrincipalesMarcas_model->actualizar(
            $id,
            $tipo_id,
            $denominacion,
            $establecimiento_id
        );
        echo json_encode($actualizado);
    }
    public function guardar_nuevo()
    {
        $tipo_id=$this->input->post('tipo');
        $denominacion=$this->input->post('denominacion');
        $establecimiento_id=$this->input->post('establecimiento_id');

        $objInsumosPrincipalesMarcas=$this->insumosPrincipalesMarcas_model->insert(
            $tipo_id,
            $denominacion,
            $establecimiento_id
        );

        echo json_encode($objInsumosPrincipalesMarcas);
    }
    public function eliminar(){
        $id=$this->input->post('id');
        $eliminado=$this->categorias_model->delete($id);
        echo json_encode($eliminado);
    }
}
