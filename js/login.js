const API = "http://localhost:81/api-github/practicasAWOS"

const modalErrorLogin = new bootstrap.Modal("#exampleModal", {
    keyboard: false
})

$.get(`${API}/servicio.php?sesion`, function (sesion) {
    if (Object.keys(sesion).length) {
        // Si inició sesión
    }
    else if (sesion.length == 0) {
        // Si no inició sesión
    }
})

$("#frmLogin").submit(function (event) {
    event.preventDefault()

    $.post(`${API}/servicio.php?iniciarSesion`, $(this).serialize(), function (respuesta) {
        if (respuesta == "error") {
            modalErrorLogin.show()
            return
        }

        localStorage.setItem("jwt", respuesta)
        window.location = "index.html"
    })
})
