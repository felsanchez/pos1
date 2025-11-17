<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      Historial de Movimientos de Stock
      <small>Auditoría completa de inventario</small>
    </h1>

    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Historial de Stock</li>
    </ol>

  </section>

  <section class="content">

    <!-- TARJETAS DE RESUMEN -->
    <div class="row" id="tarjetasResumen">
      
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3 id="totalVentas">0</h3>
            <p>Ventas Totales</p>
          </div>
          <div class="icon">
            <i class="fa fa-shopping-cart"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <h3 id="totalCreaciones">0</h3>
            <p>Creación Productos/Variantes</p>
          </div>
          <div class="icon">
            <i class="fa fa-plus-circle"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3 id="totalEdiciones">0</h3>
            <p>Edición de Stock</p>
          </div>
          <div class="icon">
            <i class="fa fa-edit"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <h3 id="totalMovimientos">0</h3>
            <p>Total Movimientos</p>
          </div>
          <div class="icon">
            <i class="fa fa-list"></i>
          </div>
        </div>
      </div>

    </div>

    <div class="box">
      
      <div class="box-header with-border">
        <h3 class="box-title">Filtros de Búsqueda</h3>
      </div>

      <div class="box-body">
        
        <!-- FORMULARIO DE FILTROS -->
        <form id="formFiltros">
          
          <div class="row">

            <!-- Filtro por Producto -->
            <div class="col-md-3">
              <div class="form-group">
                <label>Producto:</label>
                <select class="form-control" id="filtroProducto" name="filtroProducto" style="width: 100%;">
                  <option value="">Todos los productos</option>
                  <?php
                    $item = null;
                    $valor = null;
                    $orden = "descripcion";
                    $productos = ControladorProductos::ctrMostrarProductos($item, $valor, $orden);

                    foreach ($productos as $key => $value) {
                      echo '<option value="'.$value["id"].'">'.$value["descripcion"].'</option>';
                    }
                  ?>
                </select>
              </div>
            </div>

            <!-- Filtro por Tipo de Movimiento -->
            <div class="col-md-3">
              <div class="form-group">
                <label>Tipo de Movimiento:</label>
                <select class="form-control" id="filtroTipo" name="filtroTipo" style="width: 100%;">
                  <option value="">Todos los tipos</option>
                  <option value="venta">Venta</option>
                  <option value="eliminacion_venta">Eliminación de Venta</option>
                  <option value="creacion_producto">Creación Producto</option>
                  <option value="creacion_variante">Creación Variante</option>
                  <option value="edicion_stock">Edición Stock</option>
                </select>
              </div>
            </div>

            <!-- Filtro por Fecha Desde -->
            <div class="col-md-2">
              <div class="form-group">
                <label>Fecha Desde:</label>
                <input type="date" class="form-control" id="filtroFechaDesde" name="filtroFechaDesde">
              </div>
            </div>

            <!-- Filtro por Fecha Hasta -->
            <div class="col-md-2">
              <div class="form-group">
                <label>Fecha Hasta:</label>
                <input type="date" class="form-control" id="filtroFechaHasta" name="filtroFechaHasta" value="<?php echo date('Y-m-d'); ?>">
              </div>
            </div>

            <!-- Botones -->
            <div class="col-md-2">
              <div class="form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-primary btn-block" id="btnFiltrar">
                  <i class="fa fa-search"></i> Filtrar
                </button>
              </div>
            </div>

          </div>

          <div class="row">
            
            <!-- Filtro por Usuario -->
            <div class="col-md-3">
              <div class="form-group">
                <label>Usuario:</label>

                <select class="form-control" id="filtroUsuario" name="filtroUsuario" style="width: 100%;">
                  <option value="">Todos los usuarios</option>
                  <?php
                    $usuarios = ControladorUsuarios::ctrMostrarUsuarios(null, null);
                    foreach ($usuarios as $key => $value) {
                      echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                    }
                  ?>
                </select>
                
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-default btn-block" id="btnLimpiar">
                  <i class="fa fa-eraser"></i> Limpiar Filtros
                </button>
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-success btn-block" id="btnExportarExcel">
                  <i class="fa fa-file-excel-o"></i> Exportar a Excel
                </button>
              </div>
            </div>

          </div>

        </form>

      </div>

    </div>

    <!-- TABLA DE MOVIMIENTOS -->
    <div class="box">
      
      <div class="box-header with-border">
        <h3 class="box-title">Registro de Movimientos</h3>
      </div>

      <div class="box-body">
        
        <table class="table table-bordered table-striped dt-responsive tablaHistorialStock" width="100%">
          
          <thead>
            
            <tr>
              <th style="width:10px">#</th>
              <th>Fecha</th>
              <th>Producto</th>
              <th>Tipo</th>
              <th>Tipo Movimiento</th>
              <th>Cantidad</th>
              <th>Stock Anterior</th>
              <th>Stock Nuevo</th>
              <th>Usuario</th>
              <th>Referencia</th>
              <th>Notas</th>
            </tr>

          </thead>

          <tbody>
            <!-- Se llenará dinámicamente con DataTables -->
          </tbody>

        </table>

      </div>

    </div>

  </section>

</div>