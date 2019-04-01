<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

$json = $api->get_soporteEntregable($input['idetapa_proceso']);

echo json_encode($json);
