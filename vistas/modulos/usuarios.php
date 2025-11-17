<!-- Solo muestra 2 campos en movil en la Tabla 1-->
<style>
@media (max-width: 767px) {
  /* Ocultar TODAS las columnas primero */
  .tablas td,
  .tablas th {
      display: none;
  }
  
  /* Mostrar SOLO la columna 2 y columna 6 */
  .tablas td:nth-child(3),
  .tablas td:nth-child(6),
  .tablas th:nth-child(3),
  .tablas th:nth-child(6) {
      display: table-cell;
  }
}
</style>

<style>
/* Solo muestra el botón en móvil */
.solo-movil {
  display: none;
}
@media (max-width: 767px) {
  .solo-movil {
    display: inline-block !important;
  }
}
</style>

<!--Agregar espacio entre los btones en móvil-->
<style>
@media (max-width: 767px) {
  .solo-movil {
    margin-left: 3px !important;
  }
}
</style>
  
  
  
  <div class="content-wrapper">
    <section class="content-header">

      <h1>
        Administrar usuarios
      </h1>

      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li class="active">Administrar usuarios</li>
      </ol>

    </section>

    <section class="content">

      <div class="box">

        <div class="box-header with-border">

          <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarUsuario">
            
             Agregar usuario

          </button>

        </div>


        <div class="box-body table-responsive">

          <table class="table table-bordered table-striped tablas">
              
            <thead>
              <tr>
                <th style="width: 10px">#</th>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Foto</th>
                <th>Perfil</th>
                <th>Estado</th>
                <th>Ultimo login</th>
                <th>Acciones</th>
              </tr>             
            </thead>

              <tbody>

                <?php

                  $item = null;
                  $valor = null;

                  $usuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);


                  foreach ($usuarios as $key => $value) {
                    echo '<tr>
                        <td>'.($key+1).'</td>

                        <td>'.$value["nombre"].'</td>


     
                        <td>'.$value["usuario"].                       

                        '<!-- Btn Ver mas o editar en Movil -->'.

                        '<button class="btn btn-danger btnEliminarUsuario solo-movil btn-xs" style="float: right;" idUsuario="'.$value["id"].'" fotoUsuario="'.$value["foto"].'" usuario="'.$value["usuario"].'"><i class="fa fa-times"></i></button>
                        
                        <button class="btn btn-warning btnEditarUsuario solo-movil btn-xs" style="float: right;" idUsuario="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarUsuario"><i class="fa fa-pencil"></i></button>'    
                    
                      .'</td>';


                    /*if($value["foto"] != ""){ 
                      echo '<td><img src="'.$value["foto"].'" class="img-thumbnail" width="40px"></td>';
                    }
                    else{
                      echo '<td><img src="vistas/img/usuarios/default/anonymous.png" class="img-thumbnail" width="40px"></td>';
                    } */

                     if($value["foto"] != ""){
                          echo '<td><img src="'.$value["foto"].'" class="img-thumbnail img-usuario-clickeable" width="40px" style="cursor: pointer;" data-foto="'.$value["foto"].'" data-idusuario="'.$value["id"].'" data-usuario="'.$value["usuario"].'"></td>';
                      }
                      else{
                          echo '<td><img src="vistas/img/usuarios/default/anonymous.png" class="img-thumbnail img-usuario-clickeable" width="40px" style="cursor: pointer;" data-foto="vistas/img/usuarios/default/anonymous.png" data-idusuario="'.$value["id"].'" data-usuario="'.$value["usuario"].'"></td>';
                      }
                    
                    
                    echo '<td>'.$value["perfil"].'</td>';

                    if($value["estado"] != 0){

                        echo '<td><button class="btn btn-success btn-xs btnActivar" idUsuario="'.$value["id"].'" estadoUsuario="0">Activado</button></td>';                     
                    }
                    else{

                      echo '<td><button class="btn btn-danger btn-xs btnActivar" idUsuario="'.$value["id"].'" estadoUsuario="1">Desactivado</button></td>';
                    }


                    echo '<td>'.$value["ultimo_login"].'</td>

                    <td>
                      <div class="btn-group">

                        <button class="btn btn-warning btnEditarUsuario" idUsuario="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarUsuario"><i class="fa fa-pencil"></i></button>
                        
                        <button class="btn btn-danger btnEliminarUsuario" idUsuario="'.$value["id"].'" fotoUsuario="'.$value["foto"].'" usuario="'.$value["usuario"].'"><i class="fa fa-times"></i></button>

                      </div>
                    </td>

                  </tr>';
              }

                ?>


              </tbody>

          </table>

        </div>

      </div>

    </section>

  </div>
  


