<?php
    class AsignaModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarPedidosAprobados(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                    ibis.tb_pedidocab.nrodoc,
                                                    UPPER( ibis.tb_pedidocab.concepto ) AS concepto,
                                                    ibis.tb_pedidocab.idreg,
                                                    ibis.tb_pedidocab.estadodoc,
                                                    ibis.tb_pedidocab.emision,
                                                    ibis.tb_pedidocab.vence,
                                                    ibis.tb_pedidocab.idtipomov,
                                                    UPPER(
                                                    CONCAT_WS( ' ', ibis.tb_proyectos.ccodproy, ibis.tb_proyectos.cdesproy )) AS costos,
                                                    ibis.tb_pedidocab.nivelAten,
                                                    CONCAT_WS( ' ', rrhh.tabla_aquarius.apellidos, rrhh.tabla_aquarius.nombres ) AS nombres,
                                                    estados.cdescripcion AS estado,
                                                    atencion.cdescripcion AS atencion,
                                                    estados.cabrevia,
                                                    ibis.tb_pedidocab.idcostos,
                                                    ibis.tb_proyectos.ccodproy,
                                                    UPPER( ibis.tb_proyectos.cdesproy ) AS cdesproy,
                                                    UPPER(ibis.tb_user.cnameuser) as cnameuser,
                                                    ( SELECT COUNT( tb_pedidodet.estadoItem ) 
                                                        FROM tb_pedidodet 
                                                        WHERE 
                                                            tb_pedidodet.estadoItem = 54   
                                                            AND tb_pedidodet.idpedido = idreg  
                                                            AND tb_pedidodet.nflgActivo != 105) AS itemsFaltantes 
                                                FROM
                                                    ibis.tb_pedidocab
                                                    INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                    INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                    INNER JOIN ibis.tb_parametros AS atencion ON ibis.tb_pedidocab.nivelAten = atencion.nidreg
                                                    INNER JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                    LEFT JOIN ibis.tb_user ON ibis.tb_pedidocab.asigna = ibis.tb_user.iduser
                                                    LEFT JOIN ibis.tb_costusu ON ibis.tb_pedidocab.idcostos = ibis.tb_costusu.ncodproy 
                                                WHERE
                                                    ibis.tb_pedidocab.estadodoc = 54 
                                                    AND ibis.tb_pedidocab.nflgactivo = 1
                                                    AND tb_costusu.id_cuser = :user
                                                ORDER BY
                                                    ibis.tb_pedidocab.emision DESC");
                $sql->execute(['user' => $_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        if ($rs['itemsFaltantes'] > 0 ) {
                            $tipo = $rs['idtipomov'] == 37 ? "B":"S";
                            $asignado = $rs['cnameuser'] == NULL ? "--" : $rs['cnameuser'];
                            $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                            <td class="textoCentro">'.str_pad($rs['nrodoc'],4,0,STR_PAD_LEFT).'</td>
                                            <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                            <td class="textoCentro">'.$tipo.'</td>
                                            <td class="pl20px">'.$rs['concepto'].'</td>
                                            <td class="pl20px">'.utf8_decode($rs['costos']).'</td>
                                            <td class="pl20px">'.$rs['nombres'].'</td>
                                            <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['estado'].'</td>
                                            <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                            <td class="textoCentro">'.$asignado.'</td>
                                        </tr>';
                        }
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function asignarOperador($pedido,$detalles,$asignado){
            try {
                $fechaActual = date("Y-m-d");
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab 
                                                        SET tb_pedidocab.asigna     =:asignado,
                                                            tb_pedidocab.fasigna    =:fecha
                                                        WHERE tb_pedidocab.idreg    =:pedido");
                $sql->execute(["asignado"=>$asignado,
                                "pedido"=>$pedido,
                                "fecha"=>$fechaActual]);

                $rowCount = $sql->rowCount();
                if ($rowCount > 0){
                    $this->asignarDetalles($detalles,$asignado);
                }
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function asignarDetalles($detalles,$asignado){
            $datos = json_decode($detalles);
            $nreg = count($datos);

            for ($i=0; $i < $nreg; $i++) {
                try {
                    $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                        SET tb_pedidodet.idasigna=:asignado
                                                        WHERE tb_pedidodet.iditem =:item
                                                            AND (tb_pedidodet.estadoItem = 54 OR tb_pedidodet.estadoItem = 230)");
                $sql->execute(["asignado"=>$asignado,
                                "item"=>$datos[$i]->itempedido]);
                } catch (PDOException $th) {
                    echo $th->getMessage();
                    return false;
                }
            }
        }

        public function filtroAsigna($parametros){
            try {
                $salida = "";
                $mes  = date("m");

                $tipo   = $parametros['tipoSearch'] == -1 ? "%" : $parametros['tipoSearch'];
                $costos = $parametros['costosSearch'] == -1 ? "%" : $parametros['costosSearch'];
                $mes    = $parametros['mesSearch'] == -1 ? "%" :  $parametros['mesSearch'];
                $anio   = $parametros['anioSearch'];
                

                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.tb_pedidocab.nrodoc,
                                                        UPPER( ibis.tb_pedidocab.concepto ) AS concepto,
                                                        ibis.tb_pedidocab.idreg,
                                                        ibis.tb_pedidocab.estadodoc,
                                                        ibis.tb_pedidocab.emision,
                                                        ibis.tb_pedidocab.vence,
                                                        ibis.tb_pedidocab.idtipomov,
                                                        UPPER(
                                                        CONCAT_WS( ' ', ibis.tb_proyectos.ccodproy, ibis.tb_proyectos.cdesproy )) AS costos,
                                                        ibis.tb_pedidocab.nivelAten,
                                                        CONCAT_WS( ' ', rrhh.tabla_aquarius.apellidos, rrhh.tabla_aquarius.nombres ) AS nombres,
                                                        estados.cdescripcion AS estado,
                                                        atencion.cdescripcion AS atencion,
                                                        estados.cabrevia,
                                                        ibis.tb_pedidocab.idcostos,
                                                        ibis.tb_proyectos.ccodproy,
                                                        ibis.tb_proyectos.cdesproy,
                                                        UPPER(ibis.tb_user.cnameuser) as cnameuser  
                                                    FROM
                                                        ibis.tb_pedidocab
                                                        INNER JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                        INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                        INNER JOIN ibis.tb_parametros AS atencion ON ibis.tb_pedidocab.nivelAten = atencion.nidreg
                                                        INNER JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                        LEFT JOIN ibis.tb_user ON ibis.tb_pedidocab.asigna = ibis.tb_user.iduser 
                                                    WHERE
                                                        ibis.tb_pedidocab.estadodoc = 54
                                                        AND ibis.tb_pedidocab.nflgactivo = 1
                                                        AND ibis.tb_pedidocab.idtipomov LIKE :tipomov 
                                                        AND ibis.tb_pedidocab.idcostos LIKE :costos 
                                                        AND MONTH ( ibis.tb_pedidocab.emision ) LIKE :mes 
                                                        AND YEAR ( ibis.tb_pedidocab.emision ) LIKE :anio
                                                    ORDER BY ibis.tb_pedidocab.emision DESC");
                $sql->execute(["tipomov"=>$tipo,
                                "costos"=>$costos,
                                "mes"=>$mes,
                                "anio"=>$anio]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $tipo = $rs['idtipomov'] == 37 ? "B":"S";
                        $asignado = $rs['cnameuser'] == NULL ? "--" : $rs['cnameuser'];
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="textoCentro">'.$tipo.'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
                                        <td class="pl20px">'.utf8_decode($rs['costos']).'</td>
                                        <td class="pl20px">'.$rs['nombres'].'</td>
                                        <td class="textoCentro '.$rs['cabrevia'].'">'.$rs['estado'].'</td>
                                        <td class="textoCentro '.strtolower($rs['atencion']).'">'.$rs['atencion'].'</td>
                                        <td class="textoCentro">'.$asignado.'</td>
                                    </tr>';
                    }
                }else {
                    $salida = '<tr class="pointer"><td colspan="9" class="textoCentro" data-costos="'.$costos.'">No se encontraron registros en la consulta</td></tr>';
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function modificarAsignacion($pedido,$usuario) {
            try {
                $sw = $this->validarItems($pedido);

                if (!$sw) {
                    $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab 
                                                        SET tb_pedidocab.asigna=:asignado,
                                                            tb_pedidocab.libera=:usuario
                                                        WHERE tb_pedidocab.idreg =:pedido");
                    $sql->execute(["asignado"=>NULL,
                                    "pedido"=>$pedido,
                                    "usuario"=>$usuario]);

                    $rowCount = $sql->rowCount();

                    if ($rowCount > 0){
                        $this->liberarDetalles($pedido);
                    }
                }

                return array("existe" => $sw);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function validarItems($pedido){
            try {
                $sql = $this->db->connect()->prepare("SELECT COUNT(*) AS existe FROM lg_ordendet WHERE lg_ordendet.nidpedi=:pedido");
                $sql->execute(["pedido" => $pedido]);

                $result = $sql->fetchAll();

                return $result[0]['existe'];

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function liberarDetalles($pedido){
            $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                        SET tb_pedidodet.idasigna = NULL
                                                        WHERE tb_pedidodet.idpedido = :pedido");
            $sql->execute(["pedido" => $pedido]);
        }
    }
?>