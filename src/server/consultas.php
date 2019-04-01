<?php

function executeQuery($con, $sql)
{
    $result = $con->query($sql);
    if ($result) {
        $fetched_data = array();
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            array_push($fetched_data, $row);
        }
        return $fetched_data;
    } else {
        return $con->errorInfo()[2];
    }
}
function executeQueryInsert($con, $sql)
{
    $result = $con->query($sql);
    if ($result) {
        return "ok";
    } else {
        return $con->errorInfo()[2];
    }
}

//login
function logQuery($con, $usr, $pwd)
{
    $sql = "SELECT titulo_usuario, idtipo_usuario, idusuario, nombre, (nombre || ' ' || apellido) as nombre_completo, cargo, titulo_usuario as tipo_usuario_nombre
    FROM usuario JOIN tipo_usuario ON tipo_usuario_fk = idtipo_usuario
    WHERE email='$usr' AND contrasena='$pwd'";

    return executeQuery($con, $sql);
}

//listar usuarios
function listarUsuario($con)
{
    $sql = "SELECT idusuario,(nombre ||' '|| apellido) as nombre,titulo_usuario as tipo_usuario_nombre,
    email, contrasena, titulo_usuario as tipo_usuario, empresa, cargo, acceso_plataforma, imagen
    FROM usuario
    INNER JOIN tipo_usuario on tipo_usuario_fk = idtipo_usuario
    order by nombre";
    return executeQuery($con, $sql);

}

//listar usuarios
function listarUsuarioId($con, $idusuario)
{
    $sql = "SELECT idusuario,nombre, apellido, tipo_usuario_fk, titulo_usuario as tipo_usuario_nombre,
    email, contrasena, empresa, cargo, acceso_plataforma, imagen
    FROM usuario
    INNER JOIN tipo_usuario on tipo_usuario_fk = idtipo_usuario
    WHERE idusuario = $idusuario
    order by nombre";
    return executeQuery($con, $sql);

}

/* Insertar Usuarios */
function addUsuario($con, $nombre, $apellido, $email, $contrasena, $idtipo_usuario, $empresa, $cargo, $acceso_plataforma, $imagen)
{
    $sql = "INSERT INTO public.usuario(
    idusuario, nombre, apellido, email, contrasena, tipo_usuario_fk, empresa, cargo, acceso_plataforma, imagen)
    VALUES (nextval('usuario_sec'), '$nombre', '$apellido', '$email','$contrasena', $idtipo_usuario, '$empresa', '$cargo','$acceso_plataforma' ,'$imagen');";

    $result = $con->query($sql);
    if ($result) {
        return "ok";
    } else {
        return "error al insertar en la base de datos";
    }
}

//consulta tipo usuario
function getTipoUsuario($con)
{
    $sql = "SELECT idtipo_usuario, titulo_usuario, descripcion_usuario, realiza_revision
	        FROM public.tipo_usuario
            ORDER BY 1 DESC;";
    return executeQuery($con, $sql);

}

/* Insertar Usuarios */
function updateUsuario($con, $idusuario, $nombre, $apellido, $email, $contrasena, $idtipo_usuario, $empresa, $cargo, $acceso_plataforma, $imagen)
{
    $sql = "UPDATE public.usuario
	SET nombre='$nombre', apellido='$apellido', email='$email', contrasena='$contrasena', tipo_usuario_fk=$idtipo_usuario, empresa='$empresa', cargo='$cargo', acceso_plataforma='$acceso_plataforma', imagen='$imagen'
	WHERE idusuario = $idusuario;";

    $result = $con->query($sql);
    if ($result) {
        return "ok";
    } else {
        return "error al modificar en la base de datos";
    }
}
/* Eliminar usuarios */

function delete_usuario($con, $idusuario)
{
    $sql = "DELETE FROM usuario
                WHERE idusuario = $idusuario";
    return executeQueryInsert($con, $sql);

}

//consulta clientes
function getClientes($con, $anio)
{
    $sql = "SELECT idcliente, nombre_cliente, descripcion_cliente, anio
	FROM public.cliente
    WHERE anio = '$anio'
    ORDER BY 1 DESC;";
    return executeQuery($con, $sql);
}

//consulta proyecto
function getProyecto($con, $anio, $idcliente)
{
    $sql = "SELECT idproyecto, nombre_proyecto, descripcion_proyecto, cliente_fk, anio, objetivo_general
	FROM public.proyecto
    WHERE anio = '$anio' AND cliente_fk = $idcliente
    ORDER BY 1 DESC;";
    return executeQuery($con, $sql);
}

//consulta objetivo
function getObjetivo($con, $idproyecto)
{
    $wherePost = "";
    if ($idproyecto != null) {
        $wherePost = " AND proyecto_fk = $idproyecto";
    }

    $sql = "SELECT idobjetivo, nombre_objetivo, descripcion_objetivo
	FROM public.objetivo
    WHERE 1=1
    $wherePost
    ORDER BY 1 DESC;";
    return executeQuery($con, $sql);
}

