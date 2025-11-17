<style>
@media (max-width: 767px) {
  .tablaProductos td:nth-child(n+3),
  .tablaProductos th:nth-child(n+3) {
      display: none;
  }
  .tablaProductos td:first-child,
  .tablaProductos td:nth-child(2),
  .tablaProductos td:nth-child(4),
  .tablaProductos th:first-child,
  .tablaProductos th:nth-child(2),
  .tablaProductos th:nth-child(4) {
      display: table-cell;
  }
}
</style>
 

<!-- Estilos para el filtro de categoría -->
<style>
.filtro-categoria-wrapper {
  min-width: 250px;
  display: flex;
  align-items: center;
  gap: 8px;
} 

.filtro-categoria-wrapper label {
  font-weight: 400 !important;
  margin-bottom: 0;
} 

.filtro-categoria {
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 14px;
}
 
@media (max-width: 767px) {
  .filtro-categoria-wrapper {
    float: none !important;
    justify-content: center !important;
    text-align: center;
    width: 100%;
  }

  .filtro-categoria-wrapper label {
    margin-bottom: 5px;
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


<!-- Fix para modal de importación -->
<style>
/* Forzar z-index del modal por encima del backdrop */

#modalImportarProductos {
  z-index: 10050 !important;
  opacity: 1 !important;
}

#modalImportarProductos .modal-dialog {
  z-index: 10051 !important;
}

#modalImportarProductos .modal-content {
  opacity: 1 !important;
}

/* Ajustar z-index del backdrop debajo del modal */
#modalImportarProductos ~ .modal-backdrop,
.modal-backdrop.in {
  z-index: 10040 !important;
}

/* Asegurar que el modal sea completamente visible */
#modalImportarProductos .modal-body,
#modalImportarProductos .modal-header,
#modalImportarProductos .modal-footer {
  opacity: 1 !important;
}
</style>


<!-- Fix para modal de agregar producto -->
<style>
/* Forzar z-index del modal por encima del backdrop */
#modalAgregarProducto {
  z-index: 10050 !important;
  opacity: 1 !important;
} 

#modalAgregarProducto .modal-dialog {
  z-index: 10051 !important;
} 

#modalAgregarProducto .modal-content {
  opacity: 1 !important;
} 

/* Ajustar z-index del backdrop debajo del modal */
#modalAgregarProducto ~ .modal-backdrop,
.modal-backdrop.in {
  z-index: 10040 !important;
} 

/* Asegurar que el modal sea completamente visible */
#modalAgregarProducto.in {
  opacity: 1 !important;
}
</style>


<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener configuración del sistema
$configuracion = ControladorConfiguracion::ctrObtenerConfiguracion();
$tipoCodigoProducto = !empty($configuracion["tipo_codigo_producto"]) ? $configuracion["tipo_codigo_producto"] : "automatico";
?>

<script>
// Variable global con la configuración del tipo de código de producto
var tipoCodigoProducto = "<?php echo $tipoCodigoProducto; ?>";
</script>

  <div class="content-wrapper">
    <section class="content-header">

      <h1>
        Administrar productos
      </h1>

      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li class="active">Administrar productos</li>
      </ol>

    </section>

    <section class="content">

      <div class="box">

        <div class="box-header with-border">

          <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarProducto">
            
             Agregar producto

            </button>
 

          <button class="btn btn-success" data-toggle="modal" data-target="#modalImportarProductos">

            <i class="fa fa-upload"></i> Importar desde CSV

          </button>


        </div>        
        
        <!--CODIGO PARA LLAMAR AL WEBHOOK DE n8n -->
        <form id="formN8N" action="https://n8n-n8n.lhs6l6.easypanel.host/webhook/mipos" method="POST">
          <input type="hidden" name="origen" value="clientes">
          <button type="submit" class="btn btn-success">Actualizar</button>
        </form>

        <!-- Filtro de categoría -->
        <div class="box-body">
          <div class="clearfix mb-2">
            <div class="pull-right filtro-categoria-wrapper">
              <label for="filtroCategoria" class="control-label">Filtrar por Categoría:</label>
              <select id="filtroCategoria" class="form-control filtro-categoria">
                <option value="">Todas</option>
                <?php
                $categoriasFiltro = ControladorCategorias::ctrMostrarCategorias(null, null);
                foreach($categoriasFiltro as $categoriaFiltro){
                  echo '<option value="'.$categoriaFiltro["categoria"].'">'.ucfirst($categoriaFiltro["categoria"]).'</option>';
                }
                ?>
              </select>
            </div>
          </div>
          <br>
        </div>


       <div class="box-body table-responsive">

          <table class="table table-bordered table-striped tablaProductos" style="width: 95%">
              
            <thead>
              <tr>
                <th style="width: 5px">#</th>
                <th>Imagen</th>
                <th>Código</th>
                <th>Descripción</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Precio de Compra</th>
                <th>Precio de Venta</th>
                <th>Proveedor</th>
                <th>Agregado</th>
                <th>Acciones</th>
              </tr>          
            </thead>
          
          </table>

           <input type="hidden" value="<?php echo $_SESSION['perfil']; ?>" class="perfilUsuario" id="perfilOculto">

        </div>

      </div>

    </section>

  </div>


    <!-- Modal para ampliar imagen de producto desde la tabla-->
    <!--
