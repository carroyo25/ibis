<?php
    class IngresoEditModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarNotas(){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.id_cuser,
                                                        tb_costusu.ncodproy,
                                                        alm_recepcab.id_regalm,
                                                        alm_recepcab.ncodmov,
                                                        alm_recepcab.nnromov,
                                                        alm_recepcab.nnronota,
                                                        alm_recepcab.cper,
                                                        alm_recepcab.cmes,
                                                        alm_recepcab.ncodalm1,
                                                        alm_recepcab.ffecdoc,
                                                        alm_recepcab.id_centi,
                                                        alm_recepcab.cnumguia,
                                                        alm_recepcab.ncodpry,
                                                        alm_recepcab.ncodarea,
                                                        alm_recepcab.ncodcos,
                                                        alm_recepcab.idref_pedi,
                                                        alm_recepcab.idref_abas,
                                                        alm_recepcab.nEstadoDoc,
                                                        alm_recepcab.nflgCalidad,
                                                        tb_proyectos.ccodproy,
                                                        lg_ordencab.id_regmov,
                                                        UPPER( tb_almacen.cdesalm ) AS almacen,
                                                        UPPER( tb_proyectos.cdesproy ) AS proyecto,
                                                        UPPER( tb_area.cdesarea ) AS area,
                                                        LPAD(lg_ordencab.cnumero,6,0) AS orden,
                                                        LPAD(tb_pedidocab.nrodoc,6,0 ) pedido,
                                                        cm_entidad.crazonsoc
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN alm_recepcab ON tb_costusu.ncodproy = alm_recepcab.ncodpry
                                                        INNER JOIN tb_almacen ON alm_recepcab.ncodalm1 = tb_almacen.ncodalm
                                                        INNER JOIN tb_proyectos ON alm_recepcab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_area ON alm_recepcab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN lg_ordencab ON alm_recepcab.idref_abas = lg_ordencab.id_regmov
                                                        INNER JOIN tb_pedidocab ON alm_recepcab.idref_pedi = tb_pedidocab.idreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi 
                                                    WHERE
                                                        tb_costusu.id_cuser = :usr
                                                        AND alm_recepcab.nflgactivo = 1 
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND alm_recepcab.nEstadoDoc = 60
                                                        AND ((alm_recepcab.cper = YEAR (NOW())- 1 
                                                                AND alm_recepcab.cmes =
                                                            IF
                                                                (
                                                                    MONTH (
                                                                    NOW()) = 1,
                                                                    12,
                                                                    MONTH (
                                                                    NOW())) 
                                                                ) 
                                                        OR ( alm_recepcab.cper = YEAR ( NOW()) AND alm_recepcab.cmes = MONTH ( NOW()) ))
                                                    ORDER BY lg_ordencab.id_regmov DESC
                                                    LIMIT 50");
                $sql->execute(["usr"=>$_SESSION['iduser']]);
                $rowCount = $sql->rowcount();
                if ($rowCount > 0){
                    while($rs = $sql->fetch()){
                        $salida.='<tr class="pointer" data-indice="'.$rs['id_regalm'].'">
                                    <td class="textoCentro">'.$rs['cnumguia'].'</td>
                                    <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffecdoc'])).'</td>
                                    <td class="textoCentro">'.$rs['nnronota'].'</td>
                                    <td class="pl20px">'.$rs['almacen'].'</td>
                                    <td class="textoDerecha pr5px">'.$rs['ccodproy'].'</td>
                                    <td class="pl20px">'.$rs['area'].'</td>
                                    <td class="pl20px">'.$rs['crazonsoc'].'</td>
                                    <td class="textoCentro">'.$rs['orden'].'</td>
                                    <td class="textoCentro">'.$rs['pedido'].'</td>
                                </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function subirArchivosGuiasIngreso($codigo,$adjuntos){
            $countfiles = count( $adjuntos);

            for( $i=0;$i<$countfiles;$i++ ){
                try {
                    $file = "file-".$i;
                    $ext = explode('.',$adjuntos[$file]['name']);
                    $filename = uniqid().".".end($ext);
                    // Upload file
                    if (move_uploaded_file($adjuntos[$file]['tmp_name'],'public/documentos/notas_ingreso/adjuntos/'.$filename)){
                        $sql= $this->db->connect()->prepare("INSERT INTO lg_regdocumento 
                                                                    SET nidrefer=:cod,
                                                                        cmodulo=:mod,
                                                                        cdocumento=:doc,
                                                                        creferencia=:ref,
                                                                        nflgactivo=:est");
                        $sql->execute(["cod"=>$codigo,
                                        "mod"=>"NI",
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