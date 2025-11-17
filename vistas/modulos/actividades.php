<!-- Librería de estilos de Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<!-- Ruta actividades.css -->
<link rel="stylesheet" href="assets/css/actividades.css">

<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.css' rel='stylesheet' />

<!-- Estilos dinámicos para estados de actividades -->
<style>
<?php
$estadosParaEstilos = ControladorEstadosActividades::ctrMostrarEstadosActividades(null, null);
foreach($estadosParaEstilos as $estadoEstilo){
    $nombreLimpio = str_replace(" ", "-", strtolower($estadoEstilo["nombre"]));
    $color = $estadoEstilo["color"];

    // Estilos para el contenedor cerrado de Choices.js (select cuando NO está abierto)
    echo '.estado-act-'.$nombreLimpio.' .choices__inner,';
    echo '.estado-act-'.$nombreLimpio.'.choices .choices__inner {';
    echo '  background-color: '.$color.' !important;';
    echo '  border-color: '.$color.' !important;';
    echo '  color: #fff !important;';
    echo '}';
    echo "\n";

    // Estilos para el select normal (antes de que Choices.js lo transforme)
    echo 'select.estado-act-'.$nombreLimpio.' {';
    echo '  background-color: '.$color.' !important;';
    echo '  border-color: '.$color.' !important;';
    echo '  color: #fff !important;';
    echo '}';
    echo "\n";

    // Estilos para cada opción individual en el dropdown (basado SOLO en data-value, NO en clase del contenedor)
    echo '.choices__list--dropdown .choices__item--selectable[data-value="'.$estadoEstilo["nombre"].'"] {';
    echo '  background-color: '.$color.' !important;';
    echo '  color: #fff !important;';
    echo '  border: none !important;';
    echo '}';
    echo "\n";
    echo '.choices__list--dropdown .choices__item--selectable[data-value="'.$estadoEstilo["nombre"].'"]:hover {';
    echo '  background-color: '.adjustBrightnessActividad($color, -20).' !important;';
    echo '  color: #fff !important;';
    echo '}';
    echo "\n";
} 

// Función helper para ajustar brillo (hover más oscuro)
function adjustBrightnessActividad($hex, $steps) {
    $steps = max(-255, min(255, $steps));
    $hex = str_replace('#', '', $hex);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $r = max(0, min(255, $r + $steps));
    $g = max(0, min(255, $g + $steps));
    $b = max(0, min(255, $b + $steps));
    return '#'.str_pad(dechex($r), 2, '0', STR_PAD_LEFT).str_pad(dechex($g), 2, '0', STR_PAD_LEFT).str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}
?>
</style> 

<!-- Mapa de colores de estados para JavaScript -->
<script>
  window.estadosActividadesColores = {
    <?php
    foreach($estadosParaEstilos as $key => $estadoEstilo){
      $coma = ($key < count($estadosParaEstilos) - 1) ? ',' : '';
      echo '"'.strtolower($estadoEstilo["nombre"]).'": "'.$estadoEstilo["color"].'"'.$coma."\n";
    }
    ?>
  };
</script>


<!-- Centrar filtro -->
  <style>
    @media (max-width: 767px) {
    .filtro-estado-wrapper,
    .filtro-tipo-wrapper {
      float: none !important;        /* anula el pull-right */
      justify-content: center !important; /* centra con flex */
      text-align: center;            /* por si acaso */
      width: 100%;                   /* ocupa todo el ancho */
    }

    .filtro-estado-wrapper label,
    .filtro-tipo-wrapper label {
      margin-bottom: 5px;            /* pequeño espacio si se apila */
    }
  }
  </style>

