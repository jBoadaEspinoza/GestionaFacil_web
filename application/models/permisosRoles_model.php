<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PermisosRoles_model extends CI_Model
{
    
    public function __construct()
    {
        parent::__construct();
    }

    
    public function get($filtros=null)
    {
        if(!is_null($filtros)){
            $this->db->select('permisos.id as id,permisos.denominacion as denominacion,roles.id as rol_id,roles.denominacion as rol_denominacion');
            $this->db->from('permisos_roles');
            $this->db->join('permisos','permisos_roles.permiso_id=permisos.id');
            $this->db->join('roles', 'permisos_roles.rol_id = roles.id');
            

            if(isset($filtros["permiso_id"])){
                $this->db->where('permisos.id=',$filtros["permiso_id"]);   
            }
            if(isset($filtros["rol_id"])){
                $this->db->where('roles.id=',$filtros["rol_id"]);
               
            }
            
            if(isset($filtros["num_filas"]) && isset($filtros["pagina"]) && isset($filtros["fila_inicial"])){
                $this->db->limit($filtros["num_filas"],$filtros["fila_inicial"]-1);
            }                       
            $query = $this->db->get(); 

            if($query->num_rows()>=0){
                $result=$query->result_array();
                return array(
                    "success"=>true,
                    "data"=>$result
                );
            } 
            return array(
                "success"=>false,
                "msg"=>"registros no encontrads",
            ); 
           
        }
        
        return array(
            "success"=>false,
            "msg"=>"registro no encontrado",
        ); 

    }
    public function actualizar($pedido_id,$articulo_id,$precio_unitario_pen,$cantidad,$sugerencias){
        
        $this->db->set('precio_unitario_pen', $precio_unitario_pen);
        $this->db->set('cantidad', $cantidad);
        $this->db->set('sugerencias', $sugerencias);
        $this->db->where('pedido_id', $pedido_id);
        $this->db->where('articulo_id', $articulo_id);
        $this->db->update('pedidos_detalles');
        return array("success"=>true,"msg"=>"Error al actualizar el registro");       
    }

    public function insert($pedido_id,$articulo_id,$precio_unitario_pen,$cantidad,$sugerencias=""){
        
        $this->db->set('pedido_id', $pedido_id);
        $this->db->set('articulo_id', $articulo_id);
        $this->db->set('precio_unitario_pen', $precio_unitario_pen);
        $this->db->set('cantidad', $cantidad);
        $this->db->set('sugerencias', $sugerencias);

        $this->db->insert('pedidos_detalles');
        
        $item_id=$this->db->insert_id();

        if($this->db->trans_status()){
            $this->db->trans_commit();
            return array("success"=>true,"msg"=>"Registro guardado con exito","id"=>$item_id);
         
        }
        $this->db->trans_rollback();
        return array("success"=>false,"msg"=>"Error al guardar el registro");

    }
    public function delete($pedido_id,$articulo_id){
        
        $this->db->select('*');
        $this->db->from('pedidos_detalles');
        $this->db->where('pedido_id=',$pedido_id);
        $this->db->where('articulo_id=',$articulo_id);
    

        $query = $this->db->get(); 
        if($query->num_rows()>0){
            
            $this->db->where('pedido_id=',$pedido_id);
            $this->db->where('articulo_id=',$articulo_id);
            $this->db->delete('pedidos_detalles');

            return array(
                "success"=>false,
                "msg"=>"registro eliminado con exito"
            );
        } 
        return array(
            "success"=>false,
            "msg"=>"registro no encontrado",
        ); 
       
    }
}