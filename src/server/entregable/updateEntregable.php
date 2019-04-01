<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if (isset($input['fecha_estimada_inicio'])) {
    $fecha_estimada_inicio = "'".$input['fecha_estimada_inicio']."'";
} else {
    $fecha_estimada_inicio = 'null';
}

if (isset($input['fecha_estimada_fin'])) {
    $fecha_estimada_fin = "'".$input['fecha_estimada_fin']."'";
} else {
    $fecha_estimada_fin = 'null';
}
if (isset($input['nombre'])) {
    $nombre = $input['nombre'];
} else {
    $nombre = null;
}
if (isset($input['descripcion'])) {
    $descripcion = $input['descripcion'];
} else {
    $descripcion = null;
}
if (isset($input['perioricidad'])) {
    $perioricidad = $input['perioricidad'];
} else {
    $perioricidad = null;
}
if (isset($input['idresponsable'])) {
    $idresponsable = $input['idresponsable'];
} else {
    $idresponsable = null;
}

if (isset($input['idobjetivo'])) {
    $idobjetivo = $input['idobjetivo'];
} else {
    $idobjetivo = null;
}

if (isset($input['tipo_etapa'])) {
    $tipo_etapa = $input['tipo_etapa'];
} else {
    $tipo_etapa = null;
}

if (isset($input['peso_total'])) {
    $peso_total = $input['peso_total'];
} else {
    $peso_total = null;
}
if (isset($input['idproceso'])) {
    $id_proceso = $input['idproceso'];
} else {
    $id_proceso = 'null';
}

if (isset($input['id_entregable'])) {
    $json = $api->updateEntregable($input['id_entregable'], $peso_total, $tipo_etapa, $fecha_estimada_inicio, $fecha_estimada_fin, $idobjetivo, $nombre, $descripcion, $perioricidad, $idresponsable, $id_proceso);
} else {
    $json = "error, no se recibieron los datos correctos";
}

echo json_encode($json);
