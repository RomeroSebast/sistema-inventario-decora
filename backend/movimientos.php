<?php
// backend/movimientos.php
include_once 'config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    // Verificamos que lleguen todos los datos, incluyendo el estado_material (abierto/cerrado)
    if (!empty($data->id_producto) && isset($data->cantidad) && !empty($data->tipo) && !empty($data->estado_material)) {
        
        $id_producto = $data->id_producto;
        $cantidad = intval($data->cantidad);
        $tipo = $data->tipo; // 'ENTRADA' o 'SALIDA'
        $estado = $data->estado_material; // 'cerrados' o 'abiertos'
        $id_usuario = 1; // Asignado para la bitácora
        
        try {
            $conn->beginTransaction();
            
            // 1. Guardar en la bitácora de Historial
            if ($tipo === 'ENTRADA') {
                $stmtMov = $conn->prepare("INSERT INTO ENTRADAS (id_producto, cantidad, id_usuario) VALUES (?, ?, ?)");
                $stmtMov->execute([$id_producto, $cantidad, $id_usuario]);
            } else {
                $proyecto = !empty($data->proyecto) ? $data->proyecto : 'Stock General';
                $stmtMov = $conn->prepare("INSERT INTO SALIDAS (id_producto, cantidad, proyecto, id_usuario) VALUES (?, ?, ?, ?)");
                $stmtMov->execute([$id_producto, $cantidad, $proyecto, $id_usuario]);
            }
            
            // 2. Lógica Exacta: Actualizar Stock Cerrado o Abierto sin duplicar
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
            echo json_encode(["status" => "success", "message" => "Movimiento registrado con exactitud"]);
            
        } catch (Exception $e) {
            $conn->rollBack();
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        
    } else {
        echo json_encode(["status" => "error", "message" => "Faltan datos en la solicitud"]);
    }
}
?>