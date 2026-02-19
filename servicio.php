<?php

ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL & ~E_DEPRECATED);

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Allow: GET, POST, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
  http_response_code(200);
  exit;
}

if (isset($_GET["PING"])) {
  exit;
}

date_default_timezone_set("America/Matamoros");

if (isset($_GET["DATETIME"])) {
  echo date("Y-m-d H:i:s");
  exit;
}


// ------------------------------------------------------
// ------------------------------------------------------
// Debajo de este comentario irá la configuración a la BD
// y las funciones del servicio para la aplicación móvil.

require "conexion.php";
require "enviarCorreo.php";

/**
$con = new Conexion(array(
  "tipo"       => "mysql",
  "servidor"   => "46.28.42.226",
  "bd"         => "u760464709_prueba_bd",
  "usuario"    => "u760464709_prueba_usr",
  "contrasena" => "|Au/mc*H2jH3"
));
*/
$con = new Conexion(array(
  "tipo"       => "mysql",
  "servidor"   => "localhost",
  "bd"         => "prueba",
  "usuario"    => "root",
  "contrasena" => "Test12345"
));

if (isset($_GET["iniciarSesion"])) {
  $select = $con->select("usuarios", "id");
  $select->where("usuario", "=", $_POST["usuario"]);
  $select->where_and("contrasena", "=", $_POST["contrasena"]);

  if (count($select->execute())) {
    echo "correcto";
  }
  else {
    echo "error";
  }
}
elseif (isset($_GET["productos"])) {
  $select = $con->select("view_productos_categorias");
  $select->orderby("idProducto DESC");
  $select->limit(10);

  header("Content-Type: application/json");
  echo json_encode($select->execute());
}
elseif (isset($_GET["editarProducto"])) {
  $id = $_GET["id"];

  $select = $con->select("productos", "*");
  $select->where("idProducto", "=", $id);

  header("Content-Type: application/json");
  echo json_encode($select->execute());
}
elseif (isset($_GET["categoriasCombo"])) {
  $select = $con->select("categorias", "idCategoria AS value, nombre AS label");
  $select->orderby("nombre ASC");
  $select->limit(10);

  $array = array(array("index" => 0, "value" => "", "label" => "Selecciona una opción"));

  foreach ($select->execute() as $x => $categoria) {
      $array[] = array("index" => $x + 1, "value" => $categoria["value"],  "label" => $categoria["label"]);
  }

  header("Content-Type: application/json");
  echo json_encode($array);
}
elseif (isset($_GET["eliminarProducto"])) {
  $delete = $con->delete("productos");
  $delete->where("id", "=", $_POST["txtId"]);

  if ($delete->execute()) {
    echo "correcto";
  }
  else {
    echo "error";
  }
}
elseif (isset($_GET["agregarProducto"])) {
  $prepare = $con->prepare("CALL agregarProducto(:nombre, :idCategoria, @idProducto, @nombreProducto, @idCategoria)");
  $prepare->bindParam(":nombre", $_POST["txtNombre"]);
  $prepare->bindParam(":idCategoria", $_POST["cboCategoria"]);
  $prepare->execute();

  # echo "correcto";

  $productoAgregado = array();

  foreach ($con->query("SELECT @idProducto, @nombreProducto, @idCategoria;") as $producto) {
    $productoAgregado = $producto;
  }

  header("Content-Type: application/json");
  echo json_encode($productoAgregado);
}
elseif (isset($_GET["modificarProducto"])) {
  $prepare = $con->prepare("CALL modificarProducto(:idProducto, :nombre, :idCategoria)");
  $prepare->bindParam(":idProducto", $_POST["txtId"]);
  $prepare->bindParam(":nombre", $_POST["txtNombre"]);
  $prepare->bindParam(":idCategoria", $_POST["cboCategoria"]);

  if ($prepare->execute()) {
    echo "correcto";
  }
  else {
    echo "error";
  }
}

?>
