<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_model extends CI_Model
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('establecimientosTipos_model');
        $this->load->model('permisosRoles_model');
    }
    
    public function get($filtros=null)
    {
        if(!is_null($filtros)){
            
            $this->db->distinct();
            $this->db->select('usuarios.id as id,nombre,clave_acceso,personas.id as persona_id,personas.nombres as persona_nombres,personas.apellidos as persona_apellidos,personas.documento_tipo_id as persona_documento_tipo_id,personas.documento_numero as persona_documento_numero,roles.id as rol_id,roles.denominacion as rol_denominacion');
            $this->db->from('usuarios');
            $this->db->join('roles','roles.id=usuarios.rol_id');
            $this->db->join('personas','personas.id=usuarios.persona_id');
            
            
            if(isset($filtros["id"])){
                $this->db->where('usuarios.id=',$filtros["id"]);   
            }
            if(isset($filtros["rol_id"])){
                $this->db->where('roles.id=',$filtros["rol_id"]);
            }
            if(isset($filtros["establecimiento_id"])){
                $this->db->where('usuarios.establecimiento_id=',$filtros["establecimiento_id"]);
            }
            if(isset($filtros["num_filas"]) && isset($filtros["pagina"]) && isset($filtros["fila_inicial"])){
                $this->db->limit($filtros["num_filas"],$filtros["fila_inicial"]-1);
            }
            $query = $this->db->get(); 

            if($query->num_rows()>=0){
                $result=$query->result_array();
                foreach($result as $index=>$r){
                    $rol_id=$r["rol_id"];
                    $objPermisosRoles=$this->permisosRoles_model->get(array("rol_id"=>$rol_id));
                    $tiene_permiso=0;
                    if(count($objPermisosRoles["data"])>0){
                        $tiene_permiso=1;
                    }
                    $result[$index]["tiene_permiso"]=$tiene_permiso;
                }
                return array(
                    "success"=>true,
                    "data"=>$result
                );
            } 
            return array(
                "success"=>false,
                "msg"=>"cajas no encontradas",
            ); 
        }
    }
    public function insert($nombre_usuario,$clave_acceso,$persona_id,$rol_id,$establecimiento_id,$activo=1){
        $this->db->set('nombre', $nombre_usuario);
        $this->db->set('clave_acceso', md5($clave_acceso));
        $this->db->set('persona_id', $persona_id);
        $this->db->set('rol_id', $rol_id);
        $this->db->set('establecimiento_id', $establecimiento_id);
        $this->db->set('activo', $activo);
        $this->db->insert('usuarios');
        
        $item_id=$this->db->insert_id();

        if($this->db->trans_status()){
            $this->db->trans_commit();
            return array("success"=>true,"msg"=>"Registro guardado con exito","id"=>$item_id);
         
        }
        $this->db->trans_rollback();
        return array("success"=>false,"msg"=>"Error al guardar el registro");
    }
    public function update($id,$nombre_usuario,$clave_acceso,$persona_id,$rol_id,$establecimiento_id,$activo=1){
        $this->db->set('nombre', $nombre_usuario);
        $this->db->set('clave_acceso', md5($clave_acceso));
        $this->db->set('persona_id', $persona_id);
        $this->db->set('rol_id', $rol_id);
        $this->db->set('establecimiento_id', $establecimiento_id);
        $this->db->set('activo', $activo);
        $this->db->where('id=',$id);
        $this->db->update('usuarios');
        return array("success"=>true,"msg"=>"Registro actualizado con exito");   
        
        
    }
    public function change_password($business_id,$user_name,$user_password_old,$user_password_new){
        $data=self::is_password_correct($business_id,$user_name,$user_password_old);
        if($data["success"]){
            if($data["user_type"]=="admin"){
                //actualizacion usuario admin
                $this->db->set('propietario_clave_acceso', $user_password_new);
                $this->db->where('id=',$business_id);
                $this->db->update('establecimientos');
                return array("success"=>true,"msg"=>"Registro actualizado correctamente");
            }else{
                //codigo para actualizacion otro tipo de usuario
            }
        }
    }

    public function is_password_correct($business_id,$user_name,$user_password){
        
        $this->db->select('propietario_clave_acceso');
        $this->db->from('establecimientos');
        $this->db->where('establecimientos.id=',$business_id);
        //si usuario es administrador 
        $this->db->where('establecimientos.propietario_correo_electronico=',$user_name);
        //si usuario no es administrador evaluara....

        //
        $query=$this->db->get();

        if($query->num_rows()==1){
            $result=$query->row_array();  
            $business_admin_password=$result["propietario_clave_acceso"];
            if($user_password==$business_admin_password){
                return array(
                    "success"=>true,
                    "msg"=>"Contraseña correcta",
                    "user_type"=>"admin"
                );
            }
            return array(
                "success"=>false,
                "msg"=>"Contraseña incorrecta",
            );
        }
        return array(
            "success"=>false,
            "msg"=>"Error",
        );  
    }
    
    public function validate($ruc,$name,$password){
        
        $this->db->select('id,nombre_comercial,activo,propietario_id,propietario_nombres,propietario_apellidos,propietario_correo_electronico,propietario_celular,propietario_clave_acceso,abierto,direccion_denominacion,apisunat_personaToken,apisunat_personaId');
        $this->db->from('establecimientos');
        $this->db->where('establecimientos.ruc=',$ruc);
        $query_by_ruc=$this->db->get();

        //validamos si existe el establecimiento
        if($query_by_ruc->num_rows()==1){
            
            $result_by_ruc=$query_by_ruc->row_array();
            $business_id=$result_by_ruc["id"];
            $business_name=$result_by_ruc["nombre_comercial"];
            $business_active=$result_by_ruc["activo"];
            
            if($business_active==1){
                $business_admin_person_id=$result_by_ruc["propietario_id"];
                $business_admin_firstnames=$result_by_ruc["propietario_nombres"];
                $business_admin_lastnames=$result_by_ruc["propietario_apellidos"];
                $business_admin_email=$result_by_ruc["propietario_correo_electronico"];
                $business_admin_password=$result_by_ruc["propietario_clave_acceso"];
                $business_admin_cellphone=$result_by_ruc["propietario_celular"];
                $business_admin_address=$result_by_ruc["direccion_denominacion"];
                $business_admin_apisunat_personaToken=$result_by_ruc["apisunat_personaToken"];
                $business_admin_apisunat_personaId=$result_by_ruc["apisunat_personaId"];
                $business_open=$result_by_ruc["abierto"];
                if($name==$business_admin_email){
                    //Es un usuario administrador
                    if($password==$business_admin_password){
                        $objTipoEstablecimiento=$this->establecimientosTipos_model->get(array("establecimiento_id"=>$business_id));
                        if(!$objTipoEstablecimiento["success"]){
                            return array(
                                "success"=>false,
                                "error_id"=>5,
                                "msg"=>"Tipo de establecimiento no definido",
                            ); 
                        }
                        $tipoEstablecimiento=$objTipoEstablecimiento["data"][0];
                        return array(
                            "success"=>true,
                            "data"=>array(
                                "business_id"=>$business_id,
                                "business_ruc"=>$ruc,
                                "business_name"=>$business_name,
                                "business_address"=>$business_admin_address,
                                "business_apisunat_personaToken"=>$business_admin_apisunat_personaToken,
                                "business_apisunat_personaId"=>$business_admin_apisunat_personaId,
                                "business_open"=>$business_open,
                                "business_active"=>$business_active,
                                "user_person_id"=>$business_admin_person_id,
                                "user_firstname"=>$business_admin_firstnames,
                                "user_lastname"=>$business_admin_lastnames,
                                "user_cellphone"=>$business_admin_cellphone,
                                "user_email"=>$business_admin_email,
                                "user_name"=>$business_admin_email,
                                "user_type"=>"admin",
                                "business_type_id"=>$tipoEstablecimiento["establecimiento_tipo_id"],
                                "business_type_denomination_es"=>$tipoEstablecimiento["establecimiento_tipo_denominacion_es"],
                                "business_type_denomination_en"=>$tipoEstablecimiento["establecimiento_tipo_denominacion_en"],
                                "business_type_denomination_plural_es"=>$tipoEstablecimiento["establecimiento_tipo_denominacion_plural_es"],
                                "business_type_denomination_plural_en"=>$tipoEstablecimiento["establecimiento_tipo_denominacion_plural_en"]
                            )
                        );
                    }
                    return array(
                        "success"=>false,
                        "error_id"=>3,
                        "msg"=>"Contraseña incorrecta",
                    );  

                }else{
                    //Es un usuario de nivel bajo
                    return array(
                        "success"=>false,
                        "error_id"=>4,
                        "msg"=>"Correo electronico no asociado al establecimiento",
                    );  
                }
            }
            return array(
                "success"=>false,
                "error_id"=>2,
                "msg"=>"Establecimiento inactivo",
            );     
        }
        return array(
            "success"=>false,
            "error_id"=>1,
            "msg"=>"Establecimiento no encontrado",
        ); 

    }
    
}