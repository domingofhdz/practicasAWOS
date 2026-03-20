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


require "conexion.php";

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


if (isset($_GET["buscarUbicaciones"]) && $login) {
    $select = $con->select("reportes");
    $select->orderby("idReporte DESC");
    $select->limit(10);
    header("Content-Type: application/json");
    echo json_encode($select->execute());
}
elseif (isset($_GET["editarUbicacion"]) && $login) {
    $select = $con->select("reportes");
    $select->where("idReporte", "=", $_GET["id"]);
    header("Content-Type: application/json");
    echo json_encode($select->execute());
}
elseif (isset($_GET["eliminarUbicacion"]) && $login) {
    $delete = $con->delete("reportes");
    $delete->where("idReporte", "=", $_POST["hidId"]);

    if ($delete->execute()) {
        echo "correcto";
    }
    else {
        echo "error";
    }
}
elseif (isset($_GET["guardarUbicacion"]) && $login) {
    $idreporte   = $_POST["hidId"];
    $descripcion = $_POST["txtDescripcion"];
    $latitud     = $_POST["hidLatitud"];
    $longitud    = $_POST["hidLongitud"];

    if ($idreporte) {
        $update = $con->update("reportes");
        $update->set("descripcion", $descripcion);
        $update->where("idReporte", "=", $idreporte);
        if ($update->execute()) {
            echo "correcto";
        }
        else {
            echo "error";
        }
    }
    else {
        $insert = $con->insert("reportes", "descripcion, latitud, longitud");
        $insert->value($descripcion);
        $insert->value($latitud);
        $insert->value($longitud);
        $insert->execute();
    
        echo $con->lastInsertId();
    }
}

?>
