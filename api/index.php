<?php
declare (strict_types=1);
/*spl_autoload_register(function ($class){
  require __DIR__ . "/src/$class.php";
});*/

require_once("src/Database.php");
require_once("src/errorHandler.php");
require_once("src/vehiculoController.php");
require_once("src/vehiculoGateway.php");
require_once("src/usuarioController.php");
require_once("src/usuarioGateway.php");
require_once("src/contactController.php");
require_once("src/contactGateway.php");
ini_set("allow_url_fopen", true);
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");
header("Content-type: application/json; charset=UTF-8");

$parts = explode("/", $_SERVER["REQUEST_URI"]);

$database = new Database("localhost", "u143391890_auto_select", "u143391890_admin", "Admin1234");
$vehiculoGateway = new VehiculoGateway($database);
$vehiculosObj = new VehiculoController($vehiculoGateway);
$usuarioGateway = new UsuarioGateway($database);
$usuariosObj = new UsuarioController($usuarioGateway);
$contactGateway = new ContactGateway();
$contactObj = new ContactController($contactGateway, $vehiculoGateway);

switch ($parts[1]) {
  case "vehiculos":
      $id = $parts[2] ?? null;
      $vehiculosObj->processRequest($_SERVER["REQUEST_METHOD"], $id);
      break;
  case "contact":
      $id = $parts[2] ?? null;
      $contactObj->processRequest($_SERVER["REQUEST_METHOD"], $id);
      break;
  case "usuarios":
      $id = $parts[2] ?? null;
      $usuariosObj->processRequest($_SERVER["REQUEST_METHOD"], $id);
      break;
  default:
      http_response_code(404);
      exit;
}