//consulta entregable
function getEntregable($con, $idobjetivo)
{
    $sql = "SELECT identregable, fecha_estimada_inicio, fecha_estimada_fin, peso_total, nombre, descripcion, perioricidad, responsable_fk, objetivo_fk, tipo_etapa, proceso_fk
	FROM public.entregable
    WHERE objetivo_fk = $idobjetivo
    ORDER BY 1 desc;";
    return executeQuery($con, $sql);
}

//consulta objetivo
function getAnexosSoporteEntregable($con, $idsoporte)
{
    $sql = "SELECT idanexo_entregable, nombre_anexo
	FROM public.anexos_etapas_proceso
    WHERE soporte_entregable_fk = $idsoporte
    ORDER BY 1 DESC;";
    return executeQuery($con, $sql);
}

//consulta entregable
function getEntregableCompleto($con, $identregable)
{
    $wherePost = "";
    if ($identregable != null) {
        $wherePost = " AND identregable = $identregable";
    }

    $sql = "SELECT DISTINCT identregable, fecha_estimada_inicio, fecha_estimada_fin, peso_total, tipo_etapa,
    entregable.nombre, entregable.descripcion, perioricidad, responsable_fk as responsable,proceso_pertenece,
    sum(peso_total_proceso) AS progreso_actual, proceso_fk as proceso_asociado
    FROM public.entregable
	LEFT JOIN procesos on idprocesos = proceso_fk
    LEFT JOIN proceso_entregable on idprocesos = proceso_pertenece
    INNER JOIN usuario on idusuario = responsable_fk
    WHERE 1 = 1
    $wherePost
    GROUP BY identregable,idusuario,proceso_pertenece
	ORDER BY 1 DESC;";
    return executeQuery($con, $sql);
}
//consulta procesos por uuario
function get_ProcesosUsuario($con, $idusuario, $identregable)
{
    $procesos = array();
    $entregables = array();
    $objetivo = array();
    $proyectos = array();
    $cliente = array();
    $result = array();

    $wherePostUsuario = "";
    $wherePostEntregable = "";
    if ($identregable != null) {
        $wherePostUsuario = " AND identregable = $identregable";
    }
    if ($idusuario != null) {
        $wherePostEntregable = " AND responsable_fk = $idusuario";
    }

    $sql = "SELECT DISTINCT idprocesos as idagrupacion, nombre_procesos as nombre_agrupacion, descripcion_procesos as descripcion_agrupacion,
    idproceso_entregable, nombre_proceso, proceso_entregable.descripcion as descripcion_proceso,
    peso_total_proceso, estado_etapa
    FROM public.proceso_entregable
    INNER JOIN procesos on idprocesos = proceso_pertenece
    INNER JOIN entregable on proceso_fk = idprocesos $wherePostUsuario
    WHERE 1 = 1
    $wherePostEntregable
    ORDER BY 1 DESC;";
    $procesos['procesos'] = executeQuery($con, $sql);

    $sql = "SELECT DISTINCT identregable, fecha_estimada_inicio, fecha_estimada_fin, peso_total as peso_total_proceso,
    tipo_etapa, entregable.nombre as nombre_entregable, entregable.descripcion, perioricidad, proceso_fk as idproceso_agrupacion, objetivo_fk as idobjetivo
    FROM public.entregable
    WHERE 1 = 1
    $wherePostUsuario
    $wherePostEntregable
    ORDER BY 2 ASC;";
    $entregables['entregables'] = executeQuery($con, $sql);

    $sql = "SELECT DISTINCT idobjetivo, nombre_objetivo, descripcion_objetivo, proyecto_fk
	FROM public.objetivo
	INNER JOIN entregable on objetivo_fk = idobjetivo $wherePostUsuario
    WHERE 1 = 1
    $wherePostEntregable
    ORDER BY 1 DESC;";
    $objetivo['objetivo'] = executeQuery($con, $sql);

    $sql = "SELECT DISTINCT idproyecto, nombre_proyecto, descripcion_proyecto, cliente_fk, anio as anio_proyecto, objetivo_general
	FROM public.proyecto
	INNER JOIN objetivo on proyecto_fk = idproyecto
    INNER JOIN entregable on objetivo_fk = idobjetivo $wherePostUsuario
    WHERE 1 = 1
    $wherePostEntregable
    ORDER BY 1 DESC;";
    $proyectos['proyectos'] = executeQuery($con, $sql);

    $sql = "SELECT DISTINCT idcliente, nombre_cliente, descripcion_cliente, cliente.anio AS anio_cliente
	FROM public.cliente
	INNER JOIN proyecto on cLiente_fk = idcliente
	INNER JOIN objetivo on proyecto_fk = idproyecto
    INNER JOIN entregable on objetivo_fk = idobjetivo $wherePostUsuario
    WHERE 1 = 1
    $wherePostEntregable
    ORDER BY 1 DESC;";
    $cliente['cliente'] = executeQuery($con, $sql);

    array_push($result, $cliente, $proyectos, $objetivo, $entregables, $procesos);

    return $result;
}

