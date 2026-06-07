<?php
// backend/productos.php
include_once 'config.php';

// Forzar encabezados correctos para que Vue lo entienda sin errores
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
        
        // Usamos IFNULL y COALESCE para obligar a que si el nombre es vacío o nulo, pinte un texto rastreable
        $query = "SELECT p.id_producto, 
                         CASE WHEN p.nombre = '' OR p.nombre IS NULL THEN 'PRODUCTO SIN NOMBRE' ELSE p.nombre END as nombre,
                         CASE WHEN p.clave_proveedor = '' OR p.clave_proveedor IS NULL THEN 'SIN CLAVE' ELSE p.clave_proveedor END as clave_proveedor,
                         IFNULL(pr.nombre, 'SIN PROVEEDOR') as proveedor, 
                         IFNULL(s.cerrados, 0) as cerrados, 
                         IFNULL(s.abiertos, 0) as abiertos 
                  FROM PRODUCTOS p
                  LEFT JOIN STOCK s ON p.id_producto = s.id_producto
                  LEFT JOIN PROVEEDORES pr ON p.id_proveedor = pr.id_proveedor
                  ORDER BY p.id_producto DESC";
                  
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($resultado);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    break;


    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->nombre) && !empty($data->clave_proveedor) && !empty($data->id_proveedor)) {
            try {
                $conn->beginTransaction();
                $stmt = $conn->prepare("INSERT INTO PRODUCTOS (nombre, clave_proveedor, id_proveedor) VALUES (?, ?, ?)");
                $stmt->execute([$data->nombre, $data->clave_proveedor, $data->id_proveedor]);
                $id_nuevo = $conn->lastInsertId();

                $stmtStock = $conn->prepare("INSERT INTO STOCK (id_producto, cerrados, abiertos) VALUES (?, 0, 0)");
                $stmtStock->execute([$id_nuevo]);

                $conn->commit();
                echo json_encode(["status" => "success"]);
            } catch (Exception $e) {
                $conn->rollBack();
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
        }
        break;

    case 'DELETE':
        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $conn->prepare("DELETE FROM PRODUCTOS WHERE id_producto = ?");
            if($stmt->execute([$id])) {
                echo json_encode(["status" => "success"]);
            } else {
                echo json_encode(["status" => "error"]);
            }
        }
        break;
}
?>