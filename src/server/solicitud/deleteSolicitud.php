<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if(isset($input['idsolicitud'])){
    $json = $api->deleteSolicitud($input['idsolicitud']);
}
else{
    $json = "Error, no se recibieron los datos correctos";
}


echo json_encode($json);
