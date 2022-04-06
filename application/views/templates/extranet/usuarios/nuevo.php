<form class="container-fluid" id="formulario_nuevo_usuario" autocomplete="nope">
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="hidden" name="establecimiento_id" readonly value="<?php echo $user["business_id"];?>" />
            </div>
            
            <fieldset class="row scheduler-border">
                <legend class="scheduler-border">Información personal</legend>
                <div class="col-6">
                    <div class="form-group">
                        <label>Tipo de documento</label>
                        <select name="documento_tipo" class="form-control">
                            <?php
                                foreach($documentosTipos as $index=>$documento){
                                    echo '<option value="'.$documento["id"].'">'.ucfirst($documento["denominacion_largo_es"]).'</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>Número de documento</label>
                        <div class="input-group">
                            <input type="text" name="documento_numero" class="form-control"/>
                            <div class="input-group-append">
                                <button class="btn btn-info" role="busqueda_por_dni" type="button">Buscar</button>
                            </div>
                            <p class="text-danger" role="error_documento_numero"></p>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>Nombres</label>
                        <input type="text" name="nombres" autocomplete="nope" readonly class="form-control"/>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>Apellidos</label>
                        <input type="text" name="apellidos" autocomplete="nope" readonly class="form-control"/>
                    </div>
                </div>
            </fieldset>
            <fieldset class="row scheduler-border">
                <legend class="scheduler-border">Datos de la cuenta</legend>
                <div class="col-6">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre_usuario" autocomplete="nope" class="form-control"/>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" name="clave_acceso" autocomplete="nope" class="form-control"/>
                    </div>
                </div>
            </fieldset>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label>Rol</label>
                        <select name="rol" class="form-control">
                            <?php
                                foreach($roles as $index=>$rol){
                                    echo '<option value="'.$rol["id"].'">'.ucfirst($rol["denominacion"]).'</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div role="permisos">
                <?php 
                    if(count($permisos)>0){
                        echo '<fieldset class="row scheduler-border">';
                        echo '<legend class="scheduler-border">Permisos</legend>';
                        echo '<div class="col-12 d-flex flex-nowrap">';
                        foreach($permisos as $index=>$p){
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