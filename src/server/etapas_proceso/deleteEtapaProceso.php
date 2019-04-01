<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();
if (isset($input['idetapa_proceso'])) {

    $json = $api->delete_etapa_proceso($input['idetapa_proceso']);
} else {
    $json = "error, no se recibieron los datos correctos";
}

echo json_encode($json);
