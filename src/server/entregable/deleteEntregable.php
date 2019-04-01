<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();
if(isset($input['identregable'])){
    $json = $api->deleteEntregable($input['identregable']);
}
else{
    $json = "Error, no se recibieron los datos correctos";
}
echo json_encode($json);
