<?php

$configuracion = ControladorConfiguracion::ctrObtenerConfiguracion();

?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Configuración del Sistema
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Configuración</li>
    </ol>
  </section>

  <section class="content">

    <div class="box">

      <form role="form" method="post" enctype="multipart/form-data">

        <div class="box-body">

          <!--=====================================
          SECCIÓN 1: DATOS PARA LA FACTURA
          ======================================-->

          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-file-text"></i> Datos para la Factura</h3>
            </div>
            <div class="box-body">

              <div class="row">

                <!-- Logo de la Empresa -->
                <div class="col-md-3">
                  <div class="form-group text-center">
                    <label>Logo de la Empresa</label>
                    <div class="panel panel-default">
                      <div class="panel-body">
                        <?php if(!empty($configuracion["logo"]) && file_exists($configuracion["logo"])): ?>
                          <img src="<?php echo $configuracion["logo"]; ?>" class="img-responsive" id="previsualizarLogo" style="max-width: 200px; margin: 0 auto;">
                        <?php else: ?>
                          <img src="vistas/img/plantilla/logo-blanco-bloque.png" class="img-responsive" id="previsualizarLogo" style="max-width: 200px; margin: 0 auto;">
                        <?php endif; ?>
                      </div>
                    </div>
                    <input type="file" class="form-control" name="nuevoLogo" id="nuevoLogo" accept="image/*">
                    <input type="hidden" name="logoActual" value="<?php echo $configuracion["logo"]; ?>">
                    <p class="help-block">Formatos: JPG, PNG (Máx: 500x500px)</p>
                  </div>
                </div>

                <!-- Datos de la Empresa -->
                <div class="col-md-9">

                  <div class="row">

                    <!-- Nombre de la Empresa -->
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Nombre de la Empresa *</label>
                        <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-building"></i></span>
                          <input type="text" class="form-control" name="nombreEmpresa" value="<?php echo $configuracion["nombre_empresa"]; ?>" required>
                        </div>
                      </div>
                    </div>

                    <!-- NIT / RUT -->
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>NIT / RUT / Identificación Fiscal</label>
                        <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-id-card"></i></span>
                          <input type="text" class="form-control" name="nitEmpresa" value="<?php echo $configuracion["nit"]; ?>" placeholder="Ej: 123456789-0">
                        </div>
                      </div>
                    </div>

                  </div>

                  <div class="row">

                    <!-- Dirección -->
                    <div class="col-md-12">
                      <div class="form-group">
                        <label>Dirección</label>
                        <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                          <input type="text" class="form-control" name="direccionEmpresa" value="<?php echo $configuracion["direccion"]; ?>" placeholder="Dirección completa">
                        </div>
                      </div>
                    </div>

                  </div>

                  <div class="row">

                    <!-- Teléfono -->
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Teléfono</label>
                        <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                          <input type="text" class="form-control" name="telefonoEmpresa" value="<?php echo $configuracion["telefono"]; ?>" placeholder="Ej: +56 9 1234 5678">
                        </div>
                      </div>
                    </div>

                    <!-- Correo -->
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Correo Electrónico</label>
                        <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                          <input type="email" class="form-control" name="correoEmpresa" value="<?php echo $configuracion["correo"]; ?>" placeholder="contacto@empresa.com">
                        </div>
                      </div>
                    </div>

                  </div>

                </div>

              </div>

              <hr>

              <!-- Colores de Factura -->
              <h5 class="text-muted"><i class="fa fa-paint-brush"></i> Colores de Factura</h5>

              <div class="row">

                <!-- Color Principal -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Color Principal</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-square"></i></span>
                      <input type="color" class="form-control" name="colorPrincipal" value="<?php echo !empty($configuracion["color_principal"]) ? $configuracion["color_principal"] : '#667eea'; ?>" style="height: 40px;">
                    </div>
                    <p class="help-block">Color de cabecera y borde de "Información del Cliente"</p>
                  </div>
                </div>

                <!-- Color Secundario -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Color Secundario</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-square"></i></span>
                      <input type="color" class="form-control" name="colorSecundario" value="<?php echo !empty($configuracion["color_secundario"]) ? $configuracion["color_secundario"] : '#764ba2'; ?>" style="height: 40px;">
                    </div>
                    <p class="help-block">Color del borde de "Detalles de la Venta"</p>
                  </div>
                </div>

              </div>

              <hr>

              <!-- Mensaje de Ticket -->
              <h5 class="text-muted"><i class="fa fa-comment"></i> Mensaje de Ticket</h5>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Mensaje de Pie de Ticket</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-print"></i></span>
                      <textarea class="form-control" name="mensajeTicket" rows="2" placeholder="Mensaje que aparecerá al final del ticket"><?php echo $configuracion["mensaje_ticket"]; ?></textarea>
                    </div>
                    <p class="help-block">Ej: ¡Gracias por su compra! Vuelva pronto.</p>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!--=====================================
          SECCIÓN 2: CONFIGURACIÓN DE VENTAS
          ======================================-->

          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-shopping-cart"></i> Configuración de Ventas</h3>
            </div>
            <div class="box-body">

              <div class="row">

                <!-- Impuesto por Defecto -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Impuesto por Defecto (%)</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                      <input type="number" class="form-control" name="impuestoDefecto" value="<?php echo $configuracion["impuesto_defecto"]; ?>" min="0" max="100" step="0.01">
                    </div>
                    <p class="help-block">Ej: 19 para IVA del 19%</p>
                  </div>
                </div>

                <!-- Moneda -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Símbolo de Moneda</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-money"></i></span>
                      <input type="text" class="form-control" name="moneda" value="<?php echo $configuracion["moneda"]; ?>" maxlength="10">
                    </div>
                    <p class="help-block">Ej: $, USD, CLP</p>
                  </div>
                </div>

                <!-- Formato Código Venta -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Formato Código Venta</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-barcode"></i></span>
                      <input type="text" class="form-control" name="formatoCodigoVenta" value="<?php echo $configuracion["formato_codigo_venta"]; ?>" maxlength="50">
                    </div>
                    <p class="help-block">Ej: VTA-, VENTA-</p>
                  </div>
                </div>

              </div>

              <hr>

              <!-- Medios de Pago -->
              <h5 class="text-muted"><i class="fa fa-credit-card"></i> Medios de Pago</h5>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Medios de Pago Disponibles</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-list"></i></span>
                      <textarea class="form-control" name="mediosPago" rows="3" placeholder="Ingrese los medios de pago separados por comas"><?php echo !empty($configuracion["medios_pago"]) ? $configuracion["medios_pago"] : 'Efectivo,Tarjeta Débito,Tarjeta Crédito,Nequi,Bancolombia,Cheque'; ?></textarea>
                    </div>
                    <p class="help-block">Separe cada medio de pago con una coma. Ej: Efectivo,Tarjeta Débito,Tarjeta Crédito,Nequi,Bancolombia,Cheque,Transferencia</p>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!--=====================================
          SECCIÓN 3: CONFIGURACIÓN DE PRODUCTOS
          ======================================-->

          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-cube"></i> Configuración de Productos</h3>
            </div>
            <div class="box-body">

              <!-- Tipo de Código de Producto -->
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Tipo de Código de Producto</label>
                    <div class="radio">
                      <label style="font-weight: normal; cursor: pointer;">
                        <input type="radio" name="tipoCodigoProducto" value="automatico" <?php echo (!empty($configuracion["tipo_codigo_producto"]) && $configuracion["tipo_codigo_producto"] == "automatico") || empty($configuracion["tipo_codigo_producto"]) ? "checked" : ""; ?>>
                        <strong>Automático</strong> - El sistema genera el código automáticamente
                      </label>
                    </div>
                    <div class="radio">
                      <label style="font-weight: normal; cursor: pointer;">
                        <input type="radio" name="tipoCodigoProducto" value="manual" <?php echo (!empty($configuracion["tipo_codigo_producto"]) && $configuracion["tipo_codigo_producto"] == "manual") ? "checked" : ""; ?>>
                        <strong>Manual</strong> - El usuario ingresa el código manualmente
                      </label>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!--=====================================
          SECCIÓN 4: ALERTAS Y NOTIFICACIONES
          ======================================-->

          <div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bell"></i> Alertas y Notificaciones</h3>
            </div>
            <div class="box-body">

              <!-- Alertas de Stock -->
              <h5 class="text-muted"><i class="fa fa-cubes"></i> Alertas de Stock</h5>

              <div class="row">

                <!-- Alerta de Stock Bajo -->
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="checkbox">
                      <label style="font-weight: normal; cursor: pointer;">
                        <input type="checkbox" name="alertaStockBajo" value="1" <?php echo (!empty($configuracion["alerta_stock_bajo"]) && $configuracion["alerta_stock_bajo"] == 1) || !isset($configuracion["alerta_stock_bajo"]) ? "checked" : ""; ?>>
                        <strong>Activar alerta de stock bajo</strong>
                      </label>
                    </div>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-cubes"></i></span>
                      <input type="number" class="form-control" name="umbralStockMinimo" min="1" value="<?php echo !empty($configuracion["umbral_stock_minimo"]) ? $configuracion["umbral_stock_minimo"] : '5'; ?>">
                      <span class="input-group-addon">unidades</span>
                    </div>
                    <p class="help-block">Alertar cuando el stock esté por debajo de esta cantidad</p>
                  </div>
                </div>

                <!-- Alerta de Stock Agotado -->
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="checkbox">
                      <label style="font-weight: normal; cursor: pointer;">
                        <input type="checkbox" name="alertaStockAgotado" value="1" <?php echo (!empty($configuracion["alerta_stock_agotado"]) && $configuracion["alerta_stock_agotado"] == 1) || !isset($configuracion["alerta_stock_agotado"]) ? "checked" : ""; ?>>
                        <strong>Activar alerta de stock agotado</strong>
                      </label>
                    </div>
                    <p class="help-block">Notificar cuando un producto se agote completamente (stock = 0)</p>
                  </div>
                </div>

              </div>

              <hr>

              <!-- Alertas de Actividades y Gastos -->
              <h5 class="text-muted"><i class="fa fa-calendar"></i> Alertas de Actividades y Gastos</h5>

              <div class="row">

                <!-- Alerta de Actividades Pendientes -->
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="checkbox">
                      <label style="font-weight: normal; cursor: pointer;">
                        <input type="checkbox" name="alertaActividadesPendientes" value="1" <?php echo (!empty($configuracion["alerta_actividades_pendientes"]) && $configuracion["alerta_actividades_pendientes"] == 1) || !isset($configuracion["alerta_actividades_pendientes"]) ? "checked" : ""; ?>>
                        <strong>Activar alerta de actividades próximas</strong>
                      </label>
                    </div>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i></span>
                      <input type="number" class="form-control" name="diasAntesActividad" min="1" value="<?php echo !empty($configuracion["dias_antes_actividad"]) ? $configuracion["dias_antes_actividad"] : '3'; ?>">
                      <span class="input-group-addon">días antes</span>
                    </div>
                    <p class="help-block">Alertar X días antes de la fecha de la actividad</p>
                  </div>
                </div>

                <!-- Alerta de Gastos Próximos -->
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="checkbox">
                      <label style="font-weight: normal; cursor: pointer;">
                        <input type="checkbox" name="alertaGastosProximos" value="1" <?php echo (!empty($configuracion["alerta_gastos_proximos"]) && $configuracion["alerta_gastos_proximos"] == 1) || !isset($configuracion["alerta_gastos_proximos"]) ? "checked" : ""; ?>>
                        <strong>Activar alerta de gastos próximos a vencer</strong>
                      </label>
                    </div>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-money"></i></span>
                      <input type="number" class="form-control" name="diasAntesGasto" min="1" value="<?php echo !empty($configuracion["dias_antes_gasto"]) ? $configuracion["dias_antes_gasto"] : '5'; ?>">
                      <span class="input-group-addon">días antes</span>
                    </div>
                    <p class="help-block">Alertar X días antes del vencimiento del gasto</p>
                  </div>
                </div>

              </div>

              <hr>

              <!-- Alerta de Agente IA -->
              <h5 class="text-muted"><i class="fa fa-robot"></i> Alerta de Agente IA</h5>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="checkbox">
                      <label style="font-weight: normal; cursor: pointer;">
                        <input type="checkbox" name="alertaAgenteIA" value="1" <?php echo (!empty($configuracion["alerta_agente_ia"]) && $configuracion["alerta_agente_ia"] == 1) || !isset($configuracion["alerta_agente_ia"]) ? "checked" : ""; ?>>
                        <strong>Notificar cuando una orden de venta proviene del Agente IA</strong>
                      </label>
                    </div>
                    <p class="help-block">Se creará una notificación cuando el campo 'extra' de la orden contenga 'n8n'</p>
                  </div>
                </div>
              </div>

            </div>
          </div>

        </div>

        <!-- Pie del Formulario -->
        <div class="box-footer">
          <button type="submit" class="btn btn-primary btn-lg">
            <i class="fa fa-save"></i> Guardar Configuración
          </button>
          <a href="inicio" class="btn btn-default btn-lg">
            <i class="fa fa-times"></i> Cancelar
          </a>
        </div>

        <?php

          $actualizarConfiguracion = new ControladorConfiguracion();
          $actualizarConfiguracion -> ctrActualizarConfiguracion();

        ?>

      </form>

    </div>

  </section>

</div>

<!-- Script para previsualizar logo -->
<script>
$(document).ready(function(){

  $("#nuevoLogo").change(function(){

    var imagen = this.files[0];

    // Validar formato
    if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){

      $("#nuevoLogo").val("");

      swal({
        title: "Error al subir la imagen",
        text: "¡La imagen debe estar en formato JPG o PNG!",
        type: "error",
        confirmButtonText: "¡Cerrar!"
      });

    } else if(imagen["size"] > 2000000){

      $("#nuevoLogo").val("");

      swal({
        title: "Error al subir la imagen",
        text: "¡La imagen no debe pesar más de 2MB!",
        type: "error",
        confirmButtonText: "¡Cerrar!"
      });

    } else {

      var datosImagen = new FileReader;
      datosImagen.readAsDataURL(imagen);

      $(datosImagen).on("load", function(event){

        var rutaImagen = event.target.result;

        $("#previsualizarLogo").attr("src", rutaImagen);

      })

    }

  })

});
</script>