<div class="modal fade" id="modalAmpliarImagenProducto" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Imagen del Producto</h4>
      </div>
      <div class="modal-body text-center">
        <img id="imagenProductoAmpliada" src="" class="img-responsive" style="max-width: 100%; margin: 0 auto;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
-->



<!-- Modal para ampliar/editar imagen de producto -->
<div class="modal fade" id="modalAmpliarImagenProducto" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Imagen del Producto</h4>
      </div>
      <div class="modal-body text-center">
        <img id="imagenProductoAmpliada" src="" class="img-responsive" style="max-width: 100%; margin: 0 auto; margin-bottom: 20px;">        
        <hr>        
        <div class="form-group">
          <label>Cambiar Imagen del Producto</label>
          <input type="file" class="form-control nuevaImagenProducto" accept="image/*">
          <p class="help-block">Peso máximo de la imagen 2MB</p>
        </div>        
        <input type="hidden" id="idProductoImagen">
        <input type="hidden" id="codigoProductoImagen">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary btnGuardarImagenProducto">Guardar Imagen</button>
      </div>
    </div>
  </div>
</div>


<!--=====================================
MODAL AGREGAR PRODUCTO
======================================-->
  
<!-- Modal -->
<div id="modalAgregarProducto" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

      <!--=====================================
      CABEZA DEL MODAL
      ======================================-->

      <div class="modal-header" style="background:#3c8dbc; color: white">

        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agregar producto</h4>

      </div>

      <!--=====================================
      CUERPO DEL MODAL
      ======================================-->

      <div class="modal-body">
        
        <div class="box-body">

           <!-- entrada para seleccionar categoria -->

              <div class="form-group">
          
                <div class="input-group">
            
                   <span class="input-group-addon"><i class="fa fa-th"></i></span>

                     <select class="form-control input-lg" id="nuevaCategoria" name="nuevaCategoria" required>
              
                      <option value="">Seleccionar categoría</option>

                      <?php

                        $item = null;
                        $valor = null;
                        $categorias = ControladorCategorias::ctrMostrarCategorias($item, $valor);

                        foreach ($categorias as $key => $value) {
                      
                          echo'<option value="'.$value["id"].'">'.$value["categoria"].'</option>';   
                        }

                      ?>

                      </select>

                 </div>

              </div>

          <!-- entrada para el codigo -->
            
          <div class="form-group"> 

            <div class="input-group col-xs-5"> 

              <span class="input-group-addon"><i class="fa fa-code"></i></span> 

              <input type="text" class="form-control input-lg" id="nuevoCodigo" name="nuevoCodigo" placeholder="<?php echo $tipoCodigoProducto == 'manual' ? 'Ingrese el código' : 'Código'; ?>" <?php echo $tipoCodigoProducto == 'automatico' ? 'readonly' : ''; ?> required>

             </div>

           </div>



           <!-- entrada para la descripcion -->

           <div class="form-group">

           <div class="input-group"> 

              <span class="input-group-addon"><i class="fa fa-product-hunt"></i></span> 

              <input type="text" class="form-control input-lg" name="nuevaDescripcion" id="nuevaDescripcion" placeholder="Ingresar descripción" required> 

             </div> 

           </div>


               <!-- entrada para el stock -->
            
              <div class="form-group">
 
                <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-check"></i></span> 

                  <input type="number" class="form-control input-lg" name="nuevoStock" min="0" placeholder="Stock" required> 

                </div>

               <p class="help-block" id="helpStockProducto">Stock disponible del producto.</p>
               <p class="help-block text-info" id="helpStockVariantes" style="display:none;"><i class="fa fa-info-circle"></i> <strong>El stock se calculará automáticamente</strong> como la suma de todas las variantes.</p>

              </div>


              <!-- entrada para seleccionar proveedor -->

              <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-users"></i></span>

                <select class="form-control input-lg" id="nuevoProveedor" name="nuevoProveedor">

                  <option value="0" selected>Sin proveedor</option>

                  <?php

                    $item = null;
                    $valor = null;
                    $proveedores = ControladorProveedores::ctrMostrarProveedores($item, $valor);

                    if($proveedores){
                      foreach ($proveedores as $key => $value) {
                        echo'<option value="'.$value["id"].'">'.$value["nombre"].'</option>';   
                      }
                    }

                  ?>

                </select>

              </div>

            </div>


      <!-- entrada para el precio de compra -->
            
      <div class="form-group row"> 

        <div class="col-xs-6"> 

            <div class="input-group"> 

                  <span class="input-group-addon"><i class="fa fa-arrow-up"></i></span> 

                  <input type="number" class="form-control input-lg" id="nuevoPrecioCompra" name="nuevoPrecioCompra" min="0" placeholder="Precio de Compra" required> 

             </div>

             <p class="help-block">Precio base. Las variantes pueden tener un precio adicional.</p>

        </div>

      <!-- entrada para el precio de venta -->

        <div class="col-xs-6">
          
           <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-arrow-down"></i></span>

               <input type="number" class="form-control input-lg" id="nuevoPrecioVenta" name="nuevoPrecioVenta" min="0" placeholder="Precio de Venta" required>

            </div>

            <br>

            <!-- checkbox para porcentaje -->

            <div class="col-xs-6">
              
              <div class="form-group">
                
                <label>
                  
                  <input type="checkbox" class="minimal porcentaje" checked>
                  Utilizar porcentaje

                </label>

              </div>

            </div>

            <!-- entrada para porcentaje -->

            <div class="col-xs-6" style="padding:0">
              
              <div class="input-group">
                
                <input type="number" class="form-control input-lg nuevoPorcentaje" min="0" value="40" required>

                <span class="input-group-addon"><i class="fa fa-percent"></i></span>

              </div>

            </div>

        </div>

   </div>

                
           <!-- entrada para imagen -->

           <div class="form-group">
                    
              <div class="panel">SUBIR IMAGEN</div> 

                 <input type="file" class="nuevaImagen" name="nuevaImagen"> 

                 <p class="help-block">Peso máximo de la imagen 2MB</p> 

                 <img src="vistas/img/productos/default/anonymous.png" class="img-thumbnail previsualizar" width="100px"> 

           </div> 

           <!-- TOGGLE PARA VARIANTES --> 

           <div class="form-group"> 

              <label> 
                <input type="checkbox" class="minimal" id="checkTieneVariantes" name="tieneVariantes"> 
                ¿Este producto tiene variantes? (Ej: Colores, Tallas, etc.) 
              </label> 
           </div> 

           <!-- CONTENEDOR DE VARIANTES (Oculto inicialmente) -->
           <div id="contenedorVariantes" style="display:none; border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background-color: #f9f9f9;"> 

              <h4 style="margin-top: 0;">Configuración de Variantes</h4> 

              <!-- Tipos de variantes disponibles --> 

              <div class="form-group"> 

                <label>Selecciona los tipos de variantes:</label> 

                <div id="tiposVariantesContainer"> 

                  <!-- Se cargará dinámicamente con AJAX --> 

                  <p class="text-muted"><i class="fa fa-spinner fa-spin"></i> Cargando tipos de variantes...</p> 

                </div> 

              </div> 

              <!-- Contenedor para opciones de cada tipo seleccionado --> 

              <div id="opcionesVariantesContainer" style="display:none;"> 

                <!-- Se cargará dinámicamente cuando se seleccionen tipos --> 

              </div> 

              <!-- Contenedor para las combinaciones finales --> 

              <div id="combinacionesContainer" style="display:none;"> 

                <hr> 

                <h4>Variantes a crear:</h4> 

                <p class="text-muted">Selecciona las combinaciones que deseas crear y configura su precio/stock:</p> 

                <div id="listaCombinaciones"> 

                  <!-- Se generará dinámicamente --> 

                </div> 

              </div> 

           </div>

       </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar producto</button>

        </div>

     </form>


     <?php

      $crearProducto = new ControladorProductos();
      $crearProducto -> ctrCrearProducto();

     ?>

    </div>


  </div>

