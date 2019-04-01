<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if (isset($input['idreunion'])) {
    $json = $api->getAsistentes_reunion($input['idreunion']);
} else {
    $json = "error, no se recibieron los datos correctos";
}

echo json_encode($json);
