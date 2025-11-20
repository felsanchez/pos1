<style>
.formulario-fechas-container {
  max-width: 300px;
  padding: 15px;
  border-radius: 10px;
  background-color: #ffffff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  margin-bottom: 20px;
}
.formulario-fechas label {
  font-weight: 600;
  margin-top: 10px;
}
.formulario-fechas select,
.formulario-fechas input[type="date"] {
  border-radius: 8px;
  margin-bottom: 10px;
}
.d-none {
  display: none !important;
}
/* Ocultar tabla de ventas hasta que DataTables termine de procesarla */
#example:not(.datatable-ready) {
  visibility: hidden;
}
/* Mostrar un indicador de carga mientras se procesa */
#example:not(.datatable-ready) + .dataTables_wrapper {
  position: relative;
}

/* Cards para móvil */
.cards-ventas {
  display: none;
}

.card-venta {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 15px;
  padding: 15px;
  position: relative;
  border-left: 4px solid #00a65a;
}

.card-venta-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
  padding-bottom: 10px;
  border-bottom: 1px solid #eee;
}

.card-venta-codigo {
  font-size: 16px;
  font-weight: bold;
  color: #00a65a;
}

.card-venta-acciones .btn-group {
  display: flex;
  gap: 5px;
}

.card-venta-cliente {
  font-size: 18px;
  font-weight: bold;
  color: #333;
  margin-bottom: 5px;
}

.card-venta-total {
  font-size: 22px;
  font-weight: bold;
  color: #00a65a;
  margin-bottom: 10px;
}

.card-venta-info {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 10px;
}

.card-venta-info-item {
  display: flex;
  align-items: center;
  font-size: 14px;
  color: #666;
}

.card-venta-info-item i {
  margin-right: 8px;
  width: 20px;
  text-align: center;
}

.card-venta-notas {
  background: #f9f9f9;
  padding: 10px;
  border-radius: 4px;
  margin-top: 10px;
  font-size: 13px;
  color: #666;
  border-left: 3px solid #3c8dbc;
}

.card-venta-notas.editable {
  cursor: text;
  border-left-color: #00a65a;
}

.card-venta-imagen-icono {
  display: inline-block;
  padding: 5px 10px;
  background: #3c8dbc;
  color: white;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
  margin-top: 5px;
}

.card-venta-imagen-icono:hover {
  background: #2e6da4;
}

/* Responsive */
@media (max-width: 767px) {
  .tabla-ventas {
    display: none !important;
  }
  .cards-ventas {
    display: block !important;
  }
}

@media (min-width: 768px) {
  .tabla-ventas {
    display: block !important;
  }
  .cards-ventas {
    display: none !important;
  }
}
</style>