//consulta detalle reunion
function getCruceEntregable($con, $fecha_inicio, $idusuario)
{
    $sql = "SELECT identregable, fecha_estimada_inicio, fecha_estimada_fin, responsable_fk
	FROM public.entregable
	WHERE '$fecha_inicio' BETWEEN fecha_estimada_inicio AND fecha_estimada_fin
	AND responsable_fk = $idusuario;";

    return executeQuery($con, $sql);
}
//consulta detalle reunion
function get_detalle_reunion($con, $idreunion)
{
    $response = "";
    $wherePost = "";
    if ($idreunion != null) {
        $wherePost = " AND reunion_fk = $idreunion";
    }

    $sql = "SELECT iddetalle_reunion, identregable, asunto, detalle_reunion.descripcion as descripcion_detalle_reunion, idcliente, idproyecto, idobjetivo, cliente.anio as anio_cliente, proyecto.anio as proyecto_anio
    FROM public.detalle_reunion
    INNER JOIN entregable on entregable_fk = identregable
    INNER JOIN objetivo on objetivo_fk = idobjetivo
    INNER JOIN proyecto on proyecto_fk = idproyecto
    INNER JOIN cliente on cliente_fk = idcliente
    WHERE 1 = 1
    $wherePost
    ORDER BY 1 DESC;";

    return executeQuery($con, $sql);
}

//consulta entregable
function getAnexos_detalle_reunion($con, $iddetalle)
{
    $wherePost = "";
    if ($iddetalle != null) {
        $wherePost = " AND detalle_reunion_fk = $iddetalle";
    }

    $sql = "SELECT idanexos_detalle_reunion, nombre_anexo
    FROM public.anexos_detalle_reunion
    WHERE 1 = 1
    $wherePost
    ORDER BY 1 DESC;";

    return executeQuery($con, $sql);
}

//consulta entregable
function getsolicitudCompleto($con, $idsolicitud, $idusuario, $identregable)
{
    $wherePost = "";
    if ($idsolicitud != null) {
        $wherePost = " AND idsolicitud = $idsolicitud";
    }
    if ($idusuario != null) {
        $wherePost = " AND solicitudes.responsable_fk = $idusuario";
    }
    if ($identregable != null) {
        $wherePost = " AND entregable_fk = $identregable";
    }

    $sql = "SELECT idsolicitud, entregable_fk, solicitudes.descripcion as descripcion_solicitud, fecha_recepcion,
    duracion_dias, solicitudes.responsable_fk as idresponsable, usuario_genera_solicitud, prioridad,
    solicitudes.nombre as nombre_solicitud, solicitudes.proceso_fk as proceso_solicitud,
	identregable, idobjetivo, idproyecto, idcliente, cliente.anio as anio_cliente, proyecto.anio as proyecto_anio, estado_solicitud
    FROM public.solicitudes
    INNER JOIN entregable on entregable_fk = identregable
    INNER JOIN objetivo on objetivo_fk = idobjetivo
    INNER JOIN proyecto on proyecto_fk = idproyecto
    INNER JOIN cliente on cliente_fk = idcliente
    WHERE 1 = 1
    $wherePost
    ORDER BY 1 DESC";
    return executeQuery($con, $sql);

}

//consulta etapas proceso individual
function getEtapasProcesoindividual($con, $idetapaproceso)
{

    $wherePost = "";
    if ($idetapaproceso != null) {
        $wherePost = " AND idproceso_entregable = $idetapaproceso";
    }
    $sql = "SELECT idproceso_entregable, nombre_proceso, descripcion, peso_total_proceso, estado_etapa, proceso_pertenece as proceso_agrupacion
	FROM public.proceso_entregable
    WHERE 1 = 1
    $wherePost
    ORDER BY 1 DESC";
    return executeQuery($con, $sql);

}

//consulta etapas proceso agrupacion
function getEtapasProcesoAgrupacion($con, $idagrupacion)
{
    $wherePost = "";
    if ($idagrupacion != null) {
        $wherePost = " AND idprocesos = $idagrupacion";
    }
    $sql = "SELECT idprocesos, nombre_procesos as nombre_agrupacion, descripcion_procesos as descripcion_agrupacion
	FROM public.procesos
    WHERE 1 = 1
    $wherePost
    ORDER BY 1 DESC";
    return executeQuery($con, $sql);
}

//consulta etapas proceso agrupacion con las etapas de procesos que cuanta
function getEtapasProcesoAgrupacionDetalles($con, $idagrupacion)
{
    $wherePost = "";
    if ($idagrupacion != null) {
        $wherePost = " AND proceso_pertenece = $idagrupacion";
    }
    $sql = "SELECT idproceso_entregable, nombre_proceso, descripcion, peso_total_proceso, estado_etapa, proceso_pertenece as proceso_agrupacion
	FROM public.proceso_entregable
    WHERE 1 = 1
    $wherePost
    ORDER BY 1 DESC";
    return executeQuery($con, $sql);
}

//consulta agrupacion sencilla
function getAgrupacionSencilla($con, $idagrupacion)
{
    $wherePost = "";
    if ($idagrupacion != null) {
        $wherePost = " AND idprocesos = $idagrupacion";
    }
    $sql = "SELECT idprocesos as idagrupacion, nombre_procesos as nombre_agrupacion, descripcion_procesos as descripcion_agrupacion
	FROM public.procesos
    WHERE 1 = 1
    $wherePost
    ORDER BY 1 DESC";
    return executeQuery($con, $sql);
}

