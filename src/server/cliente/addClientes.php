<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if (isset($input['descripcion_cliente'])) {
    $descripcion_cliente = $input['descripcion_cliente'];
} else {
    $descripcion_cliente = null;
}

if (isset($input['anio'])) {
    $anio = $input['anio'];
} else {
    $anio = null;
}

if (isset($input['nombre_cliente'])) {
    $json = $api->addCliente($input['nombre_cliente'], $anio, $descripcion_cliente);
} else {
    $json = "error, no se recibieron los datos correctos";
}

echo json_encode($json);
