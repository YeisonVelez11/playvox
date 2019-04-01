<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if(isset($input['fecha_inicio'], $input['idusuario'])){
    $json = $api->getCruceEntregable($input['fecha_inicio'], $input['idusuario']);
}else{
    $json = "Error, no se recibieron los datos correctos";
}

echo json_encode($json);
