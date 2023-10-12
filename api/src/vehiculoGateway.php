<?php

class VehiculoGateway{

  private PDO $conn;
  public function __construct(Database $database){
    $this->conn = $database->getConnection();
  }
  public function getVehiculosLista(array $data): array | false{
    $marca = $data["marca"];
    $linea = $data["linea"];
    $modeloMin = $data["minModelo"];
    $modeloMax = $data["maxModelo"];
    $sql = "SELECT id, marca, linea, modelo, img_link FROM lista_vehiculos_email WHERE marca='$marca' AND linea ='$linea' AND modelo >= '$modeloMin' AND modelo <= '$modeloMax'";
    if($marca == "todos"){
      $sql = "SELECT id, marca, linea, modelo, img_link FROM lista_vehiculos_email WHERE modelo >= $modeloMin AND modelo <= $modeloMax";
    }
    else if($linea == "todos"){
      $sql = "SELECT id, marca, linea, modelo, img_link FROM lista_vehiculos_email WHERE marca='$marca' AND modelo >= $modeloMin AND modelo <= $modeloMax";
    }
    $stmt = $this->conn->query($sql);
    $data = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
      $data[] = $row;
    }
    return $data;
  }

  public function getVehiculo($id): array | false{
    $sql = "SELECT condicion, id, marca, linea, modelo, motor, transmision, email FROM lista_vehiculos_info WHERE id= :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    return $data;
  }

  public function getVehiculoImgs($id): array | false{
    $sql = "SELECT img_link, id FROM imglinks WHERE idVehiculo= :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $data = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
      $data[] = $row;
    }
    return $data;
  }

  public function setNewVehiculo(array $data): string{
    $sql = "INSERT INTO vehiculos(condicion, marca, linea, modelo, vendedor, motor, transmision) 
            VALUES(:condicion, :marca, :linea, :modelo, :vendedor, :motor,  :transmision)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":condicion", $data["condicion"], PDO::PARAM_STR);
    $stmt->bindValue(":marca", $data["marca"], PDO::PARAM_STR);
    $stmt->bindValue(":linea", $data["linea"], PDO::PARAM_STR);
    $stmt->bindValue(":modelo", $data["modelo"], PDO::PARAM_INT);
    $stmt->bindValue(":vendedor", $data["vendedor"], PDO::PARAM_INT);
    $stmt->bindValue(":motor", $data["motor"], PDO::PARAM_STR);
    $stmt->bindValue(":transmision", $data["transmision"], PDO::PARAM_STR);

    $stmt->execute();

    return $this->conn->lastInsertId();
  }

  public function editVehiculo(array $current, array $new): int{
    if(!$this->checkOwner($current["id"], $new["userID"])){
      return false;
    }
    $imgs = $new["imgs"];
    foreach($imgs as $img){
      $this->deleteImage($img);
    }
    $sql = "UPDATE vehiculos
            SET marca = :marca, linea = :linea, modelo = :modelo, motor = :motor, transmision = :transmision
            WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":marca", $new["marca"] ?? $current["marca"], PDO::PARAM_STR);
    $stmt->bindValue(":linea", $new["linea"] ?? $current["linea"], PDO::PARAM_STR);
    $stmt->bindValue(":modelo", $new["modelo"] ?? $current["modelo"], PDO::PARAM_INT);
    $stmt->bindValue(":motor", $new["motor"] ?? $current["motor"], PDO::PARAM_STR);
    $stmt->bindValue(":transmision", $new["transmision"] ?? $current["transmision"], PDO::PARAM_STR);

    $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);

    $stmt->execute();

    return $stmt->rowCount();

  }

  public function checkOwner($vehiculoID, $userID): bool{
    $sql = "SELECT id FROM vehiculos WHERE id=$vehiculoID AND vendedor=$userID";
    $stmt = $this->conn->query($sql);
    if ($stmt->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }

  public function deleteVehiculo($vehiculoID, $userID): string{
    if(!$this->checkOwner($vehiculoID, $userID)){
      return false;
    }
    if(!$this->deleteVehiculoImgs($vehiculoID)){
      return false;
    }
    $sql = "DELETE FROM vehiculos WHERE id= :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $vehiculoID, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount();
  }

  public function deleteVehiculoImgs($vehiculoID): int{
    $sql = "DELETE FROM imglinks WHERE idVehiculo= :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $vehiculoID, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount();
  }

  public function setImages($imglink, $idVehiculo): string{
    $sql = "INSERT INTO imglinks (img_link, idVehiculo) VALUES(:img_link, :idVehiculo)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":img_link", $imglink, PDO::PARAM_STR);
    $stmt->bindValue(":idVehiculo", $idVehiculo, PDO::PARAM_INT);
    $stmt->execute();
    return $this->conn->lastInsertId();
  }

  public function deleteImage($imgID): string{
    $sql = "DELETE FROM imglinks WHERE id= :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $imgID, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount();
  }
}