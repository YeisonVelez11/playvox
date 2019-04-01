<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

$input = $api->detectRequestBody();
if (isset($input['idagrupacion'])) {
    $idagrupacion = $input['idagrupacion'];
} else {
    $idagrupacion = null;
}

$json = $api->get_agrupacion_sencilla($idagrupacion);

echo json_encode($json);
