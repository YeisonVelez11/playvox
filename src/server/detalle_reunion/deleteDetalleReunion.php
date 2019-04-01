<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if(isset($input['iddetalle_reunion'])){
    $json = $api->delete_detalle_reunion($input['iddetalle_reunion']);
}else{
    $json = "error, no se recibieron los parametros correctos";
}

echo json_encode($json);
