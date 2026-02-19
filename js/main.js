function buscarProductos() {
    $.get("servicio.php?productos", function (productos) {
        $("#tbodyProductos").html("")
    
        for (let x in productos) {
            const producto = productos[x]
    
            $("#tbodyProductos").append(`<tr>
                <td>${producto.idProducto}</td>
                <td>${producto.nombre}</td>
                <td>${producto.categoria}</td>
                <td>
                    <button class="btn btn-info btn-editar mb-1 me-1" data-id="${producto.idProducto}">Editar</button>
                    <button class="btn btn-danger btn-eliminar" data-id="${producto.idProducto}">Eliminar</button>
                </td>
            </tr>`)
        }
    })
}

buscarProductos()

$.get("servicio.php?categoriasCombo", function (categorias) {
    $("#cboCategoria").html("")

    for (let x in categorias) {
        const categoria = categorias[x]

        $("#cboCategoria").append(`<option value="${categoria.value}">
            ${categoria.label}
        </option>`)
    }
})

$("#frmProducto").submit(function (event) {
    event.preventDefault()

    if ($("#txtId").val()) {
        $.post("servicio.php?modificarProducto", $(this).serialize(), function (respuesta) {
            if (respuesta == "correcto") {
                alert("Producto modificado correctamente")
                $("#frmProducto").get(0).reset()
                buscarProductos()
            }
        })
        return
    }

    $.post("servicio.php?agregarProducto", $(this).serialize(), function (respuesta) {
        if (Object.keys(respuesta).length) {
            alert(`Producto ${respuesta["1"]} agregado correctamente`)
            $("#frmProducto").get(0).reset()
            buscarProductos()


            conn.send("buscar-productos")


        }
    })
})

$(document).on("click", ".btn-editar", function (event) {
    const id = $(this).data("id")

    $.get("servicio.php?editarProducto", {
        id: id
    }, function (productos) {
        const producto = productos[0]

        $("#txtId").val(producto.idProducto)
        $("#txtNombre").val(producto.nombre)
        $("#cboCategoria").val(producto.idCategoria)
    })
})

$(document).on("click", ".btn-eliminar", function (event) {
    const id = $(this).data("id")

    if (!confirm("Deseas eliminar este producto?")) {
        return
    }

    $.post("servicio.php?eliminarProducto", {
        txtId: id
    }, function (respuesta) {
        if (respuesta == "correcto") {
            alert("Producto eliminado correctamente")
            // Asincrono (Dentro de la APP)
            buscarProductos()


            // En tiempo real para los demás clientes
            conn.send("buscar-productos")


        }
    })
})


const conn = new WebSocket("ws://localhost:8080/chat")
conn.onmessage = function (e) {
    const comando = e.data
    console.log(comando)
    if (comando == "buscar-productos") {
        // Asincrono (Dentro de la APP)
        buscarProductos()

        const toastLiveExample = document.getElementById("liveToast")
        const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample)
        toastBootstrap.show()
    }
}
conn.onopen = function (e) {
    conn.send("Conexión WebSocket Correcta")
}
