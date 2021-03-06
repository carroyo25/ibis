<?php
    class EvaluacionModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarOrdenes(){
            try {
                 $salida = "";
                 $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.ncodcos,
                                                        tb_costusu.ncodproy,
                                                        tb_costusu.id_cuser,
                                                        lg_ordencab.id_regmov,
                                                        lg_ordencab.cnumero,
                                                        lg_ordencab.ffechadoc,
                                                        lg_ordencab.nNivAten,
                                                        lg_ordencab.nEstadoDoc,
                                                        lg_ordencab.ncodpago,
                                                        lg_ordencab.nplazo,
                                                        lg_ordencab.cdocPDF,
                                                        lg_ordencab.ntipmov,
                                                        UPPER( tb_pedidocab.concepto ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        UPPER(
                                                        CONCAT_WS( tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        UPPER(
                                                        CONCAT_WS( tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        lg_ordencab.id_centi,
                                                        cm_entidad.cnumdoc,
                                                        UPPER( cm_entidad.crazonsoc ) AS proveedor,
                                                        tb_user.nrol 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                        INNER JOIN tb_user ON lg_ordencab.id_cuser = tb_user.iduser 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1 
                                                        AND lg_ordencab.nEstadoDoc = 67 
	                                                    AND lg_ordencab.userEval <> :userEval");
                 $sql->execute(["user"=>$_SESSION['iduser'],"userEval"=>$_SESSION['iduser']]);
                 $rowCount = $sql->rowCount();
 
                 if ($rowCount > 0){
                     while ($rs = $sql->fetch()) {
 
                         $salida .='<tr class="pointer" data-indice="'.$rs['id_regmov'].'" data-tipo="'.$rs['ntipmov'].'" data-rol="'.$rs['nrol'].'">
                                     <td class="textoCentro">'.str_pad($rs['cnumero'],4,0,STR_PAD_LEFT).'</td>
                                     <td class="textoCentro">'.date("d/m/Y", strtotime($rs['ffechadoc'])).'</td>
                                     <td class="pl20px">'.$rs['detalle'].'</td>
                                     <td class="pl20px">'.utf8_decode($rs['costos']).'</td>
                                     <td class="pl20px">'.$rs['area'].'</td>
                                     <td class="pl20px">'.$rs['proveedor'].'</td>
                                    </tr>';
                     }
                 }
 
                 return $salida;                    
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function llamarOrdenID($tipo,$id){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    lg_ordencab.id_regmov,
                                                    lg_ordencab.cnumero,
                                                    lg_ordencab.ffechadoc,
                                                    UPPER( cm_entidad.crazonsoc ) AS entidad,
                                                    cm_entidad.id_centi,
                                                    UPPER( tb_pedidocab.concepto ) AS concepto,
                                                    lg_ordencab.id_cuser,
                                                    tb_user.nrol,
                                                    lg_ordencab.ntipmov,
                                                    UPPER( tb_proyectos.cdesproy ) AS proyecto 
                                                FROM
                                                    lg_ordencab
                                                    INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                    INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                    INNER JOIN tb_user ON lg_ordencab.id_cuser = tb_user.iduser
                                                    INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg 
                                                WHERE
                                                    lg_ordencab.id_regmov =:id");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowcount();

                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }  

                return array("cabecera"=>$docData,
                            "nrol"=>$docData[0]["nrol"],
                            "criterios"=>$this->evaluar($docData[0]["nrol"],$docData[0]["ntipmov"]));

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function evaluar($rol,$tipo){
            try {
                $salida = "";
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_criterios.idreg,
                                                        tb_criterios.descripcion,
                                                        tb_criterios.ayuda,
                                                        tb_criterios.puntaje,
                                                        tb_criterios.peso 
                                                    FROM
                                                        tb_criterios 
                                                    WHERE
                                                        tb_criterios.nrol = :rol 
                                                        AND tb_criterios.tipo = :tipo");
                $sql->execute(["rol"=>$rol,"tipo"=>$tipo]);
                $rowCount = $sql->rowCount();
                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $salida.='<tr data-reg="'.$rs['idreg'].'" data-total="'.$rs['puntaje'].'" data-peso="'.$rs['peso'].'" data-tipo="'.$tipo.'">
                                    <td class="pl20px criterio">'.$rs['descripcion'].'</td>
                                    <td class="pl20px">'.$rs['ayuda'].'</td>
                                    <td>
                                        <input type="text" value="'.$rs["puntaje"].'" maxlength="1" pattern="^[1-5]+" class="textoCentro"
                                            onClick="this.select();" class="puntaje">
                                    </td>
                                  </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
            }
        }

        public function grabarEvaluacion($items){
            try {
                $datos = json_decode($items);
                $nreg = count($datos);
                $mensaje = "No se grab??, la evaluacion";
                $respuesta = false;
                $item = 0;
                $clase="mensaje_error";

                for ($i=0; $i < $nreg ; $i++) { 
                    try {
                        $sql = $this->db->connect()->prepare("INSERT INTO tb_califica SET idcriterio=:criterio,
                                                                                            idorden=:orden,
                                                                                            identidad=:entidad,
                                                                                            iduser=:usr,
                                                                                            npuntaje=:puntaje,
                                                                                            npeso=:peso,
                                                                                            ncalifica=:califica,
                                                                                            nrol=:rol");
                        $sql->execute(["criterio"=>$datos[$i]->reg,
                                        "orden"=>$datos[$i]->orden,
                                        "entidad"=>$datos[$i]->entidad,
                                        "usr"=>$datos[$i]->usuario,
                                        "puntaje"=>$datos[$i]->puntaje,
                                        "peso"=>$datos[$i]->peso,
                                        "califica"=>($datos[$i]->peso/100)*$datos[$i]->puntaje,
                                        "rol"=>$datos[$i]->rol]);
                        
                        $rowCount = $sql->rowCount();

                        if ($rowCount > 0){
                            $item++;
                        }

                    } catch (PDOException $th) {
                        echo $th->getMessage();
                        return false;
                    }
                }

                if ($item > 0){
                    $this->actualizarOrden($datos[0]->orden);
                    $mensaje = "Grabado correctamente";
                    $respuesta = false;
                    $clase = "mensaje_correcto";
                }

                return array("mensaje"=>$mensaje, 
                             "respuesta"=>$respuesta,
                            "clase"=>$clase);

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
            }
        }

        private function actualizarOrden($id){
            try {
                $sql=$this->db->connect()->prepare("UPDATE lg_ordencab SET userEval = :usr WHERE id_regmov = :id");
                $sql->execute(["usr"=>$_SESSION['iduser'],"id"=>$id]);            
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }
        
    }
?>