<!-- Solo muestra 2 campos en movil en la Tabla 1-->
<style>
@media (max-width: 767px) {
  /* Ocultar TODAS las columnas primero */
  .tablas td,
  .tablas th {
      display: none;
  }
  
  /* Mostrar SOLO la columna 2 y columna 6 */
  .tablas td:nth-child(2),
  .tablas td:nth-child(6),
  .tablas th:nth-child(2),
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


<!-- Estilos para que el dropdown de Choices.js no se corte -->
<style>
/* Permitir que el dropdown se muestre fuera del contenedor de la tabla */
.table-responsive {
    overflow: visible !important;
}

/* Asegurar que el dropdown de Choices.js tenga z-index alto y se posicione correctamente */
.choices__list--dropdown {
    position: absolute !important;
    z-index: 9999 !important;
    max-height: none !important;
    overflow: visible !important;
} 

/* El scroll solo en el contenedor interno de Choices.js */
.choices__list--dropdown .choices__list {
    max-height: 250px !important;
    overflow-y: auto !important;
}
 
/* Asegurar que el contenedor box-body permita overflow visible */
.box-body {
    overflow: visible !important;
}

 /* Pero mantener scroll horizontal solo si es necesario en pantallas pequeñas */
@media (max-width: 991px) {
    .table-responsive {
        overflow-x: auto !important;
        overflow-y: visible !important;
    }
}
</style>


<div class="content-wrapper">
<section class="content-header">

    <?php
        $editarActividad = new ControladorActividades();
        $editarActividad -> ctrEditarActividad();
    ?>

      <h1>
        Administrar Actividades
      </h1>

      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li class="active">Administrar Actividades</li>
      </ol>

    </section>

    <section class="content">

        <div class="box">

            <div class="box-header with-border">

                <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarActividad">             
                    Agregar Actividad
                </button>

            </div>


            <!--Filtro Tipos-->
            <?php
                $filtroTipo = isset($_GET['filtroTipo']) ? $_GET['filtroTipo'] : '';  // Captura el valor del filtro tipo si existe
                // Aplica el filtro para obtener las actividades correctas
                $item = "tipo";
                $valor = $filtroTipo;
                $actividades = ControladorActividades::ctrMostrarActividades($item, $valor);
            ?>

           
            <div class="box-body table-responsive">


            <!-- Filtro tipo -->
            <div class="clearfix mb-2">
            <div class="pull-right filtro-tipo-wrapper d-flex align-items-center" style="gap: 8px;">
                <label for="filtroTipo" class="control-label mb-0">Filtra por TIPO:</label>
                <select id="filtroTipo" onchange="filterTableTipo()" class="form-control filtro-tipo">
                <option value="">Todos</option>

                <?php
                $tiposFiltro = ControladorTiposActividades::ctrMostrarTiposActividades(null, null);
                foreach($tiposFiltro as $tipoFiltro){
                    $selected = ($filtroTipo == $tipoFiltro["nombre"]) ? "selected" : "";
                    echo '<option value="'.$tipoFiltro["nombre"].'" '.$selected.'>'.ucfirst($tipoFiltro["nombre"]).'</option>';
                }
                ?>

                </select>
            </div>
            </div>
            <br>
            

            <!-- Filtro estado -->
            <div class="clearfix mb-2">
                <div class="pull-right filtro-estado-wrapper d-flex align-items-center" style="gap: 8px;">
                    <label for="filtroEstado" class="control-label mb-0">Filtra por ESTADO:</label>
                    <select id="filtroEstado" class="form-control filtro-estado">
                        <option value="">Todos</option>

                         <?php
                        $filtroEstado = isset($_GET['filtroEstado']) ? $_GET['filtroEstado'] : '';
                        $estadosFiltro = ControladorEstadosActividades::ctrMostrarEstadosActividades(null, null);
                        foreach($estadosFiltro as $estadoFiltroItem){
                            $selected = ($filtroEstado == $estadoFiltroItem["nombre"]) ? "selected" : "";
                            echo '<option value="'.$estadoFiltroItem["nombre"].'" '.$selected.'>'.ucfirst($estadoFiltroItem["nombre"]).'</option>';
                        }
                        ?>

                    </select>
                </div>
            </div>

            <br><br>

                <table class="table table-bordered table-striped tablas" style="width: 95%">
                    
                    <thead>
                    <tr>
                        <th style="width: 5px">#</th>
                        <th>Descripción</th>
                        <th>Tipo <button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#modalAgregarTipoActividad" title="Agregar nuevo tipo"><i class="fa fa-plus"></i></button></th>

                        <th>Responsable</th>
                        <th>Fecha</th>
                        <th>Estado <button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#modalAgregarEstadoActividad" title="Agregar nuevo estado"><i class="fa fa-plus"></i></button></th>
                        <th>Cliente</th>
                        <th>Observación</th>
                        <th>Acciones</th> 

                    </tr>             
                    </thead>
   
                    <tbody>

                        <?php
                        $item = null;
                        $valor = null;
                        $actividades = ControladorActividades::ctrMostrarActividades($item, $valor);
                        ?>

                            <?php 
                            foreach ($actividades as $key => $value):                       
                            ?>

                        <tr>
                            <td><?php echo $key + 1; ?></td>
                            <td><?php echo $value["descripcion"]; ?></td>
                            
                            <td>
                                <select class="form-control cambiarTipo" data-id="<?php echo $value["id"]; ?>">
                                    <?php
                                    $tiposActividades = ControladorTiposActividades::ctrMostrarTiposActividades(null, null);
                                    foreach($tiposActividades as $tipo){
                                        $selected = ($value["tipo"] == $tipo["nombre"]) ? "selected" : "";
                                        echo '<option value="'.$tipo["nombre"].'" '.$selected.'>'.ucfirst($tipo["nombre"]).'</option>';
                                    }
                                    ?>
                                </select>
                            </td>

                            <?php 
                            $itemUsuario = "id";
                            $valorUsuario = $value["id_user"];
                            $respuestaUsuario = ControladorUsuarios::ctrMostrarUsuarios($itemUsuario, $valorUsuario);
                            if ($respuestaUsuario) {
                                echo '<td>' . $respuestaUsuario["nombre"] . '</td>';
                            } else {
                                echo '<td>Sin asignar</td>'; // o lo que prefieras mostrar si no hay usuario
                            }
                            ?>

                            <td><?php echo $value["fecha"]; ?></td>

                            <td>
                            <?php
                            $estadoActual = $value["estado"] ?? "";
                            $estadoClass = "estado-act-" . str_replace(" ", "-", strtolower($estadoActual));
                            ?>

                            <select class="form-control cambiarEstado cambiarEstadoActividad <?php echo $estadoClass; ?>" data-id="<?php echo $value["id"]; ?>">
                                <?php
                                $estadosActividades = ControladorEstadosActividades::ctrMostrarEstadosActividades(null, null);
                                foreach($estadosActividades as $estado){
                                    $selected = ($value["estado"] == $estado["nombre"]) ? "selected" : "";
                                    echo '<option value="'.$estado["nombre"].'" '.$selected.'>'.ucfirst($estado["nombre"]).'</option>';
                                }
                                ?>
                            </select>

                                <!--BTN EDITAR Y ELIMINAR EN MOVIL-->
                                <button class="btn btn-danger btnEliminarActividad btn-xs solo-movil" style="float: right;" idActividad="<?php echo $value["id"]; ?>"><i class="fa fa-times"></i></button>

                                <button class="btn btn-warning btnEditarActividad btn-xs solo-movil" style="float: right;" data-id="<?php echo $actividad['id']; ?>" data-toggle="modal" data-target="#modalEditarActividad" idActividad="<?php echo $value["id"]; ?>"><i class="fa fa-pencil"></i></button>
                                
                            </td>


                            <?php 
                            $itemCliente = "id";
                            $valorCliente = $value["id_cliente"];
                            $respuestaCliente = ControladorClientes::ctrMostrarClientes($itemCliente, $valorCliente);
                            if ($respuestaCliente) {
                                echo '<td>' . $respuestaCliente["nombre"] . '</td>';
                            } else {
                                echo '<td>Sin cliente</td>'; // o como quieras mostrarlo
                            }
                            ?>

                            <td contenteditable="true" class="celda-observacion" data-id="<?= $value['id']; ?>">
                                <?= $value['observacion']; ?>
                            </td>

                            <td>
                            <div class="btn-group"> 

                                <!--<button class="btn btn-warning btnEditarActividad" 
                                    idActividad="<?php echo $value["id"]; ?>">
                                    <i class="fa fa-pencil"></i>
                                </button>-->
 
                                <button class="btn btn-warning btnEditarActividad" data-id="<?php echo $actividad['id']; ?>" data-toggle="modal" data-target="#modalEditarActividad" idActividad="<?php echo $value["id"]; ?>"><i class="fa fa-pencil"></i></button>
                                
                                <button class="btn btn-danger btnEliminarActividad" idActividad="<?php echo $value["id"]; ?>"><i class="fa fa-times"></i></button>
                            </div>
                            </td>
                        </tr>

                        <?php                      
                        endforeach; 
                        ?>

  
                    </tbody>
               
                </table>

            </div>

        </div>

        <!--<button class="btn btn-primary pull-left" onclick="location.href='actividades-cuadro'">CUADRO</button>-->

        <!--Calendario
        <div class="calendar-container">
        <div id="calendar" style="width: 100%;"></div>
        </div>
        -->

    </section>

  </div>



<!--=====================================
MODAL AGREGAR actividad
======================================-->
  
<!-- Modal -->
<div id="modalAgregarActividad" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

      <!--=====================================
      CABEZA DEL MODAL
      ======================================-->

      <div class="modal-header" style="background:#3c8dbc; color: white">

        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agregar actividad</h4>

      </div>

      <!--=====================================
      CUERPO DEL MODAL
      ======================================-->

      <div class="modal-body">
        
        <div class="box-body">


                <!-- entrada para la descripcion -->
                    
                    <div class="form-group">
                    
                        <div class="input-group">
                            
                            <span class="input-group-addon"><i class="fa fa-tasks"></i></span>

                            <input type="text" class="form-control input-lg" name="nuevaActividad" id="nuevaActividad" placeholder="Ingresar descripción" required>

                        </div>

                    </div>

                <!-- entrada para tipo -->
                    
                   <!-- <div class="form-group">
                    
                        <div class="input-group">
                            
                            <span class="input-group-addon"><i class="fa fa-filter"></i></span>

                            <input type="text" class="form-control input-lg" name="nuevoTipo" id="nuevoTipo" placeholder="Ingresar Tipo" required>

                        </div>

                    </div>
                    -->

                    <!-- entrada para tipo -->
                    <input type="hidden" name="nuevoTipo" value="actividad">


                 <!-- entrada para usuario -->

                    <div class="form-group">
            
                        <div class="input-group">
                    
                            <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>

                            <select class="form-control input-lg" id="nuevoUsuario" name="nuevoUsuario" required>
                        
                                <option value="">Seleccionar Responsable</option>

                                <?php

                                $item = null;
                                $valor = null;
                                $usuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

                                foreach ($usuarios as $key => $value) {
                                
                                    echo'<option value="'.$value["id"].'">'.$value["nombre"].'</option>';   
                                }

                                ?>

                            </select>

                        </div>

                    </div>

                    <!-- entrada para fecha -->
                    
                        <div class="form-group">
                            
                            <div class="input-group">
                                
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>

                                <input type="datetime-local" class="form-control input-lg" name="nuevaFecha" id="nuevaFecha" placeholder="Ingresar Fecha" required>

                            </div>

                        </div>


                        <!-- entrada para estado -->
                    <!--
                        <div class="form-group">
                    
                            <div class="input-group">
                                
                                <span class="input-group-addon"><i class="fa fa-check-square-o"></i></span>

                                <input type="text" class="form-control input-lg" name="nuevoEstado" id="nuevoEstado" placeholder="Ingresar Estado" required>

                            </div>

                        </div>
                            -->

                        <!-- entrada para estado -->
                        <input type="hidden" name="nuevoEstado" value="programada">


                        <!-- entrada para seleccionar cliente -->

                            <div class="form-group">
                        
                                <div class="input-group">
                            
                                <span class="input-group-addon"><i class="fa fa-user"></i></span>

                                    <select class="form-control input-lg" id="nuevoCliente" name="nuevoCliente" required>
                            
                                    <!--<option value="">Seleccionar Cliente</option>-->
                                    <option value="0">Sin cliente</option>

                                    <?php

                                        $item = null;
                                        $valor = null;
                                        $clientes = ControladorClientes::ctrMostrarClientes($item, $valor);

                                        foreach ($clientes as $key => $value) {
                                    
                                        echo'<option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                                        }

                                    ?>

                                    </select>

                                </div>

                            </div>


                            <!-- entrada para observacion -->
                    
                                <div class="form-group">
                                    
                                    <div class="input-group">
                                        
                                        <span class="input-group-addon"><i class="fa fa-pencil-square-o"></i></span>

                                        <input type="text" class="form-control input-lg" name="nuevaObservacion" id="nuevaObservacion" placeholder="Ingresar Observación">

                                    </div>

                                </div>
               

             </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar actividad</button>

        </div>

     </form>


     <?php

      $crearActividad = new ControladorActividades();
      $crearActividad -> ctrCrearActividad();

     ?>

    </div>

  </div>

</div>


<!--==========================================================================
MODAL EDITAR Actividad
============================================================================-->
  
<!-- Modal -->
<div id="modalEditarActividad" class="modal fade" role="dialog">

    <div class="modal-dialog">

        <div class="modal-content">

            <form role="form" method="post">

            <!--=====================================
            CABEZA DEL MODAL
            ======================================-->

            <div class="modal-header" style="background:#3c8dbc; color: white">

                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Editar Actividad</h4>

            </div>

      <!--=====================================
      CUERPO DEL MODAL
      ======================================-->

      <div class="modal-body">
        
        <div class="box-body">

           <!-- entrada para la descripcion -->
                    
           <div class="form-group">                    
                <div class="input-group">                    
                    <span class="input-group-addon"><i class="fa fa-tasks"></i></span>
                    <input type="text" class="form-control input-lg" name="editarActividad" id="editarActividad" placeholder="Ingresar descripción *" required>
                    <input type="hidden" name="idActividad" value="<?php echo !empty($actividad['id']) ? $actividad['id'] : ''; ?>">
                </div>
            </div>


            <!-- entrada para tipo -->
            <div class="form-group">
                <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-info-circle"></i></span>
                <select class="form-control input-lg" name="editarTipo" id="editarTipo" required>
                    <option value="">Seleccionar tipo</option>
                    <?php
                    $tiposModalEditar = ControladorTiposActividades::ctrMostrarTiposActividades(null, null);
                    foreach($tiposModalEditar as $tipoModal){
                        echo '<option value="'.$tipoModal["nombre"].'">'.ucfirst($tipoModal["nombre"]).'</option>';
                    }
                    ?>
                </select>
                </div>
            </div>

            <!--<input type="hidden" name="editarTipo" id="editarTipo">-->

                             
            <!-- entrada para seleccionar usuario -->

            <div class="form-group">            
                <div class="input-group">            
                    <span class="input-group-addon"><i class="fa fa-user-plus"></i></span>
                    <select class="form-control input-lg" id="editarUsuario" name="editarUsuario">                
                        <option value="">Seleccionar Responsable</option>
                         
                        <?php
                        $item = null;
                        $valor = null;
                        $usuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
                        foreach ($usuarios as $key => $value) {                        
                            echo'<option value="'.$value["id"].'">'.$value["nombre"].'</option>';   
                        }
                        ?>
                    </select>
                </div>
            </div>


            <!-- entrada para estado -->
            <div class="form-group">
                <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                <select class="form-control input-lg" name="editarEstado" id="editarEstado" required>
                    <option value="">Seleccionar estado</option>
                    <?php
                    $estadosModalEditar = ControladorEstadosActividades::ctrMostrarEstadosActividades(null, null);
                    foreach($estadosModalEditar as $estadoModal){
                        echo '<option value="'.$estadoModal["nombre"].'">'.ucfirst($estadoModal["nombre"]).'</option>';
                    }
                    ?>
                </select>
                </div>
            </div>

            <!--<input type="hidden" name="editarEstado" id="editarEstado">-->


                <!-- entrada para fecha -->
                <!--
                    <div class="form-group">
                        
                        <div class="input-group">
                            
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>

                            <input type="datetime-local" class="form-control input-lg" name="editarFecha" id="editarFecha">

                        </div>

                    </div>
                    -->


                    <!-- entrada para seleccionar cliente -->
                            
                    <div class="form-group">
                        
                        <div class="input-group">
                    
                            <span class="input-group-addon"><i class="fa fa-user"></i></span>

                                <select class="form-control input-lg" id="editarCliente" name="editarCliente">
                        
                                    <!--<option value="">Seleccionar Cliente</option>-->
                                    <option value="0">Sin cliente</option>

                                    <?php
                                        $item = null;
                                        $valor = null;
                                        $clientes = ControladorClientes::ctrMostrarClientes($item, $valor);

                                        foreach ($clientes as $key => $value) {
                                    
                                        echo'<option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                                        }
                                    ?>
                                </select>
                        </div>
                    </div>


                        <!-- entrada para observacion -->
                
                            <div class="form-group">
                                
                                <div class="input-group">
                                    
                                    <span class="input-group-addon"><i class="fa fa-pencil-square-o"></i></span>

                                    <input type="text" class="form-control input-lg" name="editarObservacion" id="editarObservacion" placeholder="Ingresar Observación">

                                </div>

                            </div>

    </div>  

</div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>

        </div>

     </form>

    </div>

  </div>

</div>


<!--=====================================
MODAL AGREGAR ESTADO ACTIVIDAD
======================================-->
<div id="modalAgregarEstadoActividad" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->
        <div class="modal-header" style="background:#3c8dbc; color: white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Estado de Actividad</h4>
        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->
        <div class="modal-body">
          <div class="box-body">

            <!-- ENTRADA PARA EL NOMBRE -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-check-circle"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoEstadoNombre" placeholder="Ingresar nombre del estado" required>
              </div>
            </div> 

            <!-- ENTRADA PARA EL COLOR -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-paint-brush"></i></span>
                <input type="color" class="form-control input-lg" name="nuevoEstadoColor" value="#3c8dbc" required>              </div>
            </div>

            <!-- CAMPO OCULTO PARA ORIGEN -->
            <input type="hidden" name="origenModal" value="actividades">

          </div>
        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar Estado</button>
        </div>

      </form>

      <?php
        $crearEstadoActividad = new ControladorEstadosActividades();
        $crearEstadoActividad -> ctrCrearEstado();
      ?>

    </div>
  </div>
</div>


<!--=====================================
MODAL AGREGAR TIPO ACTIVIDAD
======================================-->
<div id="modalAgregarTipoActividad" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->
        <div class="modal-header" style="background:#3c8dbc; color: white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Tipo de Actividad</h4>
        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->
        <div class="modal-body">
          <div class="box-body">

            <!-- ENTRADA PARA EL NOMBRE -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoTipoNombre" placeholder="Ingresar nombre del tipo" required>
              </div>
            </div>

            <!-- CAMPO OCULTO PARA ORIGEN -->
            <input type="hidden" name="origenModal" value="actividades">

          </div>
        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar Tipo</button>
        </div>

      </form>

      <?php
        $crearTipoActividad = new ControladorTiposActividades();
        $crearTipoActividad -> ctrCrearTipo();
      ?>

    </div>
  </div>
</div>



<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
<!-- Idioma Esp FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/es.js"></script>

  <!-- Choices.js para Campo estatus-->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>



<!-- Script para inicializar Choices.js y aplicar colores a estados -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Función para aplicar color al contenedor según el estado seleccionado

  function aplicarColorEstadoActividad(select) {
    const value = select.value;
    const container = select.closest('.choices'); 

    if (!container) {
      console.log('ERROR: No se encontró contenedor .choices');
      return;
    } 

    // Eliminar clases anteriores que empiecen con "estado-act-"
    container.className = container.className
      .split(" ")
      .filter(cls => !cls.startsWith("estado-act-"))
      .join(" "); 

    // Agregar la nueva clase
    const nuevaClase = "estado-act-" + value.replace(/ /g, "-").toLowerCase();
    container.classList.add(nuevaClase); 

    // Aplicar estilos inline directamente usando el mapa de colores
    const valueLower = value.toLowerCase();
    const color = window.estadosActividadesColores ? window.estadosActividadesColores[valueLower] : null; 

    if (color) {
      const choicesInner = container.querySelector('.choices__inner');
      if (choicesInner) {
        // Aplicar con setProperty para poder usar !important
        choicesInner.style.setProperty('background-color', color, 'important');
        choicesInner.style.setProperty('border-color', color, 'important');
        choicesInner.style.setProperty('color', '#fff', 'important');
        console.log('✓ Color aplicado al contenedor:', nuevaClase, color);
      }
    }
  } 

    // Aplicar colores inline a las opciones del dropdown
  function aplicarColoresOpcionesDropdown() {
    console.log('=== Aplicando colores a opciones del dropdown ===');
    console.log('Mapa de colores:', window.estadosActividadesColores); 

    // Buscar TODAS las opciones, incluyendo las ocultas
    const opcionesVisible = document.querySelectorAll('.choices__list--dropdown .choices__item--selectable');
    const opcionesTodas = document.querySelectorAll('.choices__item--selectable');
    const dropdowns = document.querySelectorAll('.choices__list--dropdown'); 

    console.log('Dropdowns encontrados:', dropdowns.length);
    console.log('Opciones visibles encontradas:', opcionesVisible.length);
    console.log('Opciones totales encontradas:', opcionesTodas.length); 

    // Usar todas las opciones, no solo las del dropdown
    const opciones = opcionesTodas;

    opciones.forEach(function(opcion) {
      const valorEstado = opcion.getAttribute('data-value');
      if (!valorEstado) return; 

      console.log('Procesando opción:', valorEstado); 

      // Buscar el color correspondiente en el mapa
      const colorEstado = window.estadosActividadesColores[valorEstado.toLowerCase()];
      console.log('Color encontrado para "' + valorEstado + '":', colorEstado);


      if (colorEstado) {
        // Aplicar estilos inline directamente
        opcion.style.setProperty('background-color', colorEstado, 'important');
        opcion.style.setProperty('color', '#fff', 'important');
        opcion.style.setProperty('border', 'none', 'important');
        console.log('✓ Colores aplicados a:', valorEstado);
      } else {

        console.warn('✗ No se encontró color para:', valorEstado);
      }
    });
  } 

  // Inicializar Choices.js y aplicar colores
  function inicializarChoicesEstadoActividad() {
    const selects = document.querySelectorAll('.cambiarEstadoActividad');

 
    selects.forEach(function(select) {
      // Evitar reinicializar si ya tiene Choices.js
      if (select.classList.contains('choices__input')) {
        return;
      } 

      // Guardar las clases de estado del select original
      const clasesEstado = Array.from(select.classList).filter(cls => cls.startsWith('estado-act-'));
 
      // Inicializar Choices.js
      const choices = new Choices(select, {
        searchEnabled: false,
        itemSelectText: '',
        shouldSort: false
      });

       // Aplicar color inicial después de que Choices.js cree el contenedor
      setTimeout(() => {
        const container = select.closest('.choices'); 

        if (container && clasesEstado.length > 0) {
          // Agregar las clases de estado al contenedor de Choices.js
          clasesEstado.forEach(clase => {
            container.classList.add(clase);
          });
        } 

        // Aplicar estilos inline
        aplicarColorEstadoActividad(select); 

        // Aplicar colores a las opciones del dropdown
        aplicarColoresOpcionesDropdown();
      }, 100); 

      // Cambiar color dinámicamente al cambiar el valor
      select.addEventListener('change', function() {
        aplicarColorEstadoActividad(this);
      });

      // Aplicar colores a opciones cuando se abre el dropdown
      select.addEventListener('showDropdown', function() {
        setTimeout(aplicarColoresOpcionesDropdown, 50);
      }, false);
    });
  }
    // Inicializar Choices.js al cargar la página
  inicializarChoicesEstadoActividad(); 

  // Reinicializar cuando DataTables recarga los datos
  $('.tablas').on('draw.dt', function() {
    setTimeout(inicializarChoicesEstadoActividad, 100);
  });
});
</script>


<!--Ruta actividades.js-->
<script src="vistas/js/actividades.js"></script>
<script src="assets/js/actividades.js"></script>


  <?php
    $eliminarActividad = new ControladorActividades();
    $eliminarActividad -> ctrEliminarActividad();
  ?>

<!--=============CALENDARIO========================
<script>
success: function (respuesta) {
    console.log("Respuesta AJAX:", respuesta);

    $("#editarActividad").val(respuesta.descripcion);
    $("#editarTipo").val(respuesta.tipo);
    $("#editarUsuario").val(respuesta.id_user);
    $("#editarCliente").val(respuesta.id_cliente);
    $("#editarEstado").val(respuesta.estado);
    $("#editarObservacion").val(respuesta.observacion);
    $("input[name='idActividad']").val(respuesta.id);

    if (respuesta.fecha && !isNaN(new Date(respuesta.fecha))) {
        const fechaOriginal = new Date(respuesta.fecha);
        const fechaFormateada = fechaOriginal.toISOString().slice(0, 16);
        $("#editarFecha").val(fechaFormateada);
    } else {
        console.warn("Fecha inválida o vacía:", respuesta.fecha);
        $("#editarFecha").val("");
    }

    // ✅ Solución clave: cerrar primero y abrir luego
    $("#modalEditarActividad").modal("hide");

    setTimeout(() => {
        $("#modalEditarActividad").modal("show");
    }, 300); // da tiempo para cerrar correctamente antes de abrir
}
</script>
-->


