<?php
    class GuiaUpdateModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarNotasDespacho(){
            $salida = "";

            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                    alm_despachocab.cmes,
                                                    DATE_FORMAT(
                                                            alm_despachocab.ffecdoc,
                                                            '%d/%m/%Y'
                                                        ) AS ffecdoc,
                                                    YEAR(ffecdoc) AS anio,
                                                    alm_despachodet.nropedido AS orden,
                                                    alm_despachodet.nroorden AS pedido,
                                                    UPPER(origen.cdesalm) AS origen,
                                                    UPPER(origen.ctipovia) AS direccion_origen,
                                                    UPPER(destino.cdesalm) AS destino,
                                                    UPPER(destino.ctipovia) AS direccion_destino,
                                                    alm_despachocab.cnumguia,
                                                    alm_despachocab.nEstadoDoc,
                                                    lg_ordencab.cnumero,
                                                    UPPER(
                                                            CONCAT_WS(
                                                                ' ',
                                                                tb_proyectos.ccodproy,
                                                                tb_proyectos.cdesproy
                                                                
                                                            )
                                                        ) AS costos,
                                                    tb_costusu.nflgactivo,
                                                    tb_parametros.cdescripcion,
                                                    tb_parametros.cabrevia,
                                                    alm_despachocab.id_regalm 
                                                FROM
                                                    alm_despachodet
                                                    INNER JOIN alm_despachocab ON alm_despachodet.id_regalm = alm_despachocab.id_regalm
                                                    INNER JOIN tb_almacen AS origen ON alm_despachocab.ncodalm1 = origen.ncodalm
                                                    INNER JOIN tb_almacen AS destino ON alm_despachocab.ncodalm2 = destino.ncodalm
                                                    INNER JOIN tb_proyectos ON alm_despachocab.ncodpry = tb_proyectos.nidreg
                                                    INNER JOIN tb_costusu ON alm_despachocab.ncodpry = alm_despachocab.ncodpry
                                                    INNER JOIN tb_parametros ON alm_despachocab.nEstadoDoc = tb_parametros.nidreg
                                                    INNER JOIN lg_ordencab   ON lg_ordencab.id_regmov = alm_despachodet.nropedido
                                                WHERE
                                                    tb_costusu.nflgactivo = 1 
                                                    AND tb_costusu.id_cuser = :usr
                                                    AND ((alm_despachocab.cper = YEAR (NOW())- 1 
                                                                AND alm_despachocab.cmes =
                                                            IF
                                                                (
                                                                    MONTH (
                                                                    NOW()) = 1,
                                                                    12,
                                                                    MONTH (
                                                                    NOW())) 
                                                                ) 
                                                        OR ( alm_despachocab.cper = YEAR ( NOW()) AND alm_despachocab.cmes = MONTH ( NOW()) ))
                                                    AND alm_despachocab.nEstadoDoc = 62
                                                GROUP BY alm_despachocab.id_regalm
                                                    ORDER BY alm_despachocab.ffecdoc DESC
                                                LIMIT 50");
                $sql->execute(["usr"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .='<tr data-indice="'.$rs['id_regalm'].'" class="pointer">
                                        <td class="textoCentro">'.str_pad($rs['id_regalm'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ffecdoc'].'</td>
                                        <td class="textoCentro">'.$rs['origen'].'</td>
                                        <td class="pl20px">'.$rs['destino'].'</td>
                                        <td class="pl20px">'.$rs['costos'].'</td>
                                        <td class="textoCentro">'.$rs['anio'].'</td>
                                        <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                        <td class="textoCentro ">'.str_pad($rs['cnumero'],6,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro ">'.str_pad($rs['pedido'],6,0,STR_PAD_LEFT).'</td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function subirArchivosGuias($codigo,$adjuntos){
            $countfiles = count( $adjuntos);

            for( $i=0;$i<$countfiles;$i++ ){
                try {
                    $file = "file-".$i;
                    $ext = explode('.',$adjuntos[$file]['name']);
                    $filename = uniqid().".".end($ext);
                    // Upload file
                    if (move_uploaded_file($adjuntos[$file]['tmp_name'],'public/documentos/guias_remision/adjuntos/'.$filename)){
                        $sql= $this->db->connect()->prepare("INSERT INTO lg_regdocumento 
                                                                    SET nidrefer=:cod,
                                                                        cmodulo=:mod,
                                                                        cdocumento=:doc,
                                                                        creferencia=:ref,
                                                                        nflgactivo=:est");
                        $sql->execute(["cod"=>$codigo,
                                        "mod"=>"GUIA",
                                        "ref"=>$filename,
                                        "doc"=>$adjuntos[$file]['name'],
                                        "est"=>1]);
                    }
                } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }
            }

            return array("adjuntos"=>$this->contarAdjuntos($codigo,'ORD'));
        }
    }
?>