</div>



<!--==========================================================================
MODAL EDITAR PRODUCTO
============================================================================-->
  
<!-- Modal -->
<div id="modalEditarProducto" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

      <!--=====================================
      CABEZA DEL MODAL
      ======================================-->

      <div class="modal-header" style="background:#3c8dbc; color: white">

        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Editar producto</h4>

      </div>

      <!--=====================================
      CUERPO DEL MODAL
      ======================================-->

      <div class="modal-body">
        
        <div class="box-body">

          <!-- entrada para el codigo -->
              
          <div class="form-group">
            
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-code"></i></span>

              <input type="text" class="form-control input-lg" id="editarCodigo" name="editarCodigo" readonly required>

            </div>

          </div>

           <!-- entrada para seleccionar categoria -->
            <!--
              <div class="form-group">
                <div class="input-group">
                   <span class="input-group-addon"><i class="fa fa-th"></i></span>
                     <select class="form-control input-lg" name="editarCategoria" readonly required>
                      <option id="editarCategoria"></option>
                      </select>
                 </div>
              </div>
              -->
                      

              <!-- entrada para seleccionar categoria -->
              <div class="form-group">
            
                <div class="input-group">
            
                    <span class="input-group-addon"><i class="fa fa-th"></i></span>

                    <select class="form-control input-lg" id="editarCategoria" name="editarCategoria">
                
                        <option value="">Editar Categoria</option>

                        <?php
                          $item = null;
                          $valor = null;
                          $categorias = ControladorCategorias::ctrMostrarCategorias($item, $valor);

                          foreach ($categorias as $key => $value) {

                              $selected = ($producto["id_categoria"] == $value["id"]) ? "selected" : "";

                              echo'<option value="'.$value["id"].'" '.$selected.'>'.$value["categoria"].'</option>';   
                          }
                          ?>

                    </select>

                </div>
              </div>


           <!-- entrada para la descripcion -->
            
          <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-product-hunt"></i></span>

              <input type="text" class="form-control input-lg" id="editarDescripcion" name="editarDescripcion" required>

             </div>

           </div>


               <!-- entrada para el stock -->
            
              <div class="form-group">
          
                <div class="input-group">
              
                  <span class="input-group-addon"><i class="fa fa-check"></i></span>

                  <input type="number" class="form-control input-lg" id="editarStock" name="editarStock" min="0" required>

                </div>

              </div>


               <!-- entrada para el proveedor -->
              <div class="form-group">
          
                <div class="input-group">
              
                  <span class="input-group-addon"><i class="fa fa-users"></i></span>

                  <select class="form-control input-lg" id="editarProveedor" name="editarProveedor">
                      <option value="0">Sin proveedor</option>
                      
                      <?php
                          $item = null;
                          $valor = null;
                          $proveedores = ControladorProveedores::ctrMostrarProveedores($item, $valor);

                          if($proveedores){
                              foreach ($proveedores as $key => $value) {
                                  echo'<option value="'.$value["id"].'">'.$value["nombre"].'</option>';   
                              }
                          }
                      ?>
                  </select>

                </div>

              </div>


      <!-- entrada para el precio de compra -->
            
            <div class="form-group row">

                  <div class="col-xs-6">
                    
                      <div class="input-group">
                        
                            <span class="input-group-addon"><i class="fa fa-arrow-up"></i></span>

                            <input type="number" class="form-control input-lg" id="editarPrecioCompra" name="editarPrecioCompra" min="0" required>

                      </div>

                  </div>

      <!-- entrada para el precio de venta -->

        <div class="col-xs-6">
          
           <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-arrow-down"></i></span>

               <input type="number" class="form-control input-lg" id="editarPrecioVenta" name="editarPrecioVenta" min="0" readonly required>

            </div>

            <br>

            <!-- checkbox para porcentaje -->

            <div class="col-xs-6">
              
              <div class="form-group">
                
                <label>
                  
                  <input type="checkbox" class="minimal porcentaje" checked>
                  Utilizar porcentaje

                </label>

              </div>

            </div>

            <!-- entrada para porcentaje -->

            <div class="col-xs-6" style="padding:0">
              
              <div class="input-group">
                
                <input type="number" class="form-control input-lg nuevoPorcentaje" min="0" value="40" required>

                <span class="input-group-addon"><i class="fa fa-percent"></i></span>

              </div>

            </div>

        </div>

   </div>

                
           <!-- entrada para imagen -->

           <!--
           <div class="form-group">                    
              <div class="panel">SUBIR IMAGEN</div>
                 <input type="file" class="nuevaImagen" name="editarImagen">
                 <p class="help-block">Peso máximo de la imagen 2MB</p>
                 <img src="vistas/img/productos/default/anonymous.png" class="img-thumbnail previsualizar" width="100px">
                 <input type="hidden" name="imagenActual" id="imagenActual">
              </div>
           </div>
           -->

           <!-- abrir imagen con modal-->
           <div class="form-group">
                                
              <div class="panel">SUBIR IMAGEN</div>

              <input type="file" class="nuevaImagen" name="editarImagen">

              <p class="help-block">Peso máximo de la imagen 2MB</p>

              <img src="vistas/img/productos/default/anonymous.png" class="img-thumbnail previsualizar img-ampliar-producto-modal" width="100px" style="cursor: pointer;">

              <input type="hidden" name="imagenActual" id="imagenActual">

          </div>


          <!-- TOGGLE PARA AGREGAR NUEVAS VARIANTES -->

           <div class="form-group"> 

              <label> 

                <input type="checkbox" class="minimal" id="checkAgregarVariantes" name="agregarVariantes"> 

                ¿Desea agregar nuevas variantes? (Ej: Colores, Tallas, etc.) 

              </label> 

              <p class="help-block">Las variantes existentes se editan desde la tabla de variantes. Aquí solo puedes agregar nuevas.</p> 

           </div> 

           <!-- CONTENEDOR DE VARIANTES (Oculto inicialmente) --> 

           <div id="contenedorAgregarVariantes" style="display:none; border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background-color: #f9f9f9;"> 

              <h4 style="margin-top: 0;">Configuración de Nuevas Variantes</h4>

              <!-- Tipos de variantes disponibles -->
 
              <div class="form-group"> 

                <label>Selecciona los tipos de variantes:</label> 

                <div id="tiposVariantesEditarContainer"> 

                  <!-- Se cargará dinámicamente con AJAX --> 

                  <p class="text-muted"><i class="fa fa-spinner fa-spin"></i> Cargando tipos de variantes...</p> 

                </div>

              </div> 

              <!-- Contenedor para opciones de cada tipo seleccionado --> 

              <div id="opcionesVariantesEditarContainer" style="display:none;"> 

                <!-- Se cargará dinámicamente cuando se seleccionen tipos --> 

              </div> 

              <!-- Contenedor para las combinaciones finales --> 

              <div id="combinacionesEditarContainer" style="display:none;"> 

                <hr> 

                <h4>Variantes a crear:</h4> 

                <p class="text-muted">Selecciona las combinaciones que deseas crear y configura su precio/stock:</p> 

                <div id="listaCombinacionesEditar"> 

                  <!-- Se generará dinámicamente -->

                </div> 

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

     </form>


     <?php
      $editarProducto = new ControladorProductos();
      $editarProducto -> ctrEditarProducto();
     ?>

    </div>

  </div>

