<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Articulos extends CI_Controller {

	public function __construct() {
        parent::__construct();
		$this->load->library('session');
		$this->load->model('articulos_model');
        $this->load->model('productos_model');
        $this->load->model('categorias_model');
        $this->load->model('presentaciones_model');
        $this->load->model('preposiciones_model');
    }
	
	public function abrir_nuevo()
	{
        $user=$_SESSION["user"];
        $categoria_id_seleccionada=$this->input->post('c_id');
        $objProductos=$this->productos_model->get(array("establecimiento_id"=>$user["business_id"]));
        $objCategorias=$this->categorias_model->get(array("establecimiento_id"=>$user["business_id"]));
        $objPresentaciones=$this->presentaciones_model->get(array("establecimiento_id"=>$user["business_id"]));
        $objPreposiciones=$this->preposiciones_model->get();
        $categorias=$objCategorias["data"];
        $productos=$objProductos["data"];
        $preposiciones=$objPreposiciones["data"];
        $presentaciones=$objPresentaciones["data"];
        $denominacion="";

        switch($preposiciones[0]["id"]){
            case 1:
                $denominacion=ucfirst($categorias[0]["denominacion_por_unidad"].' '.$productos[0]["denominacion"].' - '.$presentaciones[0]["denominacion"]);
                break;
            case 2:
                $denominacion=ucfirst($productos[0]["denominacion"].' '.$categorias[0]["denominacion_por_unidad"].' - '.$presentaciones[0]["denominacion"]);
                break;
            case 3:
                $denominacion=ucfirst($productos[0]["denominacion"].' - '.$presentaciones[0]["denominacion"]);
                break;
            default:
                $denominacion=ucfirst($categorias[0]["denominacion_por_unidad"].' '.$preposiciones[0]["denominacion"].' '.$productos[0]["denominacion"].' - '.$presentaciones[0]["denominacion"]);
                break;
        }

        $title="Nuevo articulo - ".$denominacion;
        $dc_templates_extranet_articulos_nuevo["categoria_id_seleccionada"]=$categoria_id_seleccionada;
        $dc_templates_extranet_articulos_nuevo["user"]=$user;
        $dc_templates_extranet_articulos_nuevo["productos"]=$productos;
        $dc_templates_extranet_articulos_nuevo["categorias"]=$categorias;
        $dc_templates_extranet_articulos_nuevo["presentaciones"]=$presentaciones;
        $dc_templates_extranet_articulos_nuevo["preposiciones"]=$preposiciones;
        $dc_templates_extranet_articulos_nuevo["denominacion"]=$denominacion;
        $template=$this->load->view('templates/extranet/articulos/nuevo',$dc_templates_extranet_articulos_nuevo,true);
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

        $articulo_id=$this->input->post('id');
        

        $filtros=array("id"=>$articulo_id);
        $objArticulo=$this->articulos_model->get($filtros);
        $objProductos=$this->productos_model->get(array("establecimiento_id"=>$user["business_id"]));
        $objCategorias=$this->categorias_model->get(array("establecimiento_id"=>$user["business_id"]));
        $objPresentaciones=$this->presentaciones_model->get(array("establecimiento_id"=>$user["business_id"]));
        $objPreposiciones=$this->preposiciones_model->get();
        $categorias=$objCategorias["data"];
        $productos=$objProductos["data"];
        $preposiciones=$objPreposiciones["data"];
        $presentaciones=$objPresentaciones["data"];

        if(!$objArticulo["success"]){
            echo json_encode(array("success"=>false,"msg"=>"error"));
            return;
        }
        
        $articulo=$objArticulo["data"][0];
        $denominacion="";
        switch($articulo["preposicion_id"]){
            case 1:
                $denominacion=ucfirst($articulo["categoria_denominacion_por_unidad"].' '.$articulo["producto_denominacion"].' - '.$articulo["presentacion_denominacion"]);
                break;
            case 2:
                $denominacion=ucfirst($articulo["producto_denominacion"].' '.$articulo["categoria_denominacion_por_unidad"].' - '.$articulo["presentacion_denominacion"]);
                break;    
            case 3:
                $denominacion=ucfirst($articulo["producto_denominacion"].' - '.$articulo["presentacion_denominacion"]);
                break;
            default:
                $denominacion=ucfirst($articulo["categoria_denominacion_por_unidad"].' '.$articulo["preposicion_denominacion"].' '.$articulo["producto_denominacion"].' - '.$articulo["presentacion_denominacion"]);
                break;
        }

        $title="Editar categoria - ".$denominacion;
        $dc_templates_extranet_articulos_editar["user"]=$user;
        $dc_templates_extranet_articulos_editar["productos"]=$productos;
        $dc_templates_extranet_articulos_editar["categorias"]=$categorias;
        $dc_templates_extranet_articulos_editar["presentaciones"]=$presentaciones;
        $dc_templates_extranet_articulos_editar["preposiciones"]=$preposiciones;
        $dc_templates_extranet_articulos_editar["denominacion"]=$denominacion;
        $dc_templates_extranet_articulos_editar["articulo"]=$articulo;
        $template=$this->load->view('templates/extranet/articulos/editar',$dc_templates_extranet_articulos_editar,true);;
		echo json_encode(array("title"=>$title,"template"=>$template));
    }
    public function guardar_editar(){
        $id=$this->input->post('id');
        $categoria_id=$this->input->post('categoria');
        $producto_id=$this->input->post('producto');
        $presentacion_id=$this->input->post('presentacion');
        $preposicion_id=$this->input->post('preposicion');
        $precio=$this->input->post("precio");
        $stock=$this->input->post('stock');
        $tiempo_despacho_min=$this->input->post('tiempo_despacho_min');
        $descripcion=$this->input->post('descripcion');
        
        $establecimiento_id=$this->input->post('establecimiento_id');
        $imagen_url_loaded=$this->input->post('imagen_url_loaded');
        $imagen_url_to_change=$this->input->post('imagen_url_to_change'); 
        $imagen_url_state=$this->input->post('imagen_url_state');
        $establecimiento_id=$this->input->post('establecimiento_id');
        $url=($imagen_url_state=="changed") ? $imagen_url_to_change : $imagen_url_loaded;
       
        $actualizado=$this->articulos_model->actualizar($id,$categoria_id,$producto_id,$presentacion_id,$preposicion_id,$precio,$stock,$tiempo_despacho_min,$descripcion,$url,$establecimiento_id);

        echo json_encode($actualizado);
    }
    public function guardar_nuevo()
    {
        $categoria_id=$this->input->post('categoria');
        $producto_id=$this->input->post('producto');
        $presentacion_id=$this->input->post('presentacion');
        $preposicion_id=$this->input->post('preposicion');
        $precio=$this->input->post("precio");
        $stock=$this->input->post('stock');
        $tiempo_despacho_min=$this->input->post('tiempo_despacho_min');
        $descripcion=$this->input->post('descripcion');
        $url=$this->input->post('url');
        $establecimiento_id=$this->input->post('establecimiento_id');

        $objArticulos=$this->articulos_model->insert(
            $categoria_id,
            $producto_id,
            $presentacion_id,
            $preposicion_id,
            $precio,
            $stock,
            $tiempo_despacho_min,
            $descripcion,
            $url,
            $establecimiento_id
        );

        echo json_encode($objArticulos);
    }
    public function eliminar(){
        $id=$this->input->post('id');
        $eliminado=$this->categorias_model->delete($id);
        echo json_encode($eliminado);
    }
}