//delete etapas proceso
function deleteEtapaProceso($con, $idetapa)
{
    $sql = "DELETE FROM public.proceso_entregable
    WHERE idproceso_entregable = $idetapa;";
    return executeQueryInsert($con, $sql);
}

//delete etapas proceso
function deleteEtapaProcesoAgrupacion($con, $idagrupacion)
{
    $sql = "SELECT * FROM public.entregable
    WHERE proceso_fk = $idagrupacion;";
    if (count(executeQuery($con, $sql)) == 0) {
        $sql = "DELETE FROM public.proceso_entregable
                WHERE proceso_pertenece = $idagrupacion;";
        $response = executeQueryInsert($con, $sql);
        if ($response == 'ok') {
            $sql = "DELETE FROM public.procesos
                    WHERE idprocesos = $idagrupacion;";
            return executeQueryInsert($con, $sql);
        }
    } else {
        return "No se puede eliminar este registro, algunos entregables dependen de este";
    }
}

//consulta etapas proceso
function getEtapasProceso($con, $identregable)
{
    $sql = "SELECT idproceso_entregable, nombre_proceso, proceso_entregable.descripcion, peso_total_proceso, estado_etapa, proceso_pertenece
	FROM public.proceso_entregable
	INNER JOIN procesos on proceso_pertenece = idprocesos
	INNER JOIN entregable on proceso_fk = idprocesos
    WHERE identregable = $identregable
    ORDER BY 1 DESC;";
    return executeQuery($con, $sql);
}

//consulta etapas proceso completo
function getEtapasProcesoCompleto($con)
{
    $sql = "SELECT idproceso_entregable, nombre_proceso, descripcion, peso_total_proceso, estado_etapa, proceso_pertenece
	FROM public.proceso_entregable
    ORDER BY 1 DESC;";
    return executeQuery($con, $sql);
}

//consulta etapas proceso
function getDetalleEtapasProceso($con, $idproceso)
{
    $sql = "SELECT idsoporte as iddetalle_soporte, detalle_soporte, fecha_recepcion
    FROM public.soportes_etapas_proceso
    WHERE proceso_entregable_fk = $idproceso
    ORDER BY 1 DESC";
    return executeQuery($con, $sql);
}

//consulta etapas proceso
function getDetalleEtapasProcesoId($con, $idproceso)
{
    $sql = "SELECT idsoporte as iddetalle_soporte, detalle_soporte, fecha_recepcion
    FROM public.soportes_etapas_proceso
    WHERE idsoporte = $idproceso
    ORDER BY 1 DESC";
    return executeQuery($con, $sql);
}

//insertar entregables
function add_Entregable($con, $peso_total, $tipo_etapa, $fecha_estimada_inicio, $fecha_estimada_fin, $idobjetivo, $nombre, $descripcion, $perioricidad, $idresponsable, $id_proceso)
{

    $sql = "INSERT INTO public.entregable(
        identregable, fecha_estimada_inicio, fecha_estimada_fin, peso_total, tipo_etapa, nombre, descripcion, perioricidad, responsable_fk, objetivo_fk, proceso_fk)
        VALUES (nextval('entregable_sec'), $fecha_estimada_inicio, $fecha_estimada_fin, $peso_total, '$tipo_etapa', '$nombre', '$descripcion', '$perioricidad', $idresponsable, $idobjetivo, $id_proceso);";
    return executeQueryInsert($con, $sql);
}

//update entregables
function update_Entregable($con, $id_entregable, $peso_total, $tipo_etapa, $fecha_estimada_inicio, $fecha_estimada_fin, $idobjetivo, $nombre, $descripcion, $perioricidad, $idresponsable, $idproceso)
{
    $sql = "UPDATE public.entregable
	SET fecha_estimada_inicio=$fecha_estimada_inicio, fecha_estimada_fin=$fecha_estimada_fin, peso_total=$peso_total,
    nombre='$nombre', descripcion='$descripcion', perioricidad='$perioricidad', responsable_fk=$idresponsable,
    objetivo_fk=$idobjetivo, tipo_etapa='$tipo_etapa', proceso_fk = $idproceso
	WHERE identregable=$id_entregable;";
    return executeQueryInsert($con, $sql);
}

//update etapa proceso
function update_EtapaProcesoIndividual($con, $idproceso_entregable, $estado_etapa, $nombre_proceso, $descripcion_proceso, $peso_total_proceso)
{
    $sql = "UPDATE public.proceso_entregable
	SET nombre_proceso='$nombre_proceso', descripcion='$descripcion_proceso', peso_total_proceso=$peso_total_proceso, estado_etapa='$estado_etapa'
	WHERE idproceso_entregable=$idproceso_entregable;";
    return executeQueryInsert($con, $sql);
}

