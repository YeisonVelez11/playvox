<?php

require '../lib.php';

$api = new InfoApi();
$input = $api->detectRequestBody();

if (isset($input['idusuario'])) {
    $idusuario = $input['idusuario'];
} else {
    $idusuario = null;
}
if (isset($input['identregable'])) {
    $identregable = $input['identregable'];
} else {
    $identregable = null;
}

$json = $api->getProcesosUsuario($idusuario,$identregable);

echo json_encode($json);
