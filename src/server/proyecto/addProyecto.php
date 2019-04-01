<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if (isset($input['descripcion_proyecto'])) {
    $descripcion_proyecto = $input['descripcion_proyecto'];
} else {
    $descripcion_proyecto = null;
}

if (isset($input['anio'])) {
    $anio = $input['anio'];
} else {
    $anio = null;
}
if (isset($input['objetivo_general'])) {
    $objetivo_general = $input['objetivo_general'];
} else {
    $objetivo_general = null;
}

if (isset($input['nombre_proyecto'],$input['idcliente'])) {
    $json = $api->addProyecto($input['nombre_proyecto'], $input['idcliente'], $anio, $descripcion_proyecto, $objetivo_general);
} else {
    $json = "error, no se recibieron los datos correctos";
}

echo json_encode($json);
