<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if (isset($input['idproyecto'])) {
    $idproyecto = $input['idproyecto'];
} else {
    $idproyecto = null;
}

$json = $api->get_objetivo($idproyecto);

echo json_encode($json);