<!-- Modal para ampliar/editar imagen de usuario -->
<div class="modal fade" id="modalAmpliarImagenUsuario" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Foto de Usuario</h4>
      </div>
      <div class="modal-body text-center">
        <img id="imagenUsuarioAmpliada" src="" class="img-responsive" style="max-width: 100%; margin: 0 auto; margin-bottom: 20px;">        
        <hr>        
        <div class="form-group">
          <label>Cambiar Foto del Usuario</label>
          <input type="file" class="form-control nuevaImagenUsuario" accept="image/*">
          <p class="help-block">Peso máximo de la imagen 2MB</p>
        </div>        
        <input type="hidden" id="idUsuarioImagen">
        <input type="hidden" id="usuarioNombre">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary btnGuardarImagenUsuario">Guardar Imagen</button>
      </div>
    </div>
  </div>
</div>


<!--=====================================
MODAL AGREGAR USUARIO
======================================-->
  
<!-- Modal -->
<div id="modalAgregarUsuario" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

      <!--=====================================
      CABEZA DEL MODAL
      ======================================-->

      <div class="modal-header" style="background:#3c8dbc; color: white">

        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agregar usuario</h4>

      </div>

      <!--=====================================
      CUERPO DEL MODAL
      ======================================-->

      <div class="modal-body">
        
        <div class="box-body">

            <!-- entrada para nombre -->
            
          <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-user"></i></span>

              <input type="text" class="form-control input-lg" name="nuevoNombre" placeholder="Ingresar nombre *" required>

             </div>

           </div>

           <!-- entrada para usuario -->
            
          <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-key"></i></span>

              <input type="text" class="form-control input-lg" name="nuevoUsuario" placeholder="Ingresar usuario (Sin espacios) *" id="nuevoUsuario" required>

             </div>

           </div>

            <!-- entrada para contraseña -->
            
          <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-lock"></i></span>

              <input type="password" class="form-control input-lg" name="nuevoPassword" placeholder="Ingresar contraseña *" required>

             </div>

           </div>

           <!-- entrada para seleccionar perfil -->

              <div class="form-group">
          
                <div class="input-group">
            
                   <span class="input-group-addon"><i class="fa fa-users"></i></span>

                        <select class="form-control input-lg" name="nuevoPerfil" required>
              
                          <option value="">Seleccionar perfil</option>
                          <option value="Administrador">Administrador</option>
                          <option value="Especial">Especial</option>
                          <option value="Vendedor">Vendedor</option>
                          <option value="Visitante">Visitante</option>

                         </select>

                  </div>

                 </div>

                 <!-- entrada para foto -->

                  <div class="form-group">
                    
                    <div class="panel">SUBIR FOTO</div>

                    <input type="file" class="nuevaFoto" name="nuevaFoto">

                    <p class="help-block">Peso máximo de la foto 2MB</p>

                    <img src="vistas/img/usuarios/default/anonymous.png" class="img-thumbnail previsualizar" width="100px">

                  </div>

         </div>  

       </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar usuario</button>

        </div>

          <?php

            $crearUsuario = new ControladorUsuarios();
            $crearUsuario -> ctrCrearUsuario();

          ?>

     </form>

    </div>


  </div>

</div>




<!--==========================================================================================================
MODAL EDITAR USUARIO
===========================================================================================================-->
  
