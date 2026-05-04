<?php
// backend/historial.php
include_once 'config.php';

// Encabezados para permitir comunicación con Vue
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

try {
    // Consulta que une Entradas y Salidas
    $query = "SELECT 'ENTRADA' as tipo, e.cantidad, p.nombre as producto, e.fecha, u.nombre as responsable, 'N/A' as proyecto
              FROM ENTRADAS e
              JOIN PRODUCTOS p ON e.id_producto = p.id_producto
              JOIN USUARIOS u ON e.id_usuario = u.id_usuario
              UNION
              SELECT 'SALIDA' as tipo, s.cantidad, p.nombre as producto, s.fecha, u.nombre as responsable, s.proyecto
              FROM SALIDAS s
              JOIN PRODUCTOS p ON s.id_producto = p.id_producto
              JOIN USUARIOS u ON s.id_usuario = u.id_usuario
              ORDER BY fecha DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($resultados);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>