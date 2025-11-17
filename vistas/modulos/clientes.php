<!-- Librería de estilos de Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

<!-- Ruta clientes.css -->
<link rel="stylesheet" href="assets/css/clientes.css">


<!-- Centrar filtro -->
  <style>
    @media (max-width: 767px) {
    .filtro-estatus-wrapper {
      float: none !important;        /* anula el pull-right */
      justify-content: center !important; /* centra con flex */
      text-align: center;            /* por si acaso */
      width: 100%;                   /* ocupa todo el ancho */
    }

    .filtro-estatus-wrapper label {
      margin-bottom: 5px;            /* pequeño espacio si se apila */
    }
  }
  </style> 


<style>
td.details-control {
    background: url('https://cdn.datatables.net/1.13.6/images/details_open.png') no-repeat center center;
    cursor: pointer;
}
tr.shown td.details-control {
    background: url('https://cdn.datatables.net/1.13.6/images/details_close.png') no-repeat center center;
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


<!-- Solo muestra 2 campos en movil en la Tabla 1-->
<style> 
@media (max-width: 767px) {
  .tablas1 td:nth-child(n+3),
  .tablas1 th:nth-child(n+3) {
      display: none;
  }
  .tablas1 td:first-child,
  .tablas1 td:nth-child(2),
  .tablas1 th:first-child,
  .tablas1 th:nth-child(2) {
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


<!-- Solo muestra 2 campos en movil en la Tabla 2-->
<style> 
@media (max-width: 767px) {
  .tablas2 td:nth-child(n+3),
  .tablas2 th:nth-child(n+3) {
      display: none;
  }
  .tablas2 td:first-child,
  .tablas2 td:nth-child(2),
  .tablas2 th:first-child,
  .tablas2 th:nth-child(2) {
      display: table-cell;
  }
}
</style>


<!-- Estilos dinámicos para colores de estados -->
<style>
  <?php
  $estadosParaEstilos = ControladorEstadosClientes::ctrMostrarEstadosClientes(null, null);
  foreach($estadosParaEstilos as $estadoEstilo){
    $nombreLimpio = str_replace(" ", "-", strtolower($estadoEstilo["nombre"]));
   
    $color = $estadoEstilo["color"]; 

    // Estilos para el contenedor cerrado de Choices.js (select cuando NO está abierto)
    echo '.estatus-'.$nombreLimpio.' .choices__inner,';
    echo '.estatus-'.$nombreLimpio.'.choices .choices__inner {';
    echo '  background-color: '.$color.' !important;';
    echo '  border-color: '.$color.' !important;';
    echo '  color: #fff !important;';
    echo '}';
    

    // Estilos para cada opción individual en el dropdown (basado SOLO en data-value)
    echo '.choices__list--dropdown .choices__item--selectable[data-value="'.$estadoEstilo["nombre"].'"] {';
    echo '  background-color: '.$color.' !important;';
    echo '  color: #fff !important;';
    echo '}';
 
    echo '.choices__list--dropdown .choices__item--selectable[data-value="'.$estadoEstilo["nombre"].'"]:hover {';
    echo '  background-color: '.adjustBrightness($color, -20).' !important;';
    echo '  color: #fff !important;';
    echo '}';
  } 

  // Función helper para ajustar brillo (hover más oscuro)
  function adjustBrightness($hex, $steps) {
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
  window.estadosColores = {
    <?php
    foreach($estadosParaEstilos as $key => $estadoEstilo){
      $coma = ($key < count($estadosParaEstilos) - 1) ? ',' : '';
      echo '"'.strtolower($estadoEstilo["nombre"]).'": "'.$estadoEstilo["color"].'"'.$coma."\n";
    }
    ?>
  };

  console.log("Colores de estados cargados:", window.estadosColores);
</script>

         <?php
      $editarCliente = new ControladorClientes();
      $editarCliente -> ctrEditarCliente();
        ?>
  
  <div class="content-wrapper">
    <section class="content-header">

      <h1>
        Administrar Clientes
      </h1>

      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li class="active">Administrar Contactos</li>
      </ol>

    </section>

    <section class="content">

      <div class="box">

        <div class="box-header with-border">

          <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarCliente">            
             Agregar Nuevo
          </button>

        </div>


        <!--CODIGO PARA LLAMAR AL WEBHOOK DE n8n -->
        <!--
        <form id="formN8N" action="https://c610c962d42e.ngrok-free.app/webhook/mipos" method="POST" target="_blank">
          <input type="hidden" name="origen" value="clientes">
          <button type="submit" class="btn btn-success">Enviar a n8n</button>
        </form>
        -->

  
        <?php
          $filtroEstatus1 = isset($_GET['filtroEstatus1']) ? $_GET['filtroEstatus1'] : '';  // Captura el valor del filtro de estatus si existe.

          // Aquí aplica el filtro de estatus desde el GET para obtener los clientes correctos
          $item = "estatus";
          $valor = $filtroEstatus1;
          $clientes = ControladorClientes::ctrMostrarClientes($item, $valor);
        ?>

        <h3 style="text-align: center; font-weight: bold; margin: 20px 0; color: #4A4A4A; padding-bottom: 10px; border-bottom: 2px solid #4A4A4A;">
          Lista de Clientes
        </h3>


        <div class="box-body table-responsive">

            <!-- filtro estatus-->
            <div class="clearfix mb-2">
              <div class="pull-right filtro-estatus-wrapper d-flex align-items-center" style="gap: 8px;">
                <label for="filtroEstatus1" class="control-label mb-0">Filtra por ESTADOS:</label>
                <select id="filtroEstatus1" onchange="filterTable1()" class="form-control filtro-estatus">

                  <option value="">Todos</option>
                  <?php
                  $estadosDisponibles = ControladorEstadosClientes::ctrMostrarEstadosClientes(null, null);
                  foreach($estadosDisponibles as $estado){
                      $selected = ($filtroEstatus1 == $estado["nombre"]) ? "selected" : "";
                      echo '<option value="'.$estado["nombre"].'" '.$selected.'>'.ucfirst($estado["nombre"]).'</option>';
                  }
                  ?>

                </select>
              </div>
            </div>

            <br><br>

          
  <table class="table table-bordered table-striped tablas1 tablas">
    <thead>
        <tr>
            <th style="width: 10px">#</th>
            <th>Nombre</th>
            <th>Documento</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Departamento</th>
            <th>Ciudad</th>
            <th>Dirección</th>            
            <th>Estado <button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#modalAgregarEstado" title="Agregar nuevo estado"><i class="fa fa-plus"></i></button></th>
            <th>Notas</th>
            <!--<th>Total compras</th>-->
            <th>Última compra</th>
            <th>Ingreso al sistema</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $item = null;
        $valor = null;
        $clientes = ControladorClientes::ctrMostrarClientes($item, $valor);

        if(is_array($clientes) && count($clientes) > 0):
            $key = 1;
            foreach ($clientes as $value):
                if(isset($value["compras"]) && $value["compras"] > 0):
                    $estatus = $value["estatus"] ?? "";
                    $estatusClass = "estatus-" . str_replace(" ", "-", strtolower($estatus));
        ?>

        
        <tr>
            <td><?php echo $key; ?></td>


            <!-- BTN VERSION MOVIL-->
            <td>
                <?php echo $value["nombre"]; ?>

                <!--<button type="button"
                        class="btn btn-danger btn-xs btnEliminarCliente solo-movil"
                        style="float: right;"
                        idCliente="<?php echo $value['id']; ?>">
                  <i class="fa fa-times"></i>
                </button> -->

                <a href="index.php?ruta=cliente-ventas&idCliente=<?php echo $value['id']; ?>" 
                  class="btn btn-success btn-xs btnVerVentasCliente solo-movil" 
                  style="float: right;"
                  title="Ver ventas de este cliente">
                  <i class="fa fa-line-chart"></i>
                </a>

                <button type="button"
                        class="btn btn-info btn-xs btn-ver-mas solo-movil"
                        style="float: right;"
                        idCliente="<?php echo $value['id']; ?>">
                        <i class="fa fa-eye"></i>
                </button>                
            </td>
            <!-- FIN BTN MOVIL-->

            <td><?php echo $value["documento"]; ?></td>
            <td><?php echo $value["email"]; ?></td>
            <td><?php echo $value["telefono"]; ?></td>
            <td><?php echo $value["departamento"]; ?></td>
            <td><?php echo $value["ciudad"]; ?></td>
            <td><?php echo $value["direccion"]; ?></td>

            <td>
                <select class="form-control cambiarEstatus <?php echo $estatusClass; ?>" data-id="<?php echo $value["id"]; ?>">
                    <?php
                    $estadosDisponibles = ControladorEstadosClientes::ctrMostrarEstadosClientes(null, null);
                    foreach($estadosDisponibles as $estado){
                        $selected = ($value["estatus"] == $estado["nombre"]) ? "selected" : "";
                        echo '<option value="'.$estado["nombre"].'" '.$selected.'>'.ucfirst($estado["nombre"]).'</option>';
                    }
                    ?>
                </select>
            </td>

            <td contenteditable="true" class="celda-notas" data-id="<?= $value['id']; ?>">
                <?= $value['notas'] ?? ''; ?>
            </td>

            <!--<td><?php //echo $value["compras"]; ?></td>-->
            <td><?php echo $value["ultima_compra"]; ?></td>
            <td><?php echo $value["fecha"]; ?></td>
            <td>
                <div class="btn-group">

                <button class="btn btn-warning btnEditarCliente" data-toggle="modal" data-target="#modalEditarCliente" idCliente="<?php echo $value["id"]; ?>"
                  title="Editar cliente">
                  <i class="fa fa-pencil"></i>
                  </button>

                <a href="index.php?ruta=cliente-ventas&idCliente=<?php echo $value['id']; ?>" 
                  class="btn btn-success btnVerVentasCliente"                  
                  title="Ver ventas de este cliente">
                  <i class="fa fa-line-chart"></i>
                </a>                

                  <!--<button class="btn btn-danger btnEliminarCliente" idCliente="<?php echo $value["id"]; ?>"
                  title="Eliminar cliente">
                    <i class="fa fa-times"></i>
                 </button>  -->                     
                    
                </div>
            </td>
        </tr>
        <?php
            $key++;
            endif;
            endforeach;
        else:
        ?>
        <tr>
            <td colspan="14" class="text-center">No hay clientes registrados</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

</div>


          <!--=====================================
          2DA TABLA CLIENTES SIN VENTAS
          ======================================-->
          <br><br>

            <?php
              $filtroEstatus2 = isset($_GET['filtroEstatus2']) ? $_GET['filtroEstatus2'] : '';  // Captura el valor del filtro de estatus si existe.

              // Aquí aplica el filtro de estatus desde el GET para obtener los clientes correctos
              $item = "estatus";
              $valor = $filtroEstatus2;
              $clientes = ControladorClientes::ctrMostrarClientes($item, $valor);
            ?>

        <h3 style="text-align: center; font-weight: bold; margin: 20px 0; color: #4A4A4A; padding-bottom: 10px; border-bottom: 2px solid #4A4A4A;">
          Contactos sin Ventas
        </h3>    

            <div class="box-body table-responsive">

            <!-- filtro estatus -->
              <div class="clearfix mb-2">
              <div class="pull-right filtro-estatus-wrapper d-flex align-items-center" style="gap: 8px;">
                <label for="filtroEstatus2" class="control-label mb-0">Estados:</label>
                <select id="filtroEstatus2" onchange="filterTable2()" class="form-control filtro-estatus">

                  <option value="">Todos</option>
                  <?php
                  $estadosDisponibles = ControladorEstadosClientes::ctrMostrarEstadosClientes(null, null);
                  foreach($estadosDisponibles as $estado){
                      $selected = ($filtroEstatus2 == $estado["nombre"]) ? "selected" : "";
                      echo '<option value="'.$estado["nombre"].'" '.$selected.'>'.ucfirst($estado["nombre"]).'</option>';
                  }
                  ?>

                </select>
              </div>
            </div>

            <br><br>

              <table class="table table-bordered table-striped tablas2 tablas">          
                  
                <thead>
                  <tr>
                    <th style="width: 10px">#</th>
                    <th>Nombre</th>
                    <th>Documento</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Departamento</th>
                    <th>Ciudad</th>
                    <th>Dirección</th>
                    <!--<th>Fecha de nacimiento</th>-->
                    <th>Estado <button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#modalAgregarEstado" title="Agregar nuevo estado"><i class="fa fa-plus"></i></button></th>
                    <th><i class="fa fa-pencil"></i> <i class="fa fa-hand-o-down"></i> Notas</th>
                    <!--<th>Total compras</th>-->
                    <!--<th>Ultima compra</th>-->
                    <th>Ingreso al sistema</th>
                    <th>Acciones</th>
                  </tr>             
                </thead>

                  <tbody>

                  <?php
                    $item = null;
                    $valor = null;
                    $clientes = ControladorClientes::ctrMostrarClientes($item, $valor);
                  ?>

                    <?php 
                    $key = 1;
                    foreach ($clientes as $value): 
                      if ($value["compras"] <= 0): 
                        $estatus = $value["estatus"] ?? "sin estatus";
                        $estatusClass = "estatus-" . str_replace(" ", "-", strtolower($estatus));
                    ?>


                    <tr>
                      <td><?php echo $key + 1; ?></td>
                      <td><?php echo $value["nombre"]; ?>
                    
                      <!--BTN VERSION MOVIL-->
                        <button type="button"
                            class="btn btn-danger btn-xs btnEliminarCliente solo-movil"
                            style="float: right;"
                            idCliente="<?php echo $value['id']; ?>">
                      <i class="fa fa-times"></i>
                    </button>

                    <button type="button"
                            class="btn btn-info btn-xs btn-ver-mas solo-movil"
                            style="float: right;"
                            idCliente="<?php echo $value['id']; ?>">
                            <i class="fa fa-eye"></i>
                    </button>
                    <!-- FIN BTN VERSION MOVIL-->

                    </td>
                      <td><?php echo $value["documento"]; ?></td>
                      <td><?php echo $value["email"]; ?></td>
                      <td><?php echo $value["telefono"]; ?></td>
                      <td><?php echo $value["departamento"]; ?></td>
                      <td><?php echo $value["ciudad"]; ?></td>
                      <td><?php echo $value["direccion"]; ?></td>
                      
                      <td>
                        <select class="form-control cambiarEstatus <?php echo $estatusClass; ?>" data-id="<?php echo $value["id"]; ?>">
                         <?php
                          $estadosDisponibles = ControladorEstadosClientes::ctrMostrarEstadosClientes(null, null);
                          foreach($estadosDisponibles as $estado){
                              $selected = ($value["estatus"] == $estado["nombre"]) ? "selected" : "";
                              echo '<option value="'.$estado["nombre"].'" '.$selected.'>'.ucfirst($estado["nombre"]).'</option>';
                          }
                          ?>
                        </select>
                      </td>
        
                      <td contenteditable="true" class="celda-notas" data-id="<?= $value['id']; ?>">
                        <?= $value['notas']; ?>
                      </td>

                      
                      <td><?php echo $value["fecha"]; ?></td>
                      <td>
                        <div class="btn-group">
                          <button class="btn btn-warning btnEditarCliente" data-toggle="modal" data-target="#modalEditarCliente" idCliente="<?php echo $value["id"]; ?>"><i class="fa fa-pencil"></i></button>
                          <button class="btn btn-danger btnEliminarCliente" idCliente="<?php echo $value["id"]; ?>"><i class="fa fa-times"></i></button>
                        </div>
                      </td>
                    </tr>
                  <?php

                 $key++;
                 endif;
                 endforeach; 
                 ?>

                    
                  </tbody>

              </table>

            </div>
            <!--fin tabla-->
      </div>

    </section>

  </div>


<!--=====================================
MODAL AGREGAR CLIENTE
======================================-->
  
<!-- Modal -->
<div id="modalAgregarCliente" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post">

      <!--=====================================
      CABEZA DEL MODAL
      ======================================-->

      <div class="modal-header" style="background:#3c8dbc; color: white">

        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agregar cliente</h4>

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

              <input type="text" class="form-control input-lg" name="nuevoCliente" id="nuevoCliente" placeholder="Ingresar nombre *" required>

             </div>

           </div>


            <!-- entrada para documento ID -->
            
            <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-key"></i></span>

              <input type="number" min="0" max="9999999999" class="form-control input-lg" name="nuevoDocumentoId" placeholder="Ingresar documento *" required>

             </div>

           </div>


           <!-- entrada para telefono -->
            
            <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-phone"></i></span>

              <input type="text" class="form-control input-lg" name="nuevoTelefono" placeholder="Ingresar teléfono *" data-inputmask="'mask':'(999) 999-9999'" data-mask required>

             </div>

           </div>


            <!-- entrada para Email -->
            
            <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-envelope"></i></span>

              <!--<input type="email" class="form-control input-lg" name="nuevoEmail" placeholder="Ingresar email" required>-->
              <input type="email" class="form-control input-lg" name="nuevoEmail" placeholder="Ingresar email">

             </div>

           </div>


           <!-- entrada para departamento -->
            
           <div class="form-group">
          
          <div class="input-group">
            
            <span class="input-group-addon"><i class="fa fa-building"></i></span>

            <input type="text" class="form-control input-lg" name="nuevoDepartamento" placeholder="Ingresar departamento">

           </div>

         </div>


         <!-- entrada para ciudad -->
            
         <div class="form-group">
          
          <div class="input-group">
            
            <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>

            <input type="text" class="form-control input-lg" name="nuevoCiudad" placeholder="Ingresar Ciudad">

           </div>

         </div>


           <!-- entrada para la direccion -->
            
            <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-home"></i></span>

              <input type="text" class="form-control input-lg" name="nuevaDireccion" placeholder="Ingresar dirección *" required>

             </div>

           </div>


           <!-- entrada para estatus -->
           <input type="hidden" name="nuevoEstatus" value="nuevo">

          <!-- crear estado clientes -->
           <input type="hidden" name="vistaOrigen" value="clientes">



           <!-- Estatus 
            <div class="form-group"> 
              <label for="editarEstatus">Estatus</label>
              <select class="form-control" name="editarEstatus" id="editarEstatus">
                <option value="contactado">Contactado</option>
                <option value="en espera">En espera</option>
                <option value="interesado">Interesado</option>
                <option value="no interesado">No interesado</option>
              </select>
            </div>
            -->

           <!-- entrada para la fecha naciminiento -->            
           <!--
            <div class="form-group">          
            <div class="input-group">              
              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
              <input type="text" class="form-control input-lg" name="nuevaFechaNacimiento" placeholder="Ingresar fecha de nacimiento" data-inputmask="'alias': 'yyyy/mm/dd'" data-mask>
             </div>
           </div>
           -->

            <!-- entrada para notas -->
            
            <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-pencil-square-o"></i></span>

              <input type="text" class="form-control input-lg" name="nuevaNota" placeholder="Ingresar Nota">

             </div>

           </div>
          

         </div>  

       </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar cliente</button>

        </div>

     </form>


     <?php

      $crearCliente = new ControladorClientes();
      $crearCliente -> ctrCrearCliente();

     ?>

    </div>

  </div>

</div>


<!--==========================================================================
MODAL EDITAR CLIENTE
===========================================================================-->
  
<!-- Modal -->
<div id="modalEditarCliente" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post">

      <!--=====================================
      CABEZA DEL MODAL
      ======================================-->

      <div class="modal-header" style="background:#3c8dbc; color: white">

        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Editar cliente</h4>

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

              <input type="text" class="form-control input-lg" name="editarCliente" id="editarCliente" required>
              <input type="hidden" id="idCliente" name="idCliente">

             </div>

           </div>


            <!-- entrada para documento ID -->
            
            <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-key"></i></span>

              <input type="number" min="0" class="form-control input-lg" name="editarDocumentoId" id="editarDocumentoId" placeholder="Documento" required>

             </div>

           </div>


           <!-- entrada para telefono -->
            
           <div class="form-group">
          
          <div class="input-group">
            
            <span class="input-group-addon"><i class="fa fa-phone"></i></span>

            <input type="text" class="form-control input-lg" name="editarTelefono"  id="editarTelefono" data-inputmask="'mask':'(999) 999-9999'" data-mask placeholder="Celular" required>

           </div>

         </div>


           <!-- entrada para Email -->
            
            <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-envelope"></i></span>

              <!--<input type="email" class="form-control input-lg" name="editarEmail" id="editarEmail" required>-->
              <input type="email" class="form-control input-lg" name="editarEmail" id="editarEmail" placeholder="Correo Electrónico">

             </div>

           </div>


           <!-- entrada para la departamento -->
            
           <div class="form-group">
          
          <div class="input-group">
            
            <span class="input-group-addon"><i class="fa fa-building"></i></span>

            <input type="text" class="form-control input-lg" name="editarDepartamento" id="editarDepartamento" placeholder="Departamento">

           </div>

         </div>


         <!-- entrada para la ciudad -->
            
         <div class="form-group">
          
          <div class="input-group">
            
            <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>

            <input type="text" class="form-control input-lg" name="editarCiudad" id="editarCiudad" placeholder="Ciudad">

           </div>

         </div>


           <!-- entrada para la direccion -->
            
            <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-home"></i></span>

              <input type="text" class="form-control input-lg" name="editarDireccion" id="editarDireccion" placeholder="Dirección" required>

             </div>

           </div>


           <!-- entrada para la fecha naciminiento -->            
            <!-- 
            <div class="form-group">          
            <div class="input-group">              
              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
              <input type="text" class="form-control input-lg" name="editarFechaNacimiento" id="editarFechaNacimiento" data-inputmask="'alias': 'yyyy/mm/dd'" data-mask required>
             </div>
           </div>
           -->


          <!-- entrada para estado -->
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-flag"></i></span>
              <select class="form-control input-lg" name="editarEstado" id="editarEstado" required>
                <option value="">Seleccionar estado</option>

                <?php
                $estadosDisponibles = ControladorEstadosClientes::ctrMostrarEstadosClientes(null, null);
                foreach($estadosDisponibles as $estado){
                    echo '<option value="'.$estado["nombre"].'">'.ucfirst($estado["nombre"]).'</option>';
                }
                ?>

              </select>
            </div>
          </div>

           <!-- entrada para nota -->
            
           <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-pencil-square-o"></i></span>

              <input type="text" class="form-control input-lg" name="editarNota" id="editarNota" placeholder="Notas">

            </div>

          </div>


           <!-- entrada para estatus -->
           <!--<input type="hidden" name="editarEstatus" id="editarEstatus">-->

          

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


 <?php

 $eliminarCliente = new ControladorClientes();
 $eliminarCliente -> ctrEliminarCliente();

 ?>


<!-- jQuery 
<script src="vistas/bower_components/jquery/dist/jquery.min.js"></script>
 Datatable
<script src="vistas/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
-->


<!-- Choices.js para Campo estatus-->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<!--Ruta Clientes.js-->
<script src="assets/js/clientes.js"></script>


<!-- Filtro estatus tabla 1 -->
<script>
function filterTable1() {
  var filtro = $('#filtroEstatus1').val();
  var $rows = $('.tablas1 tbody tr').not('.fila-detalle-row');
  $rows.each(function() {
    var $mainRow = $(this);
    var $detalleRow = $mainRow.next('.fila-detalle-row');
    var estado = $mainRow.find('select.cambiarEstatus').val();
    if (filtro === "" || estado === filtro) {
      $mainRow.show();
      $detalleRow.show();
    } else {
      $mainRow.hide();
      $detalleRow.hide();
    }
  });
}
$(document).ready(function(){
  // Ejecutar filtro al cargar si hay valor
  filterTable1();
  // Si usas AJAX para cambiar estatus, llama a filterTable1() después de actualizar
});
</script>


<!-- Btn Ver mas o editar en Movil -->
<script>
$(document).ready(function() {
  $('.tablas1, .tablas2').on('click', '.btn-ver-mas', function(e) {
    e.preventDefault();
    var idCliente = $(this).attr('idCliente');
    // Dispara el mismo evento que el botón de editar
    $('.btnEditarCliente[idCliente="'+idCliente+'"]').trigger('click');
  });
});
</script>


<!--=====================================
MODAL AGREGAR ESTADO
======================================-->
<div id="modalAgregarEstado" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post">

       <!-- Campo oculto para indicar origen -->
        <input type="hidden" name="origenModal" value="clientes">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->
        <div class="modal-header" style="background:#3c8dbc; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Estado</h4>
        </div>
        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->
        <div class="modal-body">
          <div class="box-body">
            <!-- ENTRADA PARA EL NOMBRE -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-flag"></i></span>
                <input type="text" class="form-control input-lg" name="nuevoEstadoNombre" placeholder="Nombre del estado" required>
              </div>
            </div>
            <!-- ENTRADA PARA EL COLOR -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-paint-brush"></i></span>
                <input type="color" class="form-control input-lg" name="nuevoEstadoColor" value="#3c8dbc" required style="height: 46px;">
              </div>
              <small class="text-muted">Selecciona un color para este estado</small>
            </div>
            
          </div>
        </div>
        <!--=====================================
        PIE DEL MODAL
        ======================================-->
        <div class="modal-footer" style="display: block !important; visibility: visible !important;">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary" style="display: inline-block !important; visibility: visible !important;">Guardar estado</button>
        </div>
      </form>

      <?php
        $crearEstado = new ControladorEstadosClientes();
        $crearEstado -> ctrCrearEstado();
      ?>
    </div>
  </div>
</div>

<!-- Script para recargar la página después de agregar un estado -->
<script>
$(document).ready(function() {
  // Detectar si se agregó un estado exitosamente
  <?php if(isset($_GET["estadoCreado"]) && $_GET["estadoCreado"] == "ok"): ?>
    swal({
      type: "success",
      title: "¡El estado ha sido guardado correctamente!",
      showConfirmButton: true,
      confirmButtonText: "Cerrar"
    }).then(function(result){
      if (result.value) {
        window.location = "clientes";
      }
    });
  <?php endif; ?>

  <?php if(isset($_GET["estadoCreado"]) && $_GET["estadoCreado"] == "error"): ?>
    swal({
      type: "error",
      title: "¡El estado no pudo ser guardado!",
      showConfirmButton: true,
      confirmButtonText: "Cerrar"
    });
  <?php endif; ?>
});
</script>