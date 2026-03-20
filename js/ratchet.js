function agregarMensaje(data) {
    const mensaje = JSON.parse(data)
    $(".div-chat").append(`<div style="background: ${mensaje.colorFondo}; color: ${mensaje.colorTexto};">
        ${mensaje.usuario ? mensaje.usuario + ": " : ""}
        ${mensaje.mensaje}
    </div>`)
}

// Por defecto el ejemplo proporciona el puerto 8080, pero si has tenido problemas con ese puerto solo cambialo por uno diferente al de tu localhost, en mi caso:
// mi app ----> localhost:81
// websocket -> localhost:8080
// No usar el mismo puerto de la app.
const conn = new WebSocket("ws://localhost:8080/chat")
// const conn = new WebSocket("ws://192.168.1.74:8080/chat")

conn.onmessage = function (e) {
    const data = e.data
    console.log(data)
    agregarMensaje(data)
}
conn.onopen = function (e) {
    conn.send(JSON.stringify({
        colorFondo: "yellow",
        colorTexto: "black",
        usuario: "",
        mensaje: "Alguien se unió al chat."
    }))
}

$("#frmMensaje").submit(function (event) {
    event.preventDefault()
    const mensaje = {
        colorFondo: $("#colBg").val(),
        colorTexto: $("#colTexto").val(),
        usuario: $("#txtUsuario").val(),
        mensaje: $("#txtMensaje").val()
    }
    const data = JSON.stringify(mensaje)
    conn.send(data)
    agregarMensaje(data)
    $("#txtMensaje").val("")
})