//insertar etapas proceso con agrupacion
function add_EtapaProceso($con, $procesos, $nombre_agrupacion, $descripcion_agrupacion)
{
    $sqlsecuencia = "SELECT nextval('proceso2') as idproceso";
    $result = $con->query($sqlsecuencia);
    $id = $result->fetch(PDO::FETCH_ASSOC);

    $sql = "INSERT INTO public.procesos(
        idprocesos, nombre_procesos, descripcion_procesos)
        VALUES (" . $id['idproceso'] . ", '$nombre_agrupacion', '$descripcion_agrupacion');";

    if (executeQueryInsert($con, $sql) == 'ok') {
        $response = "";
        foreach ($procesos as $key => $value) {
            $sql = "INSERT INTO public.proceso_entregable(
                idproceso_entregable, nombre_proceso, descripcion, peso_total_proceso, estado_etapa, proceso_pertenece)
                VALUES (nextval('proceso_entregable_sec'), '" . $value['nombre_proceso'] . "', '" . $value['descripcion_proceso'] . "', " . $value['peso_total_proceso'] . ", '" . $value['estado_etapa'] . "', " . $id['idproceso'] . ");";
            $response = executeQueryInsert($con, $sql);
        }
        return $response;
    } else {
        return "Error al insertar proceso, comuniquese con el administrador";
    }
}

//update etapas proceso con agrupacion
function update_EtapaProcesoCompleto($con, $idagrupacion, $procesos, $nombre_agrupacion, $descripcion_agrupacion)
{
    $sql = "UPDATE public.procesos
	SET nombre_procesos='$nombre_agrupacion', descripcion_procesos='$descripcion_agrupacion'
	WHERE idprocesos=$idagrupacion;";

    if (executeQueryInsert($con, $sql) == 'ok') {
        $response = "";
        foreach ($procesos as $key => $value) {
            $sql = "INSERT INTO public.proceso_entregable(
                idproceso_entregable, nombre_proceso, descripcion, peso_total_proceso, estado_etapa, proceso_pertenece)
                VALUES (nextval('proceso_entregable_sec'), '" . $value['nombre_proceso'] . "', '" . $value['descripcion_proceso'] . "', " . $value['peso_total_proceso'] . ", '" . $value['estado_etapa'] . "', $idagrupacion);";
            $response = executeQueryInsert($con, $sql);
        }
        return $response;
    } else {
        return "Error al insertar proceso, comuniquese con el administrador";
    }
}

//insertar etapas proceso con agrupacion
function update_EtapaProceso($con, $idagrupacion, $nombre_agrupacion, $descripcion_agrupacion)
{
    $sql = "UPDATE public.procesos
	SET nombre_procesos='$nombre_agrupacion', descripcion_procesos='$descripcion_agrupacion'
	WHERE idprocesos=$idagrupacion;";

    return executeQueryInsert($con, $sql);
}

/* Eliminar entregable */

function delete_entregable($con, $identregable)
{
    $sql = "DELETE FROM public.entregable
	WHERE identregable = $identregable";
    return executeQueryInsert($con, $sql);

}

//insertar solicitud adicional
function add_Solicitud($con, $id_entregable, $id_responsable, $descripcion_solicitud, $fecha_recepcion, $prioridad, $duracion_dias, $usuario_genera_solicitud, $nombre_solicitud, $idproceso, $estado_solicitud)
{

    $sql = "INSERT INTO public.solicitudes(
        idsolicitud, entregable_fk, descripcion, fecha_recepcion, duracion_dias,
        responsable_fk, usuario_genera_solicitud, prioridad, nombre, proceso_fk, estado_solicitud)
        VALUES (nextval('solicitudes_sec'), $id_entregable, '$descripcion_solicitud', '$fecha_recepcion', $duracion_dias, $id_responsable, $usuario_genera_solicitud, '$prioridad', '$nombre_solicitud', $idproceso, '$estado_solicitud');";
    return executeQueryInsert($con, $sql);
}

//update solicitud adicional
function update_Solicitud($con, $idsolicitud, $id_entregable, $id_responsable, $descripcion_solicitud, $fecha_recepcion, $prioridad, $duracion_dias, $usuario_genera_solicitud, $nombre_solicitud, $idproceso, $estado_solicitud)
{
    $sql = "UPDATE public.solicitudes
	SET  entregable_fk=$id_entregable, descripcion='$descripcion_solicitud', fecha_recepcion='$fecha_recepcion', duracion_dias=$duracion_dias, responsable_fk=$id_responsable, usuario_genera_solicitud=$usuario_genera_solicitud, prioridad='$prioridad', nombre='$nombre_solicitud', proceso_fk=$idproceso, estado_solicitud='$estado_solicitud'
    WHERE idsolicitud=$idsolicitud;";
    return executeQueryInsert($con, $sql);
}

function delete_solicitud($con, $idsolicitud)
{
    $sql = "DELETE FROM public.solicitudes
	WHERE idsolicitud = $idsolicitud";
    return executeQueryInsert($con, $sql);
}

