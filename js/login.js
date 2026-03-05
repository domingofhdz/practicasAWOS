const modalErrorLogin = new bootstrap.Modal("#exampleModal", {
    keyboard: false
})

$.get("https://emails-cook-pubs-neck.trycloudflare.com/api-github/practicasAWOS/servicio.php?sesion", function (sesion) {
    if (Object.keys(sesion).length) {
        // Si inició sesión
    }
    else if (sesion.length == 0) {
        // Si no inició sesión
    }
})

$("#frmLogin").submit(function (event) {
    event.preventDefault()

    $.post("https://emails-cook-pubs-neck.trycloudflare.com/api-github/practicasAWOS/servicio.php?iniciarSesion", $(this).serialize(), function (respuesta) {
        if (respuesta == "correcto") {
            window.location = "index.html"
            return
        }

        modalErrorLogin.show()
    })
})
