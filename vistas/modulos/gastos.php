<?php
// Obtener configuración del sistema
$configuracion = ControladorConfiguracion::ctrObtenerConfiguracion();
$mediosPago = !empty($configuracion["medios_pago"]) ? explode(",", $configuracion["medios_pago"]) : array("Efectivo", "Tarjeta Débito", "Tarjeta Crédito", "Nequi", "Bancolombia", "Cheque");
?>

<!-- Estilos responsive -->
<style>
/* Cards para móvil */
.cards-gastos {
  display: none;
}

.card-gasto {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 15px;
  padding: 15px;
  position: relative;
  border-left: 4px solid #3c8dbc;
}

.card-gasto.gasto-hoy {
  border-left: 6px solid #28a745 !important;
  background-color: #f0f9f4;
  box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}

.card-gasto-header {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  margin-bottom: 10px;
}

.card-gasto-concepto {
  font-size: 18px;
  font-weight: bold;
  color: #333;
  margin-bottom: 15px;
}

.card-gasto-detalles {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 10px;
}

.card-gasto-imagen-icono {
  display: inline-block;
  padding: 5px 10px;
  background: #3c8dbc;
  color: white;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
  margin-top: 5px;
}

.card-gasto-imagen-icono:hover {
  background: #2e6da4;
}

.card-gasto-fecha {
  color: #666;
  font-size: 14px;
}

.card-gasto-monto {
  font-size: 20px;
  font-weight: bold;
  color: #3c8dbc;
}

.card-gasto-categoria {
  margin: 5px 0;
}

.card-gasto-proveedor {
  color: #666;
  font-size: 14px;
}

/* Responsive */
@media (max-width: 767px) {
  .tabla-gastos {
    display: none !important;
  }
  .cards-gastos {
    display: block !important;
  }
}

@media (min-width: 768px) {
  .tabla-gastos {
    display: block !important;
  }
  .cards-gastos {
    display: none !important;
  }
}

.solo-movil {
  display: none;
}

@media (max-width: 767px) {
  .solo-movil {
    display: inline-block !important;
    margin-left: 3px !important;
  }
}

