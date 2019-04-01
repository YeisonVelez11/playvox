<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if(isset($input['anio'])){
    $json = $api->get_cientes($input['anio']);
}else{
    $json = "Error, nno se recibieron los datos correctos";
}


echo json_encode($json);
