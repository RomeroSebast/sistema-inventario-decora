<?php
// backend/login.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Si es una petición de verificación (OPTIONS), responde 200 y sal
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once 'config.php';

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->usuario) && !empty($data->contraseña)){
    $query = "SELECT id_usuario, nombre, tipo_usuario FROM USUARIOS 
              WHERE usuario = :user AND contraseña = :pass LIMIT 0,1";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user', $data->usuario);
    $stmt->bindParam(':pass', $data->contraseña);
    $stmt->execute();

    if($stmt->rowCount() > 0){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode([
            "status" => "success",
            "usuario" => $row
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Credenciales incorrectas"]);
    }
}
?>