</style>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Administrar gastos
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Administrar gastos</li>
    </ol>
  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">

        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarGasto">
          <i class="fa fa-plus"></i> Agregar gasto
        </button>

        <button class="btn btn-default" data-toggle="modal" data-target="#modalGestionarCategorias">
          <i class="fa fa-tags"></i> Gestionar categorías
        </button>

      </div>

      <!-- FILTROS -->
      <div class="box-body">

        <div class="row">

          <div class="col-md-2">
            <div class="form-group">
              <label>Fecha inicio:</label>
              <input type="date" class="form-control" id="filtroFechaInicio">
            </div>
          </div>

          <div class="col-md-2">
            <div class="form-group">
              <label>Fecha fin:</label>
              <input type="date" class="form-control" id="filtroFechaFin">
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group">
              <label>Categoría:</label>
              <select class="form-control" id="filtroCategoria">
                <option value="">Todas las categorías</option>
                <?php
                  $categorias = ControladorCategoriasGastos::ctrMostrarCategoriasGastos(null, null);
                  foreach ($categorias as $key => $value) {
                    echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                  }
                ?>
              </select>
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group">
              <label>Proveedor:</label>
              <select class="form-control" id="filtroProveedor">
                <option value="">Todos los proveedores</option>
                <?php
                  $proveedores = ControladorProveedores::ctrMostrarProveedores(null, null);
                  foreach ($proveedores as $key => $value) {
                    echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                  }
                ?>
              </select>
            </div>
          </div>

          <div class="col-md-2">
            <div class="form-group">
              <label>&nbsp;</label>
              <button type="button" class="btn btn-info btn-block" id="btnFiltrarGastos">
                <i class="fa fa-search"></i> Filtrar
              </button>
            </div>
          </div>

        </div>

      </div>

      <div class="box-body">

        <div class="tabla-gastos table-responsive">
          <table id="tablaGastos" class="table table-bordered table-striped tablas1">

          <thead>
            <tr>
              <th style="width: 10px">#</th>
              <th>Concepto</th>
              <th>Fecha</th>
              <th>Monto</th>
              <th>Categoría</th>
               <th>Proveedor</th>
              <th>Imagen</th>
              <th>Acciones</th>
            </tr>
          </thead>

          <tbody>

            <?php

              $item = null;
              $valor = null;

              $gastos = ControladorGastos::ctrMostrarGastos($item, $valor);

              foreach ($gastos as $key => $value) {

                // Preparar badge de categoría
                $categoriaBadge = '';
                if(!empty($value["categoria_nombre"])){
                  $categoriaBadge = '<span class="badge" style="background-color: '.$value["categoria_color"].'">'.$value["categoria_nombre"].'</span>';
                } else {
                  $categoriaBadge = '-';
                }

                // Verificar si el gasto es de hoy para resaltarlo
                $fechaHoy = date('Y-m-d');

                $esHoy = (!empty($value["fecha"]) && $value["fecha"] == $fechaHoy);

                $rowStyle = $esHoy ? 'style="border-left: 6px solid #28a745 !important; background-color: #f0f9f4; box-shadow: inset 6px 0 0 #28a745;"' : '';

                echo '<tr '.$rowStyle.'>';

                // Columna 1: Número
                echo '<td>'.($key+1).'</td>';

                // Columna 2: Concepto
                echo '<td>'.$value["concepto"].'</td>';

                // Columna 3: Fecha
                $fecha = !empty($value["fecha"]) ? date("d/m/Y", strtotime($value["fecha"])) : '-';
                echo '<td>'.$fecha.'</td>';
 
                // Columna 4: Monto
                $monto = !empty($value["monto"]) ? '$'.number_format($value["monto"], 2, ',', '.') : '-';
                echo '<td><strong>'.$monto.'</strong></td>';
 
                // Columna 5: Categoría
                echo '<td>'.$categoriaBadge.'</td>';

               // Columna 6: Proveedor
                $proveedor = !empty($value["proveedor_nombre"]) ? $value["proveedor_nombre"] : '-';
                echo '<td>'.$proveedor.'</td>';
 
                // Columna 7: Imagen
                if(!empty($value["imagen_comprobante"])){
                  echo '<td><img src="'.$value["imagen_comprobante"].'" class="img-thumbnail img-comprobante-clickeable" width="40px" style="cursor: pointer;" data-imagen="'.$value["imagen_comprobante"].'" data-idgasto="'.$value["id"].'" data-concepto="'.$value["concepto"].'"></td>';
                } else {
                  echo '<td><img src="vistas/img/gastos/default/sin-imagen.png" class="img-thumbnail img-comprobante-clickeable" width="40px" style="cursor: pointer;" data-imagen="" data-idgasto="'.$value["id"].'" data-concepto="'.$value["concepto"].'"></td>';
                } 

                // Columna 8: Acciones
                echo '<td>
                  <div class="btn-group">
                    <button class="btn btn-warning btnEditarGasto" idGasto="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarGasto"><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-danger btnEliminarGasto" idGasto="'.$value["id"].'" codigoGasto="'.$value["codigo"].'" conceptoGasto="'.$value["concepto"].'"><i class="fa fa-times"></i></button>
                  </div>
                </td>

              </tr>';
            }
            ?>

          </tbody>

        </table>
        </div>

        <!-- CARDS PARA MÓVIL -->
        <div class="cards-gastos">

          <?php
          foreach ($gastos as $key => $value) {

            // Preparar badge de categoría
            $categoriaBadge = '';
            if(!empty($value["categoria_nombre"])){
              $categoriaBadge = '<span class="badge" style="background-color: '.$value["categoria_color"].'">'.$value["categoria_nombre"].'</span>';
            } else {
              $categoriaBadge = '<span class="text-muted">Sin categoría</span>';
            }

            // Verificar si el gasto es de hoy para resaltarlo
            $fechaHoy = date('Y-m-d');
            $esHoy = (!empty($value["fecha"]) && $value["fecha"] == $fechaHoy);
            $claseHoy = $esHoy ? ' gasto-hoy' : '';

            // Formatear datos
            $fecha = !empty($value["fecha"]) ? date("d/m/Y", strtotime($value["fecha"])) : '-';
            $monto = !empty($value["monto"]) ? '$'.number_format($value["monto"], 2, ',', '.') : '-';
            $proveedor = !empty($value["proveedor_nombre"]) ? $value["proveedor_nombre"] : 'Sin proveedor';

            echo '<div class="card-gasto'.$claseHoy.'">

                    <div class="card-gasto-header">
                      <div class="btn-group">
                        <button class="btn btn-warning btn-sm btnEditarGasto" idGasto="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarGasto">
                          <i class="fa fa-pencil"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btnEliminarGasto" idGasto="'.$value["id"].'" codigoGasto="'.$value["codigo"].'" conceptoGasto="'.$value["concepto"].'">
                          <i class="fa fa-times"></i>
                        </button>
                      </div>
                    </div>

                    <div class="card-gasto-concepto">
                      💰 '.$value["concepto"].'
                    </div>

                    <div class="card-gasto-detalles">
                      <div class="card-gasto-fecha">
                        <i class="fa fa-calendar"></i> '.$fecha.'
                      </div>
                      <div class="card-gasto-monto">
                        <i class="fa fa-money"></i> '.$monto.'
                      </div>
                      <div class="card-gasto-categoria">
                        '.$categoriaBadge.'
                      </div>
                      <div class="card-gasto-proveedor">
                        <i class="fa fa-user"></i> '.$proveedor.'
                      </div>
                    </div>';

            // Icono de imagen clickeable
            $imagenGasto = !empty($value["imagen_comprobante"]) ? $value["imagen_comprobante"] : "";

            echo '<div class="card-gasto-imagen-icono img-comprobante-clickeable"
                       data-imagen="'.$imagenGasto.'"
                       data-idgasto="'.$value["id"].'"
                       data-concepto="'.$value["concepto"].'">
                    <i class="fa fa-image"></i> Ver comprobante
                  </div>

                  </div>';
          }
          ?>

        </div>

      </div>

    </div>

  </section>