<!-- Modal -->
<div id="modalEditarUsuario" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

      <!--=====================================
      CABEZA DEL MODAL
      ======================================-->

      <div class="modal-header" style="background:#3c8dbc; color: white">

        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Editar usuario</h4>

      </div>

      <!--=====================================
      CUERPO DEL MODAL
      ======================================-->

      <div class="modal-body">
        
        <div class="box-body">

            <!-- entrada para nombre -->
            
          <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-user"></i></span>

              <input type="text" class="form-control input-lg" id="editarNombre" name="editarNombre" value="" required>

             </div>

           </div>

           <!-- entrada para usuario -->
            
          <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-key"></i></span>

              <input type="text" class="form-control input-lg" id="editarUsuario" name="editarUsuario" value="" readonly>

             </div>

           </div>

            <!-- entrada para contraseña -->
            
          <!--<div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-lock"></i></span>

              <input type="password" class="form-control input-lg" name="editarPassword" placeholder="Escriba la nueva contraseña">

              <input type="hidden" id="passwordActual" name="passwordActual">

             </div>

           </div>  -->

           <!-- entrada para seleccionar perfil -->

              <div class="form-group">
          
                <div class="input-group">
            
                   <span class="input-group-addon"><i class="fa fa-users"></i></span>

                        <select class="form-control input-lg" name="editarPerfil">
              
                          <option value="" id="editarPerfil"></option>

                          <option value="Administrador">Administrador</option>
                          <option value="Especial">Especial</option>
                          <option value="Vendedor">Vendedor</option>

                         </select>

                  </div>

                 </div>

                 <!-- entrada para foto -->
                  <!--<div class="form-group">                    
                    <div class="panel">SUBIR FOTO</div>
                    <input type="file" class="nuevaFoto" name="editarFoto">
                    <p class="help-block">Peso máximo de la foto 2MB</p>
                    <img src="vistas/img/usuarios/default/anonymous.png" class="img-thumbnail previsualizar" width="100px">
                    <input type="hidden" name="fotoActual" id="fotoActual">
                  </div> -->

                  <div class="form-group">
                    <div class="panel">SUBIR FOTO</div>
                    
                    <input type="file" class="nuevaFoto" name="editarFoto">
                    
                    <p class="help-block">Peso máximo de la foto 2MB</p>
                    
                    <img src="vistas/img/usuarios/default/anonymous.png" class="img-thumbnail previsualizar img-ampliar-usuario" width="100px" style="cursor: pointer;">
                    
                    <input type="hidden" name="fotoActual" id="fotoActual">
                </div>

         </div>  

       </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Modificar usuario</button>

        </div>

         <?php

            $editarUsuario = new ControladorUsuarios();
            $editarUsuario -> ctrEditarUsuario();

          ?>

     </form>

    </div>


  </div>

</div>


<!-- Modal para ampliar imagen de usuario, desde editar usuario-->
<div class="modal fade" id="modalAmpliarFotoUsuario" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Foto de Usuario</h4>
      </div>
      <div class="modal-body text-center">
        <img id="fotoUsuarioAmpliada" src="" class="img-responsive" style="max-width: 100%; margin: 0 auto;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<?php

  $borrarUsuario = new ControladorUsuarios();
  $borrarUsuario -> ctrBorrarUsuario();

 ?>


<!-- Ampliar foto de usuario, desde el modal editar usuario -->
<script>
$(document).on("click", ".img-ampliar-usuario", function(){
    var rutaImagen = $(this).attr("src");
    $("#fotoUsuarioAmpliada").attr("src", rutaImagen);
    $("#modalAmpliarFotoUsuario").modal("show");
});
// Cuando se sube una nueva foto, actualizar la imagen
$(".nuevaFoto").change(function(){
    var imagen = this.files[0];
    
    if(imagen){
        if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){
            $(".nuevaFoto").val("");
            swal({
                title: "Error al subir la imagen",
                text: "¡La imagen debe estar en formato JPG o PNG!",
                type: "error",
                confirmButtonText: "¡Cerrar!"
            });
        }else if(imagen["size"] > 2000000){
            $(".nuevaFoto").val("");
            swal({
                title: "Error al subir la imagen",
                text: "¡La imagen no debe pesar más de 2MB!",
                type: "error",
                confirmButtonText: "¡Cerrar!"
            });
        }else{
            var datosImagen = new FileReader;
            datosImagen.readAsDataURL(imagen);
            
            $(datosImagen).on("load", function(event){
                var rutaImagen = event.target.result;
                $(".previsualizar").attr("src", rutaImagen);
            });
        }
    }
});
</script>



