<?php
    class DetalleCsModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarReporteConsumos($costo,$codigo,$descripcion) {
            
            $cc = $costo == "-1" ? "%" : "%".$costo."%";
            $cod = $codigo == "" ? "%" : "%".$codigo."%";
            $descrip = $descripcion == "" ? "%" : "%".$descripcion."%";

            $salida = "";

            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                    ibis.cm_producto.ccodprod,
                                                    UPPER(ibis.cm_producto.cdesprod) AS producto,
                                                    ibis.tb_proyectos.ccodproy,
                                                    ibis.tb_proyectos.nidreg,
                                                    ibis.alm_consumo.cantsalida AS salida,
                                                    ibis.alm_consumo.fechasalida,
                                                    ibis.alm_consumo.nrodoc,
                                                    CONCAT_WS( ' ', rrhh.tabla_aquarius.apellidos, rrhh.tabla_aquarius.nombres ) AS usuario,
                                                    ibis.tb_unimed.cabrevia,
                                                    ibis.alm_consumo.idreg 
                                                    FROM
                                                    ibis.alm_consumo
                                                    LEFT JOIN ibis.cm_producto ON alm_consumo.idprod = cm_producto.id_cprod
                                                    LEFT JOIN ibis.tb_proyectos ON alm_consumo.ncostos = tb_proyectos.nidreg
                                                    LEFT JOIN rrhh.tabla_aquarius ON ibis.alm_consumo.nrodoc = rrhh.tabla_aquarius.dni
                                                    INNER JOIN ibis.tb_unimed ON ibis.cm_producto.nund = ibis.tb_unimed.ncodmed 
                                                    WHERE
                                                    tb_proyectos.nflgactivo = 1 
                                                    AND alm_consumo.flgactivo = 1 
                                                    AND cm_producto.cdesprod LIKE :descripcion  
                                                    AND cm_producto.ccodprod LIKE :codigo 
                                                    AND alm_consumo.ncostos LIKE :cc
                                                    ORDER BY ibis.tb_proyectos.ccodproy ASC");

                $sql->execute(["cc" => $cc,"codigo"=>$cod,"descripcion"=>$descrip]);

                $rowcount = $sql->rowcount();
                $item = 1;

                if ($rowcount > 0) {
                     while ($rs = $sql->fetch()) {
                        $salida .='<tr class="pointer" data-idexiste  ="'.$rs['idreg'].'" 
                                    data-idproducto="'.$rs['ccodprod'].'"
                                    data-idcostos  ="'.$rs['nidreg'].'">
                                        <td class="textoCentro">'.str_pad($item++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.$rs['ccodproy'].'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl20px">'.$rs['producto'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['nrodoc'].'</td>
                                        <td class="pl20px">'.$rs['usuario'].'</td>
                                        <td class="textoDerecha">'.number_format($rs['salida'],2,'.','').'</td>
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