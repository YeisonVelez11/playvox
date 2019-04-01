<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if (isset($input['hora_inicio'])) {
    $hora_inicio = "'".$input['hora_inicio']."'";
} else {
    $hora_inicio = 'null';
}
if (isset($input['hora_fin'])) {
    $hora_fin = "'".$input['hora_fin']."'";
} else {
    $hora_fin = 'null';
}

if (isset($input['descripcion_reunion'])) {
    $descripcion_reunion = $input['descripcion_reunion'];
} else {
    $descripcion_reunion = null;
}

if (isset($input['observaciones'])) {
    $observaciones = $input['observaciones'];
} else {
    $observaciones = null;
}

if (isset($input['titulo_reunion'], $input['fecha_reunion'], $input['lugar_reunion'] , $input['idusuario_convoca'])) {
    $json = $api->addReunion($input['titulo_reunion'], $input['fecha_reunion'], $hora_inicio, $hora_fin, $input['lugar_reunion'], $descripcion_reunion, $input['idusuario_convoca'], $observaciones);
} else {
    $json = "error, no se recibieron los datos correctos";
}

echo json_encode($json);
