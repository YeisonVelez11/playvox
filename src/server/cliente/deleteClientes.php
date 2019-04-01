<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if(isset($input['idcliente'])){
    $json = $api->deleteClientes($input['idcliente']);
}else{
    $json = "Error, nno se recibieron los datos correctos";
}


echo json_encode($json);
