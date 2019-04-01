<?php

require '../lib.php';

$api = new InfoApi();

$json = $api->getTipo_Usuario();

echo json_encode($json);
