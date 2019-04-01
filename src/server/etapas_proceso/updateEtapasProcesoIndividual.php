<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if (isset($input['nombre_proceso'])) {
    $nombre_proceso = $input['nombre_proceso'];
} else {
    $nombre_proceso = null;
}

if (isset($input['descripcion_proceso'])) {
    $descripcion_proceso = $input['descripcion_proceso'];
} else {
    $descripcion_proceso = null;
}

if (isset($input['peso_total_proceso'])) {
    $peso_total_proceso = $input['peso_total_proceso'];
} else {
    $peso_total_proceso = null;
}

if (isset($input['idproceso_entregable'], $input['estado_etapa'])) {
    $json = $api->updateEtapaProcesoIndividual($input['idproceso_entregable'], $input['estado_etapa'], $nombre_proceso, $descripcion_proceso, $peso_total_proceso);

} else {
    $json = "error, no se recibieron los datos correctos";
}

echo json_encode($json);
