
function eliminarUsuario(codigo4) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Una vez eliminado este dato no hay vuelta atrás.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, Eliminar!"
    }).then((result) => {
        if (result.isConfirmed) {
            mandar_php4(codigo4);
        }
    });
}

function mandar_php4(codigo4) {
    parametros = { id: codigo4 };
    $.ajax({
        data: parametros,
        url: "eliminarUsuario.php",
        type: "POST",
        success: function () {
            Swal.fire("Eliminado!", "Has eliminado este Usuario.", "success").then((result) => {
                window.location.href = "registrarPersonal.php";
            });
        },
        error: function () {
            // Manejar errores si es necesario
        }
    });
}










function eliminarBodega(codigo) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Una vez eliminado este dato no hay vuelta atrás.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, Eliminar!"
    }).then((result) => {
        if (result.isConfirmed) {
            mandar_php(codigo);
        }
    });
}

function mandar_php(codigo) {
    parametros = { id: codigo };
    $.ajax({
        data: parametros,
        url: "eliminarBodega.php",
        type: "POST",
        success: function () {
            Swal.fire("Eliminado!", "Has eliminado este material.", "success").then((result) => {
                window.location.href = "moduloBodega.php";
            });
        },
        error: function () {
            // Manejar errores si es necesario
        }
    });
}



function eliminarApartamento(codigo2) {
    //este codigo sirve para ver si el id se esta obteniendo correctamente
    //console.log("ID del apartamento a eliminar: " + codigo2);
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Una vez eliminado este dato no hay vuelta atrás.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, Eliminar!"
    }).then((result) => {
        if (result.isConfirmed) {
            mandar_php2(codigo2);
        }
    });
}

function mandar_php2(codigo2) {
    parametros = { 'id_apartamento': codigo2 };
    $.ajax({
        data: parametros,
        url: "eliminarApartamento.php",
        type: "POST",
        success: function () {
            Swal.fire("Eliminado!", "Has eliminado este Apartamento.", "success").then((result) => {
                window.location.href = "apartamentos.php";
            });
        },
        error: function () {
            // Manejar errores si es necesario
        }
    });
}





function eliminarContabilidad(codigo3) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Una vez eliminado este dato no hay vuelta atrás.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, Eliminar!"
    }).then((result) => {
        if (result.isConfirmed) {
            mandar_php3(codigo3);
        }
    });
}

function mandar_php3(codigo3) {
    parametros = { 'id_material': codigo3 };
    $.ajax({
        data: parametros,
        url: "eliminarContabilidad.php",
        type: "POST",
        success: function () {
            Swal.fire("Eliminado!", "Has eliminado esta compra.", "success").then((result) => {
                window.location.href = "registroContabilidad.php";
            });
        },
        error: function () {

        }
    });
}


