<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if(isset($input['idreunion'])){
    $idreunion = $input['idreunion'];
}else{
    $idreunion = null;
}

$json = $api->get_detalle_reunion($idreunion);

echo json_encode($json);
