<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if(isset($input['idreunion'])){
    $json = $api->delete_reunion($input['idreunion']);
}else{
    $json = "error, no se recibieron los parametros correctos";
}

echo json_encode($json);
