<!-- Solo muestra 2 campos en movil en la Tabla 1-->
<style>
@media (max-width: 767px) {
  /* Ocultar TODAS las columnas primero */
  .tablas td,
  .tablas th {
      display: none;
  }
  
  /* Mostrar SOLO la columna 2 y columna 6 */
  .tablas td:nth-child(3),
  .tablas td:nth-child(6),
  .tablas th:nth-child(3),
  .tablas th:nth-child(6) {
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
  
  
  
  <div class="content-wrapper">
    <section class="content-header">

      <h1>
        Administrar proveedores
      </h1>

      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li class="active">Administrar proveedores</li>
      </ol>

    </section>

    <section class="content">

      <div class="box">

        <div class="box-header with-border">

          <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarProveedor">
            
             Agregar proveedor

          </button>

        </div>


        <div class="box-body table-responsive">

          <table class="table table-bordered table-striped tablas">
              
            <thead>
              <tr>
                <th style="width: 10px">#</th>
                <th>Nombre</th>
                <th>Marca</th>
                <th>Celular</th>
                <th>Correo</th>
                <th>Dirección</th>
                <th>Productos</th>
                 <th>Notas</th>
                <th>Acciones</th>
              </tr>             
            </thead>

              <tbody>

                <?php

                  $item = null;
                  $valor = null;

                  $proveedores = ControladorProveedores::ctrMostrarProveedores($item, $valor);


                  foreach ($proveedores as $key => $value) {

                   // Contar productos asociados a este proveedor

                    $totalProductos = ModeloProveedores::mdlContarProductosPorProveedor($value["id"]);

                    echo '<tr> 

                        <td>'.($key+1).'</td>
                        <td>'.$value["nombre"].'</td>
                        <td>'.$value["marca"].

                       '<!-- Btn Ver mas o editar en Movil -->';

                         if($totalProductos == 0){
                          echo '<button class="btn btn-danger btnEliminarProveedor solo-movil btn-xs" style="float: right;" idProveedor="'.$value["id"].'"><i class="fa fa-times"></i></button>';

                        } else {
                          echo '<button class="btn btn-danger solo-movil btn-xs" style="float: right;" disabled title="No se puede eliminar porque tiene productos asociados"><i class="fa fa-times"></i></button>';
                        }

                        echo '<button class="btn btn-warning btnEditarProveedor solo-movil btn-xs" style="float: right;" idProveedor="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarProveedor"><i class="fa fa-pencil"></i></button>'

                        .'</td>'; 

                    echo '<td>'.$value["celular"].'</td>';
                    echo '<td>'.$value["correo"].'</td>';
                    echo '<td>'.$value["direccion"].'</td>';

                   echo '<td><span class="badge bg-blue">'.$totalProductos.'</span></td>'; 

                    // Columna notas editable
                    $notas = isset($value["notas"]) ? $value["notas"] : '';
                    echo '<td contenteditable="true" class="celda-notas-proveedor" data-id="'.$value['id'].'">'.$notas.'</td>'; 

                    echo '<td>
                      <div class="btn-group">

                        <button class="btn btn-warning btnEditarProveedor" idProveedor="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarProveedor"><i class="fa fa-pencil"></i></button>';

                         if($totalProductos == 0){
                          echo '<button class="btn btn-danger btnEliminarProveedor" idProveedor="'.$value["id"].'"><i class="fa fa-times"></i></button>';

                        } else {
                          echo '<button class="btn btn-danger" disabled title="No se puede eliminar porque tiene productos asociados"><i class="fa fa-times"></i></button>';
                        }

                     echo '</div>
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
MODAL AGREGAR Proveedor
======================================-->
  
<!-- Modal -->
<div id="modalAgregarProveedor" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

      <!--=====================================
      CABEZA DEL MODAL
      ======================================-->

      <div class="modal-header" style="background:#3c8dbc; color: white">

        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agregar Proveedor</h4>

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

              <input type="text" class="form-control input-lg" name="nuevoProveedor" placeholder="Ingresar nombre *" required>

             </div>

           </div>

           <!-- entrada para marca -->
            
          <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-key"></i></span>

              <input type="text" class="form-control input-lg" name="nuevaMarca" placeholder="Ingresar marca *" required>

             </div>

           </div>

            <!-- entrada para celular -->
            
          <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-phone"></i></span>

              <input type="text" class="form-control input-lg" name="nuevoCelular" placeholder="Ingresar celular *" required>

             </div>

           </div>

            <!-- entrada para correo -->
            
          <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-envelope"></i></span>

              <input type="email" class="form-control input-lg" name="nuevoCorreo" placeholder="Ingresar Email">

             </div>

           </div>

               <!-- entrada para direccion -->
            
                <div class="form-group">
                
                    <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fa fa-home"></i></span>

                    <input type="text" class="form-control input-lg" name="nuevaDireccion" placeholder="Ingresar dirección">

                    </div>

                </div>

         </div>  

       </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Guardar proveedor</button>

        </div>

          <?php

            $crearProveedor = new ControladorProveedores();
            $crearProveedor -> ctrCrearProveedor();

          ?>

     </form>

    </div>


  </div>

</div>




<!--==========================================================================================================
MODAL EDITAR Proveedor
===========================================================================================================-->
  
<!-- Modal -->
<div id="modalEditarProveedor" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

      <!--=====================================
      CABEZA DEL MODAL
      ======================================-->

      <div class="modal-header" style="background:#3c8dbc; color: white">

        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Editar Proveedor</h4>

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

              <input type="text" class="form-control input-lg" name="editarProveedor" id="editarProveedor" required>
              <input type="hidden" id="idProveedor" name="idProveedor">

             </div>

           </div>

           <!-- entrada para marca -->
            
          <div class="form-group">
          
            <div class="input-group">
              
              <span class="input-group-addon"><i class="fa fa-key"></i></span>

              <input type="text" class="form-control input-lg" name="editarMarca" id="editarMarca" required>

             </div>

           </div>
            <!-- entrada para celular -->   
            <div class="form-group">
          
                <div class="input-group">
                  
                  <span class="input-group-addon"><i class="fa fa-phone"></i></span>

                  <input type="text" class="form-control input-lg" name="editarCelular" id="editarCelular" required>

                 </div>

               </div>

                <!-- entrada para correo -->   
                <div class="form-group">
          
                    <div class="input-group">
                      
                      <span class="input-group-addon"><i class="fa fa-envelope"></i></span>

                      <input type="email" class="form-control input-lg" name="editarCorreo" id="editarCorreo">

                     </div>

                   </div>

                    <!-- entrada para direccion -->   
                    <div class="form-group">
          
                        <div class="input-group">
                          
                          <span class="input-group-addon"><i class="fa fa-home"></i></span>

                          <input type="text" class="form-control input-lg" name="editarDireccion" id="editarDireccion">

                         </div>

                    </div>

         </div>  

       </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-primary">Modificar proveedor</button>

        </div>

         <?php

            $editarProveedor = new ControladorProveedores();
            $editarProveedor -> ctrEditarProveedor();

          ?>

     </form>

    </div>


  </div>

</div>


<?php

  $borrarProveedor = new ControladorProveedores();
  $borrarProveedor -> ctrBorrarProveedor();

 ?>
