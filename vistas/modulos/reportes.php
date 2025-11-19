<style>
  .excelbtn{
    z-index: 9999;
  }
  .filtro-excel-container {
    padding: 15px;
    border-radius: 10px;
    background-color: #f9f9f9;
    margin-bottom: 15px;
  }
  .filtro-excel-container label {
    font-weight: 600;
    margin-top: 10px;
  }
  .filtro-excel-container select,
  .filtro-excel-container input[type="date"] {
    border-radius: 8px;
    margin-bottom: 10px;
  }
</style>

  <div class="content-wrapper">
    <section class="content-header">

      <h1>
        Reportes de ventas
      </h1>

      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li class="active">Reportes de ventas</li>
      </ol>

    </section>

    <section class="content">

       <div class="box">

          <div class="box-header with-border">


            <div class="box-tools pull-right excelbtn">
              <button class="btn btn-success" style="margin-top:5px" data-toggle="modal" data-target="#modalDescargarExcel">
                <i class="fa fa-file-excel-o"></i> Descargar reporte en excel
              </button>
            </div>


             <!-- Análisis de ventas -->
                <div id="contenedor-barras-formas-pago">
                  <div class="col-12 col-md-12">
                    <?php include "reportes/analisis-ventas1.php"; ?>
                  </div>
                </div>

          </div>

         <div class="box-body">

            <div class="row">


            <div class="col-md-6 col-xs-12">
                  <?php
                  include "reportes/metodos-pago-mas-usados.php";
                  ?>
              </div>

              <div class="col-md-6 col-xs-12">
                  <?php
                  include "reportes/productos-mas-vendidos.php";
                  ?>
              </div>

              <div class="col-md-6 col-xs-12">
                  <?php
                  include "reportes/vendedores.php";
                  ?>
              </div>

               <div class="col-md-6 col-xs-12">
                  <?php
                  include "reportes/compradores.php";
                  ?>
              </div>

            </div>

         </div>

       </div>

       <!-- REPORTE FINANCIERO -->
       <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-balance-scale"></i> Estado de Resultados</h3>
          </div>

          <div class="box-body">
            <?php include "reportes/estado-resultados.php"; ?>
          </div>
       </div>

       <!-- REPORTE DE ÓRDENES -->
       <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-shopping-cart"></i> Análisis de Órdenes</h3>
          </div>

          <div class="box-body">
            <?php include "reportes/ordenes-reporte.php"; ?>
          </div>
       </div>


    </section>

  </div>

<!-- Modal para descargar Excel con filtro de fechas -->
<div class="modal fade" id="modalDescargarExcel" tabindex="-1" role="dialog" aria-labelledby="modalDescargarExcelLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalDescargarExcelLabel"><i class="fa fa-file-excel-o"></i> Descargar Reporte en Excel</h4>
      </div>
      <div class="modal-body">
        <div class="filtro-excel-container">
          <div class="form-group">
            <label for="tipo-fecha-excel">Filtrar por fecha</label>
            <select id="tipo-fecha-excel" class="form-control">
              <option value="todo">Todas las ventas</option>
              <option value="hoy">Hoy</option>
              <option value="ayer">Ayer</option>
              <option value="mes">Mes actual</option>
              <option value="personalizado">Personalizado</option>
            </select>
          </div>

          <div id="campo-desde-excel" class="form-group" style="display:none;">
            <label for="fecha-desde-excel">Desde</label>
            <input type="date" id="fecha-desde-excel" class="form-control">
          </div>

          <div id="campo-hasta-excel" class="form-group" style="display:none;">
            <label for="fecha-hasta-excel">Hasta</label>
            <input type="date" id="fecha-hasta-excel" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <a id="btn-descargar-excel" href="vistas/modulos/descargar-reporte.php?reporte=reporte" class="btn btn-success">
          <i class="fa fa-download"></i> Descargar
        </a>
      </div>
    </div>
  </div>
</div>

<script>
// Mostrar/ocultar campos de fecha personalizada
document.getElementById('tipo-fecha-excel').addEventListener('change', function() {
  const tipo = this.value;
  const campoDesde = document.getElementById('campo-desde-excel');
  const campoHasta = document.getElementById('campo-hasta-excel');

  if (tipo === 'personalizado') {
    campoDesde.style.display = 'block';
    campoHasta.style.display = 'block';
  } else {
    campoDesde.style.display = 'none';
    campoHasta.style.display = 'none';
  }

  actualizarEnlaceExcel();
});

// Actualizar enlace cuando cambian las fechas
document.getElementById('fecha-desde-excel').addEventListener('change', actualizarEnlaceExcel);
document.getElementById('fecha-hasta-excel').addEventListener('change', actualizarEnlaceExcel);

function actualizarEnlaceExcel() {
  const tipo = document.getElementById('tipo-fecha-excel').value;
  const btnDescargar = document.getElementById('btn-descargar-excel');
  let rutaBase = window.location.hostname.includes("localhost") ? "/pos" : "";
  let url = `${rutaBase}/vistas/modulos/descargar-reporte.php?reporte=reporte`;

  let fechaInicial, fechaFinal;
  const hoy = new Date();

  switch(tipo) {
    case 'hoy':
      fechaInicial = fechaFinal = hoy.toISOString().split('T')[0];
      break;
    case 'ayer':
      const ayer = new Date(hoy);
      ayer.setDate(ayer.getDate() - 1);
      fechaInicial = fechaFinal = ayer.toISOString().split('T')[0];
      break;
    case 'mes':
      fechaInicial = new Date(hoy.getFullYear(), hoy.getMonth(), 1).toISOString().split('T')[0];
      fechaFinal = hoy.toISOString().split('T')[0];
      break;
    case 'personalizado':
      fechaInicial = document.getElementById('fecha-desde-excel').value;
      fechaFinal = document.getElementById('fecha-hasta-excel').value;
      break;
    default:
      // "todo" - sin filtro de fechas
      btnDescargar.href = url;
      return;
  }

  if (fechaInicial && fechaFinal) {
    url += `&fechaInicial=${fechaInicial}&fechaFinal=${fechaFinal}`;
  }

  btnDescargar.href = url;
}
</script>