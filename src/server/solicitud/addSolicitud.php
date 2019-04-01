<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if (isset($input['nombre'])) {
    $nombre = $input['nombre'];
} else {
    $nombre = null;
}

if (isset($input['proceso_asociado'])) {
    $proceso_asociado = $input['proceso_asociado'];
} else {
    $proceso_asociado = 'null';
}

if (isset($input['descripcion_solicitud'])) {
    $descripcion_solicitud = $input['descripcion_solicitud'];
} else {
    $descripcion_solicitud = null;
}

if (isset($input['fecha_recepcion'])) {
    $fecha_recepcion = $input['fecha_recepcion'];
} else {
    $fecha_recepcion = 'null';
}

if (isset($input['duracion_dias'])) {
    $duracion_dias = $input['duracion_dias'];
} else {
    $duracion_dias = 'null';
}
if (isset($input['usuario_genera_solicitud'])) {
    $usuario_genera_solicitud = $input['usuario_genera_solicitud'];
} else {
    $usuario_genera_solicitud = 'null';
}

if (isset($input['prioridad'])) {
    $prioridad = $input['prioridad'];
} else {
    $prioridad = null;
}
if (isset($input['identregable'], $input['idresponsable'])) {
    $json = $api->addSolicitud($input['identregable'], $input['idresponsable'], $descripcion_solicitud, $fecha_recepcion, $prioridad, $duracion_dias, $usuario_genera_solicitud, $nombre, $proceso_asociado, 'Sin Iniciar');
} else {
    $json = "error, no se recibieron los datos correctoS";
}

echo json_encode($json);
