<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar datos para los filtros
require_once "controladores/usuarios.controlador.php";
require_once "controladores/clientes.controlador.php";
require_once "controladores/productos.controlador.php";
require_once "modelos/usuarios.modelo.php";
require_once "modelos/clientes.modelo.php";
require_once "modelos/productos.modelo.php";

// Obtener vendedores (usuarios)
$vendedores = ControladorUsuarios::ctrMostrarUsuarios(null, null);

// Obtener clientes
$clientes = ControladorClientes::ctrMostrarClientes(null, null);

// Obtener productos
$productos = ControladorProductos::ctrMostrarProductos(null, null, "descripcion");
?>

<!--Estilo Filtro de fechas -->
  <style>
    .formulario-fechas-container {
      max-width: 100%;
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
    .filtros-row {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
    }
    .filtro-grupo {
      flex: 1;
      min-width: 150px;
    }
  </style>

<style>
  .d-none {
    display: none !important;
  }
</style>

  <div class="row">
      <div class="card-body">

              <!-- Filtros -->
             <div class="formulario-fechas-container">
                <form id="filtro-fechas" class="formulario-fechas">

                  <div class="filtros-row">
                    <!-- Filtro de fecha -->
                    <div class="filtro-grupo">
                      <label for="tipo-fecha">Fecha</label>
                      <select id="tipo-fecha" name="tipo" class="form-control">
                        <option value="hoy">Hoy</option>
                        <option value="ayer">Ayer</option>
                        <option value="mes">Mes actual</option>
                        <option value="personalizado">Personalizado</option>
                      </select>
                    </div>

                    <!-- Filtro de vendedor -->
                    <div class="filtro-grupo">
                      <label for="filtro-vendedor">Vendedor</label>
                      <select id="filtro-vendedor" name="vendedor" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach($vendedores as $vendedor): ?>
                          <option value="<?php echo $vendedor['id']; ?>"><?php echo $vendedor['nombre']; ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <!-- Filtro de cliente -->
                    <div class="filtro-grupo">
                      <label for="filtro-cliente">Cliente</label>
                      <select id="filtro-cliente" name="cliente" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach($clientes as $cliente): ?>
                          <option value="<?php echo $cliente['id']; ?>"><?php echo $cliente['nombre']; ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <!-- Filtro de producto -->
                    <div class="filtro-grupo">
                      <label for="filtro-producto">Producto</label>
                      <select id="filtro-producto" name="producto" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach($productos as $producto): ?>
                          <option value="<?php echo $producto['id']; ?>"><?php echo $producto['descripcion']; ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <!-- Filtro de método de pago -->
                    <div class="filtro-grupo">
                      <label for="filtro-metodo-pago">Método de Pago</label>
                      <select id="filtro-metodo-pago" name="metodo_pago" class="form-control">
                        <option value="">Todos</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="nequi">Nequi</option>
                        <option value="daviplata">Daviplata</option>
                      </select>
                    </div>
                  </div>

                  <!-- Campos de fecha personalizada -->
                  <div class="filtros-row mt-2">
                    <div id="campo-desde" class="filtro-grupo d-none">
                      <label for="fecha-desde">Desde</label>
                      <input type="date" id="fecha-desde" name="fecha_inicio" class="form-control">
                    </div>

                    <div id="campo-hasta" class="filtro-grupo d-none">
                      <label for="fecha-hasta">Hasta</label>
                      <input type="date" id="fecha-hasta" name="fecha_fin" class="form-control">
                    </div>
                  </div>

                  <button type="submit" class="btn btn-primary w-100 mt-3">Aplicar filtros</button>
                </form>
              </div>

        <div class="row">
          <!--<div class="col-md-8">-->
            <p class="text-center">
              <strong>Ventas</strong>
            </p>
              <!--VALOR VENTAS -->
              <div class="text-center mb-3">
                <h5>Total de ventas en el periodo seleccionado:</h5>
                <h3 id="total-ventas" class="text-success">$0</h3>
              </div>

            <div id="sales-chart"></div>
          <!--</div>-->
        </div>

      </div>
  </div>


<!-- GRAFICO DE VENTAS ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"></script>

<!-- Script para inicializar y actualizar el gráfico -->
<script>
  const salesChart = new ApexCharts(document.querySelector('#sales-chart'), {
    series: [],
    chart: {
      height: 180,
      type: 'area',
      toolbar: { show: false }
    },
    colors: ['#0d6efd'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth' },
    xaxis: { type: 'datetime', categories: [] },
    tooltip: { x: { format: 'dd MMM yyyy' } }
  });

  salesChart.render();

  // Ejecutar por defecto el filtro del mes al cargar la página
window.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('filtro-fechas');
  document.getElementById('tipo-fecha').value = 'mes'; // Establece tipo "mes"
  form.dispatchEvent(new Event('submit')); // Dispara el envío del formulario
});


  // Mostrar campos personalizados al seleccionar "personalizado"
  document.getElementById('tipo-fecha').addEventListener('change', function () {
    const tipo = this.value;
    document.getElementById('campo-desde').classList.toggle('d-none', tipo !== 'personalizado');
    document.getElementById('campo-hasta').classList.toggle('d-none', tipo !== 'personalizado');
  });

  // Ejecutar al cargar la página para aplicar correctamente la visibilidad
  (function () {
    const tipo = document.getElementById('tipo-fecha').value;
    document.getElementById('campo-desde').classList.toggle('d-none', tipo !== 'personalizado');
    document.getElementById('campo-hasta').classList.toggle('d-none', tipo !== 'personalizado');
  })();

  // Escuchar el envío del formulario
  document.getElementById('filtro-fechas').addEventListener('submit', function (e) {
    e.preventDefault();

    const tipo = document.getElementById('tipo-fecha').value;
    const fechaInicio = document.getElementById('fecha-desde').value;
    const fechaFin = document.getElementById('fecha-hasta').value;
    const vendedor = document.getElementById('filtro-vendedor').value;
    const cliente = document.getElementById('filtro-cliente').value;
    const producto = document.getElementById('filtro-producto').value;
    const metodoPago = document.getElementById('filtro-metodo-pago').value;

    const formData = new FormData();
    formData.append('tipo', tipo);

    if (tipo === 'personalizado') {
      if (!fechaInicio || !fechaFin) {
        alert("Selecciona ambas fechas para el filtro personalizado.");
        return;
      }
      formData.append('fecha_inicio', fechaInicio);
      formData.append('fecha_fin', fechaFin);
    }

    // Agregar filtros adicionales
    if (vendedor) formData.append('vendedor', vendedor);
    if (cliente) formData.append('cliente', cliente);
    if (producto) formData.append('producto', producto);
    if (metodoPago) formData.append('metodo_pago', metodoPago);


    //fetch('/pos/vistas/modulos/reportes/filtro_ventas.php', {
    let rutaBase = window.location.hostname.includes("localhost")
      ? "/pos"
      : ""; // en producción no va "/pos"

    fetch(`${rutaBase}/vistas/modulos/reportes/filtro_ventas.php`, {

      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      const datos = data.datos;
      const total = data.total;

      // Mostrar el total en el HTML
      document.getElementById('total-ventas').textContent = total.toLocaleString('es-CO', {
        style: 'currency',
        currency: 'COP'
      });

      // Extraer fechas y totales para la gráfica
      const fechas = datos.map(item => item.fecha);
      const totales = datos.map(item => item.total_ventas);

      // Actualizar el gráfico
      salesChart.updateOptions({
        xaxis: { categories: fechas },
        series: [{
          name: "Ventas",
          data: totales
        }]
      });

    })
    .catch(error => {
      console.error("Error al cargar datos:", error);
    });
  }); // <- cierre correcto del addEventListener
</script>