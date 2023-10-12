<?php
class UsuarioController{

  public function __construct(private UsuarioGateway $gateway){

  }
  public function processRequest(string $method, ?string $id): void{
    if($id){
      $this->processResourceRequest($method, $id);
    }else{
      $this->processCollectionRequest($method);
    }
  }

  private function processResourceRequest(string $method, string $id): void{
    switch($method){
      case "GET":
        echo json_encode($this->gateway->getUserVehiculos($id));
        break;
      default: 
        http_response_code(405);
        header("Allow: GET");
    }
  }
  private function processCollectionRequest(string $method): void{
    switch($method){
      case "GET":
        $inputData = (array) json_decode(file_get_contents("php://input"), true);
        $email = $inputData["email"];
        $pwd = $inputData["pwd"];
        echo json_encode($this->gateway->checkLogin($email, $pwd));
        break;
      case "POST":
        http_response_code(201);
        $inputData = (array) json_decode(file_get_contents("php://input"), true);
        $nombre = $inputData["nombre"];
        $apellido = $inputData["apellido"];
        $telefono = $inputData["telefono"];
        $email = $inputData["email"];
        $pwd = $inputData["pwd"];
        $fecha = $inputData["fecha"];
        $id = $this->gateway->setNewUser($nombre, $apellido, $telefono, $email, $pwd, $fecha);
        echo json_encode([
          "message" => "Usuario Registrado",
          "id" => $id
        ]);
        break;
      default:
        http_response_code(405);
        header("Allow: GET, POST");
    }
  }
}