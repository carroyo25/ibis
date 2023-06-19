<?php
    class PedidoSegModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarPedidosUsuario(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                    ibis.tb_pedidocab.idreg,
                                                    ibis.tb_pedidocab.idcostos,
                                                    ibis.tb_pedidocab.idarea,
                                                    ibis.tb_pedidocab.emision,
                                                    ibis.tb_pedidocab.vence,
                                                    ibis.tb_pedidocab.estadodoc,
                                                    ibis.tb_pedidocab.nrodoc,
                                                    ibis.tb_pedidocab.idtipomov,
                                                    UPPER(ibis.tb_pedidocab.concepto) AS concepto,
                                                    CONCAT(rrhh.tabla_aquarius.nombres,' ',rrhh.tabla_aquarius.apellidos) AS nombres,
                                                    UPPER(CONCAT(ibis.tb_proyectos.ccodproy,' ',ibis.tb_proyectos.cdesproy)) AS costos,
                                                    ibis.tb_pedidocab.nivelAten,
                                                    atenciones.cdescripcion AS atencion,
                                                    estados.cdescripcion AS estado,
                                                    estados.cabrevia 
                                                FROM
                                                    ibis.tb_pedidocab
                                                    INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                    INNER JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                    INNER JOIN ibis.tb_parametros AS atenciones ON ibis.tb_pedidocab.nivelAten = atenciones.nidreg
                                                    INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg 
                                                WHERE
                                                    ibis.tb_pedidocab.estadodoc BETWEEN 49 AND 89
                                                    AND tb_pedidocab.usuario = :user");
                $sql->execute(["user"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $tipo = $rs['idtipomov'] == 37 ? "B":"S";
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="textoCentro">'.$tipo.'</td>
                                        <td class="pl20px">'.utf8_decode($rs['concepto']).'</td>
                                        <td class="pl20px">'.$rs['costos'].'</td>
                                        <td class="pl20px">'.$rs['nombres'].'</td>
                                        <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['estado'].'</td>
                                        <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function consultarInfo($id){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                    tb_pedidocab.idcostos,
                                    DATE_FORMAT( tb_pedidocab.emision, '%d/%m/%Y' ) AS emision,
                                    DATE_FORMAT( tb_pedidocab.faprueba, '%d/%m/%Y' ) AS aprobacion,
                                    UPPER(
                                    CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS proyecto,
                                    elbora.cnombres AS elaborado,
                                    LPAD( tb_pedidocab.nrodoc, 6, 0 ) AS pedido,
                                    aprueba.cnombres AS aprobador,
                                    tb_parametros.cdescripcion,
                                    FORMAT( tb_parametros.cobservacion, 0 ) AS avance,
                                    tb_pedidocab.idreg 
                                FROM
                                    tb_pedidocab
                                    INNER JOIN tb_proyectos ON tb_pedidocab.idcostos = tb_proyectos.nidreg
                                    INNER JOIN tb_user AS elbora ON tb_pedidocab.usuario = elbora.iduser
                                    LEFT JOIN tb_user AS aprueba ON tb_pedidocab.aprueba = aprueba.iduser
                                    INNER JOIN tb_parametros ON tb_pedidocab.estadodoc = tb_parametros.nidreg 
                                WHERE
                                    tb_pedidocab.idreg =:id");
                $sql->execute(["id"=>$id]);
                $result = $sql->fetchAll();

                $json_result = array("pedido"   =>$result[0]['pedido'],
                                    "emision"   =>$result[0]['emision'],
                                    "costos"    =>$result[0]['proyecto'],
                                    "elaborado" =>$result[0]['elaborado'],
                                    "aprobador" =>$result[0]['aprobador'],
                                    "aprobacion"=>$result[0]['aprobacion'],
                                    "avance"    =>$result[0]['avance'],
                                    "ordenes"   =>$this->ordenesPedido($id),
                                    "ingresos"  =>$this->ingresosPedido($id),
                                    "despachos" =>$this->salidasPedido($id),
                                    "registros" =>$this->registrosPedido($id),
                                    "idpedido"  =>$result[0]['idreg']
                                    );

                return $json_result;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function ordenesPedido($pedido) {
            try {
                $salida =  '<tr><td colspan="3" class="textoCentro">No hay registro</td></tr>';
                $sql = $this->db->connect()->prepare("SELECT
                                LPAD(lg_ordencab.id_regmov,6,0) AS nroorden,
                                DATE_FORMAT(lg_ordencab.ffechadoc,'%d/%m/%Y') AS fechaOrden,
                                lg_ordencab.id_regmov
                        FROM
                            lg_ordencab
                        WHERE
                            lg_ordencab.id_refpedi =:pedido");
                $sql->execute(["pedido"=>$pedido]);
                $rowCount = $sql->rowcount();

                if ($rowCount > 0) {
                    $salida = "";
                    while ($rs = $sql->fetch()) {
                        $salida .= '<tr>
                                        <td class="textoCentro">'.$rs['nroorden'].'</td>
                                        <td class="textoCentro">'.$rs['fechaOrden'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['id_regmov'].'"><i class="fas fa-file-pdf"></i></a></td>
                                    </tr>';
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function ingresosPedido($pedido) {
            $salida =  '<tr><td colspan="3" class="textoCentro">No hay registro</td></tr>';
                $sql = $this->db->connect()->prepare("SELECT
                                                            alm_despachocab.id_regalm,
                                                            alm_despachocab.nnronota,
                                                            alm_despachocab.ffecdoc,
                                                            alm_despachocab.id_regalm
                                                        FROM
                                                            alm_despachocab
                                                        WHERE
                                                            alm_despachocab.idref_pedi = :pedido");
                $sql->execute(["pedido"=>$pedido]);
                $rowCount = $sql->rowcount();

                if ($rowCount > 0) {
                    $salida = "";
                    while ($rs = $sql->fetch()) {
                        $salida .= '<tr>
                                        <td class="textoCentro">'.$rs['id_regalm'].'</td>
                                        <td class="textoCentro">'.$rs['fechaDespacho'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['id_regalm'].'"><i class="fas fa-file-pdf"></i></a></td>
                                    </tr>';
                    }
                }

            return $salida;
        }

        private function salidasPedido($pedido) {
            $salida =  '<tr><td colspan="3" class="textoCentro">No hay registro</td></tr>';
                $sql = $this->db->connect()->prepare("SELECT
                                                        LPAD(alm_despachocab.id_regalm,0,6) AS depacho,
                                                        alm_despachocab.nnronota,
                                                        DATE_FORMAT(alm_despachocab.ffecdoc,'%d/%m/%Y') AS fechaDespacho,
                                                        alm_despachocab.id_regalm
                                                    FROM
                                                        alm_despachocab
                                                    WHERE
                                                        alm_despachocab.idref_pedi = :pedido");
                $sql->execute(["pedido"=>$pedido]);
                $rowCount = $sql->rowcount();

                if ($rowCount > 0) {
                    $salida = "";
                    while ($rs = $sql->fetch()) {
                        $salida .= '<tr>
                                        <td class="textoCentro">'.$rs['nroorden'].'</td>
                                        <td class="textoCentro">'.$rs['fechaOrden'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['nroorden'].'"><i class="fas fa-file-pdf"></i></a></td>
                                    </tr>';
                    }
                }

                return $salida;
        }

        private function registrosPedido($pedido) {
            $salida =  '<tr><td colspan="3" class="textoCentro">No hay registro</td></tr>';
                $sql = $this->db->connect()->prepare("SELECT
                                                        LPAD(alm_cabexist.idreg, 6, 0) AS registro,
                                                        DATE_FORMAT(
                                                            alm_cabexist.ffechadoc,
                                                            '%d/%m/&Y)'
                                                        ) AS fechaRegistro,
                                                        alm_cabexist.idreg
                                                    FROM
                                                        alm_cabexist
                                                    WHERE
                                                        alm_cabexist.idped = :pedido");
                $sql->execute(["pedido"=>$pedido]);
                $rowCount = $sql->rowcount();

                if ($rowCount > 0) {
                    $salida = "";
                    while ($rs = $sql->fetch()) {
                        $salida .= '<tr>
                                        <td class="textoCentro">'.$rs['idreg'].'</td>
                                        <td class="textoCentro">'.$rs['fechaRegistro'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['idreg'].'"><i class="fas fa-file-pdf"></i></a></td>
                                    </tr>';
                    }
                }

                return $salida;
        }

        public function listarPedidosUsuarioFiltrados($parametros){
            try {
                $salida = "";
                $mes  = date("m");

                $tipo   = $parametros['tipoSearch'] == -1 ? "%" : "%".$parametros['tipoSearch']."%";
                $costos = $parametros['costosSearch'] == -1 ? "%" : "%".$parametros['costosSearch']."%";
                $mes    = $parametros['mesSearch'] == -1 ? $mes :  $parametros['mesSearch'];
                $anio   = $parametros['anioSearch'];

                echo $tipo;
                echo $mes;
                echo $costos;
                echo $anio;
                echo $_SESSION['iduser'];
                
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.tb_pedidocab.idreg,
                                                        ibis.tb_pedidocab.idcostos,
                                                        ibis.tb_pedidocab.idarea,
                                                        ibis.tb_pedidocab.emision,
                                                        ibis.tb_pedidocab.vence,
                                                        ibis.tb_pedidocab.estadodoc,
                                                        ibis.tb_pedidocab.nrodoc,
                                                        ibis.tb_pedidocab.idtipomov,
                                                        UPPER(ibis.tb_pedidocab.concepto) AS concepto,
                                                        CONCAT(
                                                            rrhh.tabla_aquarius.nombres,
                                                            ' ',
                                                            rrhh.tabla_aquarius.apellidos
                                                        ) AS nombres,
                                                        UPPER(
                                                            CONCAT(
                                                                ibis.tb_proyectos.ccodproy,
                                                                ' ',
                                                                ibis.tb_proyectos.cdesproy
                                                            )
                                                        ) AS costos,
                                                        ibis.tb_pedidocab.nivelAten,
                                                        atenciones.cdescripcion AS atencion,
                                                        estados.cdescripcion AS estado,
                                                        estados.cabrevia
                                                    FROM
                                                        ibis.tb_pedidocab
                                                    INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                    INNER JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                    INNER JOIN ibis.tb_parametros AS atenciones ON ibis.tb_pedidocab.nivelAten = atenciones.nidreg
                                                    INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                    WHERE
                                                        ibis.tb_pedidocab.usuario = :user 
                                                    AND ibis.tb_pedidocab.idtipomov LIKE :tipomov
                                                    AND ibis.tb_pedidocab.idcostos LIKE :costos
                                                    AND MONTH (ibis.tb_pedidocab.emision) = :mes
                                                    AND YEAR (ibis.tb_pedidocab.emision) = :anio
                                                    AND ibis.tb_pedidocab.estadodoc");
                $sql->execute(["user"=>$_SESSION['iduser'],
                                "tipomov"=>$tipo,
                                "costos"=>$costos,
                                "mes"=>$mes,
                                "anio"=>$anio]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $tipo = $rs['idtipomov'] == 37 ? "B":"S";
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="textoCentro">'.$tipo.'</td>
                                        <td class="pl20px">'.utf8_decode($rs['concepto']).'</td>
                                        <td class="pl20px">'.utf8_decode($rs['costos']).'</td>
                                        <td class="pl20px">'.$rs['nombres'].'</td>
                                        <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['estado'].'</td>
                                        <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                    </tr>';
                    }
                }else {
                    $salida = '<tr class="pointer"><td colspan="8" class="textoCentro">No se encontraron registros en la consulta</td></tr>';
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>