</div>



    <!-- Modal para ampliar imagen de producto desde la tabla-->
<div class="modal fade" id="modalAmpliarFotoProducto" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Imagen del Producto</h4>
      </div>
      <div class="modal-body text-center">
        <img id="fotoProductoAmpliada" src="" class="img-responsive" style="max-width: 100%; margin: 0 auto;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<!--=============================================
MODAL EDITAR VARIANTE
=============================================-->

<div id="modalEditarVariante" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content"> 

      <form role="form" method="post" id="formEditarVariante"> 

        <!--=====================================
        CABEZA DEL MODAL
        ======================================--> 

        <div class="modal-header" style="background:#3c8dbc; color:white"> 

          <button type="button" class="close" data-dismiss="modal">&times;</button> 

          <h4 class="modal-title">Editar Variante</h4> 

        </div> 

        <!--====================================
        CUERPO DEL MODAL
        ======================================--> 

        <div class="modal-body"> 

          <div class="box-body"> 

            <!-- ENTRADA PARA ID --> 

            <input type="hidden" id="idVariante" name="idVariante"> 

            <!-- ENTRADA PARA PRECIO ADICIONAL --> 

            <div class="form-group"> 

              <div class="input-group"> 

                <span class="input-group-addon"><i class="fa fa-dollar"></i></span> 

                <input type="number" class="form-control input-lg" id="editarPrecioAdicionalVariante" name="editarPrecioAdicionalVariante" step="0.01" placeholder="Precio adicional" required>
 
              </div> 

            </div>


            <!-- ENTRADA PARA STOCK --> 

            <div class="form-group"> 

              <div class="input-group"> 

                <span class="input-group-addon"><i class="fa fa-cubes"></i></span> 

                <input type="number" class="form-control input-lg" id="editarStockVariante" name="editarStockVariante" min="0" placeholder="Stock" required>

              </div> 

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

      </form>

    </div>

  </div>

 </div>


