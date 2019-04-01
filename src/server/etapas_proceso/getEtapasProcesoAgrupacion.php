<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();
if (isset($input['idagrupacion'])) {
    $idagrupacion = $input['idagrupacion'];
} else {
    $idagrupacion = null;
}

$json = $api->get_etapas_proceso_agrupacion($idagrupacion);

echo json_encode($json);
