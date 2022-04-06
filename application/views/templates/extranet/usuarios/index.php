<section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="d-flex justify-content-between">
                  <div class="col-4">
                      <div class="form-group">
                          <label>Número de registros por página</label>
                          <select onchange="cambiaNumeroDeRegistrosPorPagina(this)" data-ref="<?php echo base_url().'dashboard_usuarios';?>" class="form-control">
                              <?php
                                $items=[10,25,50,100];
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
                  <div>
                    <a class="btn btn-primary btn-lg" onclick="abreNuevaUsuario(this,event);"><i class="fas fa-plus"></i>&nbsp;Nuevo</a>
                  </div>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example2" class="table table table-striped table-bordered table-hover">
                  <thead>
                  <tr>
                    <th class="text-center" style="width:7%;">#</th>
                    <th class="text-center">nombre</th>
                    <th class="text-center">clave_acceso</th>
                    <th class="text-center" style="width:30%;">informacion personal</th>
                    <th class="text-center">rol</th>
                    <th class="text-center" style="width:20%">Acciones</th>
                  </tr>
                  </thead>
                  <tbody>
                      <?php
                        foreach($usuarios as $index=>$usuario){
                            echo '<tr>';
                            echo '<td class="text-center">'.((($pagina_seleccionada-1)*$num_filas_por_pagina)+$index+1).'</td>';
                            echo '<td class="text-center">'.$usuario["nombre"].'</td>';
                            echo '<td class="text-truncate text-center">'.$usuario["clave_acceso"].'</td>';
                            echo '<td class="text-truncate text-left">'.$usuario["persona_apellidos"].' '.$usuario["persona_nombres"].'</td>';
                            echo '<td class="text-truncate text-center">'.$usuario["rol_denominacion"].'</td>';
                            echo '<td class="text-center">';
                            echo '        <a class="btn btn-xs btn-success" data-id="'.$usuario["id"].'" onclick="abreEditarClaveUsuario(this,event)"><i class="fas fa-pencil-alt"></i>&nbsp;Contraseña</a>&nbsp;';
                                    if($usuario["tiene_permiso"]==1){
                                      echo '<a class="btn btn-xs btn-primary" data-id="'.$usuario["id"].'" onclick="abreEditarPermisosUsuario(this,event)"><i class="fas fa-pencil-alt"></i>&nbsp;Permisos</a>&nbsp;';
                                    }                   
                            echo '        <a class="btn btn-light" data-id="'.$usuario["id"].'" onclick="abreEliminarUsuario(this,event)"><i class="fas fa-trash-alt"></i></a>';
                            echo '   </td>';
                            echo '</tr>';
                        }
                      ?>
                  </tbody>
                </table>
                <br>
                <div class="d-flex justify-content-center">
                      <?php
                          $D=$total_usuarios_sin_filtro;
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
                                  echo '<li class="page-item active"><a class="page-link" href="'.base_url().'dashboard_usuarios?rows='.$d.'&pag='.($i+1).'">'.($i+1).'</a></li>';
                                }else{
                                  echo '<li class="page-item"><a class="page-link" href="'.base_url().'dashboard_usuarios?rows='.$d.'&pag='.($i+1).'">'.($i+1).'</a></li>';
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
        filesUpload;
        function abreNuevaUsuario(bt,event){
          
            $.dialog({
                closeIcon: true,
                type: 'danger',
                typeAnimated: true,
                boxWidth: '50%',
                useBootstrap: false,
                content:function(){
                    var self=this;
                    confirm=self;
                    filesUpload=null;
                    return $.post("usuarios/abrir_nuevo")
                    .done(function(data){
                        
                        var r=JSON.parse(data);
                        self.setTitle(r.title);
                        self.setContentAppend(r.template);
                        //hide_loadingOverlay(bt);
                    });
                },
                onContentReady:function(){
                    var self=this;
                    this.$content.find('select[name=rol]').on('change',function(){
                      var sel=this;
                    
                      $.post('usuarios/permisos',{"rol_id":$(sel).val()}
                        ).done(function(data){
                              
                              var r=JSON.parse(data);
                              self.$content.find('div[role=permisos]').html(r.template);
                        });
                    });
                    this.$content.find('button[role=busqueda_por_dni]').click(function(){
                        var bt=this;
                        show_loadingOverlay(bt,[255,255,255,0,0.5]);
                        var documento_numero=self.$content.find('input[name=documento_numero]').val();
                        var documento_tipo=self.$content.find('select[name=documento_tipo]').val();
                        $.post("personas/busqueda_por_documento",{"documento_numero":documento_numero,"documento_tipo":documento_tipo}
                        ).done(function(data){
                          
                            var r=JSON.parse(data);
                            self.$content.find('p[role=error_documento_numero]').html("");
                            if(!r.success){
                              switch(r.msg_id){
                                case 1:
                                  self.$content.find('input[name=nombres]').focus();
                                  self.$content.find('input[name=nombres]').val("");
                                  self.$content.find('input[name=apellidos]').val("");
                                  self.$content.find('input[name=nombres]').removeAttr('readonly');
                                  self.$content.find('input[name=apellidos]').removeAttr('readonly');
                                 
                                  hide_loadingOverlay(bt);
                                  return;
                                  break;
                                case 2:
                                  self.$content.find('p[role=error_documento_numero]').html(r.msg);
                                  hide_loadingOverlay(bt);
                                  return;
                                  break;
                                  
                              }
                              
                            }

                            self.$content.find('input[name=nombres]').val(r.nombres);
                            self.$content.find('input[name=apellidos]').val(r.apellidos);
                            
                            if(r.return=="from_dni"){
                              self.$content.find('input[name=nombres]').attr('readonly',true);
                              self.$content.find('input[name=apellidos]').attr('readonly',true);
                        
                            }else{
                              self.$content.find('input[name=nombres]').removeAttr('readonly');
                              self.$content.find('input[name=apellidos]').removeAttr('readonly');
                            } 
                            
                            hide_loadingOverlay(bt);
                            
                        });
                    });
                    this.$content.find('select[name=documento_tipo]').on('change',function(){
                        var select=this;
                        if($(select).val()==1){
                          self.$content.find('input[name=nombres]').attr('readonly', true);
                          self.$content.find('input[name=apellidos]').attr('readonly', true);
                          self.$content.find('input[name=documento_numero]').parent().find('.input-group-append').removeClass('d-none');
                            self.$content.find('input[name=nombres]').val("");
                            self.$content.find('input[name=apellidos]').val("");
                            
                        }else{
                          if(self.$content.find('input[name=documento_numero]').parent().hasClass('input-group')){
                            self.$content.find('input[name=documento_numero]').parent().find('.input-group-append').addClass('d-none');
                            self.$content.find('input[name=documento_numero]').val("");
                            self.$content.find('input[name=nombres]').val("");
                            self.$content.find('input[name=apellidos]').val("");
                            self.$content.find('input[name=nombres]').removeAttr('readonly');
                            self.$content.find('input[name=apellidos]').removeAttr('readonly');
                          }
                        }
                    });
                    this.$content.find('a[role=guardar]').click(function(){ 
                            var bt=this;
                            $(bt).html("Guardando registro...");
                            show_loadingOverlay(bt,[255,255,255,0,0.5]);
                            var form=$("#formulario_nuevo_usuario");                           
                            $.post('usuarios/guardar_nuevo',$(form).serializeArray()
                              ).done(function(data){
                                
                                  var r=JSON.parse(data);
                                  location.reload();
                              });    
                    });
                    this.$content.find('a[role=cancel]').click(function(){
                        self.close();
                    });
                }
            });
        }
        function abreEditarClaveUsuario(bt,event){
          $.dialog({
              closeIcon: true,
              type: 'danger',
              typeAnimated: true,
              boxWidth: '60%',
              useBootstrap: false,
              content:function(){
                  var self=this;
                  confirm=self;
                  return $.post("usuarios/abrir_ver",{"id":$(bt).attr("data-id")})
                  .done(function(data){
                      var r=JSON.parse(data);
                      self.setTitle(r.title);
                      self.setContentAppend(r.template);
                      //hide_loadingOverlay(bt);
                  });
              }
            });
        }
        function abreEditarPermisosUsuario(bt,event){
            $.dialog({
              closeIcon: true,
              type: 'danger',
              typeAnimated: true,
              boxWidth: '40%',
              useBootstrap: false,
              content:function(){
                  var self=this;
                  confirm=self;
                  return $.post("usuarios/abrir_editar_permisos",{"id":$(bt).attr("data-id")})
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
                    var form=self.$content.find('form');
                    var bt=this;
                    $(bt).html("Actualizando registro...");
                    show_loadingOverlay(bt,[255,255,255,0,0.5]);
                    $.post('usuarios/guardar_editar_permisos',$(form).serializeArray()
                              ).done(function(data){
                                  var r=JSON.parse(data);
                                  alert(r.msg);
                                  location.reload();
                              }); 
                });
                this.$content.find('a[role=cancel]').click(function(){
                    self.close();
                });
              }
            });
        }
        function abreEliminarUsuario(bt,event){
            $.confirm({
                title: '<b>Confirme lo siguiente:</b>',
                content:'Realmente desea eliminar la usuario:<br> <b>'+$(bt).attr('data-denominacion')+'</b>',
                buttons:{
                  si: {
                      text: 'Si',
                      btnClass: 'btn-danger', 
                      keys: ['enter'],
                      action: function(){
                        var ref=firebase.storage().ref();
                        const task=ref.child($(bt).attr('data-ruta')).delete();
                        task
                        .then(function() {
                          $.post('usuarios/eliminar',{"id":$(bt).attr('data-id')}
                                ).done(function(data){
                                  var r=JSON.parse(data);
                                  location.reload();
                                });
                        }).catch(function(error) {
                          $.post('usuarios/eliminar',{"id":$(bt).attr('data-id')}
                                ).done(function(data){
                                  var r=JSON.parse(data);
                                  location.reload();
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
                },
            });
        }
        function cambiaNumeroDeRegistrosPorPagina(sel){
           var ref=$(sel).attr('data-ref');
           location.href = ref+'?rows='+$(sel).val();
        }
    </script>
    <style>
      .table {
          table-layout: fixed;
      }
    </style>