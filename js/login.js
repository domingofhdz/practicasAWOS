const modalErrorLogin = new bootstrap.Modal("#exampleModal", {
    keyboard: false
})

$("#frmLogin").submit(function (event) {
    event.preventDefault()

    $.post("servicio.php?iniciarSesion", $(this).serialize(), function (respuesta) {
        if (respuesta == "correcto") {
            window.location = "index.html"
            return
        }

        modalErrorLogin.show()
    })
})
