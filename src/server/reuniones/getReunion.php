<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();
if (isset($input['fecha_inicio'])) {
    $fecha_inicio = $input['fecha_inicio'];
} else {
    $fecha_inicio = null;
}
if (isset($input['fecha_fin'])) {
    $fecha_fin = $input['fecha_fin'];
} else {
    $fecha_fin = null;
}
if (isset($input['hora_inicio'])) {
    $hora_inicio = $input['hora_inicio'];
} else {
    $hora_inicio = null;
}
if (isset($input['hora_fin'])) {
    $hora_fin = $input['hora_fin'];
} else {
    $hora_fin = null;
}
if (isset($input['idusuario_convoca'])) {
    $idusuario_convoca = $input['idusuario_convoca'];
} else {
    $idusuario_convoca = null;
}
if (isset($input['identregable'])) {
    $identregable = $input['identregable'];
} else {
    $identregable = null;
}

$json = $api->getReunion($fecha_inicio, $fecha_fin, $hora_inicio, $hora_fin, $idusuario_convoca, $identregable);

echo json_encode($json);
