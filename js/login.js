// La URL de la API y endpoints cambialos según el tunnel, hosting, local o ubicación del proyecto de la aplicación web
// URL de la API
const API = "http://localhost:81/github/practicasAWOS"

// Añade a toda petición que se realice, el header que contiene el JWT, obtenido de un almacenamiento muy persistente
$.ajaxSetup({
    headers: {
        Authorization: `Bearer ${localStorage.getItem("jwt")}`
    }
})

const modalErrorLogin = new bootstrap.Modal("#exampleModal", {
    keyboard: false
})

// Endpoint combinado con la API para comprobar si se inició sesión
$.get(`${API}/servicioInicioSesion.php?sesion`, function (sesion) {
    if (sesion.length) {
        // Si inició sesión

        return
    }

    // Si no inició sesión
    // Podrías añadir un redireccionamiento si lo crees prudente
})

$("#frmLogin").submit(function (event) {
    event.preventDefault()

    // Endpoint combinado con la API para iniciar sesión
    $.post(`${API}/servicioInicioSesion.php?iniciarSesion`, $(this).serialize(), function (respuesta) {
        if (respuesta == "error") {
            modalErrorLogin.show()
            return
        }

        // Guarda el JWT en un almacenamiento persistente
        localStorage.setItem("jwt", respuesta)
        // Cambia el redireccionamiento según tu aplicación web
        window.location = "index.html"
    })
})
