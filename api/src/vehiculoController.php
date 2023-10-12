<?php
class VehiculoController{

  public function __construct(private vehiculoGateway $gateway){

  }
  public function processRequest(string $method, ?string $id): void{
    if($id){
      $this->processResourceRequest($method, $id);
    }else{
      $this->processCollectionRequest($method);
    }
  }

  private function processResourceRequest(string $method, string $id): void{
    $vehiculo = $this->gateway->getVehiculo($id);
    $vehiculoImgs = $this->gateway->getVehiculoImgs($id);
    if(!$vehiculo){
      http_response_code(404);
      echo json_encode(["message" => "0 Resultados"]);
      return;
    }
    $vehiculo['img_links'] = $vehiculoImgs;
    switch($method){
      case "GET":
        echo json_encode($vehiculo);
        break;
      case "POST":
        $inputData = (array) file_get_contents("php://input");
        $inputData = array(
          "marca" => $_POST["marca"],
          "linea" => $_POST["linea"],
          "modelo" => $_POST["modelo"],
          "motor" => $_POST["motor"],
          "transmision" => $_POST["transmision"],
          "userID" => $_POST["userID"],
          "imgs" => explode("<#>", $_POST["imgs"])
        );
        $rows = $this->gateway->editVehiculo($vehiculo, $inputData);
        $this->uploadImgs($id);
        echo json_encode([
          "message" => "Vehiculo $id Actualizado",
          "rows" => $rows
        ]);
        break;
      case "DELETE":
        $inputData = (array) json_decode(file_get_contents("php://input"), true);
        $rows = $this->gateway->deleteVehiculo($id, $inputData["userID"]);
        
        echo json_encode([
          "message" => "Vehiculo $id Eliminado",
          "rows" => $rows
        ]);
        break;
      default:
        http_response_code(405);
        header("Allow: GET, POST, DELETE");
    }
  }
  private function processCollectionRequest(string $method): void{
    switch($method){
      case "GET":
        $inputData = (array) json_decode(file_get_contents("php://input"), true);
        echo json_encode($this->gateway->getVehiculosLista($inputData));
        break;
      case "POST":
        http_response_code(201);
        $inputData = (array) file_get_contents("php://input");
        $inputData = array(
          "condicion" => $_POST["condicion"],
          "marca" => $_POST["marca"],
          "linea" => $_POST["linea"],
          "modelo" => $_POST["modelo"],
          "motor" => $_POST["motor"],
          "transmision" => $_POST["transmision"],
          "vendedor" => $_POST["vendedor"]
        );
        $id = $this->gateway->setNewVehiculo($inputData);
        $this->uploadImgs($id);
        echo json_encode([
          "message" => "Vehiculo Registrado",
          "id" => $id
        ]);
        break;
      default:
        http_response_code(405);
        header("Allow: GET, POST");
    }
  }

  private function uploadImgs($id){
    $countfiles = count($_FILES['file']['name']);
        $totalFileUploaded = 0;
        for($i=0;$i<$countfiles;$i++){
          $filename = $_FILES['file']['name'][$i];
          ## Location
          $extension = pathinfo($filename,PATHINFO_EXTENSION);

          ## File upload allowed extensions
          $valid_extensions = array("jpg","jpeg","png");

          $response = 0;
          $ftp_server = "autoselect.online";
          $ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");

          //login to FTP server
          $login = ftp_login($ftp_conn, 'u143391890', 'Admin1234');
          $remote_filename = uniqid('',true).".".$extension;
          $local_filename = $_FILES['file']["tmp_name"][$i];
          
          ## Check file extension
          if(in_array(strtolower($extension), $valid_extensions)) {
            if (ftp_put($ftp_conn, '/public_html/uploads/'.$remote_filename, $local_filename, FTP_BINARY)){
              echo "Successfully uploaded $local_filename.";
              $this->gateway->setImages('https://autoselect.online/uploads/'.$remote_filename, $id);
              $totalFileUploaded++;
            }else{
              echo "Error uploading $local_filename.";
            }
            ftp_close($ftp_conn);
          }
        }
  }
}