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


require "firebase-php-jwt/vendor/autoload.php";

$headers = getallheaders();

$token = "";
if (isset($headers["Authorization"])) {
  $token = str_replace("Bearer ", "", $headers["Authorization"]);
}

try {
  # el segundo parametro es la clave para codificar y decodificar el JWT
  # debe ser una string no corta, por eso rellené de guiones
  $decoded = Firebase\JWT\JWT::decode($token, new Firebase\JWT\Key("Test12345-----------------------------------------------", "HS256"));

  # $usuario puede ser usada para validaciones
  $usuario = explode("/", $decoded->sub);
  $id      = $usuario[0];
  $usr     = $usuario[1];
  $tipo    = $usuario[2];

  # $login puede ser usada para validaciones
  $login = true;
}
catch (Exception $error) {
  $usuario = array();
  $login   = false;
}


# en cada endpoint podemos hacer uso de la variable login para seguridad
# tenemos la variable login y usuario para realizar más validaciones
if (isset($_GET["productos"]) && $login) {
  $select = $con->select("view_productos_categorias");
  $select->orderby("idProducto DESC");
  $select->limit(10);

  header("Content-Type: application/json");
  echo json_encode($select->execute());
}
elseif (isset($_GET["editarProducto"]) && $login) {
  $id = $_GET["id"];

  $select = $con->select("productos", "*");
  $select->where("idProducto", "=", $id);

  header("Content-Type: application/json");
  echo json_encode($select->execute());
}
elseif (isset($_GET["categoriasCombo"]) && $login) {
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
elseif (isset($_GET["eliminarProducto"]) && $login) {
  $delete = $con->delete("productos");
  $delete->where("idProducto", "=", $_POST["txtId"]);

  if ($delete->execute()) {
    echo "correcto";
  }
  else {
    echo "error";
  }
}
elseif (isset($_GET["agregarProducto"]) && $login) {
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
elseif (isset($_GET["modificarProducto"]) && $login) {
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
