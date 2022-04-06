
<form class="container-fluid" id="formulario_cambiar_metodo_de_pago">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <input type="hidden" name="referencia" value="<?php echo $referencia;?>" /> 
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label>Modalidad de pago:</label>
                        <select class="form-control" name="modalidad_de_pago">
                            <?php 
                                foreach($metodos_de_pago as $index=>$mp){
                                    if(strtoupper($mp["denominacion"])==strtoupper($metodo_de_pago_seleccionado)){
                                        echo '<option selected value="'.$mp["id"].'">'.strtoupper($mp["denominacion"]).'</option>';
                                    }else{
                                        echo '<option value="'.$mp["id"].'">'.strtoupper($mp["denominacion"]).'</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <div class="form-group ">
                        <div class="d-flex justify-content-end">
                            <a role="guardar" class="btn btn-outline-danger"><i class="far fa-save"></i>&nbsp;Guardar cambios</a>
                            <a role="cancel" class="btn btn-light">Cancelar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>