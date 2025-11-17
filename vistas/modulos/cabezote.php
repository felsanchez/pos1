<?php
// Obtener configuración del sistema
$configuracion = ControladorConfiguracion::ctrObtenerConfiguracion();

// Definir logo a usar
$logoEmpresa = "vistas/img/plantilla/logo-blanco-lineal.png"; // Logo por defecto
$logoMini = "vistas/img/plantilla/icono-blanco.png"; // Logo mini por defecto

// Si existe logo en configuración, usarlo
if(!empty($configuracion["logo"]) && file_exists($configuracion["logo"])){
    $logoEmpresa = $configuracion["logo"];
    $logoMini = $configuracion["logo"]; // Usar el mismo logo para ambos
}

// Verificar actividades y gastos próximos (genera notificaciones automáticamente)
ControladorNotificaciones::ctrVerificarActividadesProximas();
ControladorNotificaciones::ctrVerificarGastosProximos();

// Verificar órdenes desde Agente IA (campo extra contiene 'n8n')
ControladorNotificaciones::ctrVerificarOrdenAgenteIA();

// Contar notificaciones no leídas
$totalNoLeidas = ControladorNotificaciones::ctrContarNoLeidas();
?>

<style>
/* Fix para dropdown de notificaciones */
.notifications-menu .dropdown-menu {
	width: 280px !important;
	padding: 0 !important;
	margin: 0 !important;
	top: 100% !important;
}

.notifications-menu .dropdown-menu > .header {
	padding: 10px;
	background-color: #ffffff;
	color: #444444;
	border-bottom: 1px solid #ddd;
}

.notifications-menu .dropdown-menu > li .menu {
	max-height: 200px;
	margin: 0;
	padding: 0;
	list-style: none;
	overflow-x: hidden;
}

.notifications-menu .dropdown-menu > li .menu > li > a {
	display: block;
	white-space: normal;
	border-bottom: 1px solid #e7e7e7;
	color: #444444;
	padding: 10px;
}

.notifications-menu .dropdown-menu > li .menu > li > a:hover {
	background: #f4f4f4;
	text-decoration: none;
}

.notifications-menu .dropdown-menu > li.footer > a {
	background-color: #ffffff;
	padding: 7px 10px;
	border-bottom: 1px solid #e7e7e7;
	color: #444444;
	text-align: center;
	display: block;
}

.notifications-menu .dropdown-menu > li.footer > a:hover {
	background: #f4f4f4;
	text-decoration: none;
}

/* Asegurar que el dropdown se muestre cuando está abierto */
.notifications-menu.open .dropdown-menu {
	display: block !important;
}
</style>

<header class="main-header">

<!--=====================================
LOGOTIPO
======================================-->
<a href="inicio" class="logo">

	<!-- logo mini -->
	<span class="logo-mini">
		<img src="<?php echo $logoMini; ?>" class="img-responsive" style="padding: 10px">
	</span>

	<!-- logo normal -->
	<span class="logo-lg">
		<img src="<?php echo $logoEmpresa; ?>" class="img-responsive" style="padding: 10px 0px">
	</span>

</a>


<!--=====================================
BARRA DE NAVEGACION
======================================-->
<nav class="navbar navbar-static-top" role="navigation">

	<!-- boton de navegacion -->
	<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
		<span class="sr-only">Toggle navigation</span>	
	</a>


	<!-- perfi de usuario -->
	<div class="navbar-custom-menu">

		<ul class="nav navbar-nav">

			<!-- Notificaciones -->
			<li class="dropdown notifications-menu">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					<i class="fa fa-bell-o"></i>
					<?php if($totalNoLeidas > 0): ?>
						<span class="label label-warning"><?php echo $totalNoLeidas; ?></span>
					<?php endif; ?>
				</a>
				<ul class="dropdown-menu">
					<li class="header">Tienes <?php echo $totalNoLeidas; ?> notificación(es)</li>
					<li>
						<!-- lista de notificaciones -->
						<ul class="menu">
							<?php
							$notificaciones = ControladorNotificaciones::ctrObtenerNotificaciones(5, true);

							if($notificaciones && count($notificaciones) > 0){
								foreach($notificaciones as $notif){
									// Determinar icono según tipo
									$icono = "fa-info-circle";
									$color = "text-blue";

									if($notif["tipo"] == "stock_agotado"){
										$icono = "fa-times-circle";
										$color = "text-red";
									} else if($notif["tipo"] == "stock_bajo"){
										$icono = "fa-exclamation-triangle";
										$color = "text-yellow";
									} else if($notif["tipo"] == "actividad_proxima"){
										$icono = "fa-calendar";
										$color = "text-blue";
									} else if($notif["tipo"] == "gasto_proximo"){
										$icono = "fa-money";
										$color = "text-orange";
									}
									else if($notif["tipo"] == "orden_agente_ia"){
										$icono = "fa-magic";
										$color = "text-green";
									}

									echo '<li>
										<a href="notificaciones">
											<i class="fa '.$icono.' '.$color.'"></i> '.$notif["titulo"].'
											<small class="text-muted"><br>'.$notif["mensaje"].'</small>
										</a>
									</li>';
								}
							} else {
								echo '<li><a href="#"><i class="fa fa-check text-green"></i> No hay notificaciones nuevas</a></li>';
							}
							?>
						</ul>
					</li>
					<li class="footer"><a href="notificaciones">Ver todas las notificaciones</a></li>
				</ul>
			</li>

			<li class="dropdown user user-menu">

				<a href="#" class="dropdown-toggle" data-toggle="dropdown">


					<?php
					
					if($_SESSION["foto"] != ""){

						echo '<img src="'.$_SESSION["foto"].'" class="user-image">';
					}
					else{

						/*PC*/echo '<img src="vistas/img/usuarios/default/anonymous.png" class="user-image">';
					}

					?>
					

					<span class="hidden-xs"><?php echo $_SESSION["nombre"]; ?></span>
					
				</a>


				<!-- Dropdown-toggle -->

				<ul class="dropdown-menu">

					<li class="user-body">

						<div class="pull-right">

							<a href="salir" class="btn btn-default btn-flat">Salir</a>								
						</div>

						</li>

				</ul>


			</li>	

		</ul>

	</div>


	
</nav>

</header>

<script>
// Fix para dropdown de notificaciones
$(document).ready(function() {
	// Forzar funcionalidad del dropdown de notificaciones
	$('.notifications-menu > a').on('click', function(e) {
		e.preventDefault();
		e.stopPropagation();

		var $parent = $(this).parent();

		// Cerrar otros dropdowns
		$('.dropdown.open').not($parent).removeClass('open');

		// Toggle este dropdown
		$parent.toggleClass('open');

		// Cerrar dropdown al hacer clic fuera
		if ($parent.hasClass('open')) {
			$(document).on('click.notif-dropdown', function(event) {
				if (!$(event.target).closest('.notifications-menu').length) {
					$parent.removeClass('open');
					$(document).off('click.notif-dropdown');
				}
			});
		} else {
			$(document).off('click.notif-dropdown');
		}
	});

	// Prevenir que se cierre al hacer clic dentro del dropdown
	$('.notifications-menu .dropdown-menu').on('click', function(e) {
		e.stopPropagation();
	});
});
</script>