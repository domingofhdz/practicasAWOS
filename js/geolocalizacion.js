function generarMapa(longitud, latitud) {
    $("#divMapa")
    /**
    .html(`

    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d7007.717655366165!2d${longitud}!3d${latitud}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses!2smx!4v1728407007773!5m2!1ses!2smx" class="w-100" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

    `)
    */
    .html(`

    <iframe src="https://www.google.com/maps?q=${latitud},${longitud}&z=15&output=embed" class="w-100" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

    `)
}

function success(pos) {
    const crd = pos.coords

    const longitud = crd.longitude
    const latitud = crd.latitude
    const precision = crd.accuracy

    console.log("Your current position is:")
    console.log(`Latitude : ${latitud}`)
    console.log(`Longitude: ${longitud}`)
    console.log(`More or less ${precision} meters.`)

    generarMapa(longitud, latitud)

    $("#hidLatitud").val(latitud)
    $("#hidLongitud").val(longitud)
}

function error(err) {
    console.warn(`ERROR(${err.code}): ${err.message}`)
}

function buscarUbicaciones() {
    $.get("servicioUbicaciones.php?buscarUbicaciones", function (ubicaciones) {
        $("#tbodyUbicaciones").html("")

        for (let x in ubicaciones) {
            const ubicacion = ubicaciones[x]
            const id = ubicacion.idReporte
            const descripcion = ubicacion.descripcion

            $("#tbodyUbicaciones").append(`
            <tr>
                <td>${descripcion}</td>
                <td>
                    <button class="btn btn-info btn-editar" data-id="${id}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-danger btn-eliminar" data-id="${id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            `)
        }
    })
}

function obtenerUbicacion() {
    navigator.geolocation.getCurrentPosition(success, error, options)
}

function revisarSesion() {
    $.get(`${API}/servicioInicioSesion.php?sesion`, function (sesion) {
        if (sesion.length) {
            $("#btnCerrarSesion")
            .show()
            .css("visibility", "visible")
    
            return
        }
    
        $("#btnIniciarSesion")
        .show()
        .css("visibility", "visible")
        $("#tbodyUbicaciones").html("")
    })
}

const API = "http://localhost:81/api-github/practicasAWOS"

// Añade a toda petición que se realice, el header que contiene el JWT, obtenido de un almacenamiento muy persistente
$.ajaxSetup({
    headers: {
        Authorization: `Bearer ${localStorage.getItem("jwt")}`
    }
})

const options = {
    enableHighAccuracy: true,
    timeout: 10000,
    maximumAge: 0,
}

buscarUbicaciones()
obtenerUbicacion()
revisarSesion()

window.addEventListener("pageshow", function(event) {
    if (event.persisted) {
        if (!localStorage.getItem("jwt")) {
            window.location = "login.html"
        }
        else {
            location.reload()
        }
    }
})

$("#btnCerrarSesion").click(function (event) {
    localStorage.removeItem("jwt")
    window.location = "login.html"
})

$("#frmUbicacion")
.submit(function (event) {
    event.preventDefault()

    $.post(`${API}/servicioUbicaciones.php?guardarUbicacion`, $(this).serialize(), function (respuesta) {
        if (respuesta) {
            if (respuesta == "correcto") {
                alert("Reporte modificado!")
            }
            else if (respuesta == "error") {
                alert("No hubieron cambios!")
            }
            else if (!isNaN(respuesta)) {
                alert("Ubicación registrada!")
            }

            $("#frmUbicacion").get(0).reset()
            buscarUbicaciones()
        }
    })
})
.on("reset", function (event) {
    $("#hidId").val("")
    obtenerUbicacion()
})

$(document).on("click", ".btn-editar", function (event) {
    const idUbicacion = $(this).data("id")

    $.get(`${API}/servicioUbicaciones.php?editarUbicacion`, {
        id: idUbicacion
    }, function (ubicaciones) {
        const ubicacion = ubicaciones[0]
        const id = ubicacion.idReporte
        const descripcion = ubicacion.descripcion
        const latitud = ubicacion.latitud
        const longitud = ubicacion.longitud

        $("#hidId").val(id)
        $("#txtDescripcion").val(descripcion)
        $("#hidLatitud").val(latitud)
        $("#hidLongitud").val(longitud)

        generarMapa(longitud, latitud)
    })
})

$(document).on("click", ".btn-eliminar", function (event) {
    const id = $(this).data("id")

    if (!confirm("Deseas eliminar esta ubicación?")) {
        return
    }

    $.post(`${API}/servicioUbicaciones.php?eliminarUbicacion`, {
        hidId: id
    }, function (respuesta) {
        if (respuesta == "correcto") {
            alert("Ubicación eliminada correctamente")
            buscarUbicaciones()
        }
    })
})
