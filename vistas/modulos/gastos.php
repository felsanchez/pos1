<?php
// Obtener configuración del sistema
$configuracion = ControladorConfiguracion::ctrObtenerConfiguracion();
$mediosPago = !empty($configuracion["medios_pago"]) ? explode(",", $configuracion["medios_pago"]) : array("Efectivo", "Tarjeta Débito", "Tarjeta Crédito", "Nequi", "Bancolombia", "Cheque");
?>

<!-- Estilos responsive -->
<style>
@media (max-width: 767px) {
  .tablas1 td,
  .tablas1 th {
      display: none;
  }

  .tablas1 td:nth-child(2),
  .tablas1 td:nth-child(4),
  .tablas1 td:nth-child(7),
  .tablas1 td:nth-child(12),
  .tablas1 th:nth-child(2),
  .tablas1 th:nth-child(4),
  .tablas1 th:nth-child(7),
  .tablas1 th:nth-child(12) {
      display: table-cell;
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

      <div class="box-body table-responsive">

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
                  echo '<td><img src="'.$value["imagen_comprobante"].'" class="img-thumbnail img-comprobante-clickeable" width="40px" style="cursor: pointer;"></td>';
                } else {
                  echo '<td>-</td>';
                } 

                // Columna 8: Acciones
                echo '<td>
                  <div class="btn-group">
                    <button class="btn btn-warning btnEditarGasto" idGasto="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarGasto"><i class="fa fa-pencil"></i></button>
                    <button class="btn btn-danger btnEliminarGasto" idGasto="'.$value["id"].'" codigoGasto="'.$value["codigo"].'"><i class="fa fa-times"></i></button>
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
MODAL AMPLIAR IMAGEN COMPROBANTE
======================================--> 

<div id="modalAmpliarComprobanteGasto" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background:#3c8dbc; color: white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Comprobante de Gasto</h4>
      </div>
      <div class="modal-body text-center">
        <img id="imagenComprobanteAmpliada" src="" class="img-responsive" style="max-width: 100%; margin: 0 auto;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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


<?php

  $borrarGasto = new ControladorGastos();
  $borrarGasto -> ctrEliminarGasto();

  $borrarCategoria = new ControladorCategoriasGastos();
  $borrarCategoria -> ctrEliminarCategoriaGasto();

?>