<!--=====================================
MODAL IMPORTAR PRODUCTOS DESDE CSV
======================================--> 

<div id="modalImportarProductos" class="modal fade" role="dialog"> 

  <div class="modal-dialog"> 

    <div class="modal-content"> 

      <form role="form" method="post" enctype="multipart/form-data"> 

        <!--=====================================
        CABEZA DEL MODAL
        ======================================--> 

        <div class="modal-header" style="background:#3c8dbc; color:white"> 

          <button type="button" class="close" data-dismiss="modal">&times;</button> 

          <h4 class="modal-title">Importar Productos desde CSV</h4>

        </div> 

        <!--=====================================
        CUERPO DEL MODAL
        ======================================--> 

        <div class="modal-body"> 

          <div class="box-body"> 

            <div class="alert alert-info">

              <h4><i class="icon fa fa-info"></i> Instrucciones:</h4>

              <ol>
                <li>Descarga la plantilla CSV haciendo clic en el botón de abajo</li>
                <li>Completa los datos de los productos (código, descripción, categoría son obligatorios)</li>
                <li>El campo <strong>proveedor es opcional</strong></li>
                <li>Asegúrate de que las categorías existan en el sistema</li>
                <li>Sube el archivo CSV completado</li>
              </ol>

            </div>


            <!-- BOTÓN PARA DESCARGAR PLANTILLA --> 

            <div class="form-group text-center">

              <a href="vistas/modulos/descargar-plantilla-productos.php" class="btn btn-info">

                <i class="fa fa-download"></i> Descargar Plantilla CSV

              </a>

            </div> 

            <hr> 

            <!-- ENTRADA PARA SUBIR ARCHIVO CSV --> 

            <div class="form-group"> 

              <label>Seleccionar archivo CSV:</label> 

              <input type="file" class="form-control" name="archivoCSV" accept=".csv" required> 

            </div> 

          </div> 

        </div> 

        <!--=====================================
        PIE DEL MODAL
        ======================================--> 

        <div class="modal-footer"> 

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancelar</button> 

          <button type="submit" class="btn btn-success">Importar Productos</button> 

        </div> 

        <?php 
          $importar = new ControladorProductos();
          $importar -> ctrImportarProductos();
        ?> 

      </form>

     </div> 

  </div> 

