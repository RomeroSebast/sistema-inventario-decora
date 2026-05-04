<?php
include_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Consultar productos con su stock y proveedor
        $query = "SELECT p.id_producto, p.nombre, pr.nombre as proveedor, s.cantidad 
                  FROM PRODUCTOS p
                  INNER JOIN STOCK s ON p.id_producto = s.id_producto
                  LEFT JOIN PROVEEDORES pr ON p.id_proveedor = pr.id_proveedor";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if(!empty($data->nombre) && !empty($data->id_proveedor)) {
            try {
                $conn->beginTransaction();
                $stmt = $conn->prepare("INSERT INTO PRODUCTOS (nombre, id_proveedor) VALUES (?, ?)");
                $stmt->execute([$data->nombre, $data->id_proveedor]);
                $id_nuevo = $conn->lastInsertId();

                $stmtStock = $conn->prepare("INSERT INTO STOCK (id_producto, cantidad) VALUES (?, 0)");
                $stmtStock->execute([$id_nuevo]);

                $conn->commit();
                echo json_encode(["status" => "success", "message" => "PRODUCTO REGISTRADO"]);
            } catch (Exception $e) {
                $conn->rollBack();
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        }
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM PRODUCTOS WHERE id_producto = ?");
        $stmt->execute([$id]);
        echo json_encode(["status" => "success"]);
        break;
}
?>