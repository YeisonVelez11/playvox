<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require 'consultas.php';
include 'upload_GoogleDrive.php';

class InfoApi
{
    private $con;

    public function __construct()
    {
        $this->connectDB();
    }

    public function connectDB()
    {
        //DB DATA PRODUCCIÓN
        $database = "d7jmsqb0pb9n11";
        $uid = "ymuglgckigeyxm";
        $pwd = "8a86f637e663ed9f778e1ec74e3da85d6f6aec7ce57dbbd2cf3c5c82afa3380a";
        $host = "ec2-184-73-201-79.compute-1.amazonaws.com"; 

        //DB DATA DEVEPLOPER
        /*$database = "d5sdlsrh69tg7v";
        $uid = "yockkrzyjzfmtt";
        $pwd = "e0c3afe7326d5f8f377730b3e166e2456363333d972a44a2b7ad424c5be019ac";
        $host = "ec2-54-235-119-0.compute-1.amazonaws.com";
*/
        //establecer la conexión
        $this->con = new PDO("pgsql:host=$host;port=5432;dbname=$database;user=$uid;password=$pwd");
        if (!$this->con) {
            die('error de conexión');
        }
    }

    //Obtener el cuerpo de la peticion POST del chatbot
    public function detectRequestBody()
    {
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, true);
        return $input;
    }

    public function login($usr, $pwd)
    {
        return logQuery($this->con, $usr, $pwd);
    }

    /* Formulario Añadir Reunión */

    public function listaUsuarios()
    {
        return listarUsuario($this->con);
    }
    /* Formulario Añadir Reunión */

    public function listaUsuariosId($idusuario)
    {
        return listarUsuarioId($this->con, $idusuario);
    }

    /* Insertar Usuarios */
    public function agregarUsuario($nombre, $apellido, $email, $contrasena, $idtipo_usuario, $empresa, $cargo, $acceso_plataforma, $imagen)
    {
        return addUsuario($this->con, $nombre, $apellido, $email, $contrasena, $idtipo_usuario, $empresa, $cargo, $acceso_plataforma, $imagen);
    }

    //CONSULTAR TIPO USUARIO
    public function getTipo_Usuario()
    {
        return getTipoUsuario($this->con);
    }

    /* Insertar Usuarios */
    public function updateUsuario($idusuario, $nombre, $apellido, $email, $contrasena, $idtipo_usuario, $empresa, $cargo, $acceso_plataforma, $imagen)
    {
        return updateUsuario($this->con, $idusuario, $nombre, $apellido, $email, $contrasena, $idtipo_usuario, $empresa, $cargo, $acceso_plataforma, $imagen);
    }

    /* Eliminar tema tratado */

    public function deleteUsuario($idusuario)
    {
        return delete_usuario($this->con, $idusuario);
    }
    //insert clientes
    public function addCliente($nombre, $anio, $descripcion_cliente)
    {
        return add_Clientes($this->con, $nombre, $anio, $descripcion_cliente);
    }

    //update clientes
    public function updateCliente($idcliente, $nombre, $anio, $descripcion_cliente)
    {
        return update_Clientes($this->con, $idcliente, $nombre, $anio, $descripcion_cliente);
    }

    /* Eliminar clientes */

    public function deleteClientes($idcliente)
    {
        return delete_clientes($this->con, $idcliente);
    }

    /* consultar clientes */

    public function get_cientes($anio)
    {
        return getClientes($this->con, $anio);
    }
    /* consultar proyecto */

    public function get_proyecto($anio, $idcliente)
    {
        return getProyecto($this->con, $anio, $idcliente);
    }

    /* consultar objetivo */

    public function get_objetivo($idproyecto)
    {
        return getObjetivo($this->con, $idproyecto);
    }

    /* consultar entregable */

    public function get_entregable($idobjetivo)
    {
        return getEntregable($this->con, $idobjetivo);
    }

    /* consultar entregable */

    public function getCruceEntregable($fecha_inicio, $idusuario)
    {
        $result = getCruceEntregable($this->con, $fecha_inicio, $idusuario);
        if (count($result) == 0) {
            return 'ok';
        } else {
            return 'cruce';
        }
    }
    /* consultar procesos de suuario */

    public function getProcesosUsuario($idusuario, $identregable)
    {
        return get_ProcesosUsuario($this->con, $idusuario, $identregable);
    }

    /* consultar entregable */

    public function get_entregableCompleto($identregable)
    {
        return getEntregableCompleto($this->con, $identregable);
    }

    /* consultar entregable */

    public function get_solicitudCompleto($idsolicitud, $idusuario, $identregable)
    {
        return getsolicitudCompleto($this->con, $idsolicitud, $idusuario, $identregable);
    }

    /* consultar soporte */

    public function get_soporteEntregable($idproceso)
    {
        return getDetalleEtapasProcesoId($this->con, $idproceso);
    }

    /* consultar soporte */

    public function get_AnexosSoporteEntregable($idsoporte)
    {
        return getAnexosSoporteEntregable($this->con, $idsoporte);
    }

    /* delete detalle de etapa de proceso */
    public function delete_detalle_etapa_proceso($iddetalleEtapaProceso)
    {
        $response = getAnexoDetalleEtapaProceso($this->con, $iddetalleEtapaProceso);
        $flag = false;
        if (count($response) > 0) {
            foreach ($response as $res) {
                $delete = $this->deleteAnexoDrive($res['id_anexo']);
                if ($delete == 'ok') {
                    $deleteAnexobd = deleteAnexoEtapaproceso($this->con, $res['idanexo_entregable']);
                    if ($deleteAnexobd == 'ok') {
                        $flag = true;
                    } else {
                        return 'error al eliminar archivo de la base de datos';
                    }
                } else {
                    return 'error al eliminar archivo del drive';
                }
            }
            if ($flag) {
                return deleteDetalleEtapaProceso($this->con, $iddetalleEtapaProceso);
            } else {
                return 'error al eliminar anexo';
            }
        } else {
            return deleteDetalleEtapaProceso($this->con, $iddetalleEtapaProceso);
        }
    }

    /* eliminar anexos de etapa proceso */

    public function delete_Anexo_etapa_proceso($idanexo)
    {
        $response = getAnexoEtapaProceso($this->con, $idanexo);
        $delete = $this->deleteAnexoDrive($response[0]['id_anexo']);

        if ($delete == 'ok') {
            return deleteAnexoEtapaproceso($this->con, $idanexo);
        } else {
            return 'error al eliminar archivo del drive';
        }
    }

    /* consultar etapas proceso */

    public function get_etapas_proceso($identregable)
    {
        $etapas = getEtapasProceso($this->con, $identregable);
        $response = array();
        if (is_array($etapas)) {
            foreach ($etapas as $key => $value) {
                $detalle = getDetalleEtapasProceso($this->con, $value['idproceso_entregable']);
                if (is_array($detalle)) {
                    $resp = array(
                        'idproceso_entregable' => $value['idproceso_entregable'],
                        'nombre_proceso' => $value['nombre_proceso'],
                        'descripcion' => $value['descripcion'],
                        'peso_total_proceso' => $value['peso_total_proceso'],
                        'estado_etapa' => $value['estado_etapa'],
                        'proceso_pertenece' => $value['proceso_pertenece'],
                        'detalles' => $detalle,
                    );
                    array_push($response, $resp);
                } else {
                    $resp = array(
                        'idproceso_entregable' => $value['idproceso_entregable'],
                        'nombre_proceso' => $value['nombre_proceso'],
                        'descripcion' => $value['descripcion'],
                        'peso_total_proceso' => $value['peso_total_proceso'],
                        'estado_etapa' => $value['estado_etapa'],
                        'proceso_pertenece' => $value['proceso_pertenece'],
                        'detalles' => array(),
                    );
                    array_push($response, $resp);
                }
            }
        }

        return $response;
    }

    /* consultar detalle etapas proceso */

    public function get_detalle_etapas_proceso($idproceso)
    {
        $detalles = getDetalleEtapasProceso($this->con, $idproceso);
        $response = array();
        //var_dump($etapas);
        if (count($detalles) > 0) {
            foreach ($detalles as $key => $value) {
                $anexo = getAnexosSoporteEntregable($this->con, $value['iddetalle_soporte']);
                if (count($anexo) > 0) {
                    $resp = array(
                        'iddetalle_soporte' => $value['iddetalle_soporte'],
                        'detalle_soporte' => $value['detalle_soporte'],
                        'fecha_recepcion' => $value['fecha_recepcion'],
                        'anexos' => $anexo,
                    );
                    array_push($response, $resp);
                } else {
                    $resp = array(
                        'iddetalle_soporte' => $value['iddetalle_soporte'],
                        'detalle_soporte' => $value['detalle_soporte'],
                        'fecha_recepcion' => $value['fecha_recepcion'],
                        'anexos' => array(),
                    );
                    array_push($response, $resp);
                }
            }
        }

        return $response;
    }

    /* consultar agrupcacion sencilla */

    public function get_agrupacion_sencilla($idagrupacion)
    {
        return getAgrupacionSencilla($this->con, $idagrupacion);
    }

    /* consultar etapas proceso inidivudla */

    public function get_etapas_proceso_individual($idetapaproceso)
    {
        return getEtapasProcesoindividual($this->con, $idetapaproceso);
    }

    /* detele etapas proceso */

    public function delete_etapa_proceso($idetapaproceso)
    {
        return deleteEtapaProceso($this->con, $idetapaproceso);
    }

    /* detele etapas proceso agrupacion comleta */

    public function delete_etapa_proceso_agrupacion($idagrupacion)
    {
        return deleteEtapaProcesoAgrupacion($this->con, $idagrupacion);
    }

    /* consultar etapas proceso agrupacion */

    public function get_etapas_proceso_agrupacion($idagrupacion)
    {
        $response['agrupacion'] = getEtapasProcesoAgrupacion($this->con, $idagrupacion);
        $response['procesos'] = getEtapasProcesoAgrupacionDetalles($this->con, $idagrupacion);
        return $response;
    }

    /* consultar etapas proceso completo */

    public function get_etapas_procesoCompleto()
    {
        return getEtapasProcesoCompleto($this->con);
    }

    /* Insertar entregable */
    public function addEntregable($peso_total, $tipo_etapa, $fecha_estimada_inicio, $fecha_estimada_fin, $idobjetivo, $nombre, $descripcion, $perioricidad, $idresponsable, $id_proceso)
    {
        return add_Entregable($this->con, $peso_total, $tipo_etapa, $fecha_estimada_inicio, $fecha_estimada_fin, $idobjetivo, $nombre, $descripcion, $perioricidad, $idresponsable, $id_proceso);
    }

    /* update entregable */
    public function updateEntregable($id_entregable, $peso_total, $tipo_etapa, $fecha_estimada_inicio, $fecha_estimada_fin, $idobjetivo, $nombre, $descripcion, $perioricidad, $idresponsable, $idproceso)
    {
        return update_Entregable($this->con, $id_entregable, $peso_total, $tipo_etapa, $fecha_estimada_inicio, $fecha_estimada_fin, $idobjetivo, $nombre, $descripcion, $perioricidad, $idresponsable, $idproceso);
    }

    /* Insertar etapas proceso */
    public function addEtapaProceso($procesos, $nombre_agrupacion, $descripcion_agrupacion)
    {
        return add_EtapaProceso($this->con, $procesos, $nombre_agrupacion, $descripcion_agrupacion);
    }

    /* update agrupacion etapas proceso */
    public function updateEtapaProceso($idagrupacion, $procesos, $nombre_agrupacion, $descripcion_agrupacion)
    {
        if ($procesos == null) {
            return update_EtapaProceso($this->con, $idagrupacion, $nombre_agrupacion, $descripcion_agrupacion);
        } else {
            return update_EtapaProcesoCompleto($this->con, $idagrupacion, $procesos, $nombre_agrupacion, $descripcion_agrupacion);
        }
    }

    /* UPDATE etapas proceso */
    public function updateEtapaProcesoIndividual($idproceso_entregable, $estado_etapa, $nombre_proceso, $descripcion_proceso, $peso_total_proceso)
    {
        return update_EtapaProcesoIndividual($this->con, $idproceso_entregable, $estado_etapa, $nombre_proceso, $descripcion_proceso, $peso_total_proceso);
    }

    /* eliminar entregable */
    public function deleteEntregable($id_entregable)
    {
        return delete_entregable($this->con, $id_entregable);
    }

    /* insertar solicitud adicional */
    public function addSolicitud($id_entregable, $id_responsable, $descripcion_solicitud, $fecha_recepcion, $prioridad, $duracion_dias, $usuario_genera_solicitud, $nombre_solicitud, $idproceso, $estado_solicitud)
    {
        return add_Solicitud($this->con, $id_entregable, $id_responsable, $descripcion_solicitud, $fecha_recepcion, $prioridad, $duracion_dias, $usuario_genera_solicitud, $nombre_solicitud, $idproceso, $estado_solicitud);
    }

    /* update solicitud adicional */
    public function updateSolicitud($idsolicitud, $id_entregable, $id_responsable, $descripcion_solicitud, $fecha_recepcion, $prioridad, $duracion_dias, $usuario_genera_solicitud, $nombre_solicitud, $idproceso, $estado_solicitud)
    {
        return update_Solicitud($this->con, $idsolicitud, $id_entregable, $id_responsable, $descripcion_solicitud, $fecha_recepcion, $prioridad, $duracion_dias, $usuario_genera_solicitud, $nombre_solicitud, $idproceso, $estado_solicitud);
    }

    /* eliminar solicitud */
    public function deleteSolicitud($idsolicitud)
    {
        return delete_solicitud($this->con, $idsolicitud);
    }

    /* insertra asistentes reunion */
    public function addAsistentes_reunion($asistentes)
    {
        return addAsistentesReunion($this->con, $asistentes);
    }
    /* update asistentes reunion */
    public function updateAsistentes_reunion($asistentes)
    {
        $delete = deleteAsistentesReunion($this->con, $asistentes[0]);
        if ($delete == 'ok') {
            return addAsistentesReunion($this->con, $asistentes);
        } else {
            return 'error al modificar los asistentes de la base de datos';
        }
    }
    /* get asistentes reunion */
    public function getAsistentes_reunion($idreunion)
    {
        return getAsistentesReunion($this->con, $idreunion);
    }
    /* Insertar Reunión */
    public function addReunion($titulo_reunion, $fecha_reunion, $hora_inicio, $hora_fin, $lugar_reunion, $descripcion_reunion, $idusuario_convoca, $observaciones)
    {
        return add_Reunion($this->con, $titulo_reunion, $fecha_reunion, $hora_inicio, $hora_fin, $lugar_reunion, $descripcion_reunion, $idusuario_convoca, $observaciones);
    }

    /* update Reunión */
    public function updateReunion($idreunion, $titulo_reunion, $fecha_reunion, $hora_inicio, $hora_fin, $lugar_reunion, $descripcion_reunion, $idusuario_convoca, $observaciones)
    {
        return update_Reunion($this->con, $idreunion, $titulo_reunion, $fecha_reunion, $hora_inicio, $hora_fin, $lugar_reunion, $descripcion_reunion, $idusuario_convoca, $observaciones);
    }

    /* Consultar Reunion */
    public function getReunion($fecha_inicio, $fecha_fin, $hora_inicio, $hora_fin, $idusuario_convoca, $identregable)
    {
        return get_Reunion($this->con, $fecha_inicio, $fecha_fin, $hora_inicio, $hora_fin, $idusuario_convoca, $identregable);
    }

    /* Consultar detalle Reunion */
    public function get_detalle_reunion($idreunion)
    {
        return get_detalle_reunion($this->con, $idreunion);
    }

    /* Consultar detalle Reunion */
    public function get_anexos_detalle_reunion($iddetalle)
    {
        return getAnexos_detalle_reunion($this->con, $iddetalle);
    }

    /* DELETE REUNION */

    /* delete detalle Reunion */
    public function delete_reunion($idreunion)
    {
        $response = get_detalle_reunion($this->con, $idreunion);
        if (count($response) > 0) {
            foreach ($response as $res) {
                if ($this->delete_detalle_reunion($res['iddetalle_reunion']) != 'ok') {
                    return 'error al eliminar los detalles de la reunion';
                }
            }
            return delete_reunion($this->con, $idreunion);
        }
    }

    /* delete detalle Reunion */
    public function deleteAnexo_detalle_reunion($idanexo)
    {
        $response = getAnexo($this->con, $idanexo);
        $delete = $this->deleteAnexoDrive($response[0]['id_anexo']);

        if ($delete == 'ok') {
            return delete_Anexo_detalle_reunion($this->con, $idanexo);
        } else {
            return 'error al eliminar archivo del drive';
        }

    }

    /* delete detalle Reunion */
    public function delete_detalle_reunion($iddetallereunion)
    {
        $response = getAnexoDetalle($this->con, $iddetallereunion);
        $flag = false;
        if (count($response) > 0) {
            foreach ($response as $res) {
                $delete = $this->deleteAnexoDrive($res['id_anexo']);
                if ($delete == 'ok') {
                    $deleteAnexobd = delete_Anexo_detalle_reunion($this->con, $res['idanexos_detalle_reunion']);
                    if ($deleteAnexobd == 'ok') {
                        $flag = true;
                    } else {
                        return 'error al eliminar archivo de la base de datos';
                    }
                } else {
                    return 'error al eliminar archivo del drive';
                }
            }
            if ($flag) {
                return delete_detalle_reunion($this->con, $iddetallereunion);
            } else {
                return 'error al eliminar anexo';
            }
        } else {
            return delete_detalle_reunion($this->con, $iddetallereunion);
        }
    }

    //generar archivo en drive de anexo de reunion
    public function deleteAnexoDrive($file)
    {
        $drive = new GoogleDrive();
        $fileId = $drive->deleteFiles($file);

        return 'ok';
    }

    // UPDATE detalle reunion
    public function updateDetalleReunion($iddetalle_reunion, $idreunion, $identregable, $asunto, $descripcion, $anexos_detalle_reunion)
    {
        $responseDetalleReunion = update_DetalleReunion($this->con, $iddetalle_reunion, $identregable, $idreunion, $asunto, $descripcion);
        if ($responseDetalleReunion == 'ok') {

            if (is_null($anexos_detalle_reunion)) {
                return 'ok';
            } else {
                $filesUpdated = $this->generateFileNamereunion('', $anexos_detalle_reunion);
                $results = array();
                $tmp = 0;
                if (is_array($filesUpdated)) {
                    if (count($filesUpdated) > 0) {
                        //var_dump($filesUpdated);
                        foreach ($filesUpdated as $file) {
                            array_push($results, $this->addFileReunion($file, $iddetalle_reunion));
                        }
                        foreach ($results as $res) {
                            if ($res != 'ok') {
                                $tmp = 1;
                            }
                        }
                        if ($tmp == 0) {
                            return 'ok';
                        } else {
                            return 'error';
                        }
                    }
                } else {
                    return $filesUpdated;
                }
            }
        } else {
            return 'error al modificar los datos';
        }
    }

    // INSERTAR  detalle de reunion
    public function addDetalleReunion($idreunion, $identregable, $asunto, $descripcion, $anexos_detalle_reunion)
    {
        $pulldetallereunion = add_DetalleReunion($this->con, $idreunion, $identregable, $asunto, $descripcion);
        if (is_null($anexos_detalle_reunion)) {
            return 'ok';
        } else {
            $filesUpdated = $this->generateFileNamereunion($pulldetallereunion, $anexos_detalle_reunion);
            $results = array();
            $tmp = 0;
            if (is_array($filesUpdated)) {
                if (count($filesUpdated) > 0) {
                    //var_dump($filesUpdated);
                    foreach ($filesUpdated as $file) {
                        array_push($results, $this->addFileReunion($file, $pulldetallereunion['iddetalle_reunion']));
                    }
                    foreach ($results as $res) {
                        if ($res != 'ok') {
                            $tmp = 1;
                        }
                    }
                    if ($tmp == 0) {
                        return 'ok';
                    } else {
                        return 'error';
                    }
                }
            } else {
                return $filesUpdated;
            }
        }
    }
    public function generateFileNamereunion($pulldetallereunion, $anexos)
    {
        $filesAndNames = array();
        $contadorAnexos = 0;
        if (is_array($anexos['name'])) {
            for ($i = 0; $i < count($anexos['name']); $i++) {
                $tmp = explode('.', $anexos['name'][$i]);
                $extension = end($tmp);
                array_push($filesAndNames, array(
                    'file' => $anexos['tmp_name'][$i],
                    'newName' => "E.REUNION_" . $tmp[0] . "-" . $i . "." . $extension,
                ));
                $contadorAnexos++;
            }
            return $filesAndNames;
        } else {
            return "La estructura del envio de los archivos ha cambiado";
        }
    }
    //generar archivo en drive de anexo de reunion
    public function addFileReunion($file, $iddetalle_reunion)
    {
        $drive = new GoogleDrive();
        $folderId = '1hgDkscs-reU-wGcwebSGOv-46LAifqDN';
        $fileId = $drive->processFile($file['file'], $folderId, $file['newName']);

        return addAnexosDetalleReunion($this->con, $fileId, $file['newName'], $folderId, $iddetalle_reunion);
    }

    // insertar soporte entregable
    public function addSoporteEntregable($estado_etapa, $idetapa_proceso, $detalle_avance, $soportes)
    {
        if (is_null($soportes)) {
            $reponse = add_SoporteEntregable($this->con, $idetapa_proceso, $detalle_avance, $estado_etapa);
            if ($reponse != 'error') {
                return 'ok';
            }
        } else {
            $filesUpdated = $this->generateFileNameEntregable($soportes);
            $results = array();
            $tmp = 0;
            if (is_array($filesUpdated)) {
                if (count($filesUpdated) > 0) {
                    //var_dump($filesUpdated);
                    $idsoporteentregable = add_SoporteEntregable($this->con, $idetapa_proceso, $detalle_avance, $estado_etapa);
                    if ($idsoporteentregable != 'error') {
                        foreach ($filesUpdated as $file) {
                            array_push($results, $this->addFileEntregable($file, $idsoporteentregable));
                        }
                        foreach ($results as $res) {
                            if ($res != 'ok') {
                                $tmp = 1;
                            }
                        }
                        if ($tmp == 0) {
                            return 'ok';
                        } else {
                            return 'error';
                        }
                    } else {
                        return 'error al insertar el anexo del entregable';
                    }
                }
            } else {
                return $filesUpdated;
            }
        }
    }

    // update soporte entregable
    public function updateSoporteEntregable($estado_etapa, $iddetalle_avance, $idetapa_proceso, $detalle_avance, $soportes)
    {
        if (is_null($soportes)) {
            $reponse = update_SoporteEntregable($this->con, $idetapa_proceso, $iddetalle_avance, $detalle_avance, $estado_etapa);
            if ($reponse != 'error') {
                return 'ok';
            }
        } else {
            $filesUpdated = $this->generateFileNameEntregable($soportes);
            $results = array();
            $tmp = 0;
            if (is_array($filesUpdated)) {
                if (count($filesUpdated) > 0) {
                    //var_dump($filesUpdated);
                    $resultUpdate = update_SoporteEntregable($this->con, $idetapa_proceso, $iddetalle_avance, $detalle_avance, $estado_etapa);
                    if ($resultUpdate == 'ok') {
                        foreach ($filesUpdated as $file) {
                            array_push($results, $this->addFileEntregable($file, $iddetalle_avance));
                        }
                        foreach ($results as $res) {
                            if ($res != 'ok') {
                                $tmp = 1;
                            }
                        }
                        if ($tmp == 0) {
                            return 'ok';
                        } else {
                            return 'error';
                        }
                    } else {
                        return 'error al insertar el anexo del entregable';
                    }
                }
            } else {
                return $filesUpdated;
            }
        }
    }

    public function generateFileNameEntregable($anexos)
    {
        $filesAndNames = array();
        $contadorAnexos = 0;
        if (is_array($anexos['name'])) {
            for ($i = 0; $i < count($anexos['name']); $i++) {
                $tmp = explode('.', $anexos['name'][$i]);
                $extension = end($tmp);
                array_push($filesAndNames, array(
                    'file' => $anexos['tmp_name'][$i],
                    'newName' => "E.ENTREGABLE_" . $tmp[0] . "-" . $i . "." . $extension,
                ));
                $contadorAnexos++;
            }
            return $filesAndNames;
        } else {
            return "La estructura del envio de los archivos ha cambiado";
        }
    }

    //generar archivo en drive de anexo de reunion
    public function addFileEntregable($file, $idsoporte)
    {
        $drive = new GoogleDrive();
        $folderId = '1PQR8avBRJ7BogZVDIBwsLAxOgBu87url';
        $fileId = $drive->processFile($file['file'], $folderId, $file['newName']);

        return addAnexosEntregableSoporte($this->con, $fileId, $file['newName'], $folderId, $idsoporte);
    }

    /* get entregables por usuario */
    public function get_procesoUsuario($idusuario)
    {
        return getProcesoUsuario($this->con, $idusuario);
    }

    /* get objetivos especificos*/
    public function getObjetivoEspecifico($idobjetivo)
    {
        return get_ObjetivoEspecifico($this->con, $idobjetivo);
    }

    /* add proyectos*/
    public function addProyecto($nombre_proyecto, $idcliente, $anio, $descripcion_proyecto, $objetivo_general)
    {
        return add_Proyecto($this->con, $nombre_proyecto, $idcliente, $anio, $descripcion_proyecto, $objetivo_general);
    }

    /* update proyectos*/
    public function updateProyecto($idproyecto, $nombre_proyecto, $idcliente, $anio, $descripcion_proyecto, $objetivo_general)
    {
        return update_Proyecto($this->con, $idproyecto, $nombre_proyecto, $idcliente, $anio, $descripcion_proyecto, $objetivo_general);
    }

    /* gdelete proyectos */
    public function deteleProyecto($idproyecto)
    {
        return delete_Proyecto($this->con, $idproyecto);
    }

    /* add objetivo*/
    public function addObjetivo($nombre_objetivo, $idproyecto, $descripcion_objetivo)
    {
        return add_Objetivo($this->con, $nombre_objetivo, $idproyecto, $descripcion_objetivo);
    }

    /* add objetivo especifoco*/
    public function addObjetivoEspecifico($nombre_objetivo, $idobjetivo, $descripcion_objetivo)
    {
        return add_ObjetivoEspecifico($this->con, $nombre_objetivo, $idobjetivo, $descripcion_objetivo);
    }
    /* update objetivo*/
    public function updateObjetivo($idobjetivo, $nombre_objetivo, $descripcion_objetivo)
    {
        return update_Objetivo($this->con, $idobjetivo, $nombre_objetivo, $descripcion_objetivo);
    }
    /* update objetivo especifico*/
    public function updateObjetivoEspecifico($idobjetivo, $nombre_objetivo, $descripcion_objetivo)
    {

        return update_ObjetivoEspecifico($this->con, $idobjetivo, $nombre_objetivo, $descripcion_objetivo);
    }

    /* delete objetivo */
    public function deleteObjetivo($idobjetivo)
    {
        return delete_Objetivo($this->con, $idobjetivo);
    }
    /* delete objetivo especifico */
    public function deleteObjetivoEspecifico($idobjetivo)
    {
        return delete_ObjetivoEspecifico($this->con, $idobjetivo);
    }

    public function addActividadSemanal($fecha, $descripcion, $idusuario, $detalleactividad, $pendienteactividad, $idreunion, $flag, $evidencias)
    {
        if (is_null($evidencias)) {
            return actSemanalQuery($this->con, $fecha, $descripcion, $idusuario, $detalleactividad, $pendienteactividad, $idreunion);
        } else {
            $idActividadSemanal = getLastActividadSemanal($this->con);

            if ($idActividadSemanal[0]['detalle_actividad_fk'] != null) {
                $flag = false;
            } elseif ($idActividadSemanal[0]['pendientes_fk'] != null) {
                $flag = true;
            }
            $filesUpdated = $this->generateFileName($idActividadSemanal[0]['max'], $flag, $evidencias);

            $results = array();
            foreach ($filesUpdated as $file) {
                array_push($results, $this->addFileActividadSemanal($file, $idActividadSemanal[0]['max']));
            }
            return array();
        }
    }

    public function codeActivity($json)
    {
        $newName = 'B' . $json['numero_bloque'] . '-O' . $json['numero_objetivo'] . '-P' . $json['numero_producto'] . '-A' . $json['num'];

        return $newName;
    }

    //listar productos por bloque
    public function productos_bloque()
    {
        $result_query = productos_bloque($this->con);
        $numero_bloque = $result_query[0]['numero_bloque'];

        $bloques = array();
        $productos = array();

        for ($i = 0; $i < count($result_query); $i++) {
            if ($result_query[$i] != end($result_query)) {
                if ($numero_bloque == $result_query[$i + 1]['numero_bloque']) {
                    array_push($productos, array(['idproducto' => $result_query[$i]['idproducto']], ['nombre_producto' => $result_query[$i]['nombre_producto']]));
                } else {
                    array_push($productos, array(['idproducto' => $result_query[$i]['idproducto']], ['nombre_producto' => $result_query[$i]['nombre_producto']]));

                    array_push($bloques, array('nombre_bloque' => $result_query[$i]['nombre_bloque'], 'productos' => $productos));

                    $productos = array();
                    $numero_bloque = $result_query[$i + 1]['numero_bloque'];
                }
            } else {
                array_push($productos, array(['idproducto' => $result_query[$i]['idproducto']], ['nombre_producto' => $result_query[$i]['nombre_producto']]));
                array_push($bloques, array('nombre_bloque' => $result_query[$i]['nombre_bloque'], 'productos' => $productos));
            }
        }
        return $bloques;
    }
}
