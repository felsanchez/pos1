//Campo estatus
$(document).on("click", ".btnEditarCliente", function () {
  var idCliente = $(this).attr("idCliente");

  var datos = new FormData();
  datos.append("idClienteEditar", idCliente);

  $.ajax({
    url: "ajax/clientes.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {

      $("#editarCliente").val(respuesta.nombre); // mantiene tu variable personalizada
      $("#editarDocumentoId").val(respuesta.documento); // mantiene tu variable personalizada
      $("#editarEmail").val(respuesta.email);
      $("#editarTelefono").val(respuesta.telefono);
      $("#editarDepartamento").val(respuesta.departamento);
      $("#editarCiudad").val(respuesta.ciudad);
      $("#editarDireccion").val(respuesta.direccion);
      $("#editarFechaNacimiento").val(respuesta.fecha_nacimiento);
      $("#editarEstatus").val(respuesta.estatus);
      $("#editarNota").val(respuesta.notas);

      // Input oculto para el ID del cliente
      $("#idCliente").val(respuesta.id);
    }
  });
});


// Función para aplicar el color del estatus al contenedor de Choices.js
function aplicarColorEstatus(select) {
    const value = select.value;
    const container = select.closest('.choices');

    console.log("=== DEBUG aplicarColorEstatus ===");
    console.log("Select value:", value);
    console.log("Container found:", container); 

    if (!container) {
        console.log("ERROR: No se encontró contenedor .choices");
        return;
    }

    // Eliminar clases anteriores que empiecen con "estatus-"
    container.className = container.className
      .split(" ")
      .filter(cls => !cls.startsWith("estatus-"))
      .join(" "); 

    // Agregar la nueva clase
    const nuevaClase = "estatus-" + value.replace(/ /g, "-").toLowerCase();
    console.log("Agregando clase:", nuevaClase);
    container.classList.add(nuevaClase);
    console.log("Clases finales del contenedor:", container.className); 

    // Aplicar estilos inline directamente usando el mapa de colores
    const valueLower = value.toLowerCase();
    const color = window.estadosColores ? window.estadosColores[valueLower] : null; 

    console.log("Color encontrado para '" + valueLower + "':", color); 

    if (color) {
      const choicesInner = container.querySelector('.choices__inner');
      if (choicesInner) {

        // Aplicar con setProperty para poder usar !important
        choicesInner.style.setProperty('background-color', color, 'important');
        choicesInner.style.setProperty('border-color', color, 'important');
        choicesInner.style.setProperty('color', '#fff', 'important');
        console.log("Estilos inline aplicados al .choices__inner con !important");

        console.log("Estilo actual:", choicesInner.style.cssText);

      } else {
        console.log("ERROR: No se encontró .choices__inner dentro del contenedor");
      }

    } else {
      console.log("ERROR: No se encontró color para el estado '" + valueLower + "'");
      console.log("Colores disponibles:", window.estadosColores);
    }
  }

 // Función para inicializar Choices.js en los selects de estatus
  function inicializarChoicesEstatus() {
    console.log("=== inicializarChoicesEstatus LLAMADA ===");
    const selects = document.querySelectorAll('.cambiarEstatus');
    console.log("Total de selects .cambiarEstatus encontrados:", selects.length); 

    selects.forEach(function (select, index) {
      console.log("Procesando select #" + index, select); 

      // Evitar reinicializar si ya tiene Choices.js
      if (select.classList.contains('choices__input')) {
        console.log("Select #" + index + " ya tiene Choices.js, saltando...");
        return;
      }

     // Guardar las clases de estatus del select original

      const clasesEstatus = Array.from(select.classList).filter(cls => cls.startsWith('estatus-')); 

      console.log("Clases estatus encontradas en select #" + index + ":", clasesEstatus); 

      const choices = new Choices(select, {
        searchEnabled: false,
        itemSelectText: '',

        shouldSort: false,  // Mantener el orden original del HTML (ordenado por columna "orden")
        position: 'auto',   // Posicionar automáticamente el dropdown (arriba o abajo según espacio disponible)
        shouldSortItems: false
      }); 

      console.log("Choices.js inicializado para select #" + index); 

      // Aplicar color inicial después de que Choices.js cree el contenedor
      setTimeout(() => {
        console.log("Timeout ejecutándose para select #" + index);
        const container = select.closest('.choices');
        console.log("Container encontrado:", container); 

        if (container && clasesEstatus.length > 0) {
          // Agregar las clases de estatus al contenedor de Choices.js
          clasesEstatus.forEach(clase => {
            container.classList.add(clase);
            console.log("Clase agregada al contenedor:", clase);
          });

        } 

        aplicarColorEstatus(select);

      }, 100);

       // Cambiar color dinámicamente al cambiar el valor

      select.addEventListener('change', function () {

        console.log("Select cambió, aplicando color...");

        aplicarColorEstatus(select);
        
      });

    });

  }

  // Script para guardar el estatus por AJAX
  $(document).on("change", ".cambiarEstatus", function () {
    var idCliente = $(this).data("id");
    var nuevoEstatus = $(this).val();
    var select = $(this)[0]; // Para usarlo en aplicarColorEstatus

    $.ajax({
      url: "ajax/clientes.ajax.php",
      method: "POST",
      data: {
        idCliente: idCliente,
        nuevoEstatus: nuevoEstatus
      },
      success: function (respuesta) {
        console.log("RESPUESTA:", respuesta);
        if (respuesta === "ok") {
          aplicarColorEstatus(select); // Asegura aplicar color correcto después del cambio
        } else {
          alert("Error al guardar el estatus");
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
      }
    });
  });



  // BUSCADOR ESTATUS
// Función para aplicar el filtro personalizado
var tabla1;

function filterTable1() {
  tabla1.draw(); // Usamos directamente la instancia
}

$(document).ready(function () {
  

   tabla1 = $('.tablas1').DataTable({

    responsive: true,

    language: {
      url: "vistas/bower_components/datatables.net/Spanish.json",
      search: "Buscar:",
      lengthMenu: "Mostrar _MENU_ entradas",
      info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
      "sLoadingRecords": "Cargando...",
      "oPaginate": {
        "sFirst": "Primero",
        "sLast": "Último",
        "sNext": "Siguiente",
        "sPrevious": "Anterior"
      },
    }

  });

  // Inicializar Choices.js después de que la tabla cargue
  inicializarChoicesEstatus(); 

  // Filtro individual SOLO para tabla1
  tabla1.on('draw', function () {
    const filtroSeleccionado = $('#filtroEstatus1').val().toLowerCase(); 

    tabla1.rows().every(function () {
      const row = $(this.node());
      const estatus = row.find('td:eq(8) select option:selected').text().toLowerCase(); 

      if (filtroSeleccionado === "" || estatus === filtroSeleccionado) {
        row.show();

      } else {
        row.hide();
      }

    });

    // Reinicializar Choices.js después de cada redibujado
    inicializarChoicesEstatus(); 
  });

  // Al cambiar el filtro, disparar
  $('#filtroEstatus1').on('change', function () {
    filterTable1();
  });

});


// BUSCADOR ESTATUS 2da Tabla
// Función para aplicar el filtro personalizado
var tabla2;

function filterTable2() {
  tabla2.draw(); // Usamos directamente la instancia
}

$(document).ready(function () {
  
   

  tabla2 = $('.tablas2').DataTable({
    responsive: true,

    language: {
      url: "vistas/bower_components/datatables.net/Spanish.json",
      search: "Buscar:",
      lengthMenu: "Mostrar _MENU_ entradas",
      info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
      "sLoadingRecords": "Cargando...",
      "oPaginate": {
        "sFirst": "Primero",
        "sLast": "Último",
        "sNext": "Siguiente",
        "sPrevious": "Anterior"
      },
    }

  }); 

  // Inicializar Choices.js después de que la tabla cargue
  inicializarChoicesEstatus(); 

  // Filtro individual SOLO para tabla2
  tabla2.on('draw', function () {
    const filtroSeleccionado = $('#filtroEstatus2').val().toLowerCase(); 

    tabla2.rows().every(function () {
      const row = $(this.node());
      const estatus = row.find('td:eq(8) select option:selected').text().toLowerCase(); 

      if (filtroSeleccionado === "" || estatus === filtroSeleccionado) {
        row.show();

      } else {
        row.hide();
      }

    });
 
   // Reinicializar Choices.js después de cada redibujado
    inicializarChoicesEstatus();
  });

  // Al cambiar el filtro, disparar
  $('#filtroEstatus2').on('change', function () {
    filterTable2();
  });

});



// EDITAR NOTAS
// Permitir edición directa en campo "notas"
function inicializarEdicionNotas() {
  $('.celda-notas').off('blur').on('blur', function () {
    const id = $(this).data('id');
    const nuevaNota = $(this).text().trim();

    $.ajax({
      url: 'ajax/clientes.ajax.php',
      method: 'POST',
      data: {
        id: id,
        notas: nuevaNota,
        accion: 'actualizarNota'
      },
      success: function (respuesta) {
        console.log('Nota actualizada:', respuesta);
      },
      error: function () {
        alert('Error al actualizar la nota');
      }
    });
  });
}

// Ejecutar al cargar por primera vez
inicializarEdicionNotas();
