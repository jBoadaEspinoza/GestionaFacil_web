<section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="d-flex justify-content-between">
                  <div class="">
                     <div class="d-flex flex-wrap">
                        <div class="col-4">
                            <div class="form-group">
                                <label>Número de registros por página</label>
                                <select onchange="cambiaNumeroDeRegistrosPorPagina(this)" id="rows" data-ref="<?php echo base_url().'dashboard_ventas';?>" class="form-control">
                                    <?php
                                      $items=[50,100,250,500,1000];
                                      for($i=0;$i<count($items);$i++){
                                        if($items[$i]==$num_filas_por_pagina){
                                          echo '<option value="'.$items[$i].'" selected>'.$items[$i].' registros</option>';
                                        }else{
                                          echo '<option value="'.$items[$i].'">'.$items[$i].' registros</option>';
                                        }
                                      }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                          <div class="form-group">
                            <label>Desde:</label>
                            <div class="input-group" id="dt">
                              <input type="text" name="date_from" readonly class="form-control" value="<?php echo $date_from;?>" />
                              <div class="input-group-append">
                                <a role="button" class="text-light btn btn-primary" onclick="openDatepickerFrom(this,event)" id="basic-addon2"><i class="fas fa-calendar-alt"></i></a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                              <label>A:</label>
                              <div class="input-group" id="dt">
                                <input type="text" name="date_to" readonly class="form-control" value="<?php echo $date_to;?>" />
                                <div class="input-group-append">
                                  <a role="button" class="text-light btn btn-primary" onclick="openDatepickerTo(this,event)" id="basic-addon2"><i class="fas fa-calendar-alt"></i></a>
                                </div>
                              </div>
                            </div>
                        </div>
                        <div class="col-12">
                          <label>Modalidades de pago</label>
                          <div class="d-flex align-items-start">
                          <?php 
                            foreach($modalidades_de_pago as $index=>$m){
                              if($modalidades==""){
                                echo '<div class="form-check">
                                  <input class="form-check-input" type="checkbox" value="'.$m["id"].'" onchange="agregarModalidad(this,event)">
                                  <label class="form-check-label" for="flexCheckIndeterminate">'.ucfirst($m["denominacion"]).'</label>
                                </div>&nbsp;&nbsp;&nbsp;';
                              }else{
                                $modalidades_array=explode("_",$modalidades);
                                $esta=false;
                                for($i=0;$i<count($modalidades_array);$i++){
                                  if($m["id"]==$modalidades_array[$i]){
                                    $esta=true;
                                    break;
                                  }
                                }
                                if($esta){
                                  echo '<div class="form-check">
                                  <input class="form-check-input" checked type="checkbox" value="'.$m["id"].'" onchange="agregarModalidad(this,event)">
                                  <label class="form-check-label" for="flexCheckIndeterminate">'.ucfirst($m["denominacion"]).'</label>
                                </div>&nbsp;&nbsp;&nbsp;';
                                }else{
                                  echo '<div class="form-check">
                                  <input class="form-check-input" type="checkbox" value="'.$m["id"].'" onchange="agregarModalidad(this,event)">
                                  <label class="form-check-label" for="flexCheckIndeterminate">'.ucfirst($m["denominacion"]).'</label>
                                </div>&nbsp;&nbsp;&nbsp;';
                                }
                              }
                             
                            }
                          ?>
                          </div>
                        </div>
                      </div>
                  </div>
                  <!--<div>
                    <a class="btn btn-primary btn-lg" onclick="abreNuevoPedido(this,event);"><i class="fas fa-plus"></i>&nbsp;Nuevo</a>
                  </div>-->
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example2" class="table table table-striped table-bordered table-hover">
                  <thead>
                  <tr>
                    <th class="text-center" style="width:5%;">#</th>
                    <th class="text-center" style="width:15%;">Caja</th>
                    <th class="text-center" style="width:7%;">Ref</th>
                    <th class="text-center" style="width:15%;">Cierre</th>
                    <!--<th class="text-center">Cliente</th>-->
                    <th class="text-center">Comprobante Electronico</th>
                    <th class="text-center" style="width:12%;">Total</th>
                    <th class="text-center" style="width:20%;">Acciones</th>
                  </tr>
                  </thead>
                  <tbody>
                      <?php
                        foreach($pedidos as $index=>$pedido){
                            echo '<tr>';
                            echo '<td class="text-center">'.((($pagina_seleccionada-1)*$num_filas_por_pagina)+$index+1).'</td>';
                            if($pedido["lote"]==""){
                              echo '<td class="text-center">?</td>';
                            }else{
                              if($pedido["lote"]["fecha_cierre"]=="0000-00-00 00:00:00"){
                                $caja_estado='<span class="badge badge-success">Abierta</span>';
                                echo '<td class="text-center text-success"><i class="fas fa-cash-register"></i><br>'.$pedido["lote"]["caja_denominacion"] .'&nbsp;('.str_pad($pedido["lote"]["id"], 5, "0", STR_PAD_LEFT).')<br>'.$caja_estado.'</td>';
                              }else{
                                $caja_estado='<span class="badge badge-danger">Cerrada</span>';
                                echo '<td class="text-center text-danger"><i class="fas fa-cash-register"></i><br>'.$pedido["lote"]["caja_denominacion"] .'&nbsp;('.str_pad($pedido["lote"]["id"], 5, "0", STR_PAD_LEFT).')<br>'.$caja_estado.'</td>';
                              }
                            }
                            
                            echo '<td class="text-center">'.str_pad($pedido["id"], 5, "0", STR_PAD_LEFT).'<br><span class="badge badge-primary">'.$pedido["mesa"]["denominacion"].'</td>';
                            $fecha_emision=DATE::convertUTCToDateTimeZone($pedido["pedido_fecha_hora_cierre"]);
                            echo '<td class="text-center">'.($pedido["cerrado"]==1 ? $fecha_emision->format('Y-m-d h:i a') : '-').'</td>';
                            //echo '<td class="text-center">'.strtoupper($pedido["cliente_full_name"]).'</td>';
                            if(count($pedido["comprobante_electronico"])==0){
                              echo '<td class="text-center">-</td>';
                            }else{
                              $color="warning";
                              switch($pedido["comprobante_electronico"][0]["estado"]){
                                case "ACEPTADO":
                                  $color="success";
                                  break;
                                case "RECHAZADO":
                                  $color="danger";
                                  break;
                                case "EXCEPCION":
                                  $color="danger";
                              }
                              echo '<td class="text-center">'.strtoupper($pedido["comprobante_electronico"][0]["tipo_denominacion"]).'<br>'.$pedido["comprobante_electronico"][0]["fileName"].'<br><span class="badge badge-'.$color.'">'.$pedido["comprobante_electronico"][0]["estado"].'</span></td>';
                            }
                            
                            echo '<td class="text-center">S/.'.number_format($pedido["total"], 2, '.', '').'<br>'.$pedido["modalidad_pago"].'</td>';
                            
                            echo '<td class="d-flex flex-wrap">';
                            //echo '        <a class="btn btn-warning btn-xs" data-id="'.$pedido["id"].'" onclick="abreVerpedido(this,event)"><i class="fas fa-eye"></i>&nbsp;Detalle</a>&nbsp;';
                            if(count($pedido["comprobante_electronico"])==0){
                              if($pedido["lote"]["fecha_cierre"]=="0000-00-00 00:00:00"){
                                echo    '<a class="btn btn-danger btn-xs m-1" data-id="'.$pedido["id"].'" onclick="generarComprobanteDePago(this,event)">&nbsp;Generar boleta o factura electronica</a>&nbsp;<a class="btn btn-primary btn-xs m-1" data-id="'.$pedido["id"].'" target="_blank" href="'.base_url().'pedidos/print_ticket?p_id='.$pedido["id"].'"><i class="fas fa-print"></i>&nbsp;Nota de pedido</a>&nbsp;<a class="btn btn-success btn-xs m-1" data-id="'.$pedido["id"].'" onclick="abreCambiarMetodoDePago(this,event)"><i class="fas fa-pen"></i>&nbsp;Cambiar metodo de pago</a>&nbsp;<a class="btn btn-warning btn-xs m-1" data-id="'.$pedido["id"].'" onclick="abreCambiarCaja(this,event)"><i class="fas fa-pen"></i>&nbsp;Cambiar caja</a>&nbsp;<a class="btn btn-info btn-xs m-1" data-id="'.$pedido["id"].'" onclick="abreReabrir(this,event)"><i class="fa-solid fa-unlock"></i>&nbsp;Reabrir</a>';
                              }else{
                                echo    '<a class="btn btn-primary btn-xs m-1" data-id="'.$pedido["id"].'" target="_blank" href="'.base_url().'pedidos/print_ticket?p_id='.$pedido["id"].'"><i class="fas fa-print"></i>&nbsp;Nota de pedido</a>';
                              }
                            }else{
                              $url="https://back.apisunat.com/documents/".$pedido["comprobante_electronico"][0]["documentId"]."/getPDF/ticket58mm/".$pedido["comprobante_electronico"][0]["fileName"].".pdf";
                                echo    '<a class="btn btn-success btn-xs m-1" target="_blank" href="'.$url.'"><i class="fas fa-print"></i>&nbsp;Copia de '.$pedido["comprobante_electronico"][0]["tipo_denominacion"].'</a>';
                             
                            }
                            
                            
                            
                            echo '</td>';
                            echo '</tr>';
                        }
                      ?>
                  </tbody>
                </table>
                <br>
                <div class="d-flex justify-content-center">
                      <?php
                          $D=$total_pedidos_sin_filtro;
                          $d=$num_filas_por_pagina;
                          $q=intval($D/$d);
                          $r=$D-$d*$q;
                          if($q!=0){
                            echo '<nav aria-label="...">';
                            echo '  <ul class="pagination">';
                            if($r>0){
                              $q=$q+1;
                            }
                            for($i=0;$i<$q;$i++){
                              if(($i+1)==$pagina_seleccionada){
                                if($mod_sel==""){
                                  echo '<li class="page-item active"><a class="page-link" href="'.base_url().'dashboard_ventas?desde='.$date_from.'&hasta='.$date_to.'&rows='.$d.'&pag='.($i+1).'">'.($i+1).'</a></li>';
                                }else{
                                  echo '<li class="page-item active"><a class="page-link" href="'.base_url().'dashboard_ventas?desde='.$date_from.'&hasta='.$date_to.'&modalidades='.$mod_sel.'&rows='.$d.'&pag='.($i+1).'">'.($i+1).'</a></li>';
                                }
                                
                              }else{
                                if($mod_sel==""){
                                  echo '<li class="page-item"><a class="page-link" href="'.base_url().'dashboard_ventas?desde='.$date_from.'&hasta='.$date_to.'&rows='.$d.'&pag='.($i+1).'">'.($i+1).'</a></li>';
                                }else{
                                  echo '<li class="page-item"><a class="page-link" href="'.base_url().'dashboard_ventas?desde='.$date_from.'&hasta='.$date_to.'&modalidades='.$mod_sel.'&rows='.$d.'&pag='.($i+1).'">'.($i+1).'</a></li>';
                                }
                                
                              }   
                            }
                            echo '  </ul>';
                            echo '</nav>';
                        }
                      ?>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    <script>
        
        function openDatepickerFrom(bt,event){
            var div=$(bt).parent().parent();
            var ref="<?php echo base_url().'dashboard_ventas';?>";
            $(div).find('input[name=date_from]').datepicker({format: 'yyyy-mm-dd',language: 'es'})
            .datepicker('show').on("changeDate",function(ev){
                var date=moment(ev.date.valueOf()).add('d',1).format('YYYY-MM-DD');
                $(div).find('input[name=date_from]').val(date);
                var v_modalidad="";
                  var array=[];
                  $('.form-check').find('input').each(function(){
                      if($(this).is(":checked")){
                        array.push($(this).val());
                      }
                  });
                  for(var i=0;i<array.length;i++){
                    if((i+1)==array.length){
                      v_modalidad+=array[i];
                    }else{
                      v_modalidad+=array[i]+'_';
                    }
                  }
                  //alert(v_modalidad);
                location.href = ref+'?desde='+date+'&hasta='+$('input[name=date_to]').val()+'&modalidades='+v_modalidad+'&rows='+$("#rows").val();
            });
            $('.prev i').removeClass();
            $('.prev i').addClass("fa fa-chevron-left");
            $('.next i').removeClass();
            $('.next i').addClass("fa fa-chevron-right");
        }
        function openDatepickerTo(bt,event){
            var div=$(bt).parent().parent();
            var ref="<?php echo base_url().'dashboard_ventas';?>";
            $(div).find('input[name=date_to]').datepicker({format: 'yyyy-mm-dd',language: 'es'})
            .datepicker('show').on("changeDate",function(ev){
                var date=moment(ev.date.valueOf()).add('d',1).format('YYYY-MM-DD');
                $(div).find('input[name=date_to]').val(date);
                  var v_modalidad="";
                  var array=[];
                  $('.form-check').find('input').each(function(){
                      if($(this).is(":checked")){
                        array.push($(this).val());
                      }
                  });
                  for(var i=0;i<array.length;i++){
                    if((i+1)==array.length){
                      v_modalidad+=array[i];
                    }else{
                      v_modalidad+=array[i]+'_';
                    }
                  }
                location.href = ref+'?desde='+$('input[name=date_from]').val()+'&hasta='+date+'&modalidades='+v_modalidad+'&rows='+$("#rows").val();
            });
            $('.prev i').removeClass();
            $('.prev i').addClass("fa fa-chevron-left");
            $('.next i').removeClass();
            $('.next i').addClass("fa fa-chevron-right");
        }
        function generarComprobanteDePago(bt,event){
          event.preventDefault();
          $.dialog({
              closeIcon: true,
                type: 'danger',
                typeAnimated: true,
                boxWidth: '50%',
                useBootstrap: false,
                content:function(){
                    var self=this;
                    formulario_detalle_mesa=self;
                    return $.post("pedidos/abrir_generar_comprobante_electronico",{"ref":$(bt).attr('data-id')})
                    .done(function(data){
                     
                        var r=JSON.parse(data);
                        self.setTitle(r.title);
                        self.setContentAppend(r.template);
                        //hide_loadingOverlay(bt);
                    });
                },
                onContentReady:function(){
                  var self=this;
                  this.$content.find('a[role=generar]').click(function(){ 
                    var bt=this;
                    var form=self.$content.find('form');
                    $(bt).html("generando comprobante de pago...");
                    show_loadingOverlay(bt,[255,255,255,0,0.5]);
                                              
                    $.post('pedidos/guardar_generar_comprobante_electronico',$(form).serializeArray()
                      ).done(function(data){
                        var r=JSON.parse(data);
                        if(r.success){
                          hide_loadingOverlay(bt);
                          $.alert({
                              title: 'Mensaje de sistema',
                              content: '<i class="mt-2 text-success fa-solid fa-check fa-2x"></i>&nbsp;'+r.msg,
                              buttons: {
                                ok: function () {
                                  var url="https://back.apisunat.com/documents/"+r.data.documentId+"/getPDF/ticket58mm/"+r.data.fileName+".pdf";
                                  window.open(url,"_blank");
                                  show_loadingOverlay($('body'),[255,255,255,0,0.5]);
                                  location.reload();
                                  return;
                                }
                            }
                          });
                        }
                      });    
                  });
                  this.$content.find('a[role=cancel]').click(function(){
                      self.close();
                  });
                  this.$content.find('select[name="comprobante_tipo_serie"]').on('change',function(){
                    var sel=self.$content.find('select[name="documento_tipo"]');
                    $(sel).html();
                    $.post('pedidos/documentos_segun_comprobante_tipo',{"comprobante_tipo_serie_id":$(this).val()}
                      ).done(function(data){
                          var r=JSON.parse(data);
                          var options="";
                          for(var i=0;i<r.data.length;i++){
                            options+='<option value="'+r.data[i].id+'">'+r.data[i].denominacion_largo_es+' ('+r.data[i].denominacion_corto+')</option>';
                          }
                          $(sel).html(options);
                          switch(parseInt(r.from)){
                            case 1:
                              self.$content.find("input[name=nombres]").parent().find('label').html("Razon social");
                              self.$content.find("input[name=apellidos]").parent().find('label').html("Direccion");
                              self.$content.find("input[name=documento_numero]").val("");
                              self.$content.find("input[name=nombres]").val("");
                              self.$content.find("input[name=apellidos]").val("");
                              self.$content.find("input[name=celular]").val("");
                              self.$content.find("input[name=correo_electronico]").val("");
                              self.$content.find('input[name=nombres]').attr('readonly',true);
                              self.$content.find('input[name=apellidos]').attr('readonly',true);
                              break;
                            case 3:
                              self.$content.find("input[name=nombres]").parent().find('label').html("Nombres");
                              self.$content.find("input[name=apellidos]").parent().find('label').html("Apellidos");
                              self.$content.find("input[name=documento_numero]").val("");
                              self.$content.find("input[name=nombres]").val("");
                              self.$content.find("input[name=apellidos]").val("");
                              self.$content.find("input[name=celular]").val("");
                              self.$content.find("input[name=correo_electronico]").val("");
                              break;
                          }
                      });
                  });
                  this.$content.find('select[name="documento_tipo"]').on('change',function(){     
                      switch(parseInt($(this).val())){
                          case 1:
                            self.$content.find("input[name=nombres]").parent().find('label').html("Razon social");
                            self.$content.find("input[name=apellidos]").parent().find('label').html("Direccion");
                            self.$content.find("input[name=documento_numero]").val("");
                            self.$content.find("input[name=nombres]").val("");
                            self.$content.find("input[name=apellidos]").val("");
                            self.$content.find("input[name=celular]").val("");
                            self.$content.find("input[name=correo_electronico]").val("");
                            self.$content.find('input[name=nombres]').attr('readonly',true);
                            self.$content.find('input[name=apellidos]').attr('readonly',true);
                            break;
                          case 2:
                            self.$content.find("input[name=nombres]").parent().find('label').html("Razon social");
                            self.$content.find("input[name=apellidos]").parent().find('label').html("Direccion");
                            self.$content.find("input[name=documento_numero]").val("");
                            self.$content.find("input[name=nombres]").val("");
                            self.$content.find("input[name=apellidos]").val("");
                            self.$content.find("input[name=celular]").val("");
                            self.$content.find("input[name=correo_electronico]").val("");
                            self.$content.find('input[name=nombres]').attr('readonly',true);
                              self.$content.find('input[name=apellidos]').attr('readonly',true);
                            break;
                          default:
                            self.$content.find("input[name=nombres]").parent().find('label').html("Nombres");
                            self.$content.find("input[name=apellidos]").parent().find('label').html("Apellidos");
                            self.$content.find("input[name=documento_numero]").val("");
                            self.$content.find("input[name=nombres]").val("");
                            self.$content.find("input[name=apellidos]").val("");
                            self.$content.find("input[name=celular]").val("");
                            self.$content.find("input[name=correo_electronico]").val("");
                            
                            break;
                        }  
                    });
                  this.$content.find('button[role=busqueda]').click(function(){
                      var bt=this;
                      show_loadingOverlay(bt,[255,255,255,0,0.5]);
                      var documento_numero=self.$content.find('input[name=documento_numero]').val();
                      var documento_tipo_id=self.$content.find('select[name=documento_tipo]').val();
                      
                      $.post("personas/busqueda_por_documento",{"documento_numero":documento_numero,"documento_tipo":documento_tipo_id}
                      ).done(function(data){
                          self.$content.find('p[role=error_documento_numero]').html("");
                          
                          var r=JSON.parse(data);
                          if(!r.success){
                            switch(r.msg_id){
                              case 1:
                                self.$content.find('input[name=nombres]').focus();
                                self.$content.find('input[name=nombres]').val("");
                                self.$content.find('input[name=apellidos]').val("");
                                self.$content.find('select[name=postal]').find('option').each(function(){
                                  $(this).removeAttr('selected');
                                  if($(this).val()==51){
                                    $(this).attr('selected','selected');
                                  }
                                });

                                self.$content.find('input[name=celular]').val("");
                                self.$content.find('input[name=correo_electronico]').val("");
                                self.$content.find('input[name=nombres]').removeAttr('readonly');
                                self.$content.find('input[name=apellidos]').removeAttr('readonly');
                                
                                hide_loadingOverlay(bt);
                                return;
                                break;
                              case 2:
                                self.$content.find('p[role=error_documento_numero]').html(r.msg);
                                self.$content.find('input[name=documento_numero]').val("");
                                hide_loadingOverlay(bt);
                                return;
                                break;
                            }
                            
                          }
                          
                          if(r.return=='from_ruc' ){
                            self.$content.find('input[name=nombres]').val(r.razon_social);
                            self.$content.find('input[name=apellidos]').val(r.direccion);
                            self.$content.find('input[name=celular]').val(r.celular_numero);
                            self.$content.find('input[name=correo_electronico]').val(r.correo_electronico);
                            hide_loadingOverlay(bt);
                            return;
                          }
                          if(r.return=='from_dni'){
                            self.$content.find('input[name=nombres]').val(r.nombres);
                            self.$content.find('input[name=apellidos]').val(r.apellidos);
                            self.$content.find('input[name=celular]').val(r.celular_numero);
                            self.$content.find('input[name=correo_electronico]').val(r.correo_electronico);
                            hide_loadingOverlay(bt);
                            return;
                          }
                          if(r.return=='from_bd'){
                            if(documento_tipo_id==2){
                              self.$content.find('input[name=nombres]').val(r.razon_social);
                              self.$content.find('input[name=apellidos]').val(r.direccion);
                              self.$content.find('input[name=celular]').val(r.celular_numero);
                              self.$content.find('input[name=correo_electronico]').val(r.correo_electronico);
                              hide_loadingOverlay(bt);
                              return;
                            }else{
                                self.$content.find('input[name=nombres]').val(r.nombres);
                                self.$content.find('input[name=apellidos]').val(r.apellidos);
                                self.$content.find('input[name=celular]').val(r.celular_numero);
                                self.$content.find('input[name=correo_electronico]').val(r.correo_electronico);
                                hide_loadingOverlay(bt);
                                return;
                            }
                          }
                      });
                  });
                }
          });
        } 
        function abreCambiarCaja(bt,event){
          event.preventDefault();
          $.dialog({
                closeIcon: true,
                type: 'danger',
                typeAnimated: true,
                boxWidth: '40%',
                useBootstrap: false,
                content:function(){
                    var self=this;
                    formulario_detalle_mesa=self;
                    return $.post("pedidos/abrir_cambiar_caja",{"ref":$(bt).attr('data-id')})
                    .done(function(data){
                        var r=JSON.parse(data);
                        self.setTitle(r.title);
                        self.setContentAppend(r.template);
                        //hide_loadingOverlay(bt);
                    });
                },
                onContentReady:function(){
                    var self=this;
                    
                    this.$content.find('a[role=guardar]').click(function(){
                      var bt=this;
                      show_loadingOverlay(bt,[255,255,255,0,0.5]);
                      var form=self.$content.find('form');
                      $.post("pedidos/guardar_cambiar_caja",$(form).serializeArray())
                        .done(function(data){
                            hide_loadingOverlay(bt);
                            var r=JSON.parse(data);
                            if(!r.success){
                              $.alert({
                                  title: 'Mensaje de sistema',
                                  content: r.msg,
                              });
                              return;
                            }
                            
                            $.alert({
                                title: 'Mensaje de sistema',
                                content: '<i class="mt-2 text-success fa-solid fa-check fa-2x"></i>&nbsp;'+r.msg,
                                buttons: {
                                    ok: function () {
                                      show_loadingOverlay($('body'),[255,255,255,0,0.5]);
                                      location.reload();
                                    }
                                }
                            });
                            
                            
                        });
                    });
                    this.$content.find('a[role=cancel]').click(function(){
                        self.close();
                    });
                }
          });
        }
        function abreReabrir(bt,event){
          event.preventDefault();
          $.confirm({
                title: 'Confirme:',
                content: '<i class="text-danger mt-2 fas fa-question-circle fa-2x"></i>&nbsp;¿Realmente desea <b>reabrir</b> el pedido <b class="text-danger">'+$(bt).attr('data-id')+'</b>?',
                buttons: {
                    si: {
                        text: 'Si',
                        btnClass: 'btn-danger',
                        keys: ['enter'],
                        action: function(){
                          $.post("pedidos/reabrir",{"ref":$(bt).attr('data-id')})
                              .done(function(data){
                                    
                                    var r=JSON.parse(data);
                                    if(!r.success){
                                      hide_loadingOverlay(bt);
                                      $.alert({
                                          title: 'Mensaje de sistema',
                                          content: '<i class="mt-2 text-danger fa-solid fa-circle-exclamation fa-2x"></i>&nbsp;'+r.msg,
                                          buttons: {
                                            ok: function () {
                                              show_loadingOverlay($('body'),[255,255,255,0,0.5]);
                                              location.reload();
                                            }
                                        }
                                      });
                                      
                                      return;
                                    }
                                    hide_loadingOverlay(bt);
                                    $.alert({
                                        title: 'Mensaje de sistema',
                                        content: '<i class="mt-2 text-success fa-solid fa-check fa-2x"></i>&nbsp;'+r.msg,
                                        buttons: {
                                            ok: function () {
                                              show_loadingOverlay($('body'),[255,255,255,0,0.5]);
                                              location.reload();
                                            }
                                        }
                                    });
                              });
                        }
                    },
                    no: {
                      text: 'No',
                      btnClass: 'btn-light', 
                      keys: ['esc'],
                      action: function(){
                      }
                  },
                }
            });
     
        }
        function abreCambiarMetodoDePago(bt,event){
          event.preventDefault();
          $.dialog({
                closeIcon: true,
                type: 'danger',
                typeAnimated: true,
                boxWidth: '40%',
                useBootstrap: false,
                content:function(){
                    var self=this;
                    formulario_detalle_mesa=self;
                    return $.post("pedidos/abrir_cambiar_metodo_de_pago",{"ref":$(bt).attr('data-id')})
                    .done(function(data){
                        var r=JSON.parse(data);
                        self.setTitle(r.title);
                        self.setContentAppend(r.template);
                        //hide_loadingOverlay(bt);
                    });
                },
                onContentReady:function(){
                    var self=this;
                    
                    this.$content.find('a[role=guardar]').click(function(){
                      var bt=this;
                      show_loadingOverlay(bt,[255,255,255,0,0.5]);
                      var form=self.$content.find('form');
                      $.post("pedidos/guardar_cambiar_metodo_de_pago",$(form).serializeArray())
                        .done(function(data){
                            
                            var r=JSON.parse(data);
                            if(!r.success){
                              $.alert({
                                  title: 'Mensaje de sistema',
                                  content: r.msg,
                              });
                              hide_loadingOverlay(bt);
                              return;
                            }
                            hide_loadingOverlay(bt);
                            $.alert({
                                title: 'Mensaje de sistema',
                                content: '<i class="mt-2 text-success fa-solid fa-check fa-2x"></i>&nbsp;'+r.msg,
                                buttons: {
                                    ok: function () {
                                      show_loadingOverlay($('body'),[255,255,255,0,0.5]);
                                      location.reload();
                                    }
                                }
                            });
                            
                            
                        });
                    });
                    this.$content.find('a[role=cancel]').click(function(){
                        self.close();
                    });
                }
          });
        }
        function capitalizeFirstLetter(string) {
          return string.charAt(0).toUpperCase() + string.slice(1);
        }
        
        function cambiaNumeroDeRegistrosPorPagina(sel){
           var ref=$(sel).attr('data-ref');
           var v_modalidad="";
            var array=[];
            $('.form-check').find('input').each(function(){
                if($(this).is(":checked")){
                  array.push($(this).val());
                }
            });
            for(var i=0;i<array.length;i++){
              if((i+1)==array.length){
                v_modalidad+=array[i];
              }else{
                v_modalidad+=array[i]+'_';
              }
            }
           location.href = ref+'?desde='+$('input[name=date_from]').val()+'&hasta='+$('input[name=date_to]').val()+'&modalidades='+v_modalidad+'&rows='+$(sel).val();
        }
        function agregarModalidad(ck,event){
          var ref="<?php echo base_url().'dashboard_ventas';?>";
          var v_modalidad="";
          var array=[];
          $('.form-check').find('input').each(function(){
              if($(this).is(":checked")){
                array.push($(this).val());
              }
          });
          for(var i=0;i<array.length;i++){
            if((i+1)==array.length){
              v_modalidad+=array[i];
            }else{
              v_modalidad+=array[i]+'_';
            }
          }
          
          location.href = ref+'?desde='+$('input[name=date_from]').val()+'&hasta='+$('input[name=date_to]').val()+'&modalidades='+v_modalidad+'&rows='+$("#rows").val();
        }
        
    </script>
    <style>
      .table {
          table-layout: fixed;
      }
    </style>