<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if (isset($input['asistentes'])) {
    if (count($input['asistentes']) > 0) {
        $json = $api->addAsistentes_reunion($input['asistentes']);
    } else {
        return "error, no existen asistentes para la reunión";
    }
} else {
    $json = "error, no se recibieron los datos correctos";
}

echo json_encode($json);
