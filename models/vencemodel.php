<?php
    class VenceModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function mostrarvencimento($cc,$codigo) {
            $codigo = '%';
            $salida = "";

            $sql = $this->db->connect()->prepare("SELECT
                                                    alm_existencia.codprod,
                                                    DATE_FORMAT(date_add(alm_existencia.vence, interval 190 day),'%d/%m/%Y') AS vence,
                                                    UPPER( cm_producto.cdesprod ) AS descripcion,
                                                    cm_producto.ccodprod,
                                                    lg_ordencab.cmes,
                                                    lg_ordencab.cnumero,
                                                    lg_ordencab.ffechadoc,
                                                    lg_ordencab.ncodcos,
                                                    tb_unimed.cabrevia,
                                                    alm_existencia.cant_ingr  
                                                FROM
                                                    alm_existencia
                                                    INNER JOIN cm_producto ON alm_existencia.codprod = cm_producto.id_cprod
                                                    INNER JOIN lg_ordencab ON alm_existencia.nropedido = lg_ordencab.id_regmov
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed  
                                                WHERE
                                                    alm_existencia.vence <> '' 
                                                    AND lg_ordencab.ncodcos =:costo
                                                    AND cm_producto.ccodprod LIKE :codigo
                                                ORDER BY
                                                    alm_existencia.vence ASC");
            $sql->execute(['costo'=>$cc,'codigo'=>$codigo]);
            $rowcount = $sql->rowcount();
            $item = 1;

            if ($rowcount > 0) {
                while ($rs = $sql->fetch()) {
                    $salida .='<tr class="pointer">
                                    <td>'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                    <td class="textocentro">'.$rs['ccodprod'].'</td>
                                    <td class="pl20px">'.$rs['descripcion'].'</td>
                                    <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                    <td class="textoDerecha">'.$rs['cant_ingr'].'</td>
                                    <td class="textoCentro">'.str_pad($rs['cnumero'],6,0,STR_PAD_LEFT).'</td>
                                    <td></td>
                                    <td class="textoCentro">'.$rs['vence'].'</td>
                                    <td></td>
                                    <td></td>
                                </tr>';
                }
            }

            return $salida;

        }

        public function listarVencimientos($costo,$codigo,$descripcion) {
            $cc = $costo == "" ? "%" : "%".$costo."%";
            $cod = $codigo == "" ? "%" : "%".$codigo."%";
            $descrip = $descripcion == "" ? "%" : "%".$descripcion."%";

            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                            alm_existencia.idreg,
                                                            cm_producto.id_cprod,
                                                            alm_existencia.idpedido,
                                                            alm_existencia.freg,
                                                            DATE_FORMAT(alm_existencia.vence,'%d/%m/%Y') AS vence,
                                                            alm_existencia.codprod,
                                                            cm_producto.ccodprod,
                                                            UPPER( cm_producto.cdesprod ) AS producto,
                                                            tb_proyectos.ccodproy,
                                                            tb_unimed.cabrevia,
                                                            tb_pedidocab.nrodoc,
                                                            tb_pedidocab.idorden,
                                                            DATEDIFF(NOW(),alm_existencia.vence) AS dias_pasados
                                                        FROM
                                                            alm_existencia
                                                            LEFT JOIN cm_producto ON alm_existencia.codprod = cm_producto.id_cprod
                                                            INNER JOIN alm_cabexist ON alm_existencia.idregistro = alm_cabexist.idreg
                                                            INNER JOIN tb_proyectos ON alm_cabexist.idcostos = tb_proyectos.nidreg
                                                            INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                            LEFT JOIN tb_pedidocab ON alm_existencia.idpedido = tb_pedidocab.idreg
                                                        WHERE
                                                            alm_existencia.vence <> '' 
                                                            AND tb_proyectos.nidreg LIKE :cc 
                                                            AND cm_producto.cdesprod LIKE :descripcion 
                                                            AND cm_producto.ccodprod LIKE :codigo
                                                            AND alm_existencia.nflgActivo = 1 
                                                        ORDER BY
                                                            alm_existencia.idreg DESC");
                 $sql->execute(["cc" => $cc,"codigo"=>$cod,"descripcion"=>$descrip]);

                 $rowcount = $sql->rowcount();
                 $item = 1;
                 $salida = "";
                 $estado = "";

                 if ($rowcount > 0) {
                     while ($rs = $sql->fetch()) {

                         $estado = intval($rs['dias_pasados']);

                         if ($estado > 7) {
                             $alerta ="semaforoRojo";
                         }elseif ($estado == 7) {
                             $alerta ="semaforNaranja";
                         }elseif($estado < 7) {
                             $alerta ="semaforoVerde";
                         }

                         $salida .='<tr class="pointer" data-idexiste="'.$rs['idreg'].'" data-idproducto="'.$rs['id_cprod'].'">
                                         <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                         <td class="textoCentro">'.$rs['ccodproy'].'</td>
                                         <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                         <td class="pl20px">'.$rs['producto'].'</td>
                                         <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                         <td class="textoCentro"></td>
                                         <td class="textoDerecha">'.str_pad($rs['idorden'],6,0,STR_PAD_LEFT).'</td>
                                         <td></td>
                                         <td class="textoCentro '.$alerta.'">'.$rs['vence'].'</td>
                                         <td></td>
                                         <td></td>
                                         <td class="textoCentro">'.str_pad($rs['nrodoc'],6,0,STR_PAD_LEFT).'</td>
                                         <td class="textoDerecha">'.$rs['dias_pasados'].'</td>
                                     </tr>';
                     }
                 }

                 return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
        
        public function detallarItem($item){
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    alm_existencia.vence,
                                                    alm_cabexist.idcostos,
                                                    tb_proyectos.ccodproy,
                                                    tb_proyectos.cdesproy,
                                                    tb_pedidodet.observaciones,
                                                    tb_pedidodet.idorden,
                                                    tb_pedidodet.cant_orden
                                                FROM
                                                    alm_existencia
                                                    INNER JOIN alm_cabexist ON alm_existencia.idalm = alm_cabexist.idreg
                                                    INNER JOIN tb_proyectos ON alm_cabexist.idcostos = tb_proyectos.nidreg
                                                    INNER JOIN tb_pedidodet ON alm_existencia.idpedido = tb_pedidodet.iditem 
                                                WHERE
                                                    alm_existencia.codprod = :id");
                $sql->execute(["id"=>$item]);

                $result = $sql->fetchAll();
                
                return array("detalles"=>$result); 
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>