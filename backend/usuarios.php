<?php
include_once 'config.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit;

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $stmt = $conn->query("SELECT id_usuario, nombre, usuario, tipo_usuario FROM USUARIOS");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $stmt = $conn->prepare("INSERT INTO USUARIOS (nombre, usuario, contraseña, tipo_usuario) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data->nombre, $data->usuario, $data->contraseña, $data->tipo_usuario]);
        echo json_encode(["status" => "success"]);
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM USUARIOS WHERE id_usuario = ?");
        $stmt->execute([$id]);
        echo json_encode(["status" => "success"]);
        break;
}
?>