/* Insertar Reunión */
function add_Reunion($con, $titulo_reunion, $fecha_reunion, $hora_inicio, $hora_fin, $lugar_reunion, $descripcion_reunion, $idusuario_convoca, $observaciones)
{
    $sqlsecuencia = "SELECT nextval('reunion_sec') as idreunion";
    $result = $con->query($sqlsecuencia);
    $id = $result->fetch(PDO::FETCH_ASSOC);

    $sql = "INSERT INTO public.reunion(
        idreunion, titulo_reunion, fecha_reunion, hora_inicio, hora_fin, lugar_reunion, descripcion_reunion, usuario_convoca_fk, observaciones)
        VALUES (" . $id['idreunion'] . ", '$titulo_reunion', '$fecha_reunion', $hora_inicio, $hora_fin, '$lugar_reunion', '$descripcion_reunion', $idusuario_convoca, '$observaciones');";
    if (executeQueryInsert($con, $sql) == 'ok') {
        return $id['idreunion'];
    } else {
        return "Error al insertar, comuniquese con el administrador";
    }
}

/* update Reunión */
function update_Reunion($con, $idreunion, $titulo_reunion, $fecha_reunion, $hora_inicio, $hora_fin, $lugar_reunion, $descripcion_reunion, $idusuario_convoca, $observaciones)
{
    $sql = "UPDATE public.reunion
        SET titulo_reunion = '$titulo_reunion', fecha_reunion = '$fecha_reunion', hora_inicio = $hora_inicio, hora_fin = $hora_fin, lugar_reunion = '$lugar_reunion', descripcion_reunion = '$descripcion_reunion', usuario_convoca_fk = $idusuario_convoca, observaciones = '$observaciones'
        WHERE idreunion = $idreunion;";
    return executeQueryInsert($con, $sql);
}

/* Eliminar REUNION */

function delete_reunion($con, $idreunion)
{
    $sql = "DELETE FROM public.reunion
	WHERE idreunion = $idreunion";
    return executeQueryInsert($con, $sql);

}

/* Consultar Reunión */
function get_Reunion($con, $fecha_inicio, $fecha_fin, $hora_inicio, $hora_fin, $idusuario_convoca, $identregable)
{

    $where_post = "";

    if ($fecha_inicio != null) {
        $where_post .= " AND fecha_reunion >=  '$fecha_inicio'";
    }
    if ($fecha_fin != null) {
        $where_post .= " AND fecha_reunion <=  '$fecha_fin'";
    }
    if ($hora_inicio != null) {
        $where_post .= " AND hora_inicio >=  '$hora_inicio'";
    }
    if ($hora_fin != null) {
        $where_post .= " AND hora_fin <=  '$hora_fin'";
    }
    if ($idusuario_convoca != null) {
        $where_post .= " AND usuario_convoca_fk =  $idusuario_convoca";
    }
    if ($identregable != null) {
        $where_post .= " AND entregable_fk =  $identregable";
    }

/* consulta anidada como campo */
    $sql = "SELECT idreunion, titulo_reunion, fecha_reunion, hora_inicio, hora_fin, lugar_reunion, descripcion_reunion, usuario_convoca_fk as idusuario_convoca, observaciones
	FROM public.reunion
    LEFT JOIN detalle_reunion on idreunion = reunion_fk
    where 1 = 1
    $where_post";
    return executeQuery($con, $sql);
}

//insertar detalle de reunion
function add_DetalleReunion($con, $idreunion, $identregable, $asunto, $descripcion)
{
    $sqlsecuencia = "SELECT nextval('detalle_reunion_sec') as iddetalle_reunion";
    $result = $con->query($sqlsecuencia);
    $id = $result->fetch(PDO::FETCH_ASSOC);

    $sql = "INSERT INTO public.detalle_reunion(
            iddetalle_reunion, reunion_fk, entregable_fk, asunto, descripcion)
            VALUES (" . $id['iddetalle_reunion'] . ",$idreunion , $identregable, '$asunto', '$descripcion');";

    if (executeQueryInsert($con, $sql) == 'ok') {
        $sql = "SELECT iddetalle_reunion, reunion_fk, entregable_fk, asunto, descripcion
            FROM public.detalle_reunion
            WHERE iddetalle_reunion = " . $id['iddetalle_reunion'];

        $result = $con->query($sql);
        $detalleReunion = $result->fetch(PDO::FETCH_ASSOC);

        return $detalleReunion;
    } else {
        return "Error al insertar, comuniquese con el administrador";
    }
}

//insertar soporte entregable
function add_SoporteEntregable($con, $idetapa_proceso, $detalle_avance, $estado_etapa)
{
    $sql = "UPDATE public.proceso_entregable
	SET estado_etapa='$estado_etapa'
	WHERE idproceso_entregable=$idetapa_proceso;";

    if (executeQueryInsert($con, $sql) == 'ok') {

        $sqlsecuencia = "SELECT nextval('soporte_entregable_sec') as idanexo";
        $result = $con->query($sqlsecuencia);
        $id = $result->fetch(PDO::FETCH_ASSOC);

        $sql = "INSERT INTO public.soportes_etapas_proceso(
            idsoporte, detalle_soporte, fecha_recepcion, proceso_entregable_fk)
            VALUES (" . $id['idanexo'] . ", '$detalle_avance', current_date, $idetapa_proceso);";

        if (executeQueryInsert($con, $sql) == 'ok') {
            return $id['idanexo'];
        } else {
            return 'error';
        }
    }
}

