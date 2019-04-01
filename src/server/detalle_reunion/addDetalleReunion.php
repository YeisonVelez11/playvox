<?php

require '../lib.php';

$api = new InfoApi();

if (isset($_POST['asunto'])) {
    $asunto = $_POST['asunto'];
} else {
    $asunto = null;
}
if (isset($_POST['descripcion'])) {
    $descripcion = $_POST['descripcion'];
} else {
    $descripcion = null;
}

if (isset($_POST['idreunion'], $_POST['identregable'])) {
    if (isset($_FILES['anexos_detalle_reunion'])) {
        //$anexos_detalle_reunion = reArrayFiles($_FILES['anexos_detalle_reunion']);
        $json = $api->addDetalleReunion($_POST['idreunion'], $_POST['identregable'], $asunto, $descripcion, $_FILES['anexos_detalle_reunion']);

    } else {
        $json = $api->addDetalleReunion($_POST['idreunion'], $_POST['identregable'], $asunto, $descripcion, null);
    }
} else {
    $json = "error, no se recibieron los datos correctos";
}

echo json_encode($json);

function reArrayFiles(&$file_post)
{   
    //var_dump($file_post);

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}
