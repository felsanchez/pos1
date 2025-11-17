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
        Venta - Grupo FEJ Technologies
      </h1>

      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li class="active">Venta - Grupo FEJ Technologies</li>
      </ol>

    </section>

    
    <section class="content">

      <div class="row">

            <!--=====================================
            EL FORMULARIO
            ======================================-->
            
            <div class="col-lg-12 col-xs-12">

              <div class="box box-success">

                <div class="box-header with-border"></div>

                <form role="form" method="post" class="formularioVenta">

                <div class="box-body">

                    <div class="box">

                       <?php

                          $item = "id";
                          $valor = $_GET["idVenta"];

                          $venta = ControladorVentas::ctrMostrarVentas($item, $valor);


                          $itemUsuario = "id";
                          $valorUsuario = $venta["id_vendedor"];

                          $vendedor = ControladorUsuarios::ctrMostrarUsuarios($itemUsuario, $valorUsuario);

                          
                          $itemCliente = "id";
                          $valorCliente = $venta["id_cliente"];

                          $cliente = ControladorClientes::ctrMostrarClientes($itemCliente, $valorCliente);

                          //$porcentajeImpuesto = $venta["impuesto"] * 100 / $venta["neto"];

                          $porcentajeImpuesto = 0; // Inicializamos el porcentaje de impuesto en 0 por defecto
                            if ($venta["neto"] != 0) {
                              $porcentajeImpuesto = $venta["impuesto"] * 100 / $venta["neto"];
                            }

                       ?>
                    

                      <!--=====================================
                      ENTRADA DEL VENDEDOR
                      ======================================-->

                      <div class="form-group">

                        <div class="input-group">

                          <span class="input-group-addon"><i class="fa fa-user"></i></span>

                          <input type="text" class="form-control" id="nuevoVendedor" value="<?php echo $vendedor["nombre"]; ?>" readonly>

                          <input type="hidden" name="idVendedor" value="<?php echo $vendedor["id"]; ?>">
                          
                        </div>
                        
                      </div>

                      <!--=====================================
                      ENTRADA DEL CODIGO
                      ======================================-->

                      <div class="form-group">

                        <div class="input-group">

                          <span class="input-group-addon"><i class="fa fa-key"></i></span>

                          <input type="text" class="form-control" id="nuevaVenta" name="editarVenta" value="<?php echo $venta["codigo"]; ?>" readonly>
                          
                        </div>
                        
                      </div>


                       <!--=====================================
                      ENTRADA notas
                      ======================================-->

                      <!--
                      <div class="form-group">
                        <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-sticky-note"></i></span>
                          <textarea class="form-control input-lg" name="notas" placeholder="Sin notas" readonly><?php echo $venta["notas"]; ?></textarea>

                        </div>
                      </div>
                        -->


                      <!--=====================================
                      ENTRADA DEL CLIENTE
                      ======================================-->
                      <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-users"></i></span>
                                
                                <input type="text" 
                                    class="form-control" 
                                    value="<?php echo $cliente["nombre"]; ?>" 
                                    readonly>
                                
                                <!-- Campo oculto para enviar el ID -->
                                <input type="hidden" 
                                    name="seleccionarCliente" 
                                    value="<?php echo $cliente["id"]; ?>">
                            </div>
                        </div>


                      <!--=====================================
                      ENTRADA PARA AGREGAR PRODUCTO
                      ======================================-->

                      <div class="form-group row nuevoProducto">

                        <?php

                          $listaProducto = json_decode($venta["productos"], true);

                          foreach ($listaProducto as $key => $value) {

                            $item = "id";
                            $valor = $value["id"];
                            $orden = "id";

                            $respuesta = ControladorProductos::ctrMostrarProductos($item, $valor, $orden);

                            $stockAntiguo = $respuesta["stock"] + $value["cantidad"];
                            
                            echo '<div class="row" style="padding:5px 15px">

                          
                            <div class="col-xs-6" style="padding-right:0px">

                              <div class="input-group">

                                 <input type="text" class="form-control nuevaDescripcionProducto" idProducto="'.$value["id"].'" name="agregarProducto" value="'.$value["descripcion"].'" readonly required>
                                          
                              </div>

                            </div>


                            <div class="col-xs-3">
                                       
                                <input type="number" class="form-control nuevaCantidadProducto" name="nuevaCantidadProducto" min="1" value="'.$value["cantidad"].'" stock="'.$stockAntiguo.'" nuevoStock="'.$value["stock"].'" readonly>

                            </div>


                            <div class="col-xs-3 ingresoPrecio" style="padding-left:0px">
                                        
                                <div class="input-group">

                                    
                                          
                                    <input type="text" class="form-control nuevoPrecioProducto" precioReal="'.$respuesta["precio_venta"].'" name="nuevoPrecioProducto" value="'.$value["total"].'" readonly required>

                                </div>

                            </div>

                          </div>';

                          }


                        ?>

                      </div>

                       <input type="hidden" id="listaProductos" name="listaProductos">

                       <!--=====================================
                       BOTON PARA AGREGAR PRODUCTO
                       ======================================-->

                       <!--<button type="button" class="btn btn-default btnAgregarProducto solo-movil">Agregar producto</button>-->

                       <hr>

                       <div class="row">

                        <!--=====================================
                        ENTRADA IMPUESTOS Y TOTAL
                        ======================================-->
                         
                          <div class="col-xs-8 pull-left">
                           
                             <table class="table">                     

                               <thead>
                                 
                                 <tr>                             
                                   <!--<th>Impuesto</th>-->
                                   <th>Total</th>
                                 </tr>

                               </thead>

                               <tbody>
                                 
                                   <td style="width: 60%">
                                     
                                     <div class="input-group">                             
                                        <input type="text" class="form-control input-lg" id="nuevoTotalVenta" name="nuevoTotalVenta" total="" value="<?php echo $venta["total"]; ?>" readonly required>

                                        <input type="hidden" name="totalVenta" value="<?php echo $venta["total"]; ?>" id="totalVenta">

                                     </div>

                                   </td>

                                 </tr>

                               </tbody>

                             </table>


                           </div>
                       
                        </div>

                        <hr>

                        
                          <!-- ENTRADA ESTADO-->                        
                          <input type="hidden" name="estado" value="venta">


                        </div>

                        <br>

                        
                   </div>
                  
                </div>


               </form>


             <?php

              $editarVenta = new ControladorVentas();
              $editarVenta -> ctrEditarVenta();

             ?>

              <button  class="btn btn-danger pull-left" onclick="location.href='ordenes-visita'">Cancelar</button>
             
                
              </div>
              
            </div>   

            <!--=====================================
            LA TABLA DE PRODUCTOS
            ======================================-->

            <!--<div class="col-lg-7 hidden-md hidden-sm hidden-xs">

              <div class="box box-warning">

                <div class="box-header with-border"></div>

                <div class="box-body">
                  
                  <table class="table table-bordered table-striped dt-responsive tablaVentas">

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
                        -->




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


           <!-- entrada para Email -->
            
            <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-envelope"></i></span>

              <input type="email" class="form-control input-lg" name="nuevoEmail" placeholder="Ingresar email" required>

             </div>

           </div>


           <!-- entrada para telefono -->
            
            <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-phone"></i></span>

              <input type="text" class="form-control input-lg" name="nuevoTelefono" placeholder="Ingresar teléfono" data-inputmask="'mask':'(999) 999-9999'" data-mask required>

             </div>

           </div>


           <!-- entrada para la direccion -->
            
            <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>

              <input type="text" class="form-control input-lg" name="nuevaDireccion" placeholder="Ingresar dirección" required>

             </div>

           </div>


           <!-- entrada para la fecha naciminiento -->
            
            <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>

              <input type="text" class="form-control input-lg" name="nuevaFechaNacimiento" placeholder="Ingresar fecha de nacimiento" data-inputmask="'alias': 'yyyy/mm/dd'" data-mask required>

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

