<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

$json = $api->get_detalle_etapas_proceso($input['idproceso_entregable']);

echo json_encode($json);
