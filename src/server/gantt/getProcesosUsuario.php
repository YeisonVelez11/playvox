<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

$json = $api->get_procesoUsuario($input['idusuario']);

echo json_encode($json);