</div>

  <?php
    $eliminarProducto = new ControladorProductos();
    $eliminarProducto -> ctrEliminarProducto();
  ?>


<!--=============================================
AMPLIAR Y EDITAR IMAGEN DE PRODUCTO DESDE LA TABLA
=============================================-->
<script>
        $(document).on("click", ".img-ampliar-producto", function(){
          var rutaImagen = $(this).attr("data-imagen");
          var idProducto = $(this).attr("data-idproducto");
          
          // Obtener el código del producto desde la fila
          var codigo = $(this).closest("tr").find("td:nth-child(3)").text();
          
          console.log("ID Producto:", idProducto);
          console.log("Código Producto:", codigo);
          console.log("Ruta Imagen:", rutaImagen);
          
          $("#imagenProductoAmpliada").attr("src", rutaImagen);
          $("#idProductoImagen").val(idProducto);
          $("#codigoProductoImagen").val(codigo);
          $(".nuevaImagenProducto").val("");
          $("#modalAmpliarImagenProducto").modal("show");
      });

      // Previsualizar nueva imagen cuando se selecciona
      $(".nuevaImagenProducto").change(function(){
          var imagen = this.files[0];
          
          if(imagen){
              if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){
                  $(".nuevaImagenProducto").val("");
                  swal({
                      title: "Error al subir la imagen",
                      text: "¡La imagen debe estar en formato JPG o PNG!",
                      type: "error",
                      confirmButtonText: "¡Cerrar!"
                  });
              }else if(imagen["size"] > 2000000){
                  $(".nuevaImagenProducto").val("");
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
                      $("#imagenProductoAmpliada").attr("src", rutaImagen);
                  });
              }
          }
      });

      // Guardar la nueva imagen del producto
      $(document).on("click", ".btnGuardarImagenProducto", function(){
          
          var idProducto = $("#idProductoImagen").val();
          var codigo = $("#codigoProductoImagen").val();
          var imagen = $(".nuevaImagenProducto")[0].files[0];
          
          console.log("ID al guardar:", idProducto);
          console.log("Código al guardar:", codigo);
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
          
          if(!idProducto || !codigo){
              swal({
                  title: "Error",
                  text: "No se pudo obtener el ID o código del producto",
                  type: "error",
                  confirmButtonText: "¡Cerrar!"
              });
              return;
          }
          
          var datos = new FormData();
          datos.append("idProductoImagen", idProducto);
          datos.append("codigoProductoImagen", codigo);
          datos.append("nuevaImagenProducto", imagen);
          
          // Mostrar loading
          swal({
              title: 'Cargando...',
              allowOutsideClick: false,
              onBeforeOpen: () => {
                  swal.showLoading()
              }
          });
          
          $.ajax({
              url: "ajax/productos.ajax.php",
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
                              $("#modalAmpliarImagenProducto").modal("hide");
                              window.location = "productos";
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
                  console.log("Error AJAX:", textStatus, errorThrown);
                  console.log("Respuesta:", jqXHR.responseText);
                  
                  swal({
                      type: "error",
                      title: "Error en la petición",
                      text: "Por favor revisa la consola para más detalles",
                      confirmButtonText: "Cerrar"
                  });
              }
          });
      });
