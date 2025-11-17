<div class="content-wrapper">

  <section class="content-header">

    <h1>
      Administrar Tipos de Actividades
      <small>Panel de Control</small>
    </h1>

    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Tipos de Actividades</li>
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarTipo">
          <i class="fa fa-plus"></i> Agregar Tipo
        </button>
      </div>

      <div class="box-body">

        <table class="table table-bordered table-striped dt-responsive tablaTiposActividades" width="100%">

          <thead>

            <tr>
              <th style="width:10px">#</th>
              <th>Nombre</th>
              <th>Orden</th>
              <th>En Uso</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
          <?php 

          $item = null;
          $valor = null; 

          $tipos = ControladorTiposActividades::ctrMostrarTiposActividades($item, $valor); 

          foreach ($tipos as $key => $value) {
            // Contar actividades usando este tipo
            $actividadesUsando = ModeloTiposActividades::mdlVerificarTipoEnUso($value["nombre"]);
            echo '<tr>
                    <td>'.($key+1).'</td>
                    <td>'.$value["nombre"].'</td>
                    <td>'.$value["orden"].'</td>
                    <td>'.$actividadesUsando.'</td>
                    <td>
                      <div class="btn-group">
                        <button class="btn btn-warning btnEditarTipoActividad" idTipo="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarTipo">
                          <i class="fa fa-pencil"></i>
                        </button>';

                        if($actividadesUsando == 0){
                          echo '<button class="btn btn-danger btnEliminarTipoActividad" idTipo="'.$value["id"].'" nombreTipo="'.$value["nombre"].'">
                                  <i class="fa fa-times"></i>
                                </button>';

                        } else { 
                          echo '<button class="btn btn-danger" disabled title="No se puede eliminar porque está en uso por '.$actividadesUsando.' actividad(es)">
                                  <i class="fa fa-times"></i>
                                </button>';
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
MODAL AGREGAR TIPO
======================================-->

<div id="modalAgregarTipo" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#3c8dbc; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar Tipo</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <!-- ENTRADA PARA EL NOMBRE -->

            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-tag"></i></span>

                <input type="text" class="form-control input-lg" name="nuevoTipoNombre" placeholder="Nombre del tipo" required>

              </div>

            </div>

          
          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Guardar tipo</button>

        </div>

      </form>

      <?php

        $crearTipo = new ControladorTiposActividades();
        $crearTipo -> ctrCrearTipo();

      ?>

    </div>

  </div>

</div>

<!--=====================================
MODAL EDITAR TIPO
======================================-->

<div id="modalEditarTipo" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#3c8dbc; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Editar Tipo</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <!-- ENTRADA PARA EL NOMBRE -->

            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-tag"></i></span>

                <input type="text" class="form-control input-lg" name="editarTipoNombre" id="editarTipoNombre" required>
                <input type="hidden" name="idTipo" id="idTipo">

              </div>

            </div>

            <!-- ENTRADA PARA EL ORDEN -->

            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-sort-numeric-asc"></i></span>

                <input type="number" class="form-control input-lg" name="editarTipoOrden" id="editarTipoOrden" min="1" required>

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

        $editarTipo = new ControladorTiposActividades();
        $editarTipo -> ctrEditarTipo();

      ?>

    </div>

  </div>

</div>

<?php

  $eliminarTipo = new ControladorTiposActividades();
  $eliminarTipo -> ctrEliminarTipo();

?>