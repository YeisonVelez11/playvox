<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();
if (isset($input['idetapa_proceso'])) {
    $idetapaproceso = $input['idetapa_proceso'];
} else {
    $idetapaproceso = null;
}

$json = $api->get_etapas_proceso_individual($idetapaproceso);

echo json_encode($json);
