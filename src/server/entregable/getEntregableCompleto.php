<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if (isset($input['identregable'])) {
    $identregable = $input['identregable'];
} else {
    $identregable = null;
}

$json = $api->get_entregableCompleto($identregable);

echo json_encode($json);
