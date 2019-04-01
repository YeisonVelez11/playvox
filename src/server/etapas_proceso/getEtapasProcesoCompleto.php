<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

$json = $api->get_etapas_procesoCompleto();

echo json_encode($json);
