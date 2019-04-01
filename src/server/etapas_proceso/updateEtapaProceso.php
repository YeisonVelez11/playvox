<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if (isset($input['descripcion_agrupacion'])) {
    $descripcion_agrupacion = $input['descripcion_agrupacion'];
} else {
    $descripcion_agrupacion = null;
}

if (isset($input['idagrupacion'], $input['nombre_agrupacion'], $input['procesos'])) {
    if(count($input['procesos']) > 0){
        $json = $api->updateEtapaProceso($input['idagrupacion'], $input['procesos'], $input['nombre_agrupacion'], $descripcion_agrupacion);
    }
    else{
        $json = $api->updateEtapaProceso(null, $input['nombre_agrupacion'], $descripcion_agrupacion);
    }
} else {
    $json = "error, no se recibieron los datos correctos";
}

echo json_encode($json);
