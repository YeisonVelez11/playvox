<?php

require '../lib.php';

$api = new InfoApi();
$input = $api->detectRequestBody();

if (isset($input['id_usuario'])) {
    $json = $api->deleteUsuario($input['id_usuario']);
} else {
    $json = "No se enviaron los parametros correctos";
}

echo json_encode($json);
