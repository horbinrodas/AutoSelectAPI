<?php

class UsuarioGateway{

  private PDO $conn;
  public function __construct(Database $database){
    $this->conn = $database->getConnection();
  }
  public function checkLogin($email, $pwd): array | false{
    $sql = "SELECT UserID, nombre, apellido, email, telefono FROM usuarios WHERE email = '$email' AND contraseña = '$pwd'";
    $stmt = $this->conn->query($sql);
    $data = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
      $data[] = $row;
    }
    return $data;
  }

  public function checkUser($email){
    $sql = "SELECT email FROM usuarios WHERE email = '$email'";
    $stmt = $this->conn->query($sql);
    if ($stmt->rowCount() > 0) {
      return false;
    } else {
      return true;
    }
  }
  public function setNewUser($nombre, $apellido, $telefono, $email, $pwd, $fechaRegistro): string{
    if(!$this->checkUser($email)){
      return "email taken";
    }
    $sql = "INSERT INTO usuarios (nombre, apellido, email, contraseña, telefono, FechaRegistro) 
            VALUES(:nombre, :apellido, :email, :contrasena, :telefono, :FechaRegistro)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":nombre", $nombre, PDO::PARAM_STR);
    $stmt->bindValue(":apellido", $apellido, PDO::PARAM_STR);
    $stmt->bindValue(":email", $email, PDO::PARAM_STR);
    $stmt->bindValue(":telefono", $telefono, PDO::PARAM_INT);
    $stmt->bindValue(":contrasena", $pwd, PDO::PARAM_STR);
    $stmt->bindValue(":FechaRegistro", $fechaRegistro, PDO::PARAM_STR);

    $stmt->execute();

    return $this->conn->lastInsertId();
  }
  
  public function getUserVehiculos($userID){
    $sql = "SELECT vehiculos.id, vehiculos.marca, vehiculos.linea, vehiculos.modelo, imglinks.img_link, usuarios.UserID
            FROM vehiculos JOIN imglinks ON vehiculos.id = imglinks.idVehiculo
            JOIN usuarios ON vehiculos.vendedor = usuarios.UserID
            WHERE vehiculos.vendedor = :id
            GROUP BY vehiculos.id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $userID, PDO::PARAM_INT);
    $stmt->execute();
    $data = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
      $data[] = $row;
    }
    return $data;
  }
}