<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AperturaCajaItems_model extends CI_Model
{
    
    public function __construct()
    {
        parent::__construct();
    }

    public function get($filtros=null)
    {
        if(!is_null($filtros)){
            $this->db->distinct();
            $this->db->select('*');
            $this->db->from('apertura_caja_items');

            if(isset($filtros["id"])){
                $this->db->where('apertura_caja_items.id=',$filtros["id"]);   
            }
            if(isset($filtros["apertura_caja_id"])){
                $this->db->where('apertura_caja_items.apertura_caja_id=',$filtros["apertura_caja_id"]);
            }
            if(isset($filtros["referencia"])){
                $this->db->like('apertura_caja_items.referencia', $filtros["referencia"], 'both'); 
            }
            if(isset($filtros["num_filas"]) && isset($filtros["pagina"]) && isset($filtros["fila_inicial"])){
                $this->db->limit($filtros["num_filas"],$filtros["fila_inicial"]-1);
            }
            if(isset($filtros["ordenado_por"]) && isset($filtros["en_orden"])){
                $this->db->order_by($filtros["ordenado_por"], $filtros["en_orden"]);
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
                "msg"=>"registros no encontrados",
            ); 
           
        }
       
    }
    
    public function insert($apertura_caja_id,$item_descripcion,$moneda_id,$monto,$referencia){
        $this->db->set('apertura_caja_id', $apertura_caja_id);
        $this->db->set('item_descripcion',$item_descripcion);
        $this->db->set('fecha_de_registro', DATE::getNowAccordingUTC());
        $this->db->set('moneda_id',$moneda_id);
        $this->db->set('monto',$monto);
        $this->db->set('referencia',$referencia);
        $this->db->insert('apertura_caja_items');
        
        $item_id=$this->db->insert_id();

        if($this->db->trans_status()){
            $this->db->trans_commit();
            return array("success"=>true,"msg"=>"Registro guardado con exito","id"=>$item_id);
         
        }
        $this->db->trans_rollback();
        return array("success"=>false,"msg"=>"Error al guardar el registro");

    }

    public function update($id,$apertura_caja_id,$item_descripcion,$fecha_de_registro,$moneda_id,$monto,$referencia){
        
        $this->db->set('apertura_caja_id', $apertura_caja_id);
        $this->db->set('item_descripcion',$item_descripcion);
        $this->db->set('fecha_de_registro', $fecha_de_registro);
        $this->db->set('moneda_id',$moneda_id);
        $this->db->set('monto',$monto);
        $this->db->set('referencia',$referencia);
        $this->db->where('id=',$id);
        $this->db->update('apertura_caja_items');

        return array("success"=>true,"msg"=>"Registro actualizado con exito"); 
        
    }

    public function delete($id){
        $this->db->where('id=',$id);
        $this->db->delete('apertura_caja_items');

        $this->db->select('*');
        $this->db->from('apertura_caja_items');
        $this->db->where('id=',$id);
    

        $query = $this->db->get(); 
        if($query->num_rows()>0){
            
            return array(
                "success"=>false,
                "msg"=>"Error al eliminar el registro ".md5($id)
            );
        } 
        return array(
            "success"=>true,
            "msg"=>"Registro eliminado con exito",
        ); 
    }
}