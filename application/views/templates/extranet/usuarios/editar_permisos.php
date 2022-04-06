<form class="container-fluid" id="formulario_editar_permisos_usuarios">
   
<div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="hidden" name="establecimiento_id" readonly value="<?php echo $user["business_id"];?>" />
                <input type="hidden" name="id" readonly value="<?php echo $usuario["id"];?>" />
            </div>
            <div role="permisos">
                <?php 
                    if(count($permisos)>0){
                        echo '<fieldset class="row scheduler-border">';
                        echo '<legend class="scheduler-border">Permisos</legend>';
                        echo '<div class="col-12 d-flex flex-nowrap">';
                        foreach($permisos as $index=>$p){
                            if(count($permisos_segun_usuario)>0){
                                $esta_checked=false;
                                foreach($permisos_segun_usuario as $index2=>$psu){
                                    if($psu["id"]==$p["id"]){
                                        $esta_checked=true;
                                    }
                                }       
                                if($esta_checked){
                                    echo '<div class="form-check">';
                                    echo '<input class="form-check-input" type="checkbox" checked value="'.$p["id"].'" name="permiso['.$index.'][id]">';
                                    echo '<label class="form-check-label" >';
                                    echo    $p["denominacion"];
                                    echo '</label>';
                                    echo '</div>';
                                    echo '&nbsp;';
                                    echo '&nbsp;';
                                    echo '&nbsp;';
                                    echo '&nbsp;';
                                }else{
                                    echo '<div class="form-check">';
                                    echo '<input class="form-check-input" type="checkbox" value="'.$p["id"].'" name="permiso['.$index.'][id]">';
                                    echo '<label class="form-check-label" >';
                                    echo    $p["denominacion"];
                                    echo '</label>';
                                    echo '</div>';
                                    echo '&nbsp;';
                                    echo '&nbsp;';
                                    echo '&nbsp;';
                                    echo '&nbsp;';
                                }
                            }else{
                                echo '<div class="form-check">';
                                        echo '<input class="form-check-input" type="checkbox" value="'.$p["id"].'" name="permiso['.$index.'][id]">';
                                        echo '<label class="form-check-label" >';
                                        echo    $p["denominacion"];
                                        echo '</label>';
                                        echo '</div>';
                                        echo '&nbsp;';
                                        echo '&nbsp;';
                                        echo '&nbsp;';
                                        echo '&nbsp;';
                            }
                            
                            

                        }
                        echo '</div>';
                        echo '</fieldset>';
                    }
                ?>
            </div>                    
            <div class="form-group ">
                <div class="d-flex justify-content-end">
                    <a role="guardar" class="btn btn-outline-danger"><i class="far fa-save"></i>&nbsp;Guardar</a>
                    <a role="cancel" class="btn btn-light">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>