<!-- Ocultar todas las columnas por defecto -->
<style> 
@media (max-width: 767px) {
  .tablas td,
  .tablas th {
      display: none;
  }
  
  /* Mostrar solo las columnas 2, 6, 7 y 8 en MOVIL*/
  .tablas td:nth-child(2),
  .tablas td:nth-child(3),
  .tablas td:nth-child(6),
  .tablas td:nth-child(8),
  .tablas td:nth-child(9),
  .tablas td:nth-child(10),
  .tablas th:nth-child(2),
  .tablas th:nth-child(3),
  .tablas th:nth-child(6),
  .tablas th:nth-child(8),
  .tablas th:nth-child(9),
  .tablas th:nth-child(10) {
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


<!-- DateRangePicker -->
<link rel="stylesheet" href="vistas/bower_components/bootstrap-daterangepicker/daterangepicker.css">
  
  
  <?php

  // Obtener configuración del sistema
  $configuracion = ControladorConfiguracion::ctrObtenerConfiguracion();
  $moneda = !empty($configuracion["moneda"]) ? $configuracion["moneda"] : "$";
  $formatoCodigoVenta = !empty($configuracion["formato_codigo_venta"]) ? $configuracion["formato_codigo_venta"] : "";

  /*echo "<pre>";
  var_dump($_GET);
  echo "</pre>";
  */

    $xml = ControladorVentas::ctrDescargarXML();

    if($xml){

      rename($_GET["xml"].".xml", "xml/".$_GET["xml"].".xml");
      echo '<a class="btn btn-block btn-success abrirXML" archivo="xml/'.$_GET["xml"].'.xml" href="ventas">Se ha creado correctamente el archivo XML<span class="fa fa-times pull-right"></span></a>';
    }
  ?>

  <div class="content-wrapper">
    <section class="content-header">


      <h1>
        Administrar ventas
      </h1>

      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li class="active">Administrar ventas</li>
      </ol>

    </section>

    <section class="content">

      <div class="box">

        <div class="box-header with-border">


          <a href="crear-venta">
            <button class="btn btn-primary">              
               Agregar venta
            </button>
          </a>

          <!-- Formulario de filtro de fechas -->
           <!--
          <div class="formulario-fechas-container">
            <form id="filtro-fechas" class="formulario-fechas">
              <label for="tipo-fecha">Filtrar por</label>
              <select id="tipo-fecha" name="tipo" class="form-control">
                <option value="hoy">Hoy</option>
                <option value="ayer">Ayer</option>
                <option value="mes">Mes actual</option>
                <option value="personalizado">Personalizado</option>
              </select>

              <div id="campo-desde" class="form-group d-none">
                <label for="fecha-desde">Desde</label>
                <input type="date" id="fecha-desde" name="fecha_inicio" class="form-control">
              </div>

              <div id="campo-hasta" class="form-group d-none">
                <label for="fecha-hasta">Hasta</label>
                <input type="date" id="fecha-hasta" name="fecha_fin" class="form-control">
              </div>

              <button type="submit" class="btn btn-primary w-100 mt-2">Aplicar filtro</button>
            </form>
          </div>
          -->

          <div class="pull-right">
            <button class="btn btn-default" id="daterange-btn">
              <span>
                <i class="fa fa-calendar"></i> Rango de fecha
              </span>
              <i class="fa fa-caret-down"></i>
            </button>

            <a href="index.php?ruta=ventas" class="btn btn-default">Mostrar Todo</a>
            <!--<a href="index.php?ruta=ventas&fechaInicial=<?php echo date('Y-m-d', strtotime('-1 day')); ?>&fechaFinal=<?php echo date('Y-m-d', strtotime('-1 day')); ?>" class="btn btn-default">Hoy</a>
            <a href="index.php?ruta=ventas&fechaInicial=<?php echo date('Y-m-d', strtotime('-2 day')); ?>&fechaFinal=<?php echo date('Y-m-d', strtotime('-2 day')); ?>" class="btn btn-default">Ayer</a>
            <a href="index.php?ruta=ventas&fechaInicial=<?php echo date('Y-m-01'); ?>&fechaFinal=<?php echo date('Y-m-d'); ?>" class="btn btn-default">Mes actual</a>-->
          </div>

   
        </div>

        <div class="box-body">

          <div class="tabla-ventas table-responsive">
            <table id="example" class="table table-bordered table-striped tablas display nowrap">
              
            <thead>
              <tr>
                <th style="width: 10px">#</th>
                <th>Código</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Forma de pago</th>
                <th>Imagen</th>
                <th>Neto</th>
                <th>Total</th>
                <th>Notas</th>
                <th>Fecha</th>
                <th>Acciones</th>
              </tr>             
            </thead>

              <tbody>

                <?php 

                  if (isset($_GET["fechaInicial"]) && isset($_GET["fechaFinal"])) {
                    $fechaInicial = $_GET["fechaInicial"];
                    $fechaFinal = $_GET["fechaFinal"];
                    echo "<p>Filtrando desde $fechaInicial hasta $fechaFinal</p>";
                  } else {
                    $fechaInicial = null;
                    $fechaFinal = null;
                    echo "<p>Mostrando todas las ventas</p>";
                  }

                  //$respuesta = ControladorVentas::ctrRangoFechasVentas($fechaInicial, $fechaFinal);
                  $respuesta = ControladorVentas::ctrRangoFechasVentasPorEstado($fechaInicial, $fechaFinal, "venta");


                  foreach ($respuesta as $key => $value) {
                    
                    echo '<tr>
                        <td>'.($key+1).'</td>

                         <td>'.$formatoCodigoVenta.$value["codigo"].'</td>';
 
                        $itemCliente = "id";
                        $valorCliente = $value["id_cliente"];
                        $respuestaCliente = ControladorClientes::ctrMostrarClientes($itemCliente, $valorCliente);

                          echo'<td>

                                  <span class="btnVerClienteDesdeVenta"
                                        data-toggle="modal"
                                        data-target="#modalEditarCliente"
                                        idCliente="'.$value["id_cliente"].'"
                                        style="cursor: pointer; color: #337ab7; text-decoration: underline;">
                                      '.$respuestaCliente["nombre"].'
                                  </span>
                              </td>'; 

                        $itemUsuario = "id";

                        $valorUsuario = $value["id_vendedor"];

                        $respuestaUsuario = ControladorUsuarios::ctrMostrarUsuarios($itemUsuario, $valorUsuario);

                        echo'<td>'.$respuestaUsuario["nombre"].'</td> 

                        <td>'.$moneda.' '.$value["metodo_pago"].'</td>';

                        // Validación de la foto
                      if($value["imagen"] != ""){
                            echo '<td><img src="'.$value["imagen"].'" class="img-thumbnail img-ampliar-venta" width="40px" style="cursor: pointer;" data-imagen="'.$value["imagen"].'" data-idventa="'.$value["id"].'"></td>';
                        }
                        else{
                            echo '<td><img src="vistas/img/ventas/default/sinventa.png" class="img-thumbnail img-ampliar-venta" width="40px" style="cursor: pointer;" data-imagen="vistas/img/ventas/default/sinventa.png" data-idventa="'.$value["id"].'"></td>';
                        }

                        echo '<td>'.$moneda.' '.number_format($value["neto"],2).'</td> 

                        <td>'.$moneda.' '.number_format($value["total"],2).'</td>

                        <td contenteditable="true" class="celda-nota" data-id="'.$value['id'].'">'.$value['notas'].'</td>
                        
                       <td>'.$value["fecha"];

                            // 1. Botón Eliminar - Solo Administrador
                            if ($_SESSION["perfil"] == "Administrador") {
                              echo '<button class="btn btn-danger btn-xs solo-movil btnEliminarVenta" style="float: right;" idVenta="'.$value["id"].'">
                                      <i class="fa fa-times"></i>
                                    </button>';
                            }

                            // 2. Botón Imprimir
                            echo '<button class="btn btn-info btn-xs solo-movil btnImprimirFactura" style="float: right;" codigoVenta="'.$value["codigo"].'">
                              <i class="fa fa-print"></i>
                            </button>
                            ';
                            
                            // 3. Botón Editar (Ver)
                            echo '<button class="btn btn-warning btn-xs solo-movil btnEditarVenta" style="float: right;" idVenta="'.$value["id"].'">
                              <i class="fa fa-eye"></i>
                            </button>';

                            echo '</td>


                        <td>
                          <div class="btn-group">

                            <a class="btn btn-success" href="index.php?ruta=ventas&xml=' . $value["codigo"] . '">xml</a>

                            <button class="btn btn-info btnImprimirFactura" codigoVenta="' . $value["codigo"] . '">
                              <i class="fa fa-print"></i>
                            </button>

                            <button class="btn btn-warning btnEditarVenta" idVenta="' . $value["id"] . '">
                              <i class="fa fa-eye"></i>
                            </button>';

                            if ($_SESSION["perfil"] == "Administrador") {
                              echo '<button class="btn btn-danger btnEliminarVenta" idVenta="' . $value["id"] . '">
                                      <i class="fa fa-times"></i>
                                    </button>';
                            }

                            echo '</div>
                        </td>


                      </tr>';
                  }
                ?>

              </tbody>

          </table>
          </div>

          <!-- CARDS PARA MÓVIL -->
          <div class="cards-ventas">

            <?php
            // Obtener ventas nuevamente para las cards
            $respuestaCards = ControladorVentas::ctrRangoFechasVentasPorEstado($fechaInicial, $fechaFinal, "venta");

            foreach ($respuestaCards as $key => $value) {

              // Obtener cliente
              $itemCliente = "id";
              $valorCliente = $value["id_cliente"];
              $respuestaCliente = ControladorClientes::ctrMostrarClientes($itemCliente, $valorCliente);

              // Obtener vendedor
              $itemUsuario = "id";
              $valorUsuario = $value["id_vendedor"];
              $respuestaUsuario = ControladorUsuarios::ctrMostrarUsuarios($itemUsuario, $valorUsuario);

              // Imagen
              $imagenVenta = !empty($value["imagen"]) ? $value["imagen"] : "vistas/img/ventas/default/sinventa.png";

              echo '<div class="card-venta">

                      <div class="card-venta-header">
                        <div class="card-venta-codigo">
                          🧾 '.$formatoCodigoVenta.$value["codigo"].'
                        </div>
                        <div class="card-venta-acciones">
                          <div class="btn-group">
                            <button class="btn btn-info btn-sm btnImprimirFactura" codigoVenta="'.$value["codigo"].'">
                              <i class="fa fa-print"></i>
                            </button>
                            <button class="btn btn-warning btn-sm btnEditarVenta" idVenta="'.$value["id"].'">
                              <i class="fa fa-eye"></i>
                            </button>';

              if ($_SESSION["perfil"] == "Administrador") {
                echo '<button class="btn btn-danger btn-sm btnEliminarVenta" idVenta="'.$value["id"].'">
                        <i class="fa fa-times"></i>
                      </button>';
              }

              echo '      </div>
                        </div>
                      </div>

                      <div class="card-venta-cliente">
                        👤 <span class="btnVerClienteDesdeVenta"
                                  data-toggle="modal"
                                  data-target="#modalEditarCliente"
                                  idCliente="'.$value["id_cliente"].'"
                                  style="cursor: pointer; color: #337ab7; text-decoration: underline;">
                            '.$respuestaCliente["nombre"].'
                          </span>
                      </div>

                      <div class="card-venta-total">
                        💵 Total: '.$moneda.' '.number_format($value["total"],2).'
                      </div>

                      <div class="card-venta-info">
                        <div class="card-venta-info-item">
                          <i class="fa fa-calendar"></i> '.$value["fecha"].'
                        </div>
                        <div class="card-venta-info-item">
                          <i class="fa fa-credit-card"></i> '.$value["metodo_pago"].'
                        </div>
                        <div class="card-venta-info-item">
                          <i class="fa fa-user"></i> Vendedor: '.$respuestaUsuario["nombre"].'
                        </div>
                        <div class="card-venta-info-item">
                          <i class="fa fa-money"></i> Neto: '.$moneda.' '.number_format($value["neto"],2).'
                        </div>
                      </div>

                      <div class="card-venta-imagen-icono img-ampliar-venta"
                           data-imagen="'.$imagenVenta.'"
                           data-idventa="'.$value["id"].'">
                        <i class="fa fa-image"></i> Ver comprobante
                      </div>';

              // Notas editables
              if(!empty($value["notas"])){
                echo '<div class="card-venta-notas editable celda-nota" data-id="'.$value["id"].'">
                        📝 '.$value["notas"].'
                      </div>';
              }

              echo '</div>';
            }
            ?>

          </div>


        <!-- Modal para ampliar/editar imagen de venta -->
        <div class="modal fade" id="modalAmpliarImagenVenta" tabindex="-1" role="dialog">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Imagen de la Venta</h4>
              </div>
              <div class="modal-body text-center">
                <img id="imagenVentaAmpliada" src="" class="img-responsive" style="max-width: 100%; margin: 0 auto; margin-bottom: 20px;">
                
                <hr>
                
                <div class="form-group">
                  <label>Cambiar Imagen de la Venta</label>
                  <input type="file" class="form-control nuevaImagenVenta" accept="image/*">
                  <p class="help-block">Peso máximo de la imagen 2MB</p>
                </div>
                
                <input type="hidden" id="idVentaImagen">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btnGuardarImagenVenta">Guardar Imagen</button>
              </div>
            </div>
          </div>
        </div>


          <?php
            $eliminarVenta = new ControladorVentas(); 
            $eliminarVenta -> ctrEliminarVenta();
          ?>

        </div>
      </div>
    </section>
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
        <h4 class="modal-title">Ver cliente</h4>

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
              <input type="text" class="form-control input-lg" name="editarCliente" id="editarCliente" readonly>
              <input type="hidden" id="idCliente" name="idCliente">
             </div>
           </div>

            <!-- entrada para documento ID -->            
            <div class="form-group">          
            <div class="input-group">              
              <span class="input-group-addon"><i class="fa fa-key"></i></span>
              <input type="number" min="0" class="form-control input-lg" name="editarDocumentoId" id="editarDocumentoId" placeholder="Documento" readonly>
             </div>
           </div>

           <!-- entrada para telefono -->            
           <div class="form-group">          
          <div class="input-group">            
            <span class="input-group-addon"><i class="fa fa-phone"></i></span>
            <input type="text" class="form-control input-lg" name="editarTelefono"  id="editarTelefono" data-inputmask="'mask':'(999) 999-9999'" data-mask placeholder="Celular" readonly>
           </div>
         </div>

           <!-- entrada para Email -->            
            <div class="form-group">          
            <div class="input-group">              
              <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
              <!--<input type="email" class="form-control input-lg" name="editarEmail" id="editarEmail" required>-->
              <input type="email" class="form-control input-lg" name="editarEmail" id="editarEmail" placeholder="Correo Electrónico" readonly>
             </div>
           </div>

           <!-- entrada para la departamento -->            
           <div class="form-group">          
          <div class="input-group">            
            <span class="input-group-addon"><i class="fa fa-building"></i></span>
            <input type="text" class="form-control input-lg" name="editarDepartamento" id="editarDepartamento" placeholder="Departamento" readonly>
           </div>
         </div>

         <!-- entrada para la ciudad -->            
         <div class="form-group">          
          <div class="input-group">            
            <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
            <input type="text" class="form-control input-lg" name="editarCiudad" id="editarCiudad" placeholder="Ciudad" readonly>
           </div>
         </div>

           <!-- entrada para la direccion -->            
            <div class="form-group">          
            <div class="input-group">              
              <span class="input-group-addon"><i class="fa fa-home"></i></span>
              <input type="text" class="form-control input-lg" name="editarDireccion" id="editarDireccion" placeholder="Dirección" required readonly>
             </div>
           </div>

          <!-- entrada para estado -->
         <div class="form-group">
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-flag"></i></span>
            <input type="text" 
                  class="form-control input-lg" 
                  id="editarEstado" 
                  name="editarEstado" 
                  readonly
                  style="background-color: #f4f4f4; cursor: not-allowed;">
          </div>
        </div>

           <!-- entrada para nota -->            
           <div class="form-group">          
            <div class="input-group">              
              <span class="input-group-addon"><i class="fa fa-pencil-square-o"></i></span>
              <input type="text" class="form-control input-lg" name="editarNota" id="editarNota" placeholder="Notas" readonly>
            </div>

          </div>
         </div>
       </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <!--<button type="submit" class="btn btn-primary">Guardar cambios</button>-->
        </div>

     </form>
    </div>
  </div>
</div>


<!--Ruta Clientes.js-->
<script src="vistas/js/ventas.js"></script>

<!-- DateRangePicker -->
<script src="vistas/bower_components/moment/min/moment.min.js"></script>
<script src="vistas/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>


<!-- Filtro de Fechas -->
<script>
$('#daterange-btn').daterangepicker(
  {
    ranges   : {
      'Hoy'       : [moment(), moment()],
      'Ayer'      : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Últimos 7 días' : [moment().subtract(6, 'days'), moment()],
      'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
      'Este mes'  : [moment().startOf('month'), moment().endOf('month')],
      'Mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    startDate: moment(),
    endDate  : moment()
  },
  function (start, end) {
    var fechaInicial = start.format('YYYY-MM-DD');
    //var fechaFinal = end.format('YYYY-MM-DD');
    var fechaFinal = end.endOf('day').format('YYYY-MM-DD HH:mm:ss');
    var fechaInicial = start.startOf('day').format('YYYY-MM-DD HH:mm:ss');

    var nuevaURL = 'index.php?ruta=ventas&fechaInicial=' + encodeURIComponent(fechaInicial) + '&fechaFinal=' + encodeURIComponent(fechaFinal);
    //var nuevaURL = 'index.php?ruta=ventas&fechaInicial=' + fechaInicial + '&fechaFinal=' + fechaFinal;
    window.location.href = nuevaURL;
  }
);
</script>

<!--Guarddar notas-->
<script>
$(document).on('blur', '.celda-nota', function() {
  const idVenta = $(this).data('id');
  const nuevaNota = $(this).text().trim();

  console.log("Guardando nota:", nuevaNota, "para ID:", idVenta); // <== prueba

  $.ajax({
    url: "ajax/datatable-ventas.ajax.php",
    method: "POST",
    data: {
      idVentaNota: idVenta,
      nuevaNota: nuevaNota
    },
    success: function(respuesta) {
      console.log("Respuesta del servidor:", respuesta);
    },
    error: function() {
      alert("Hubo un error al guardar la nota.");
    }
  });
});
</script>


<!-- Ampliar foto -->
<script>
// Ampliar imagen de venta al hacer clic
$(document).on("click", ".img-ampliar-venta", function(){
    var rutaImagen = $(this).attr("data-imagen");
    
    // Buscar el ID de la venta en la misma fila
    var idVenta = $(this).closest("tr").find("td:last").text(); // Ajusta según tu estructura
    
    // Si tienes un botón con el ID, usa esto:
    // var idVenta = $(this).closest("tr").find(".btnEliminarVenta").attr("idVenta");
    
    console.log("ID Venta:", idVenta); // Para debug
    console.log("Ruta Imagen:", rutaImagen); // Para debug
    
    $("#imagenVentaAmpliada").attr("src", rutaImagen);
    $("#idVentaImagen").val(idVenta);
    $(".nuevaImagenVenta").val(""); // Limpiar el input file
    $("#modalAmpliarImagenVenta").modal("show");
});

// Previsualizar nueva imagen cuando se selecciona
$(".nuevaImagenVenta").change(function(){
    var imagen = this.files[0];
    
    if(imagen){
        if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){
            $(".nuevaImagenVenta").val("");
            swal({
                title: "Error al subir la imagen",
                text: "¡La imagen debe estar en formato JPG o PNG!",
                type: "error",
                confirmButtonText: "¡Cerrar!"
            });
        }else if(imagen["size"] > 2000000){
            $(".nuevaImagenVenta").val("");
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
                $("#imagenVentaAmpliada").attr("src", rutaImagen);
            });
        }
    }
});

