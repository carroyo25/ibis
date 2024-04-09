<?php
    class MarcasModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarPedidosScrollMarca($pagina,$cantidad){
            try {
                $inicio = ($pagina - 1) * $cantidad;
                $limite = $this->contarItems();

                $sql = $this->db->connect()->prepare("SELECT
                                                    ibis.tb_pedidocab.idreg,
                                                    ibis.tb_pedidocab.idcostos,
                                                    ibis.tb_pedidocab.idarea,
                                                    DATE_FORMAT(ibis.tb_pedidocab.emision,'%d/%m/%Y') AS emision,
                                                    ibis.tb_pedidocab.vence,
                                                    ibis.tb_pedidocab.estadodoc,
                                                    LPAD(ibis.tb_pedidocab.nrodoc,6,0) AS nrodoc,
                                                    IF(ibis.tb_pedidocab.idtipomov = 37,'B','S') AS idtipomov,
                                                    UPPER(ibis.tb_pedidocab.concepto) AS concepto,
                                                    CONCAT(rrhh.tabla_aquarius.nombres,' ',rrhh.tabla_aquarius.apellidos) AS nombres,
                                                    UPPER(CONCAT(ibis.tb_proyectos.ccodproy,' ',ibis.tb_proyectos.cdesproy)) AS costos,
                                                    ibis.tb_pedidocab.nivelAten,
                                                    atenciones.cdescripcion AS atencion,
                                                    estados.cdescripcion AS estado,
                                                    UPPER(estados.cabrevia) AS cabrevia 
                                                FROM
                                                    ibis.tb_pedidocab
                                                    LEFT JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                    INNER JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                    INNER JOIN ibis.tb_parametros AS atenciones ON ibis.tb_pedidocab.nivelAten = atenciones.nidreg
                                                    INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                WHERE ibis.tb_pedidocab.estadodoc = 54
                                                AND ibis.tb_pedidocab.idtipomov = 37
                                                AND ibis.tb_pedidocab.nflgactivo = 1
                                                AND YEAR(ibis.tb_pedidocab.emision) = YEAR(NOW())
                                                ORDER BY ibis.tb_pedidocab.nrodoc DESC
                                                LIMIT $inicio,$cantidad");
                
                $sql->execute();

                $rc = $sql->rowcount();
                $item = 1;

                if ($rc > 0){
                    while( $rs = $sql->fetch()) {
                        $pedidos[] = $rs;
                    }
                }

                return array("pedidos"=>$pedidos,
                            'quedan'=>($inicio + $cantidad) < $limite);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function contarItems(){
            try {
                $sql = $this->db->connect()->query("SELECT COUNT(*) AS regs FROM tb_pedidocab WHERE nflgActivo = 1");
                $sql->execute();
                $filas = $sql->fetch();

                return $filas['regs'];
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function marcarItems($cabecera,$detalles,$user){
            $datos = json_decode($detalles);
            $items_actualizados = 0;
            $total_atendido = 0;
            $total_cantidad = 0;
            $estadoCabecera = 54;

            foreach($datos as $dato){
                $estado = $dato->cantidad == $dato->atendida ? 52 : 54;

                $total_atendido =  $total_atendido+$dato->atendida;
                $total_cantidad =  $total_cantidad+$dato->cantidad;

                $sql = $this->db->connect()->prepare("UPDATE tb_pedidodet 
                                                            SET tb_pedidodet.cant_atend =:cant_atend,
                                                                tb_pedidodet.estadoItem =:estado
                                                            WHERE tb_pedidodet.iditem =:indice");

                $sql->execute(["cant_atend"=>$dato->atendida,
                                "estado"=>$estado,
                                "indice"=>$dato->idx]);
                
                $rowCount = $sql->rowCount();

                if ( $rowCount > 0 )
                    $items_actualizados++;
            }

            if ( $total_cantidad == $total_atendido ) {
                echo "actualiza";
                $estadoCabecera = 52;
                $this->modificarCabeceraPedido($cabecera['codigo_pedido'],$estadoCabecera,$user);
            }
            
            
            return array("comprobar"=>$total_cantidad == $total_atendido);
        }

        private function modificarCabeceraPedido($indice,$estado,$user){
            try {
                $sql = $this->db->connect()->prepare("UPDATE tb_pedidocab 
                                                        SET tb_pedidocab.estadodoc=:estado,
                                                            tb_pedidocab.atiende=:user
                                                        WHERE tb_pedidocab.idreg=:indice
                                                        LIMIT 1");
                
                $sql->execute(["estado"=>$estado,"indice"=>$indice,"user"=>$user]);
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function listarPedidosMarca($parametros){

            $anio = isset($parametros['anioSearch']) ? $parametros['anioSearch']:2024;
            $cc   = isset($parametros['costosSearch']) ? $parametros['costosSearch']: "%";
            $nu   = isset($parametros['numeroSearch']) ? $parametros['numeroSearch']: "%";

            $c = $cc == -1 ? "%":$cc;
            $n = $nu == "" ? "%":$nu;

            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        ibis.tb_pedidocab.idreg,
                                                        ibis.tb_pedidocab.idcostos,
                                                        ibis.tb_pedidocab.idarea,
                                                        ibis.tb_pedidocab.emision,
                                                        ibis.tb_pedidocab.vence,
                                                        ibis.tb_pedidocab.estadodoc,
                                                        LPAD(ibis.tb_pedidocab.nrodoc,6,0) AS nrodoc,
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
                                                        LEFT JOIN rrhh.tabla_aquarius ON ibis.tb_pedidocab.idsolicita = rrhh.tabla_aquarius.internal
                                                        INNER JOIN ibis.tb_proyectos ON ibis.tb_pedidocab.idcostos = ibis.tb_proyectos.nidreg
                                                        INNER JOIN ibis.tb_parametros AS atenciones ON ibis.tb_pedidocab.nivelAten = atenciones.nidreg
                                                        INNER JOIN ibis.tb_parametros AS estados ON ibis.tb_pedidocab.estadodoc = estados.nidreg
                                                    WHERE 
                                                        YEAR(ibis.tb_pedidocab.emision) = :anio
                                                            AND ibis.tb_pedidocab.idcostos LIKE :cc
                                                            AND ibis.tb_pedidocab.nrodoc LIKE :num
                                                            AND ibis.tb_pedidocab.estadodoc = 54
                                                            AND ibis.tb_pedidocab.idtipomov = 37
                                                            AND ibis.tb_pedidocab.nflgactivo = 1
                                                    ORDER BY  ibis.tb_pedidocab.nrodoc DESC");
                
                $sql->execute(["anio"=>$anio,"cc"=>$c,"num"=>$n]);

                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $tipo = $rs['idtipomov'] == 37 ? "B":"S";
                        $salida .='<tr class="pointer" data-indice="'.$rs['idreg'].'">
                                        <td class="textoCentro">'.str_pad($rs['nrodoc'],4,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">-</td>
                                        <td class="textoCentro">'.date("d/m/Y", strtotime($rs['emision'])).'</td>
                                        <td class="textoCentro">'.$tipo.'</td>
                                        <td class="pl20px">'.$rs['concepto'].'</td>
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
    }
?>