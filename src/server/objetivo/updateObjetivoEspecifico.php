<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if (isset($input['descripcion_objetivo'])) {
    $descripcion_objetivo = $input['descripcion_objetivo'];
} else {
    $descripcion_objetivo = null;
}

if (isset($input['idobjetivo'], $input['nombre_objetivo'])) {
    $json = $api->updateObjetivo($input['idobjetivo'], $input['nombre_objetivo'], $descripcion_objetivo);
} else {
    $json = "error, no se recibieron los datos correctos";
}

echo json_encode($json);