</script>


<!-- Ampliar Imagen desde Editar Modal -->
<script>
$(document).on("click", ".img-ampliar-producto-modal", function(){
    var rutaImagen = $(this).attr("src");
    $("#fotoProductoAmpliada").attr("src", rutaImagen);
    $("#modalAmpliarFotoProducto").modal("show");
});

$(".nuevaImagen").change(function(){
    var imagen = this.files[0];
    
    if(imagen){
        if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){
            $(".nuevaImagen").val("");
            swal({
                title: "Error al subir la imagen",
                text: "¡La imagen debe estar en formato JPG o PNG!",
                type: "error",
                confirmButtonText: "¡Cerrar!"
            });
        }else if(imagen["size"] > 2000000){
            $(".nuevaImagen").val("");
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
                $(".previsualizar").attr("src", rutaImagen);
            });
        }
    }
});
</script>



<!-- Ampliar imagen de producto al hacer clic en la tabla -->
 <!--
<script>
$(document).on("click", ".img-ampliar-producto", function(){
    var rutaImagen = $(this).attr("data-imagen");
    $("#imagenProductoAmpliada").attr("src", rutaImagen);
    $("#modalAmpliarImagenProducto").modal("show");
});
</script>
-->

<!-- Mensaje al actualizar productos a n8n -->
<!--<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('formN8N').addEventListener('submit', function(e) {
  e.preventDefault(); // Evitar el envío tradicional del formulario
  
  // Obtener los datos del formulario
  const formData = new FormData(this);
  
  // Enviar con fetch
  fetch('https://6eddcbd9ed49.ngrok-free.app/webhook/mipos', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    alert('Productos actualizados correctamente');
  })
  .catch(error => {
    alert('Error al actualizar productos');
    console.error('Error:', error);
  });
});
</script>
-->

