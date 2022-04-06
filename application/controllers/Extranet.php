<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Extranet extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct() {
        parent::__construct();
		$this->load->library('session');
		$this->load->model('usuarios_model');
		$this->load->model('categorias_model');
		$this->load->model('articulos_model');
		$this->load->model('presentaciones_model');
		$this->load->model('productos_model');
		$this->load->model('mesas_model');	
		$this->load->model('pedidos_model');	
		$this->load->model('cajas_model');
		$this->load->model('insumosPrincipalesMarcas_model');
		$this->load->model('permisosRoles_model');
    }
	//Url acceso a logeo
	public function index()
	{
		$lang="es";
		$title="Inicio de sesion";
		$css=array("index");
		if(isset($_GET["error"])){
			$error=$this->input->get("error");
			$error=base64_decode($error);
			$error=json_decode($error,true);
			$data_templates_login_index["url_action"]=base_url().'dashboard';
			$data_templates_login_index["input"]["user"]=$error;
			$content=$this->load->view('templates/login/index',$data_templates_login_index,true);
		self::load($lang,$title,$content,$bgbody='white',$bgimagefull='',$css,$js=array(),$toolbarcolor='#0080FF');
			return;
		}
		$data_templates_login_index["url_action"]=base_url().'dashboard';
		$data_templates_login_index["input"]["user"]["business_id"]["value"]='';
		$data_templates_login_index["input"]["user"]["business_id"]["msg"]="";
		$data_templates_login_index["input"]["user"]["business_id"]["autofocus"]="autofocus";
		$data_templates_login_index["input"]["user"]["name"]["value"]="";
		$data_templates_login_index["input"]["user"]["name"]["msg"]="";
		$data_templates_login_index["input"]["user"]["name"]["autofocus"]="";
		$data_templates_login_index["input"]["user"]["password"]["value"]="";
		$data_templates_login_index["input"]["user"]["password"]["msg"]="";
		$data_templates_login_index["input"]["user"]["password"]["autofocus"]="";
		$content=$this->load->view('templates/login/index',$data_templates_login_index,true);
		self::load($lang,$title,$content,$bgbody='white',$bgimagefull='',$css,$js=array(),$toolbarcolor='#0080FF');
	}

	//Url de acceso al dashboard
	public function dashboard(){
		
		$user_business_id=$this->input->post('userbusinessid');
		$user_name=$this->input->post('username');
		$user_password=md5($this->input->post('userpassword'));
		$obj_user=$this->usuarios_model->validate($user_business_id,$user_name,$user_password);
		
		if(!$obj_user["success"]){
			$error_id=$obj_user["error_id"];
			$error_msg=$obj_user["msg"];
			switch ($error_id){
				case 1:
					//Establecimiento no encontrado
					$error=array(
						"business_id"=>array("value"=>"","msg"=>$error_msg,"autofocus"=>""),
						"name"=>array("value"=>"","msg"=>"","autofocus"=>""),
						"password"=>array("value"=>"","msg"=>"","autofocus"=>"")
					);
					break;

				case 2:
					//Establecimiento inactivo
					$error=array(
						"business_id"=>array("value"=>"","msg"=>$error_msg,"autofocus"=>""),
						"name"=>array("value"=>"","msg"=>"","autofocus"=>""),
						"password"=>array("value"=>"","msg"=>"","autofocus"=>"")
					);
					break;

				case 3:
					//Contraseña incorrecta
					$error=array(
						"business_id"=>array("value"=>"","msg"=>"","autofocus"=>""),
						"name"=>array("value"=>"","msg"=>"","autofocus"=>""),
						"password"=>array("value"=>"","msg"=>$error_msg,"autofocus"=>"")
					);
					break;

				case 4:
					//Correo electronico no asociado al establecimiento
					$error=array(
						"business_id"=>array("value"=>"","msg"=>"","autofocus"=>""),
						"name"=>array("value"=>"","msg"=>$error_msg,"autofocus"=>""),
						"password"=>array("value"=>"","msg"=>"","autofocus"=>"")
					);
					break;
				case 5:
					//Tipo de establecimiento no establecido
					echo $error_msg;
					break;
			}
			$error=json_encode($error);
			header('Location: '.base_url().'extranet?error='.base64_encode($error));
			return;
		}

		$lang="es";$title="Bienvenido al sistema";$css=array("index");
		
		$user=$obj_user["data"];
		$_SESSION["user"]=$user;

		//Redireccionamos a mostrador
		header('Location: '.base_url().'dashboard_mostrador');

		$content="";

		$data["lang"]=$lang;
		$data["title"]=$title;
		$data["user"]=$user;
		$data["css"]=$css;
		$data["section"]="Home";
		$data["section_title"]=$title;
		$data["section_root"]="insumos principales";
		$data["content"]=$content;
		
		$this->load->view('index2',$data);
	}
	public function dashboard_mi_establecimiento(){
		if(isset($_SESSION["user"])){
			
			$lang="es";$title="Mi establecimiento";$css=array("index");
		
			$content="";
			$user=$_SESSION["user"];

			$data["lang"]=$lang;
			$data["title"]=$title;
			$data["css"]=$css;
			$data["user"]=$user;
			$data["section_title"]=$title;
			$data["section_root"]="mi_establecimiento";

			if(!isset($_GET["rows"])){
				$num_filas=10;
			}else{
				$num_filas=$_GET["rows"];
			}
			if(!isset($_GET["pag"])){
				$pag_seleccionada=1;
			}else{
				$pag_seleccionada=$_GET["pag"];
			}
			
			$dc_templates_extranet_establecimientos_index["user"]=$user;
			$content=$this->load->view('templates/extranet/establecimientos/index',$dc_templates_extranet_establecimientos_index,true);
			$data["content"]=$content;
			
			$this->load->view('index2',$data);
			
			return;
		}else{
			header('Location: '.base_url().'extranet');
		}
	}
	
	public function dashboard_mostrador(){
		if(isset($_SESSION["user"])){
			
			$lang="es";$title="Control de mesas/productos";$css=array("index");
		
			$content="";
			$user=$_SESSION["user"];

			$data["lang"]=$lang;
			$data["title"]=$title;
			$data["user"]=$user;
			$data["css"]=$css;
			$data["section_title"]=$title;
			$data["section_root"]="Mostrador";
			

			$filtros=array("establecimiento_id"=>$user["business_id"]);
			$objMesas=$this->mesas_model->get($filtros);
			
			$mesas=$objMesas["data"];
			foreach($mesas as $index=>$mesa){
				$objPedidos=$this->pedidos_model->get(array("establecimiento_id"=>$user["business_id"],"cerrado"=>0));
				$pedidos=$objPedidos["data"];
				$mesas[$index]["estado"]="disponible";
				$mesas[$index]["referencia_pedido"]=0;
				foreach($pedidos as $index_pedidos=>$pedido){
					if($pedido["referencia_a_bd"]!=""){
						$referencia_array = array();
						parse_str($pedido["referencia_a_bd"], $referencia_array);
						if($referencia_array["mesa_id"]==$mesa["id"]){
							$mesas[$index]["estado"]="ocupado";
							$mesas[$index]["referencia_pedido"]=$pedido["id"];
							break;
						}
					}
				}
			}
			$dc_templates_extranet_mostrador_index["user"]=$user;
			$dc_templates_extranet_mostrador_index["mesas"]=$mesas;
			$content=$this->load->view('templates/extranet/mostrador/index',$dc_templates_extranet_mostrador_index,true);
			
			$data["content"]=$content;
			
			$this->load->view('index2',$data);
			
			return;
		}else{
			header('Location: '.base_url().'extranet');
		}
	}
	public function dashboard_presentaciones(){
		if(isset($_SESSION["user"])){
			
			$lang="es";$title="Gestion de presentaciones";$css=array("index");
		
			$content="";
			$user=$_SESSION["user"];

			$data["lang"]=$lang;
			$data["title"]=$title;
			$data["user"]=$user;
			$data["css"]=$css;
			$data["section_title"]=$title;
			$data["section_root"]="presentaciones";

			if(!isset($_GET["rows"])){
				$num_filas=10;
			}else{
				$num_filas=$_GET["rows"];
			}
			if(!isset($_GET["pag"])){
				$pag_seleccionada=1;
			}else{
				$pag_seleccionada=$_GET["pag"];
			}
			
			$fila_inicial=(($pag_seleccionada-1)*$num_filas)+1;
			$filtrosWithOffset=array("establecimiento_id"=>$user["business_id"],"pagina"=>$pag_seleccionada,"fila_inicial"=>$fila_inicial,"num_filas"=>$num_filas);
			$objPresentacionesWithOffset=$this->presentaciones_model->get($filtrosWithOffset);
			$presentaciones=$objPresentacionesWithOffset["data"];
			$dc_templates_extranet_presentaciones_index["presentaciones"]=$presentaciones;
			
			$filtrosWithOutOffset=array("establecimiento_id"=>$user["business_id"]);
			$objPresentacionesWithOutOffset=$this->presentaciones_model->get($filtrosWithOutOffset);
			$total_presentaciones_sin_filtro=count($objPresentacionesWithOutOffset["data"]);
			$dc_templates_extranet_presentaciones_index["total_presentaciones_sin_filtro"]=$total_presentaciones_sin_filtro;
			$dc_templates_extranet_presentaciones_index["num_filas_por_pagina"]=$num_filas;
			$dc_templates_extranet_presentaciones_index["pagina_seleccionada"]=$pag_seleccionada;
			$content=$this->load->view('templates/extranet/presentaciones/index',$dc_templates_extranet_presentaciones_index,true);
			$data["content"]=$content;
			
			$this->load->view('index2',$data);
			
			return;
		}else{
			header('Location: '.base_url().'extranet');
		}
	}
	
	public function dashboard_insumos_principales_marcas(){
		if(isset($_SESSION["user"])){
			
			$lang="es";$title="Gestion de insumos principales o marcas";$css=array("index");
		
			$content="";
			$user=$_SESSION["user"];

			$data["lang"]=$lang;
			$data["title"]=$title;
			$data["user"]=$user;
			$data["css"]=$css;
			$data["section_title"]=$title;
			$data["section_root"]="insumos-principales-marcas";

			if(!isset($_GET["rows"])){
				$num_filas=10;
			}else{
				$num_filas=$_GET["rows"];
			}
			if(!isset($_GET["pag"])){
				$pag_seleccionada=1;
			}else{
				$pag_seleccionada=$_GET["pag"];
			}
			
			$fila_inicial=(($pag_seleccionada-1)*$num_filas)+1;
			$filtrosWithOffset=array("establecimiento_id"=>$user["business_id"],"pagina"=>$pag_seleccionada,"fila_inicial"=>$fila_inicial,"num_filas"=>$num_filas);
			$objInsumosPrincipalesMarcasWithOffset=$this->insumosPrincipalesMarcas_model->get($filtrosWithOffset);
			$insumosPrincipalesMarcas=$objInsumosPrincipalesMarcasWithOffset["data"];
			$dc_templates_extranet_insumos_principales_marcas_index["insumosPrincipalesMarcas"]=$insumosPrincipalesMarcas;
			
			$filtrosWithOutOffset=array("establecimiento_id"=>$user["business_id"]);
			$objInsumosPrincipalesMarcasWithOutOffset=$this->insumosPrincipalesMarcas_model->get($filtrosWithOutOffset);
			$total_insumos_principales_marcas_sin_filtro=count($objInsumosPrincipalesMarcasWithOutOffset["data"]);
			$dc_templates_extranet_insumos_principales_marcas_index["total_insumos_principales_marcas_sin_filtro"]=$total_insumos_principales_marcas_sin_filtro;
			$dc_templates_extranet_insumos_principales_marcas_index["num_filas_por_pagina"]=$num_filas;
			$dc_templates_extranet_insumos_principales_marcas_index["pagina_seleccionada"]=$pag_seleccionada;
			$content=$this->load->view('templates/extranet/insumos_principales_marcas/index',$dc_templates_extranet_insumos_principales_marcas_index,true);
			$data["content"]=$content;
			
			$this->load->view('index2',$data);
			
			return;
		}else{
			header('Location: '.base_url().'extranet');
		}
	}
	public function dashboard_ventas(){
		if(isset($_SESSION["user"])){
			
			$lang="es";$title="Gestion de ventas";$css=array("index");
		
			$content="";
			$user=$_SESSION["user"];

			$data["lang"]=$lang;
			$data["title"]=$title;
			$data["user"]=$user;
			$data["css"]=$css;
			$data["section_title"]=$title;
			$data["section_root"]="ventas";

			$today1=new Datetime();
			$today2=new DateTime();
			$daysbeforeToday=$today1->sub(new DateInterval("P7D"));
			
			if(!isset($_GET["rows"])){
				$num_filas=50;
			}else{
				$num_filas=$_GET["rows"];
			}
			if(!isset($_GET["pag"])){
				$pag_seleccionada=1;
			}else{
				$pag_seleccionada=$_GET["pag"];
			}
			if(!isset($_GET["c_id"])){
				$c_id=0;
			}else{
				$c_id=$_GET["c_id"];
			}
			if(!isset($_GET["desde"])){
				$desde=$daysbeforeToday->format('Y-m-d');
			}else{
				$desde=$_GET["desde"];
			}
			if(!isset($_GET["hasta"])){
				$hasta=$today2->format('Y-m-d');
			}else{
				$hasta=$_GET["hasta"];
			}
			if(!isset($_GET["modalidades"])){
				$modalidades="";
			}else{
				$modalidades=$_GET["modalidades"];
			}
			$fila_inicial=(($pag_seleccionada-1)*$num_filas)+1;
			$filtrosWithOffset=array("establecimiento_id"=>$user["business_id"],"cerrado"=>1,"pagina"=>$pag_seleccionada,"fila_inicial"=>$fila_inicial,"num_filas"=>$num_filas,"ordenado_por"=>"pedidos.fecha_hora_cierre","en_orden"=>"DESC","desde"=>$desde,"hasta"=>$hasta,"modalidades"=>$modalidades);
			
			$objPedidosWithOffset=$this->pedidos_model->get($filtrosWithOffset);
			
			$pedidos=$objPedidosWithOffset["data"];
			$dc_templates_extranet_pedidos_index["pedidos"]=$pedidos;
			
			$filtrosWithOutOffset=array("establecimiento_id"=>$user["business_id"],"cerrado"=>1,"desde"=>$desde,"hasta"=>$hasta,"modalidades"=>$modalidades);
			$objPedidosWithOutOffset=$this->pedidos_model->get($filtrosWithOutOffset);
			$total_pedidos_sin_filtro=count($objPedidosWithOutOffset["data"]);
			
			
			$pedidos=$objPedidosWithOffset["data"];
			$dc_templates_extranet_pedidos_index["modalidades"]=$modalidades;
			$dc_templates_extranet_pedidos_index["modalidades_de_pago"]=APIS::getModalidadesDePago();
			$dc_templates_extranet_pedidos_index["date_from"]=$desde;
			$dc_templates_extranet_pedidos_index["date_to"]=$hasta;
			$dc_templates_extranet_pedidos_index["mod_sel"]=$modalidades;
			$dc_templates_extranet_pedidos_index["total_pedidos_sin_filtro"]=$total_pedidos_sin_filtro;
			$dc_templates_extranet_pedidos_index["num_filas_por_pagina"]=$num_filas;
			$dc_templates_extranet_pedidos_index["pagina_seleccionada"]=$pag_seleccionada;
			$content=$this->load->view('templates/extranet/pedidos/index',$dc_templates_extranet_pedidos_index,true);
			$data["content"]=$content;
			
			$this->load->view('index2',$data);
			
			return;
		}else{
			header('Location: '.base_url().'extranet');
		}
	}
	public function dashboard_articulos(){
		if(isset($_SESSION["user"])){
			
			$lang="es";$title="Gestion de articulos ó productos";$css=array("index");
		
			$content="";
			$user=$_SESSION["user"];

			$data["lang"]=$lang;
			$data["title"]=$title;
			$data["user"]=$user;
			$data["css"]=$css;
			$data["section_title"]=$title;
			$data["section_root"]="articulos";

			if(!isset($_GET["rows"])){
				$num_filas=10;
			}else{
				$num_filas=$_GET["rows"];
			}
			if(!isset($_GET["pag"])){
				$pag_seleccionada=1;
			}else{
				$pag_seleccionada=$_GET["pag"];
			}
			if(!isset($_GET["c_id"])){
				$c_id=0;
			}else{
				$c_id=$_GET["c_id"];
			}
			
			$fila_inicial=(($pag_seleccionada-1)*$num_filas)+1;
			$filtrosWithOffset=array("establecimiento_id"=>$user["business_id"],"categoria_id"=>$c_id,"pagina"=>$pag_seleccionada,"fila_inicial"=>$fila_inicial,"num_filas"=>$num_filas);
			
			$objArticulosWithOffset=$this->articulos_model->get($filtrosWithOffset);
			
			$articulos=$objArticulosWithOffset["data"];
			$dc_templates_extranet_articulos_index["articulos"]=$articulos;
			
			$filtrosWithOutOffset=array("establecimiento_id"=>$user["business_id"],"categoria_id"=>$c_id);
			$objArticulosWithOutOffset=$this->articulos_model->get($filtrosWithOutOffset);
			$total_articulos_sin_filtro=count($objArticulosWithOutOffset["data"]);

			$objCategorias=$this->categorias_model->get(array("establecimiento_id"=>$user["business_id"]));
			$categorias=$objCategorias["data"];
			$dc_templates_extranet_articulos_index["categorias"]=$categorias;
			$dc_templates_extranet_articulos_index["total_articulos_sin_filtro"]=$total_articulos_sin_filtro;
			$dc_templates_extranet_articulos_index["num_filas_por_pagina"]=$num_filas;
			$dc_templates_extranet_articulos_index["categoria_seleccionada"]=$c_id;
			$dc_templates_extranet_articulos_index["pagina_seleccionada"]=$pag_seleccionada;
			$content=$this->load->view('templates/extranet/articulos/index',$dc_templates_extranet_articulos_index,true);
			$data["content"]=$content;
			
			$this->load->view('index2',$data);
			
			return;
		}else{
			header('Location: '.base_url().'extranet');
		}
	}
	public function dashboard_categorias(){
		if(isset($_SESSION["user"])){
			
			$lang="es";$title="Gestion de categorias";$css=array("index");
		
			$content="";
			$user=$_SESSION["user"];

			$data["lang"]=$lang;
			$data["title"]=$title;
			$data["user"]=$user;
			$data["css"]=$css;
			$data["section_title"]=$title;
			$data["section_root"]="categorias";

			if(!isset($_GET["rows"])){
				$num_filas=10;
			}else{
				$num_filas=$_GET["rows"];
			}
			if(!isset($_GET["pag"])){
				$pag_seleccionada=1;
			}else{
				$pag_seleccionada=$_GET["pag"];
			}
			
			$fila_inicial=(($pag_seleccionada-1)*$num_filas)+1;
			$filtrosWithOffset=array("establecimiento_id"=>$user["business_id"],"pagina"=>$pag_seleccionada,"fila_inicial"=>$fila_inicial,"num_filas"=>$num_filas);
			$objCategoriasWithOffset=$this->categorias_model->get($filtrosWithOffset);
			$categorias=$objCategoriasWithOffset["data"];
			$dc_templates_extranet_categorias_index["categorias"]=$categorias;
			
			$filtrosWithOutOffset=array("establecimiento_id"=>$user["business_id"]);
			$objCategoriasWithOutOffset=$this->categorias_model->get($filtrosWithOutOffset);
			$total_categorias_sin_filtro=count($objCategoriasWithOutOffset["data"]);
			$dc_templates_extranet_categorias_index["total_categorias_sin_filtro"]=$total_categorias_sin_filtro;
			$dc_templates_extranet_categorias_index["num_filas_por_pagina"]=$num_filas;
			$dc_templates_extranet_categorias_index["pagina_seleccionada"]=$pag_seleccionada;
			$content=$this->load->view('templates/extranet/categorias/index',$dc_templates_extranet_categorias_index,true);
			$data["content"]=$content;
			
			$this->load->view('index2',$data);
			
			return;
		}else{
			header('Location: '.base_url().'extranet');
		}
	}
	public function dashboard_cajas(){
		if(isset($_SESSION["user"])){
			
			$lang="es";$title="Gestion de cajas";$css=array("index");
		
			$content="";
			$user=$_SESSION["user"];

			$data["lang"]=$lang;
			$data["title"]=$title;
			$data["user"]=$user;
			$data["css"]=$css;
			$data["section_title"]=$title;
			$data["section_root"]="cajas";

			if(!isset($_GET["rows"])){
				$num_filas=10;
			}else{
				$num_filas=$_GET["rows"];
			}
			if(!isset($_GET["pag"])){
				$pag_seleccionada=1;
			}else{
				$pag_seleccionada=$_GET["pag"];
			}
			
			$fila_inicial=(($pag_seleccionada-1)*$num_filas)+1;
			$filtrosWithOffset=array("establecimiento_id"=>$user["business_id"],"pagina"=>$pag_seleccionada,"fila_inicial"=>$fila_inicial,"num_filas"=>$num_filas);
			$objCajasWithOffset=$this->cajas_model->get($filtrosWithOffset);
			$cajas=$objCajasWithOffset["data"];
			$dc_templates_extranet_cajas_index["cajas"]=$cajas;
			
			$filtrosWithOutOffset=array("establecimiento_id"=>$user["business_id"]);
			$objCajasWithOutOffset=$this->cajas_model->get($filtrosWithOutOffset);
			$total_cajas_sin_filtro=count($objCajasWithOutOffset["data"]);
			$dc_templates_extranet_cajas_index["total_cajas_sin_filtro"]=$total_cajas_sin_filtro;
			$dc_templates_extranet_cajas_index["num_filas_por_pagina"]=$num_filas;
			$dc_templates_extranet_cajas_index["pagina_seleccionada"]=$pag_seleccionada;
			$content=$this->load->view('templates/extranet/cajas/index',$dc_templates_extranet_cajas_index,true);
			$data["content"]=$content;
			
			$this->load->view('index2',$data);
			
			return;
		}else{
			header('Location: '.base_url().'extranet');
		}
	}
	public function dashboard_usuarios(){
		if(isset($_SESSION["user"])){
			
			$lang="es";$title="Gestion de usuarios";$css=array("index");
		
			$content="";
			$user=$_SESSION["user"];

			$data["lang"]=$lang;
			$data["title"]=$title;
			$data["user"]=$user;
			$data["css"]=$css;
			$data["section_title"]=$title;
			$data["section_root"]="usuarios";

			if(!isset($_GET["rows"])){
				$num_filas=10;
			}else{
				$num_filas=$_GET["rows"];
			}
			if(!isset($_GET["pag"])){
				$pag_seleccionada=1;
			}else{
				$pag_seleccionada=$_GET["pag"];
			}
			
			$fila_inicial=(($pag_seleccionada-1)*$num_filas)+1;
			$filtrosWithOffset=array("establecimiento_id"=>$user["business_id"],"pagina"=>$pag_seleccionada,"fila_inicial"=>$fila_inicial,"num_filas"=>$num_filas);
			$objUsuariosWithOffset=$this->usuarios_model->get($filtrosWithOffset);
			$usuarios=$objUsuariosWithOffset["data"];
			$dc_templates_extranet_usuarios_index["usuarios"]=$usuarios;
			
			$filtrosWithOutOffset=array("establecimiento_id"=>$user["business_id"]);
			$objUsuariosWithOutOffset=$this->usuarios_model->get($filtrosWithOutOffset);
			$total_usuarios_sin_filtro=count($objUsuariosWithOutOffset["data"]);
			$dc_templates_extranet_usuarios_index["total_usuarios_sin_filtro"]=$total_usuarios_sin_filtro;
			$dc_templates_extranet_usuarios_index["num_filas_por_pagina"]=$num_filas;
			$dc_templates_extranet_usuarios_index["pagina_seleccionada"]=$pag_seleccionada;
			$content=$this->load->view('templates/extranet/usuarios/index',$dc_templates_extranet_usuarios_index,true);
			$data["content"]=$content;
			
			$this->load->view('index2',$data);
			
			return;
		}else{
			header('Location: '.base_url().'extranet');
		}
	}
	private function load($lang,$title,$content,$bgbody='white',$bgimagefull='',$css=array(),$js=array(),$toolbarcolor='#0080FF'){
		$data["template"]["toolbarcolor"]=$toolbarcolor;
		$data["template"]["lang"]=$lang;
		$data["template"]["title"]=$title;
		$data["template"]["bgbody"]=$bgbody;
		$data["template"]["bgimagefull"]=$bgimagefull;
		$data["template"]["content"]=$content;
		$data["template"]["lib"]["css"]=$css;
		$data["template"]["lib"]["js"]=$js;
		$this->load->view('index',$data);
	}
}
