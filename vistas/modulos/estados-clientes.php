<div class="content-wrapper">

  <section class="content-header">

    <h1>
      Gestionar Estados de Clientes
      <small>Panel de Control</small>
    </h1>

    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">Estados de Clientes</li>
    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarEstado">
          <i class="fa fa-plus"></i> Agregar Estado
        </button>
      </div>

      <div class="box-body">

        <table class="table table-bordered table-striped dt-responsive tablaEstadosClientes" width="100%">

          <thead>

            <tr>
              <th style="width:10px">#</th>
              <th>Nombre</th>
              <th>Color</th>
              <th>Orden</th>
              <th>Clientes usando</th>
              <th>Acciones</th>
            </tr>

          </thead>

          <tbody>

          <?php

          $item = null;
          $valor = null;

          $estados = ControladorEstadosClientes::ctrMostrarEstadosClientes($item, $valor);

          foreach ($estados as $key => $value) {

            // Contar clientes usando este estado
            $clientesUsando = ModeloEstadosClientes::mdlVerificarEstadoEnUso($value["nombre"]);

            echo '<tr>

                    <td>'.($key+1).'</td>

                    <td>'.$value["nombre"].'</td>

                    <td>
                      <span class="label" style="background-color: '.$value["color"].'; color: #fff; padding: 5px 10px; border-radius: 3px;">
                        <i class="fa fa-circle"></i> '.$value["color"].'
                      </span>
                    </td>

                    <td>'.$value["orden"].'</td>

                    <td>'.$clientesUsando.'</td>

                    <td>

                      <div class="btn-group">

                        <button class="btn btn-warning btnEditarEstado" idEstado="'.$value["id"].'" data-toggle="modal" data-target="#modalEditarEstado">
                          <i class="fa fa-pencil"></i>
                        </button>';

                        if($clientesUsando == 0){

                          echo '<button class="btn btn-danger btnEliminarEstado" idEstado="'.$value["id"].'" nombreEstado="'.$value["nombre"].'">
                                  <i class="fa fa-times"></i>
                                </button>';

                        } else {

                          echo '<button class="btn btn-danger" disabled title="No se puede eliminar porque está en uso">
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
MODAL AGREGAR ESTADO
======================================-->

<div id="modalAgregarEstado" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#3c8dbc; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Agregar Estado</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <!-- ENTRADA PARA EL NOMBRE -->

            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-flag"></i></span>

                <input type="text" class="form-control input-lg" name="nuevoEstadoNombre" placeholder="Nombre del estado" required>

              </div>

            </div>

            <!-- ENTRADA PARA EL COLOR -->

            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-paint-brush"></i></span>

                <input type="color" class="form-control input-lg" name="nuevoEstadoColor" value="#3c8dbc" required style="height: 46px;">

              </div>

              <small class="text-muted">Selecciona un color para este estado</small>

            </div>

          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>

          <button type="submit" class="btn btn-primary">Guardar estado</button>

        </div>

      </form>

      <?php

        $crearEstado = new ControladorEstadosClientes();
        $crearEstado -> ctrCrearEstado();

      ?>

    </div>

  </div>

</div>

<!--=====================================
MODAL EDITAR ESTADO
======================================-->

<div id="modalEditarEstado" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#3c8dbc; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Editar Estado</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <!-- ENTRADA PARA EL NOMBRE -->

            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-flag"></i></span>

                <input type="text" class="form-control input-lg" name="editarEstadoNombre" id="editarEstadoNombre" required>
                <input type="hidden" name="idEstado" id="idEstado">

              </div>

            </div>

            <!-- ENTRADA PARA EL COLOR -->

            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-paint-brush"></i></span>

                <input type="color" class="form-control input-lg" name="editarEstadoColor" id="editarEstadoColor" required style="height: 46px;">

              </div>

              <small class="text-muted">Selecciona un color para este estado</small>

            </div>

            <!-- ENTRADA PARA EL ORDEN -->

            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-sort-numeric-asc"></i></span>

                <input type="number" class="form-control input-lg" name="editarEstadoOrden" id="editarEstadoOrden" min="1" required>

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

        $editarEstado = new ControladorEstadosClientes();
        $editarEstado -> ctrEditarEstado();

      ?>

    </div>

  </div>

</div>

<?php

  $eliminarEstado = new ControladorEstadosClientes();
  $eliminarEstado -> ctrEliminarEstado();

?>