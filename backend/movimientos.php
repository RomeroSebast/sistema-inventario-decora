<?php
// backend/movimientos.php
include_once 'config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit;

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    // Validar que los datos requeridos no estén vacíos
    if (!empty($data->id_producto) && isset($data->cantidad) && !empty($data->estado_material) && !empty($data->tipo)) {
        
        $id_producto = $data->id_producto;
        $cantidad = intval($data->cantidad);
        $estado = $data->estado_material; // 'cerrados' o 'abiertos'
        $tipo = $data->tipo; // 'ENTRADA' o 'SALIDA'
        $id_usuario = 1; // ID por defecto de Valeria Admin para pruebas
        
        try {
            $conn->beginTransaction();
            
            // 1. Insertar el registro correspondiente en su bitácora histórica
            if ($tipo === 'ENTRADA') {
                $stmtMov = $conn->prepare("INSERT INTO ENTRADAS (id_producto, cantidad, id_usuario) VALUES (?, ?, ?)");
                $stmtMov->execute([$id_producto, $cantidad, $id_usuario]);
            } else {
                $proyecto = !empty($data->proyecto) ? $data->proyecto : 'Stock General';
                $stmtMov = $conn->prepare("INSERT INTO SALIDAS (id_producto, cantidad, proyecto, id_usuario) VALUES (?, ?, ?, ?)");
                $stmtMov->execute([$id_producto, $cantidad, $proyecto, $id_usuario]);
            }
            
            // 2. Modificar directamente el Stock en base a si es Cerrado o Abierto
            if ($tipo === 'ENTRADA') {
                if ($estado === 'cerrados') {
                    $stmtStock = $conn->prepare("UPDATE STOCK SET cerrados = cerrados + ? WHERE id_producto = ?");
                } else {
                    $stmtStock = $conn->prepare("UPDATE STOCK SET abiertos = abiertos + ? WHERE id_producto = ?");
                }
            } else { // Si es SALIDA
                if ($estado === 'cerrados') {
                    $stmtStock = $conn->prepare("UPDATE STOCK SET cerrados = cerrados - ? WHERE id_producto = ?");
                } else {
                    $stmtStock = $conn->prepare("UPDATE STOCK SET abiertos = abiertos - ? WHERE id_producto = ?");
                }
            }
            
            $stmtStock->execute([$cantidad, $id_producto]);
            $conn->commit();
            
            echo json_encode(["status" => "success", "message" => "Stock modificado correctamente"]);
            
        } catch (Exception $e) {
            $conn->rollBack();
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        
    } else {
        echo json_encode(["status" => "error", "message" => "Datos incompletos en la solicitud"]);
    }
}
?>