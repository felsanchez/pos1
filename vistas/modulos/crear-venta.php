<?php
  // Obtener configuración del sistema
  $configuracion = ControladorConfiguracion::ctrObtenerConfiguracion();
  $impuestoDefecto = !empty($configuracion["impuesto_defecto"]) ? $configuracion["impuesto_defecto"] : 0;
  $formatoCodigoVenta = !empty($configuracion["formato_codigo_venta"]) ? $configuracion["formato_codigo_venta"] : "";

  $mediosPago = !empty($configuracion["medios_pago"]) ? explode(",", $configuracion["medios_pago"]) : array("Efectivo", "Tarjeta Débito", "Tarjeta Crédito", "Nequi", "Bancolombia", "Cheque");
?> 

  <style>
    @media (min-width: 769px) {
      .solo-movil {
        display: none !important;
      }
    }
  </style>

  
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Crear venta
      </h1>

      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li class="active">Crear venta</li>
      </ol>
    </section>

    
    <section class="content">

      <div class="row">

            <!--=====================================
            EL FORMULARIO
            ======================================-->
            
            <div class="col-lg-5 col-xs-12">

              <div class="box box-success">

                <div class="box-header with-border"></div>

                <form role="form" method="post" class="formularioVenta">

                <div class="box-body">

                    <div class="box">

                      <!--=====================================
                      ENTRADA DEL VENDEDOR
                      ======================================-->

                      <div class="form-group">

                        <div class="input-group">

                          <span class="input-group-addon"><i class="fa fa-user"></i></span>

                          <input type="text" class="form-control" id="nuevoVendedor" name="nuevoVendedor" value="<?php echo $_SESSION["nombre"]; ?>" readonly>

                          <input type="hidden" name="idVendedor" value="<?php echo $_SESSION["id"]; ?>">
                          
                        </div>
                        
                      </div>


                      <!--=====================================
                       ENTRADA DEL FORMATO DE CÓDIGO
                      ======================================--> 

                      <div class="form-group"> 

                        <div class="input-group"> 

                          <span class="input-group-addon"><i class="fa fa-barcode"></i></span> 

                          <input type="text" class="form-control" id="formatoCodigoVenta" name="formatoCodigoVenta" value="<?php echo $formatoCodigoVenta; ?>" readonly placeholder="Formato de código">

                         </div> 

                      </div> 

                      <!--=====================================
                      ENTRADA DE LA VENTA
                      ======================================--> 

                      <div class="form-group"> 

                        <div class="input-group"> 

                          <span class="input-group-addon"><i class="fa fa-key"></i></span> 

                          <?php 

                            $item = null;
                            $valor = null; 

                            $ventas = ControladorVentas::ctrMostrarVentas($item, $valor); 

                            /*
                            if(!$ventas){
                              echo '<input type="text" class="form-control" id="nuevaVenta" name="nuevaVenta" value="10001" readonly>';
                            }
                            else {
                                  foreach ($ventas as $key => $value) {
                                  }
                             $codigo = $value["codigo"] +1;
                              echo '<input type="text" class="form-control" id="nuevaVenta" name="nuevaVenta" value="'.$codigo.'" readonly>';
                            }
                            */
                            /*
                               $fecha = $value["fecha"] +1;
                              echo '<input type="date" class="form-control" id="nuevaVenta" name="nuevaVenta" value="'.$fecha.'">';
                              */

                            // Obtener el siguiente consecutivo
                            $siguienteNumero = ModeloVentas::mdlObtenerSiguienteConsecutivo("ventas");
                          ?>

                          <!-- Mostrar el codigo en el campo de texto -->
                          <input type="text" class="form-control" id="nuevaVenta" name="nuevaVenta" value="<?php echo $siguienteNumero; ?>" readonly> 

                        </div>
                        
                      </div>

                      <!--=====================================
                      ENTRADA DEL CLIENTE
                      ======================================-->

                      <div class="form-group">

                        <div class="input-group">

                          <span class="input-group-addon"><i class="fa fa-users"></i></span>

                          <select class="form-control" id="seleccionarCliente" name="seleccionarCliente" required>

                            <option value="">Seleccionar cliente</option>

                              <?php

                                $item = null;
                                $valor = null;

                                $categorias = ControladorClientes::ctrMostrarClientes($item, $valor);

                                foreach ($categorias as $key => $value) {
                                  
                                  echo '<option value="'.$value["id"].'">'.$value["nombre"].'</option>';
                                }

                              ?>

                          </select>

                          <span class="input-group-addon"><button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#modalAgregarCliente" data-dismiss="modal">Agregar cliente</button></span>
                          
                        </div>
                        
                      </div>


                      <!--=====================================
                      ENTRADA PARA IMAGEN
                      ======================================-->

                      <!--<input type="hidden" id="nuevaimagen" name="nuevaimagen">-->
                      <!--<input type="hidden" name="nuevaimagen" value="vistas/img/ventas/default/sinventa.png">-->
                                           

                      <!--=====================================
                      ENTRADA PARA AGREGAR PRODUTO
                      ======================================-->

                      <div class="form-group row nuevoProducto">

                       </div>

                       <input type="hidden" id="listaProductos" name="listaProductos">

                       <!--=====================================
                       BOTON PARA AGREGAR PRODUCTO
                       ======================================-->
                     <!--BTN SE MUESTRA SOLO DESDE MOVIL-->
                        <button type="button" class="btn btn-default  btnAgregarProducto solo-movil">Agregar producto</button>
                        
                       <hr>

                       <div class="row">

                        <!--=====================================
                        ENTRADA IMPUESTOS Y TOTAL
                        ======================================-->
                         
                          <div class="col-xs-12 pull-left">
                           
                              <table class="table"> 

                               <thead> 

                                 <tr>
                                   <th>Subtotal</th>
                                   <th>Impuesto</th>
                                   <th>Total</th>
                                 </tr>

                               </thead>

                               <tbody> 

                                 <tr>

                                   <td style="width: 35%"> 

                                     <div class="input-group">

                                        <input type="text" class="form-control input-lg" id="nuevoSubtotalVenta" name="nuevoSubtotalVenta" placeholder="00000" readonly>

                                     </div> 

                                   </td> 

                                   <td style="width: 25%"> 

                                     <div class="input-group">

                                        <input type="number" class="form-control input-lg" min="0" id="nuevoImpuestoVenta" name="nuevoImpuestoVenta" value="<?php echo $impuestoDefecto; ?>">

                                         <input type="hidden" name="nuevoPrecioImpuesto" id="nuevoPrecioImpuesto" required>

                                        <input type="hidden" name="nuevoPrecioNeto" id="nuevoPrecioNeto" required>

                                         <!--<span class="input-group-addon"><i class="fa fa-percent"></i></span>-->

                                     </div>

                                   </td> 

                                   <td style="width: 40%"> 

                                     <div class="input-group">

                                        <!--<span class="input-group-addon"><i class="ion ion-social-usd"></i></span> -->                       
                                        <input type="text" class="form-control input-lg" id="nuevoTotalVenta" name="nuevoTotalVenta" total="" placeholder="00000" readonly required>

                                        <input type="hidden" name="totalVenta" id="totalVenta">

                                     </div>

                                   </td>

                                 </tr>

                               </tbody>

                             </table>

                           </div>



                            </div>
                        <hr> 

                        <!--=====================================
                        SECCIÓN DE DESCUENTOS
                        ======================================--> 

                        <div class="row">
                          <div class="col-xs-12"> 

                            <!-- Checkboxes para tipo de descuento -->
                            <div class="form-group">
                              <label style="font-weight: normal; cursor: pointer;">
                                <input type="checkbox" id="checkDescuentoPorcentaje" name="checkDescuentoPorcentaje" style="margin-right: 5px; transform: scale(1.2);">
                                Agregar descuento por %
                              </label>
                              &nbsp;&nbsp;&nbsp;

                              <label style="font-weight: normal; cursor: pointer;">
                                <input type="checkbox" id="checkDescuentoFijo" name="checkDescuentoFijo" style="margin-right: 5px; transform: scale(1.2);">
                                Agregar descuento por valor fijo
                              </label>
                            </div>

                            <!-- Campo de entrada para descuento (oculto inicialmente) -->
                            <div class="form-group" id="campoDescuento" style="display: none;">
                              <div class="input-group">
                                <span class="input-group-addon" id="iconoDescuento"><i class="fa fa-percent"></i></span>
                                <input type="number" class="form-control input-lg" min="0" id="valorDescuento" name="valorDescuento" placeholder="0" value="0">
                                <span class="input-group-addon" id="labelDescuento">Descuento</span>
                              </div>
                              <small class="text-muted" id="textoAyudaDescuento">Ingrese el porcentaje de descuento</small>
                            </div>

                             <!-- Campos ocultos para guardar información del descuento -->
                            <input type="hidden" id="tipoDescuento" name="tipoDescuento" value="">
                            <input type="hidden" id="montoDescuento" name="montoDescuento" value="0">
                          </div>
                       
                        </div>

                        <hr>

                        <!--=====================================
                        ENTRADA METODO DE PAGO
                        ======================================-->

                        <div class="form-group row">
                          
                          <div class="col-xs-6" style="padding-right:0px">

                              <div class="input-group">

                                <select class="form-control" id="nuevoMetodoPago" name="nuevoMetodoPago" required>

                                  <option value="">Seleccione método de pago</option>
                                   <?php
                                    foreach($mediosPago as $medio){
                                      $medio = trim($medio); // Eliminar espacios en blanco
                                      echo '<option value="'.$medio.'">'.$medio.'</option>';
                                    }
                                  ?>

                                </select>   

                              </div>
                            
                          </div>

                          <div class="cajasMetodoPago"></div>

                          <input type="hidden" id="listaMetodoPago" name="listaMetodoPago">

                        </div>

                        <!--=====================================
                        ENTRADA ESTADO
                        ======================================-->
                        <input type="hidden" name="estado" value="venta">

                        <br>
                    
                   </div>
                  
                </div>


                  <!--<div class="box-footer">

                    <button type="submit" class="btn btn-primary pull-right">Guardar venta</button>
                    
                  </div>-->


                  <div class="box-footer">
                      <button type="submit" class="btn btn-primary pull-right">Guardar venta</button>
                  </div>

               </form>


               <?php
                
                $guardarVenta = new ControladorVentas();
                $guardarVenta -> ctrCrearVenta();
                
               ?>

               <button  class="btn btn-danger pull-left" onclick="location.href='ventas'">Cancelar</button>
             
              </div>
              
            </div>   

            <!--=====================================
            LA TABLA DE PRODUCTOS
            ======================================-->

            <div class="col-lg-7 hidden-md hidden-sm hidden-xs">

              <div class="box box-warning">

                <div class="box-header with-border"></div>

                <div class="box-body">
                  
                 <!--<table class="table table-bordered table-striped dt-responsive tablaVentas">-->
                  	<table class="table table-bordered table-striped tablaVentas">

                    <thead>
                      <tr>
                        <th style="width: 10px">#</th>
                        <th>Imagen</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                      </tr>             
                    </thead>
                 
                  </table>

                </div>
                
              </div>
              
            </div>


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

              <input type="text" class="form-control input-lg" name="nuevoCliente" placeholder="Ingresar nombre" required>

             </div>

           </div>


            <!-- entrada para documento ID -->
            
            <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-key"></i></span>

              <input type="number" min="0" class="form-control input-lg" name="nuevoDocumentoId" placeholder="Ingresar documento" required>

             </div>

           </div>

            <!-- entrada para telefono -->
            
          <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-phone"></i></span>

              <input type="text" class="form-control input-lg" name="nuevoTelefono" placeholder="Ingresar teléfono" data-inputmask="'mask':'(999) 999-9999'" data-mask required>

            </div>

          </div>


           <!-- entrada para Email -->
            
            <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-envelope"></i></span>

              <input type="email" class="form-control input-lg" name="nuevoEmail" placeholder="Ingresar email">

             </div>

           </div>

          <!-- entrada para departamento -->

          <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-building"></i></span>

              <input type="text" class="form-control input-lg" name="nuevoDepartamento" placeholder="Ingresar departamento" required>

            </div>

          </div>


         <!-- entrada para ciudad -->
            
         <div class="form-group">
          
          <div class="input-group">
            
            <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>

            <input type="text" class="form-control input-lg" name="nuevoCiudad" placeholder="Ingresar Ciudad" required>

           </div>

         </div>


           <!-- entrada para la direccion -->
            
            <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>

              <input type="text" class="form-control input-lg" name="nuevaDireccion" placeholder="Ingresar dirección" required>

             </div>

           </div>

           <!-- entrada para estatus -->
           <input type="hidden" name="nuevoEstatus" value="nuevo">


           <!-- entrada para notas -->
            
           <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-pencil-square-o"></i></span>

              <input type="text" class="form-control input-lg" name="nuevaNota" placeholder="Ingresar Nota">

            </div>

           </div>

           <!-- Al guardar me reenvie a esta pagina de crear venta y no a la de clientes  -->
           <input type="hidden" name="origen" value="crear-venta">

          <!--Crear estado venats-->
           <input type="hidden" name="vistaOrigen" value="crear-venta">


           <!-- entrada para la fecha naciminiento -->            
            <!--
           <div class="form-group">         
            <div class="input-group">              
              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
              <input type="text" class="form-control input-lg" name="nuevaFechaNacimiento" placeholder="Ingresar fecha de nacimiento" data-inputmask="'alias': 'yyyy/mm/dd'" data-mask required>
             </div>
           </div>
          -->
         

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

           <!--Verificar que tenga productos , antes de guardar la venta-->

        <script>

          document.querySelector('form').addEventListener('submit', function (e) {

            const listaProductos = document.getElementById('listaProductos').value;

            if (!listaProductos || listaProductos === '[]') {

              e.preventDefault(); // Detiene el envío del formulario

              Swal.fire({

                icon: 'warning',

                title: 'Sin productos',

                text: 'Debe agregar al menos un producto para guardar la venta',

                confirmButtonText: 'OK'

              });

              return false;

            }

          });

        </script>
 

        <!--=====================================
        SCRIPT PARA MANEJAR DESCUENTOS
        ======================================-->
        <script>

        $(document).ready(function() {
           // Manejar checkbox de descuento por porcentaje

          $('#checkDescuentoPorcentaje').on('change', function() {
            if ($(this).is(':checked')) {
              // Desmarcar el otro checkbox
              $('#checkDescuentoFijo').prop('checked', false); 

              // Mostrar campo de descuento
              $('#campoDescuento').slideDown(); 

              // Cambiar icono y texto a porcentaje
              $('#iconoDescuento').html('<i class="fa fa-percent"></i>');
              $('#labelDescuento').text('% Descuento');
              $('#textoAyudaDescuento').text('Ingrese el porcentaje de descuento (0-100)');
              $('#valorDescuento').attr('max', '100');
              $('#valorDescuento').attr('placeholder', '0');
              $('#valorDescuento').val('0'); 

              // Guardar tipo de descuento
              $('#tipoDescuento').val('porcentaje');
            } else {
              // Si se desmarca
              $('#campoDescuento').slideUp();
              $('#valorDescuento').val('0');
              $('#tipoDescuento').val('');
              $('#montoDescuento').val('0'); 

              // Recalcular total sin descuento
              sumarTotalPrecios();
              agregarImpuesto();
            }

          }); 

          // Manejar checkbox de descuento fijo
          $('#checkDescuentoFijo').on('change', function() {

            if ($(this).is(':checked')) {

              // Desmarcar el otro checkbox
              $('#checkDescuentoPorcentaje').prop('checked', false); 

              // Mostrar campo de descuento
              $('#campoDescuento').slideDown(); 

              // Cambiar icono y texto a valor fijo
              $('#iconoDescuento').html('<i class="fa fa-money"></i>');
              $('#labelDescuento').text('Valor Descuento');
              $('#textoAyudaDescuento').text('Ingrese el valor fijo del descuento');
              $('#valorDescuento').removeAttr('max');
              $('#valorDescuento').attr('placeholder', '0');
              $('#valorDescuento').val('0'); 

              // Guardar tipo de descuento
              $('#tipoDescuento').val('fijo');

            } else {
              // Si se desmarca
              $('#campoDescuento').slideUp();
              $('#valorDescuento').val('0');
              $('#tipoDescuento').val('');
              $('#montoDescuento').val('0'); 

              // Recalcular total sin descuento
              sumarTotalPrecios();
              agregarImpuesto();
            }
          });

           // Manejar checkbox de descuento fijo
          $('#checkDescuentoFijo').on('ifChecked', function() {

            // Desmarcar el otro checkbox
            $('#checkDescuentoPorcentaje').iCheck('uncheck');
            checkboxActivo = 'fijo';

             // Mostrar campo de descuento
            $('#campoDescuento').slideDown(); 

            // Cambiar icono y texto a valor fijo
            $('#iconoDescuento').html('<i class="fa fa-money"></i>');
            $('#labelDescuento').text('Valor Descuento');
            $('#textoAyudaDescuento').text('Ingrese el valor fijo del descuento');
            $('#valorDescuento').removeAttr('max');
            $('#valorDescuento').attr('placeholder', '0');
            $('#valorDescuento').val('0'); 

            // Guardar tipo de descuento
            $('#tipoDescuento').val('fijo');
          });
 
          $('#checkDescuentoFijo').on('ifUnchecked', function() {

            checkboxActivo = null;
            $('#campoDescuento').slideUp();
            $('#valorDescuento').val('0');
            $('#tipoDescuento').val('');
            $('#montoDescuento').val('0'); 

            // Recalcular total sin descuento
            sumarTotalPrecios();
            agregarImpuesto();
          });

           // Cuando cambia el valor del descuento, recalcular
          $('#valorDescuento').on('change keyup', function() {
            aplicarDescuento();
          });
 
        });

        </script>


  </div>

</div>
