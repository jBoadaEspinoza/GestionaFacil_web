
<form class="container-fluid">
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
                        <label>Seleccione Caja Aperturada:</label>
                        <select class="form-control" name="apertura_caja">
                            <?php 
                                foreach($aperturas_cajas as $index=>$mp){
                                    if(strtoupper($mp["id"])==strtoupper($apertura_caja_actual["id"])){
                                        echo '<option selected value="'.$mp["id"].'">(LOTE:'.str_pad($mp["id"], 5, "0", STR_PAD_LEFT).') '.strtoupper($mp["caja_denominacion"]).'</option>';
                                    }else{
                                        echo '<option value="'.$mp["id"].'">(LOTE:'.str_pad($mp["id"], 5, "0", STR_PAD_LEFT).') '.strtoupper($mp["caja_denominacion"]).'</option>';
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