<!-- Actualizar productos a n8n con SweetAlert -->
<script>
document.getElementById('formN8N').addEventListener('submit', function(e) {
  e.preventDefault();
  
  // Mostrar loading
  swal({
    title: 'Actualizando productos...',
    text: 'Por favor espera',
    type: 'info',
    showConfirmButton: false,
    allowOutsideClick: false
  });
  
  const formData = new FormData(this);
  const webhookUrl = this.action;
  
  fetch(webhookUrl, {
    method: 'POST',
    headers: {
      'ngrok-skip-browser-warning': 'true',
    },
    body: formData
  })
  .then(response => {
    console.log('Status:', response.status);
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    return response.text().then(text => {
      try {
        return JSON.parse(text);
      } catch {
        return { success: true, message: text };
      }
    });
  })
  .then(data => {
    console.log('Data recibida:', data);
    swal({
      title: '¡Éxito!',
      text: 'Productos actualizados correctamente',
      type: 'success',
      timer: 2000
    });
  })
  .catch(error => {
    console.error('Error completo:', error);
    swal({
      title: 'Error',
      text: 'Error al actualizar productos: ' + error.message,
      type: 'error'
    });
  });
});
</script>


<script>
/*=============================================
FILTRO DE CATEGORÍA EN PRODUCTOS
=============================================*/

$(document).ready(function() {
  var tablaProductos = $('.tablaProductos').DataTable(); 

  // Agregar filtro personalizado a DataTables
  $.fn.dataTable.ext.search.push(
    function(settings, data, dataIndex) {
      // Verificar si es la tabla de productos
      if (settings.nTable.className.indexOf('tablaProductos') === -1) {
        return true;
      } 

      var filtroCategoria = $('#filtroCategoria').val().toLowerCase();
 

      // Si no hay filtro seleccionado, mostrar todo
      if (filtroCategoria === "") {
        return true;
      } 

      // La columna 4 (índice 4) es la categoría
      var categoriaTexto = data[4].toLowerCase(); 

      // Comparar la categoría del producto con el filtro
      return categoriaTexto.indexOf(filtroCategoria) !== -1;
    }
  );

  // Evento al cambiar el filtro
  $('#filtroCategoria').on('change', function() {
    tablaProductos.draw();
  });
});
</script>


<script>
/*=============================================
FIX MODALES DE PRODUCTOS - Mover al body para evitar bloqueos
=============================================*/

$(document).ready(function() {
  // SOLUCIÓN: Mover todos los modales al body para evitar problemas de z-index/contenedores 

  // Modal de importación
  if ($('#modalImportarProductos').length) {
    $('#modalImportarProductos').appendTo('body');
    console.log('✓ Modal de importación movido al body correctamente');
  }

  // Modal de editar producto
  if ($('#modalEditarProducto').length) {
    $('#modalEditarProducto').appendTo('body');
    console.log('✓ Modal de editar producto movido al body correctamente');
  }

  // Modal de agregar producto
  if ($('#modalAgregarProducto').length) {
    $('#modalAgregarProducto').appendTo('body');
    console.log('✓ Modal de agregar producto movido al body correctamente');
  }

  // Modal de ampliar imagen
  if ($('#modalAmpliarImagenProducto').length) {
    $('#modalAmpliarImagenProducto').appendTo('body');
    console.log('✓ Modal de ampliar imagen movido al body correctamente');
  }

  // Modal de editar variante
  if ($('#modalEditarVariante').length) {
    $('#modalEditarVariante').appendTo('body');
    console.log('✓ Modal de editar variante movido al body correctamente');
  }
});
</script>