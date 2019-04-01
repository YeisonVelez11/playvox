<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

/* if (isset($input['nombre_proceso'])) {
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
 */

if (isset($input['descripcion_agrupacion'])) {
    $descripcion_agrupacion = $input['descripcion_agrupacion'];
} else {
    $descripcion_agrupacion = null;
}

if (isset($input['nombre_agrupacion'], $input['procesos'])) {
    if(count($input['procesos']) > 0){
        $json = $api->addEtapaProceso($input['procesos'], $input['nombre_agrupacion'], $descripcion_agrupacion);
    }
    else{
        return "Debe tener por lo menos un proceso asociado";
    }
} else {
    $json = "error, no se recibieron los datos correctos";
}

echo json_encode($json);
