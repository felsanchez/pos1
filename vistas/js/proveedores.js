console.log("✅ Archivo JS cargado correctamente");

$(document).ready(function(){
    console.log("✅ jQuery está funcionando");
});



/*=============================================
EDITAR PROVEEDOR
=============================================*/

$(".tablas").on("click", ".btnEditarProveedor", function(){
	var idProveedor = $(this).attr("idProveedor");
    console.log("ID Proveedor: " + idProveedor); 

	// Rellenar el input hidden
    $('#modalEditarProveedor input[name="idProveedor"]').val(idProveedor);

	var datos = new FormData();
	datos.append("idProveedor", idProveedor);

	$.ajax({

		url:"ajax/proveedores.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){           
           
            //console.log("Respuesta AJAX:", respuesta);

			$("#editarProveedor").val(respuesta["nombre"]);
            $("#editarMarca").val(respuesta["marca"]);
            $("#editarCelular").val(respuesta["celular"]);
            $("#editarCorreo").val(respuesta["correo"]);
            $("#editarDireccion").val(respuesta["direccion"]);

            // ✅ mostrar el modal
			//$('#modalEditarProveedor').modal('show');
            
		},

	}) 

});



/*=============================================
ELIMINAR PROVEEDOR
=============================================*/
$(".tablas").on("click", ".btnEliminarProveedor", function(){
	
	var idProveedor = $(this).attr("idProveedor");

	//var_dump($idProveedor);

	swal({

		title: '¿Esta seguro de borrar el proveedor?',
		text: "¡Si no lo está puede cancelar la acción!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Si, borrar proveedor!'
	}).then((result)=>{

		if(result.value){

			window.location = "index.php?ruta=proveedores&idProveedor="+idProveedor;
		}
	})
})


/*=============================================
EDITAR NOTAS PROVEEDOR - Edición en línea
=============================================*/

function inicializarEdicionNotas() {

    $('.celda-notas-proveedor').off('blur').on('blur', function () {
      const id = $(this).data('id');
      const nuevasNotas = $(this).text().trim();

      $.ajax({
        url: 'ajax/proveedores.ajax.php',
        method: 'POST',
        data: {
          id: id,
          notas: nuevasNotas,
          accion: 'actualizarNotas'
        },

        success: function (respuesta) {
          console.log('Notas actualizadas:', respuesta);
        },

        error: function () {
          alert('Error al actualizar las notas');
        }
      });
    });
}

// Ejecutar al cargar por primera vez
inicializarEdicionNotas();