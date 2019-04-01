<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if(isset($input['idanexo'])){
    $json = $api->deleteAnexo_detalle_reunion($input['idanexo']);
}else{
    $json = "error, no se recibieron los parametros correctos";
}

echo json_encode($json);
