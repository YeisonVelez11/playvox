<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();
$json = "";
if (isset($input['objetivos_especificos'])) {

    foreach ($input['objetivos_especificos'] as $key => $value) {
        
        if (isset($value['descripcion_objetivo'])) {
            $descripcion_objetivo = $value['descripcion_objetivo'];
        } else {
            $descripcion_objetivo = null;
        }
        
        if (isset($value['nombre_objetivo'], $value['idproyecto'])) {
            $json = $api->addObjetivo($value['nombre_objetivo'],$value['idproyecto'], $descripcion_objetivo);
        } else {
            $json = "error, no se recibieron los datos correctos";
        }
    }

}

echo json_encode($json);