</div>

<!--=====================================
MODAL AGREGAR GASTO
======================================-->

<div id="modalAgregarGasto" class="modal fade" role="dialog">

  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#3c8dbc; color: white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Gasto</h4>
        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <div class="row">

              <!-- Concepto -->
              <div class="col-md-6">
                <div class="form-group">
                  <label>Concepto *</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-file-text"></i></span>
                    <input type="text" class="form-control" name="nuevoConceptoGasto" placeholder="Concepto del gasto" required>
                  </div>
                </div>
              </div>

              <!-- Monto -->
              <div class="col-md-6">
                <div class="form-group">
                  <label>Monto *</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                    <input type="number" class="form-control" name="nuevoMontoGasto" placeholder="0" min="0" step="0.01" required>
                  </div>
                </div>
              </div>

            </div>

            <div class="row">

              <!-- Fecha -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>Fecha *</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="date" class="form-control" name="nuevaFechaGasto" value="<?php echo date('Y-m-d'); ?>" required>
                  </div>
                </div>
              </div>

              <!-- Categoría -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>Categoría *</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                    <select class="form-control" name="nuevaCategoriaGasto" required>
                      <option value="">Seleccionar categoría</option>
                      <?php
                        $categorias = ControladorCategoriasGastos::ctrMostrarCategoriasGastos(null, null);
                        foreach ($categorias as $key => $value) {
                          echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Proveedor -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>Proveedor</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-truck"></i></span>
                    <select class="form-control" name="nuevoProveedorGasto">
                      <option value="">Sin proveedor</option>
                      <?php
                        $proveedores = ControladorProveedores::ctrMostrarProveedores(null, null);
                        foreach ($proveedores as $key => $value) {
                          echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
              </div>

            </div>

            <div class="row">

              <!-- Método de Pago -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>Método de Pago *</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
                    <select class="form-control" name="nuevoMetodoPagoGasto" required>
                      <?php
                        foreach($mediosPago as $medio){
                          $medio = trim($medio); // Eliminar espacios en blanco
                          echo '<option value="'.$medio.'">'.$medio.'</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Número de Comprobante -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>N° Comprobante</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-barcode"></i></span>
                    <input type="text" class="form-control" name="nuevoNumeroComprobante" placeholder="Número de comprobante">
                  </div>
                </div>
              </div>

              <!-- Estado -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>Estado *</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-check"></i></span>
                    <select class="form-control" name="nuevoEstadoGasto" required>
                      <option value="aprobado">Aprobado</option>
                      <option value="pendiente">Pendiente</option>
                      <option value="rechazado">Rechazado</option>
                    </select>
                  </div>
                </div>
              </div>

            </div>

            <!-- Imagen Comprobante -->
            <div class="form-group">
              <label>Imagen Comprobante</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-image"></i></span>
                <input type="file" class="form-control" name="nuevaImagenComprobante" accept="image/*">
              </div>
            </div>

            <!-- Notas -->
            <div class="form-group">
              <label>Notas</label>
              <textarea class="form-control" name="nuevasNotasGasto" rows="3" placeholder="Notas adicionales"></textarea>
            </div>

          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar gasto</button>
        </div>

        <?php

          $crearGasto = new ControladorGastos();
          $crearGasto -> ctrCrearGasto();

        ?>

      </form>

    </div>

  </div>

</div>

<!--=====================================
MODAL EDITAR GASTO
======================================-->

<div id="modalEditarGasto" class="modal fade" role="dialog">

  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#3c8dbc; color: white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar Gasto</h4>
        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <div class="row">

              <!-- Concepto -->
              <div class="col-md-6">
                <div class="form-group">
                  <label>Concepto *</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-file-text"></i></span>
                    <input type="text" class="form-control" name="editarConceptoGasto" id="editarConceptoGasto" required>
                    <input type="hidden" id="idGasto" name="idGasto">
                  </div>
                </div>
              </div>

              <!-- Monto -->
              <div class="col-md-6">
                <div class="form-group">
                  <label>Monto *</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                    <input type="number" class="form-control" name="editarMontoGasto" id="editarMontoGasto" min="0" step="0.01" required>
                  </div>
                </div>
              </div>

            </div>

            <div class="row">

              <!-- Fecha -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>Fecha *</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="date" class="form-control" name="editarFechaGasto" id="editarFechaGasto" required>
                  </div>
                </div>
              </div>

              <!-- Categoría -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>Categoría *</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                    <select class="form-control" name="editarCategoriaGasto" id="editarCategoriaGasto" required>
                      <option value="">Seleccionar categoría</option>
                      <?php
                        $categorias = ControladorCategoriasGastos::ctrMostrarCategoriasGastos(null, null);
                        foreach ($categorias as $key => $value) {
                          echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Proveedor -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>Proveedor</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-truck"></i></span>
                    <select class="form-control" name="editarProveedorGasto" id="editarProveedorGasto">
                      <option value="">Sin proveedor</option>
                      <?php
                        $proveedores = ControladorProveedores::ctrMostrarProveedores(null, null);
                        foreach ($proveedores as $key => $value) {
                          echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
              </div>

            </div>

            <div class="row">

              <!-- Método de Pago -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>Método de Pago *</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
                    <select class="form-control" name="editarMetodoPagoGasto" id="editarMetodoPagoGasto" required>
                      <option value="Efectivo">Efectivo</option>
                      <option value="Transferencia">Transferencia</option>
                      <option value="Tarjeta">Tarjeta</option>
                      <option value="Cheque">Cheque</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Número de Comprobante -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>N° Comprobante</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-barcode"></i></span>
                    <input type="text" class="form-control" name="editarNumeroComprobante" id="editarNumeroComprobante">
                  </div>
                </div>
              </div>

              <!-- Estado -->
              <div class="col-md-4">
                <div class="form-group">
                  <label>Estado *</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-check"></i></span>
                    <select class="form-control" name="editarEstadoGasto" id="editarEstadoGasto" required>
                      <option value="aprobado">Aprobado</option>
                      <option value="pendiente">Pendiente</option>
                      <option value="rechazado">Rechazado</option>
                    </select>
                  </div>
                </div>
              </div>

            </div>

            <!-- Imagen Comprobante -->
            <div class="form-group">
              <label>Imagen Comprobante</label>
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-image"></i></span>
                <input type="file" class="form-control" name="editarImagenComprobante" accept="image/*">
              </div>
              <input type="hidden" name="imagenActual" id="imagenActual">
              <div id="previsualizarImagen" style="margin-top: 10px;"></div>
            </div>

            <!-- Notas -->
            <div class="form-group">
              <label>Notas</label>
              <textarea class="form-control" name="editarNotasGasto" id="editarNotasGasto" rows="3"></textarea>
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

        <?php

          $editarGasto = new ControladorGastos();
          $editarGasto -> ctrEditarGasto();

        ?>

      </form>

    </div>

  </div>

</div>

<!--=====================================
MODAL GESTIONAR CATEGORÍAS
======================================-->

<div id="modalGestionarCategorias" class="modal fade" role="dialog">

  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <!--=====================================
      CABEZA DEL MODAL
      ======================================-->

      <div class="modal-header" style="background:#3c8dbc; color: white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Gestionar Categorías de Gastos</h4>
      </div>

      <!--=====================================
      CUERPO DEL MODAL
      ======================================-->

      <div class="modal-body">

        <!-- Formulario agregar categoría -->
        <div class="panel panel-primary">
          <div class="panel-heading">
            <h3 class="panel-title">Agregar Nueva Categoría</h3>
          </div>
          <div class="panel-body">
            <form role="form" method="post" id="formAgregarCategoria">
              <div class="row">
                <div class="col-md-5">
                  <div class="form-group">
                    <input type="text" class="form-control" name="nombreCategoriaGasto" placeholder="Nombre de la categoría *" required>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <input type="color" class="form-control" name="colorCategoriaGasto" value="#3c8dbc">
                  </div>
                </div>
                <div class="col-md-4">
                  <button type="submit" class="btn btn-primary btn-block">
                    <i class="fa fa-plus"></i> Agregar
                  </button>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <textarea class="form-control" name="descripcionCategoriaGasto" rows="2" placeholder="Descripción (opcional)"></textarea>
                  </div>
                </div>
              </div>

              <?php
                $crearCategoria = new ControladorCategoriasGastos();
                $crearCategoria -> ctrCrearCategoriaGasto();
              ?>

            </form>
          </div>
        </div>

        <!-- Lista de categorías -->
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Categorías Existentes</h3>
          </div>
          <div class="panel-body">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nombre</th>
                  <th>Color</th>
                  <th>Descripción</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $categorias = ControladorCategoriasGastos::ctrMostrarCategoriasGastos(null, null);
                  foreach ($categorias as $key => $value) {
                    echo '<tr>
                      <td>'.($key+1).'</td>
                      <td><span class="badge" style="background-color: '.$value["color"].'">'.$value["nombre"].'</span></td>
                      <td><input type="color" value="'.$value["color"].'" disabled style="width: 50px;"></td>
                      <td>'.$value["descripcion"].'</td>
                      <td>
                        <button class="btn btn-warning btn-xs btnEditarCategoriaGasto" idCategoria="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarCategoria"><i class="fa fa-pencil"></i></button>
                        <button class="btn btn-danger btn-xs btnEliminarCategoriaGasto" idCategoria="'.$value["id"].'" nombreCategoria="'.$value["nombre"].'"><i class="fa fa-times"></i></button>
                      </td>
                    </tr>';
                  }
                ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <!--=====================================
      PIE DEL MODAL
      ======================================-->

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>

    </div>

  </div>

</div>

<!--=====================================
MODAL EDITAR CATEGORÍA
======================================-->

<div id="modalEditarCategoria" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#3c8dbc; color: white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar Categoría</h4>
        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <div class="form-group">
              <label>Nombre *</label>
              <input type="text" class="form-control" name="editarNombreCategoriaGasto" id="editarNombreCategoriaGasto" required>
              <input type="hidden" name="idCategoriaGasto" id="idCategoriaGasto">
            </div>

            <div class="form-group">
              <label>Color</label>
              <input type="color" class="form-control" name="editarColorCategoriaGasto" id="editarColorCategoriaGasto">
            </div>

            <div class="form-group">
              <label>Descripción</label>
              <textarea class="form-control" name="editarDescripcionCategoriaGasto" id="editarDescripcionCategoriaGasto" rows="3"></textarea>
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

        <?php

          $editarCategoria = new ControladorCategoriasGastos();
          $editarCategoria -> ctrEditarCategoriaGasto();

        ?>

      </form>

    </div>

  </div>

</div>

<!--=====================================
MODAL VER COMPROBANTE
======================================-->

<div id="modalVerComprobante" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header" style="background:#3c8dbc; color: white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Comprobante</h4>
      </div>

      <div class="modal-body">
        <img id="imagenComprobante" src="" style="width: 100%; height: auto;">
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>

    </div>

  </div>

</div>


<!--=====================================
MODAL AMPLIAR Y EDITAR IMAGEN COMPROBANTE
======================================-->

<div id="modalAmpliarComprobanteGasto" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background:#3c8dbc; color: white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Comprobante de Gasto</h4>
      </div>
      <div class="modal-body text-center">
        <img id="imagenComprobanteAmpliada" src="" class="img-responsive" style="max-width: 100%; margin: 0 auto; margin-bottom: 20px;">
        <hr>
        <div class="form-group">
          <label>Cambiar Imagen del Comprobante</label>
          <input type="file" class="form-control nuevaImagenComprobante" accept="image/*">
          <p class="help-block">Peso máximo de la imagen 2MB</p>
        </div>
        <input type="hidden" id="idGastoImagen">
        <input type="hidden" id="conceptoGasto">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary btnGuardarImagenComprobante">Guardar Imagen</button>
      </div>
    </div>
  </div>
</div>

<!-- Script para ampliar imagen del comprobante desde modal editar -->
<script>
$(document).on("click", ".img-ampliar-gasto", function(){
    var rutaImagen = $(this).attr("src");
    $("#imagenComprobanteAmpliada").attr("src", rutaImagen);
    $("#modalAmpliarComprobanteGasto").modal("show");
});
</script>

<!--=====================================
SCRIPT AMPLIAR Y EDITAR IMAGEN DESDE LA TABLA
======================================-->
<script>
    // Ampliar imagen de comprobante al hacer clic desde la tabla
    $(document).on("click", ".img-comprobante-clickeable", function(){
        var rutaImagen = $(this).attr("data-imagen");
        var idGasto = $(this).attr("data-idgasto");
        var concepto = $(this).attr("data-concepto");

        // Si no hay imagen, mostrar placeholder
        if(!rutaImagen || rutaImagen === ""){
            rutaImagen = "vistas/img/gastos/default/sin-imagen.png";
        }

        console.log("ID Gasto:", idGasto);
        console.log("Concepto:", concepto);
        console.log("Ruta Imagen:", rutaImagen);

        $("#imagenComprobanteAmpliada").attr("src", rutaImagen);
        $("#idGastoImagen").val(idGasto);
        $("#conceptoGasto").val(concepto);
        $(".nuevaImagenComprobante").val("");
        $("#modalAmpliarComprobanteGasto").modal("show");
    });

    // Previsualizar nueva imagen cuando se selecciona
    $(".nuevaImagenComprobante").change(function(){
        var imagen = this.files[0];

        if(imagen){
            if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){
                $(".nuevaImagenComprobante").val("");
                swal({
                    title: "Error al subir la imagen",
                    text: "¡La imagen debe estar en formato JPG o PNG!",
                    type: "error",
                    confirmButtonText: "¡Cerrar!"
                });
            }else if(imagen["size"] > 2000000){
                $(".nuevaImagenComprobante").val("");
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
                    $("#imagenComprobanteAmpliada").attr("src", rutaImagen);
                });
            }
        }
    });

    // Guardar la nueva imagen del comprobante
    $(document).on("click", ".btnGuardarImagenComprobante", function(){

        var idGasto = $("#idGastoImagen").val();
        var concepto = $("#conceptoGasto").val();
        var imagen = $(".nuevaImagenComprobante")[0].files[0];

        console.log("ID al guardar:", idGasto);
        console.log("Concepto al guardar:", concepto);
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

        if(!idGasto){
            swal({
                title: "Error",
                text: "No se pudo obtener el ID del gasto",
                type: "error",
                confirmButtonText: "¡Cerrar!"
            });
            return;
        }

        var datos = new FormData();
        datos.append("idGastoImagen", idGasto);
        datos.append("conceptoGasto", concepto);
        datos.append("nuevaImagenComprobante", imagen);

        // Mostrar loading
        swal({
            title: 'Cargando...',
            allowOutsideClick: false,
            onBeforeOpen: () => {
                swal.showLoading()
            }
        });

        $.ajax({
            url: "ajax/gastos.ajax.php",
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
                            window.location = "gastos";
                        }
                    });
                } else {
                    swal({
                        type: "error",
                        title: "Error al actualizar la imagen",
                        text: respuesta,
                        confirmButtonText: "¡Cerrar!"
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.error("Error en AJAX:", textStatus, errorThrown);
                console.error("Respuesta:", jqXHR.responseText);
                swal({
                    type: "error",
                    title: "Error de conexión",
                    text: "No se pudo conectar con el servidor",
                    confirmButtonText: "¡Cerrar!"
                });
            }
        });
    });
</script>


<?php

  $borrarGasto = new ControladorGastos();
  $borrarGasto -> ctrEliminarGasto();

  $borrarCategoria = new ControladorCategoriasGastos();
  $borrarCategoria -> ctrEliminarCategoriaGasto();

?>