//insertar soporte entregable
function update_SoporteEntregable($con, $idetapa_proceso, $iddetalle_avance, $detalle_avance, $estado_etapa)
{

    $sql = "UPDATE public.proceso_entregable
	SET estado_etapa='$estado_etapa'
	WHERE idproceso_entregable=$idetapa_proceso;";
    if (executeQueryInsert($con, $sql) == 'ok') {
        $sql = "UPDATE public.soportes_etapas_proceso
        SET detalle_soporte='$detalle_avance'
        WHERE idsoporte=$iddetalle_avance;";

        return executeQueryInsert($con, $sql);

    }
}

//insertar¿ asistentes reunion
function addAsistentesReunion($con, $asistentes)
{
    $agregado = 'ok';
    foreach ($asistentes as $key => $value) {
        if ($agregado == 'ok') {
            $sql = "INSERT INTO public.asistentes_reunion(
                idasistentes_reunion, reunion_fk, usuario_fk)
                VALUES (nextval('asistentes_reunion_sec'), " . $value['idreunion'] . ", " . $value['idusuario'] . ");";
            //var_dump($sql);
            $agregado = executeQueryInsert($con, $sql);
        } else {
            $agregado = 'error al insertar uno de los asistentes';
        }
    }
    return $agregado;
}
//delete asistentes reunion
function deleteAsistentesReunion($con, $idreunion)
{
    $sql = "DELETE FROM public.asistentes_reunion
	WHERE reunion_fk = " . $idreunion['idreunion'] . ";";

    return executeQueryInsert($con, $sql);
}
//GET asistentes reunion
function getAsistentesReunion($con, $idreunion)
{
    $sql = "SELECT idasistentes_reunion, reunion_fk, usuario_fk  FROM public.asistentes_reunion
            WHERE reunion_fk = $idreunion;";
    return executeQuery($con, $sql);
}

//insertar¿ anexos de sorpote de engtregable
function addAnexosEntregableSoporte($con, $fileId, $newName, $folderId, $idsoporte)
{
    $sql = "INSERT INTO public.anexos_etapas_proceso(
            idanexo_entregable, nombre_anexo, id_anexo, id_carpeta, soporte_entregable_fk)
            VALUES (nextval('anexos_entregables_sec'), '$newName', '$fileId', '$folderId', $idsoporte);";
    return executeQueryInsert($con, $sql);
}

//insertar detalle de reunion
function update_DetalleReunion($con, $iddetalle_reunion, $identregable, $idreunion, $asunto, $descripcion)
{
    $sql = "UPDATE public.detalle_reunion
            SET entregable_fk=$identregable, reunion_fk=$idreunion, asunto='$asunto', descripcion='$descripcion'
            WHERE iddetalle_reunion=$iddetalle_reunion;";
    return executeQueryInsert($con, $sql);
}

//insertar anexos de detalle de reunion
function addAnexosDetalleReunion($con, $fileId, $newName, $folderId, $iddetalle_reunion)
{
    $sql = "INSERT INTO public.anexos_detalle_reunion(
            idanexos_detalle_reunion, id_anexo, detalle_reunion_fk, id_carpeta, nombre_anexo)
            VALUES (nextval('anexos_reunion_sec'), '$fileId', $iddetalle_reunion, '$folderId','$newName');";
    return executeQueryInsert($con, $sql);
}

//get anexo etapa de proceso por id
function getAnexoEtapaProceso($con, $idanexo)
{
    $sql = "SELECT idanexo_entregable, nombre_anexo, id_anexo, id_carpeta, soporte_entregable_fk
	FROM public.anexos_etapas_proceso
    WHERE idanexo_entregable = $idanexo;";
    return executeQuery($con, $sql);
}

//delete anexos etapa proceso
function deleteAnexoEtapaproceso($con, $idanexo)
{
    $sql = "DELETE FROM public.anexos_etapas_proceso
            WHERE idanexo_entregable = $idanexo;";
    return executeQueryInsert($con, $sql);
}

//get anexo detalle reunion por id
function getAnexo($con, $idanexo)
{
    $sql = "SELECT idanexos_detalle_reunion, id_anexo, detalle_reunion_fk, id_carpeta, nombre_anexo
	FROM public.anexos_detalle_reunion
    WHERE idanexos_detalle_reunion = $idanexo;";
    return executeQuery($con, $sql);
}

//get anexos etalle reunion por id detalle
function getAnexoDetalle($con, $iddetalle)
{
    $sql = "SELECT idanexos_detalle_reunion, id_anexo, detalle_reunion_fk, id_carpeta, nombre_anexo
	FROM public.anexos_detalle_reunion
    WHERE detalle_reunion_fk = $iddetalle;";
    return executeQuery($con, $sql);
}

//get anexos etalle reunion por id detalle
function getAnexoDetalleEtapaProceso($con, $iddetalleEtapa)
{
    $sql = "SELECT idanexo_entregable, nombre_anexo, id_anexo, id_carpeta, soporte_entregable_fk
	FROM public.anexos_etapas_proceso
    WHERE soporte_entregable_fk = $iddetalleEtapa;";
    return executeQuery($con, $sql);
}

