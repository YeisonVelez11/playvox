<?php

require '../lib.php';

$api = new InfoApi();
$input = $api->detectRequestBody();

if (isset($input['id_usuario'])) {
    $json = $api->listaUsuariosId($input['id_usuario']);
} else {
    $json = "error, no se recibieron los datos correctos";
}

echo json_encode($json);
