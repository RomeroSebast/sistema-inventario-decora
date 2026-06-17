<?php
// backend/proveedores.php
include_once 'config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit;

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        try {
            $conn->exec("SET NAMES utf8");
            // Traemos todos los proveedores ordenados por el más reciente
            $stmt = $conn->prepare("SELECT id_proveedor, nombre, contacto FROM PROVEEDORES ORDER BY id_proveedor DESC");
            $stmt->execute();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->nombre)) {
            try {
                $contacto = !empty($data->contacto) ? $data->contacto : 'Sin contacto';
                // Insertamos el proveedor, el id_proveedor se genera solo por ser AUTO_INCREMENT
                $stmt = $conn->prepare("INSERT INTO PROVEEDORES (nombre, contacto) VALUES (?, ?)");
                $stmt->execute([$data->nombre, $contacto]);
                echo json_encode(["status" => "success"]);
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "El nombre es obligatorio"]);
        }
        break;

    case 'DELETE':
        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            try {
                // Al eliminar, los productos asociados pondrán su id_proveedor en NULL automáticamente
                $stmt = $conn->prepare("DELETE FROM PROVEEDORES WHERE id_proveedor = ?");
                $stmt->execute([$id]);
                echo json_encode(["status" => "success"]);
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        }
        break;
}
?>