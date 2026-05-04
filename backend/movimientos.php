<?php
// backend/movimientos.php
include_once 'config.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id_producto) && !empty($data->cantidad) && !empty($data->tipo)) {
    $id_prod = $data->id_producto;
    $cant = $data->cantidad;
    $user = $data->id_usuario;
    $fecha = date('Y-m-d');

    if ($data->tipo == 'ENTRADA') {
        $query = "INSERT INTO ENTRADAS (id_producto, cantidad, fecha, id_usuario) 
                  VALUES (:prod, :cant, :fecha, :user)";
    } else {
        $proyecto = $data->proyecto ?? 'GENERAL';
        $query = "INSERT INTO SALIDAS (id_producto, cantidad, proyecto, fecha, id_usuario) 
                  VALUES (:prod, :cant, :proy, :fecha, :user)";
    }

    $stmt = $conn->prepare($query);
    $params = [':prod' => $id_prod, ':cant' => $cant, ':fecha' => $fecha, ':user' => $user];
    if ($data->tipo == 'SALIDA') $params[':proy'] = $proyecto;

    if ($stmt->execute($params)) {
        echo json_encode(["status" => "success", "message" => "Movimiento registrado"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al registrar"]);
    }
}
?>