// Guardar la nueva imagen de la venta
$(document).on("click", ".btnGuardarImagenVenta", function(){
    
    var idVenta = $("#idVentaImagen").val();
    var imagen = $(".nuevaImagenVenta")[0].files[0];
    
    console.log("ID al guardar:", idVenta); // Para debug
    console.log("Imagen al guardar:", imagen); // Para debug
    
    if(!imagen){
        swal({
            title: "Advertencia",
            text: "No has seleccionado ninguna imagen",
            type: "warning",
            confirmButtonText: "¡Cerrar!"
        });
        return;
    }
    
    if(!idVenta){
        swal({
            title: "Error",
            text: "No se pudo obtener el ID de la venta",
            type: "error",
            confirmButtonText: "¡Cerrar!"
        });
        return;
    }
    
    var datos = new FormData();
    datos.append("idVentaImagen", idVenta);
    datos.append("nuevaImagenVenta", imagen);
    
    // Mostrar loading
    swal({
        title: 'Cargando...',
        allowOutsideClick: false,
        onBeforeOpen: () => {
            swal.showLoading()
        }
    });
    
    $.ajax({
        url: "ajax/ventas.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(respuesta){
            console.log("Respuesta del servidor:", respuesta); // Para debug
            
            if(respuesta == "ok"){
                swal({
                    type: "success",
                    title: "¡La imagen ha sido actualizada correctamente!",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then(function(result){
                    if(result.value){
                        $("#modalAmpliarImagenVenta").modal("hide");
                        window.location = "ventas";
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
            console.log("Error AJAX:", textStatus, errorThrown); // Para debug
            console.log("Respuesta:", jqXHR.responseText); // Para debug
            
            swal({
                type: "error",
                title: "Error en la petición",
                text: "Por favor revisa la consola para más detalles",
                confirmButtonText: "Cerrar"
            });
        }
    });
});


// Ampliar imagen de venta al hacer clic
$(document).on("click", ".img-ampliar-venta", function(){
    var rutaImagen = $(this).attr("data-imagen");
    var idVenta = $(this).attr("data-idventa");
    
    console.log("ID Venta:", idVenta); // Para debug
    console.log("Ruta Imagen:", rutaImagen); // Para debug
    
    $("#imagenVentaAmpliada").attr("src", rutaImagen);
    $("#idVentaImagen").val(idVenta);
    $(".nuevaImagenVenta").val("");
    $("#modalAmpliarImagenVenta").modal("show");
});
</script>



<!-- Abrir modal de clientes desde ordenes -->
<script>
$("#example").on("click", ".btnVerClienteDesdeVenta", function(){
    
    var idCliente = $(this).attr("idCliente");
    
    var datos = new FormData();
    datos.append("idCliente", idCliente);

    $.ajax({
        url: "ajax/clientes.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "text",
        success: function(respuesta){
            
            // Extraer solo el JSON
            var jsonStart = respuesta.indexOf('{');
            var jsonString = respuesta.substring(jsonStart);
            var data = JSON.parse(jsonString);
            
            // Llenar el modal
            $("#idCliente").val(data["id"]);
            $("#editarCliente").val(data["nombre"]);
            $("#editarDocumentoId").val(data["documento"]);
            $("#editarEmail").val(data["email"]);
            $("#editarTelefono").val(data["telefono"]);
            $("#editarDireccion").val(data["direccion"]);
            $("#editarNotas").val(data["notas"]);
            
            // AGREGAR ESTA LÍNEA para preseleccionar el estado
            $("#editarEstado").val(data["estatus"]);
            
            // Si tienes más campos, agrégalos aquí:
            $("#editarDepartamento").val(data["departamento"]);
            $("#editarCiudad").val(data["ciudad"]);
            
            // Abrir el modal
            $('#modalEditarCliente').modal('show');
        }
    });
});
</script>