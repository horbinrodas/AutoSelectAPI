<?php

class ContactController{

  public function __construct(private ContactGateWay $gateway, private VehiculoGateway $vehiculoGateway){

  }
  public function processRequest(string $method, ?string $id): void{
    if($id){
      $this->processResourceRequest($method, $id);
    }else{
      $this->processCollectionRequest($method);
    }
  }

  private function processResourceRequest(string $method, string $id): void{
    $vehiculo = $this->vehiculoGateway->getVehiculo($id);
    $destino = $vehiculo["email"];
    switch($method){
      case "POST":
        $inputData = (array) json_decode(file_get_contents("php://input"), true);
        $name = $inputData["nombre"];
        $email = $inputData["email"];
        $razon = $inputData["razon"];
        $message = $inputData["mensaje"];
        $telefono = $inputData["telefono"];
        $mensaje = "Vehiculo: autoselect.online/vehiculo.php?id=$id\nNombre: $name\nCorreo: $email\nTelefono: $telefono\nRazon: $razon\nMensaje: $message";
        $status = $this->gateway->sendMailInternal($name, $destino, 'Nuevo mensaje de contacto', $mensaje);
        if(!$status){
          echo $status;
        }
          
        $status = $this->gateway->sendMailExternal($email);
        echo json_encode([
          "message" => "Correo Enviado",
          "status" => $status
        ]);
        break;
      default:
        http_response_code(405);
        header("Allow: POST");
    }
  }
  private function processCollectionRequest(string $method): void{
    switch($method){
      case "POST":
        http_response_code(201);
        $inputData = (array) json_decode(file_get_contents("php://input"), true);
        $name = $inputData["nombre"];
        $email = $inputData["email"];
        $message = $inputData["mensaje"];
        $marca = $inputData["marca"];
        $linea = $inputData["linea"];
        $modelo = $inputData["modelo"];

        $mensaje = "Nombre: $name\n Correo: $email\nMarca: $marca\nLinea: $linea\nModelo: $modelo\nMensaje: $message";
        $status = $this->gateway->sendMailInternal($name, 'autoselect@autoselect.online', 'Cotizacion sobre vehiculo no disponible', $mensaje);
        if(!$status){
          echo $status;
        }
      
        $status = $this->gateway->sendMailExternal($email);
        echo json_encode([
          "message" => "Correo Enviado",
          "status" => $status
        ]);
        break;
      default:
        http_response_code(405);
        header("Allow: POST");
    }
  }  

}