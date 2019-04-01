<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if(isset($input['idobjetivo'])){
    $json = $api->get_entregable($input['idobjetivo']);
}else{
    $json = "Error, no se recibieron los datos correctos";
}

echo json_encode($json);