//delete de detalle de etapa proceso
function deleteDetalleEtapaProceso($con, $iddetalle_etapa_proceso)
{
    $sql = "DELETE FROM public.soportes_etapas_proceso
	        WHERE idsoporte = $iddetalle_etapa_proceso;";
    return executeQueryInsert($con, $sql);
}

//delete anexos de detalle de reunion
function delete_Anexo_detalle_reunion($con, $idanexo)
{
    $sql = "DELETE FROM public.anexos_detalle_reunion
            WHERE idanexos_detalle_reunion = $idanexo;";
    return executeQueryInsert($con, $sql);
}

//delete de detalle de reunion
function delete_detalle_reunion($con, $iddetalle)
{
    $sql = "DELETE FROM public.detalle_reunion
	        WHERE iddetalle_reunion = $iddetalle;";
    return executeQueryInsert($con, $sql);
}

//consulta etapas proceso
function getProcesoUsuario($con, $idusuario)
{
    $sql = "SELECT identregable, fecha_estimada_inicio, fecha_estimada_fin, peso_total, nombre,
    descripcion, perioricidad, objetivo_fk as idobjetivo, tipo_etapa, proceso_fk as idproceso
	FROM public.entregable
    WHERE responsable_fk = $idusuario;";
    return executeQuery($con, $sql);
}

//insertar clientes
function add_Clientes($con, $nombre, $anio, $descripcion_cliente)
{
    $sql = "INSERT INTO public.cliente(
        idcliente, nombre_cliente, descripcion_cliente, anio)
        VALUES (nextval('cliente_sec'), '$nombre', '$descripcion_cliente', '$anio');";
    return executeQueryInsert($con, $sql);
}

//insertar clientes
function update_Clientes($con, $idcliente, $nombre, $anio, $descripcion_cliente)
{
    $sql = "UPDATE public.cliente
	SET nombre_cliente='$nombre', descripcion_cliente='$descripcion_cliente', anio='$anio'
	WHERE idcliente=$idcliente;";
    return executeQueryInsert($con, $sql);
}

//delete clientes
function delete_clientes($con, $idcliente)
{
    $sql = "DELETE FROM public.cliente
	WHERE idcliente=$idcliente;";
    return executeQueryInsert($con, $sql);
}

//insertar proyectos
function add_Proyecto($con, $nombre_proyecto, $idcliente, $anio, $descripcion_proyecto, $objetivo_general)
{
    $sql = "INSERT INTO public.proyecto(
        idproyecto, nombre_proyecto, descripcion_proyecto, cliente_fk, anio, objetivo_general)
        VALUES (nextval('proyecto_sec'), '$nombre_proyecto', '$descripcion_proyecto', $idcliente, '$anio', '$objetivo_general');";
    return executeQueryInsert($con, $sql);
}

//update proyectos
function update_Proyecto($con, $idproyecto, $nombre_proyecto, $idcliente, $anio, $descripcion_proyecto, $objetivo_general)
{
    $sql = "UPDATE public.proyecto
	SET nombre_proyecto='$nombre_proyecto', descripcion_proyecto='$descripcion_proyecto', cliente_fk=$idcliente, anio='$anio',objetivo_general='$objetivo_general'
	WHERE idproyecto=$idproyecto";
    return executeQueryInsert($con, $sql);
}

//delete clientes
function delete_Proyecto($con, $idproyecto)
{
    $sql = "DELETE FROM public.proyecto
	WHERE idproyecto=$idproyecto;";
    return executeQueryInsert($con, $sql);
}

//insertar objetivo
function add_Objetivo($con, $nombre_objetivo, $idproyecto, $descripcion_objetivo)
{
    /*  $sqlsecuencia = "SELECT nextval('objetivo_sec') as idobjetivo";
    $result = $con->query($sqlsecuencia);
    $id = $result->fetch(PDO::FETCH_ASSOC); */

    $sql = "INSERT INTO public.objetivo(
        idobjetivo, nombre_objetivo, descripcion_objetivo, proyecto_fk)
        VALUES (nextval('objetivo_sec'), '$nombre_objetivo', '$descripcion_objetivo', $idproyecto);";

    return executeQueryInsert($con, $sql);
    /* if (executeQueryInsert($con, $sql) == 'ok') {
return $id['idobjetivo'];
} else {
return 'error';
} */
}

//update objetivo
function update_Objetivo($con, $idobjetivo, $nombre_objetivo, $descripcion_objetivo)
{
    $sql = "UPDATE public.objetivo
	SET nombre_objetivo='$nombre_objetivo', descripcion_objetivo='$descripcion_objetivo'
	WHERE idobjetivo=$idobjetivo;";
    return executeQueryInsert($con, $sql);
}

//delete objetivo
function delete_Objetivo($con, $idobjetivo)
{
    $sql = "DELETE FROM public.objetivo
	WHERE idobjetivo=$idobjetivo;";
    return executeQueryInsert($con, $sql);
}
