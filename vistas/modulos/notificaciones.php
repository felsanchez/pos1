<div class="content-wrapper">

  <section class="content-header">

    <h1>
      Notificaciones
      <small>Panel de Control</small>
    </h1>

    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Notificaciones</li>
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
        <button class="btn btn-success" id="btnMarcarTodasLeidas">
          <i class="fa fa-check"></i> Marcar todas como leídas
        </button>

        <button class="btn btn-danger" id="btnBorrarSeleccionadas" style="display:none;">
          <i class="fa fa-trash"></i> Borrar seleccionadas
        </button>

      </div>

      <div class="box-body">

        <?php

        $notificaciones = ControladorNotificaciones::ctrObtenerNotificaciones();

        if($notificaciones && count($notificaciones) > 0){

          echo '<div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>

                      <th style="width: 30px">
                          <input type="checkbox" id="checkTodos">
                        </th>

                        <th style="width: 50px"></th>
                        <th>Tipo</th>
                        <th>Título</th>
                        <th>Mensaje</th>
                        <th>Fecha</th>
                        <th style="width: 100px">Acciones</th>
                      </tr>
                    </thead>
                    <tbody>';

          foreach($notificaciones as $notif){

            // Determinar icono y color según tipo
            $icono = "fa-info-circle";
            $color = "text-blue";
            $tipoTexto = "Información";

            if($notif["tipo"] == "stock_agotado"){
              $icono = "fa-times-circle";
              $color = "text-red";
              $tipoTexto = "Stock Agotado";
            } else if($notif["tipo"] == "stock_bajo"){
              $icono = "fa-exclamation-triangle";
              $color = "text-yellow";
              $tipoTexto = "Stock Bajo";
            }
            else if($notif["tipo"] == "actividad_proxima"){
              $icono = "fa-calendar";
              $color = "text-blue";
              $tipoTexto = "Actividad Próxima";
            } else if($notif["tipo"] == "gasto_proximo"){
              $icono = "fa-money";
              $color = "text-orange";
              $tipoTexto = "Gasto Próximo";
            } else if($notif["tipo"] == "orden_agente_ia"){
              $icono = "fa-magic";
              $color = "text-green";
              $tipoTexto = "Orden Agente IA";
            }

            // Determinar estilo de fila según si está leída
            $estiloFila = $notif["leida"] == 0 ? 'style="background-color: #f9f9f9; font-weight: bold;"' : '';

            echo '<tr '.$estiloFila.'>

                    <td>
                      <input type="checkbox" class="checkNotificacion" value="'.$notif["id"].'">
                    </td>

                    <td><i class="fa '.$icono.' '.$color.' fa-2x"></i></td>
                    <td>'.$tipoTexto.'</td>
                    <td>'.$notif["titulo"].'</td>
                    <td>'.$notif["mensaje"].'</td>
                    <td>'.date("d/m/Y H:i", strtotime($notif["fecha"])).'</td>
                    <td>';

            if($notif["leida"] == 0){
              echo '<button class="btn btn-xs btn-primary btnMarcarLeida" data-id="'.$notif["id"].'">
                      <i class="fa fa-check"></i>
                    </button> ';
            }

            echo '<button class="btn btn-xs btn-danger btnEliminarNotificacion" data-id="'.$notif["id"].'">
                    <i class="fa fa-trash"></i>
                  </button>
                </td>
              </tr>';
          }

          echo '</tbody></table></div>';

        } else {

          echo '<div class="callout callout-success">
                  <h4><i class="icon fa fa-check"></i> No hay notificaciones!</h4>
                  <p>No tienes notificaciones en este momento.</p>
                </div>';

        }

        ?>

      </div>

    </div>

  </section>

</div>

<script>
$(document).ready(function(){

  // Marcar una notificación como leída
  $(document).on("click", ".btnMarcarLeida", function(){

    var idNotificacion = $(this).attr("data-id");

    var datos = new FormData();
    datos.append("idNotificacion", idNotificacion);

    $.ajax({
      url: "ajax/notificaciones.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      success: function(respuesta){
        if(respuesta == "ok"){
          window.location.reload();
        }
      }
    });

  });

  // Marcar todas como leídas
  $("#btnMarcarTodasLeidas").click(function(){

    var datos = new FormData();
    datos.append("marcarTodasLeidas", 1);

    $.ajax({
      url: "ajax/notificaciones.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      success: function(respuesta){
        if(respuesta == "ok"){
          window.location.reload();
        }
      }
    });

  });

  // Eliminar notificación
  $(document).on("click", ".btnEliminarNotificacion", function(){

    var idNotificacion = $(this).attr("data-id");

    swal({
      title: '¿Está seguro de eliminar esta notificación?',
      text: "Esta acción no se puede revertir",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Sí, eliminar'
    }).then(function(result){
      if(result.value){

        var datos = new FormData();
        datos.append("idEliminarNotificacion", idNotificacion);

        $.ajax({
          url: "ajax/notificaciones.ajax.php",
          method: "POST",
          data: datos,
          cache: false,
          contentType: false,
          processData: false,
          success: function(respuesta){
            if(respuesta == "ok"){
              swal({
                type: "success",
                title: "Notificación eliminada correctamente",
                showConfirmButton: true,
                confirmButtonText: "Cerrar"
              }).then(function(result){
                if(result.value){
                  window.location = "notificaciones";
                }
              });
            }
          }
        });
      }
    });

  });


  // Seleccionar/deseleccionar todas las notificaciones
  $("#checkTodos").change(function(){
    $(".checkNotificacion").prop('checked', $(this).prop('checked'));
    mostrarBotonBorrar();
  });

  // Mostrar u ocultar el botón "Borrar seleccionadas"
  $(document).on("change", ".checkNotificacion", function(){
    mostrarBotonBorrar();
  });

  function mostrarBotonBorrar(){
    var checkedCount = $(".checkNotificacion:checked").length;
    if(checkedCount > 0){
      $("#btnBorrarSeleccionadas").show();
    } else {
      $("#btnBorrarSeleccionadas").hide();
    }
  }

  // Borrar notificaciones seleccionadas
  $("#btnBorrarSeleccionadas").click(function(){ 

    var idsSeleccionados = [];
    $(".checkNotificacion:checked").each(function(){
      idsSeleccionados.push($(this).val());
    });

    if(idsSeleccionados.length == 0){
      return;
    } 

    swal({
      title: '¿Está seguro de eliminar las notificaciones seleccionadas?',
      text: "Se eliminarán " + idsSeleccionados.length + " notificación(es)",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Sí, eliminar'
    }).then(function(result){
      if(result.value){ 

        var datos = new FormData();
        datos.append("idsEliminarNotificaciones", JSON.stringify(idsSeleccionados)); 

        $.ajax({
          url: "ajax/notificaciones.ajax.php",
          method: "POST",
          data: datos,
          cache: false,
          contentType: false,
          processData: false,
          success: function(respuesta){
            if(respuesta == "ok"){
              swal({
                type: "success",
                title: "Notificaciones eliminadas correctamente",
                showConfirmButton: true,
                confirmButtonText: "Cerrar"
              }).then(function(result){
                if(result.value){
                  window.location = "notificaciones";
                }
              });
            }
          }
        });
      }
    });

  });


});
</script>