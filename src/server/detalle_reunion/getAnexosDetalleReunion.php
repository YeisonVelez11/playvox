<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if(isset($input['iddetalle_reunion'])){
    $iddetalle_reunion = $input['iddetalle_reunion'];
}else{
    $iddetalle_reunion = null;
}

$json = $api->get_anexos_detalle_reunion($iddetalle_reunion);

echo json_encode($json);
