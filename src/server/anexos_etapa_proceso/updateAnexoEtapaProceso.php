<?php

require '../lib.php';

$api = new InfoApi();

if (isset($_POST['detalle_avance'])) {
    $detalle_soporte = $_POST['detalle_avance'];
} else {
    $detalle_soporte = null;
}

if (isset($_POST['iddetalle_avance'], $_POST['idetapa_proceso'])) {
    if (isset($_FILES['evidencia_entregable'])) {

        $json = $api->updateSoporteEntregable($_POST['estado_etapa'], $_POST['iddetalle_avance'], $_POST['idetapa_proceso'], $detalle_soporte, $_FILES['evidencia_entregable']);

    } else {
        $json = $api->updateSoporteEntregable($_POST['estado_etapa'], $_POST['iddetalle_avance'], $_POST['idetapa_proceso'], $detalle_soporte, null);
    }
} else {
    $json = "error, no se recibieron los datos correctos";
}

echo json_encode($json);