<!--=============================================
AMPLIAR Y EDITAR IMAGEN DE USUARIO DESDE LA TABLA
=============================================-->
<script>
    // Ampliar imagen de usuario al hacer clic
    $(document).on("click", ".img-usuario-clickeable", function(){
        var rutaImagen = $(this).attr("data-foto");
        var idUsuario = $(this).attr("data-idusuario");
        var usuario = $(this).attr("data-usuario");
        
        console.log("ID Usuario:", idUsuario);
        console.log("Usuario:", usuario);
        console.log("Ruta Imagen:", rutaImagen);
        
        $("#imagenUsuarioAmpliada").attr("src", rutaImagen);
        $("#idUsuarioImagen").val(idUsuario);
        $("#usuarioNombre").val(usuario);
        $(".nuevaImagenUsuario").val("");
        $("#modalAmpliarImagenUsuario").modal("show");
    });

    // Previsualizar nueva imagen cuando se selecciona
    $(".nuevaImagenUsuario").change(function(){
        var imagen = this.files[0];
        
        if(imagen){
            if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){
                $(".nuevaImagenUsuario").val("");
                swal({
                    title: "Error al subir la imagen",
                    text: "¡La imagen debe estar en formato JPG o PNG!",
                    type: "error",
                    confirmButtonText: "¡Cerrar!"
                });
            }else if(imagen["size"] > 2000000){
                $(".nuevaImagenUsuario").val("");
                swal({
                    title: "Error al subir la imagen",
                    text: "¡La imagen no debe pesar más de 2MB!",
                    type: "error",
                    confirmButtonText: "¡Cerrar!"
                });
            }else{
                var datosImagen = new FileReader;
                datosImagen.readAsDataURL(imagen);
                
                $(datosImagen).on("load", function(event){
                    var rutaImagen = event.target.result;
                    $("#imagenUsuarioAmpliada").attr("src", rutaImagen);
                });
            }
        }
    });

    // Guardar la nueva imagen del usuario
    $(document).on("click", ".btnGuardarImagenUsuario", function(){
        
        var idUsuario = $("#idUsuarioImagen").val();
        var usuario = $("#usuarioNombre").val();
        var imagen = $(".nuevaImagenUsuario")[0].files[0];
        
        console.log("ID al guardar:", idUsuario);
        console.log("Usuario al guardar:", usuario);
        console.log("Imagen al guardar:", imagen);
        
        if(!imagen){
            swal({
                title: "Advertencia",
                text: "No has seleccionado ninguna imagen",
                type: "warning",
                confirmButtonText: "¡Cerrar!"
            });
            return;
        }
        
        if(!idUsuario || !usuario){
            swal({
                title: "Error",
                text: "No se pudo obtener el ID o nombre del usuario",
                type: "error",
                confirmButtonText: "¡Cerrar!"
            });
            return;
        }
        
        var datos = new FormData();
        datos.append("idUsuarioImagen", idUsuario);
        datos.append("usuarioNombre", usuario);
        datos.append("nuevaImagenUsuario", imagen);
        
        // Mostrar loading
        swal({
            title: 'Cargando...',
            allowOutsideClick: false,
            onBeforeOpen: () => {
                swal.showLoading()
            }
        });
        
        $.ajax({
            url: "ajax/usuarios.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(respuesta){
                console.log("Respuesta del servidor:", respuesta);
                
                if(respuesta == "ok"){
                    swal({
                        type: "success",
                        title: "¡La imagen ha sido actualizada correctamente!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if(result.value){
                            $("#modalAmpliarImagenUsuario").modal("hide");
                            window.location = "usuarios";
                        }
                    });
                }else{
                    swal({
                        type: "error",
                        title: "Error al actualizar la imagen",
                        text: JSON.stringify(respuesta),
                        confirmButtonText: "Cerrar"
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log("Error AJAX:", textStatus, errorThrown);
                console.log("Respuesta:", jqXHR.responseText);
                
                swal({
                    type: "error",
                    title: "Error en la petición",
                    text: "Por favor revisa la consola para más detalles",
                    confirmButtonText: "Cerrar"
                });
            }
        });
    });
</script>