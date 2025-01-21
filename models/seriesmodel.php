<?php
    class SeriesModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function grupoProyectosSerie($costos,$serie,$descripcion){
            $s = $serie == "" ? "" : "%".$serie."%";
            $d = $descripcion == "" ? "%" : "%".$descripcion."%";
            $c = $costos == "-1" ? "%" : "%".$costos."%";

            $salida = '<thead class="stickytop">
                            <tr>
                                <th>Item</th>
                                <th>DNI</th>
                                <th>Nombre</th>
                                <th>CCs</th>
                                <th>Codigo</th>
                                <th width="30%">Descripcion</th>
                                <th>UND.</th>
                                <th>Cant.</th>
                                <th>Fecha</br>Salida</th>
                                <th>Fecha</br>Devolucion</th>  
                                <th>N° Hoja</th>
                                <th>Isometricos</th>
                                <th>Observaciones</th>
                                <th>Serie</th>
                                <th>Patrimonio</th>
                                <th>Estado</th>
                                <th width="20px">Firma</th>
                            </tr>
                        </thead>';

            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                UPPER(CONCAT_WS( ' ', tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS proyecto,
                                                alm_consumo.ncostos 
                                            FROM
                                                alm_consumo
                                                INNER JOIN tb_proyectos ON alm_consumo.ncostos = tb_proyectos.nidreg
                                            INNER JOIN cm_producto ON alm_consumo.idprod = cm_producto.id_cprod 	
                                            WHERE
                                                    alm_consumo.cserie LIKE :serie
                                                AND  cm_producto.cdesprod LIKE  :descripcion
                                            GROUP BY
                                                alm_consumo.ncostos");
                $sql->execute(["serie"=>$s, "descripcion"=>$d]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){
                        $salida .= '<tbody class="cc">
                                        <tr class="separatortr">
                                            <th class="pl5px" colspan="17">'.$rs['proyecto'].'</th>
                                        </tr>';
                        $salida .= '<tbody class="items">'.$this->itemsSeries($rs['ncostos'],$serie,$descripcion).'</tbody>';
                        
                        $salida.='</tbody>';
                                   
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function itemsSeries($cc,$serie,$descripcion) {

            $s = $serie == "" ? "%" :"%".$serie."%";
            $d = $descripcion == "" ? "%" :"%".$descripcion."%";

            $salida = '<tr>
                            <td colspan="17">'.$s.'</td>
                        </tr>';

        
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        alm_consumo.idreg,
                                                        alm_consumo.reguser,
                                                        alm_consumo.nrodoc,
                                                        alm_consumo.idprod,
                                                        alm_consumo.cantsalida,
                                                        DATE_FORMAT( alm_consumo.fechasalida, '%d/%m/%Y' ) AS fechasalida,
                                                        DATE_FORMAT( alm_consumo.fechadevolucion, '%d/%m/%Y' ) AS fechadevolucion,
                                                        alm_consumo.nhoja,
                                                        alm_consumo.cisometrico,
                                                        UPPER(alm_consumo.cobserentrega) AS cobserentrega,
                                                        alm_consumo.cobserdevuelto,
                                                        UPPER(alm_consumo.cestado) AS cestado,
                                                        UPPER(alm_consumo.cserie) AS cserie,
                                                        alm_consumo.flgdevolver,
                                                        alm_consumo.cfirma,
                                                        cm_producto.ccodprod,
                                                        alm_consumo.nkardex,
                                                        alm_consumo.calmacen,
                                                        alm_consumo.ncostos,
                                                        UPPER( cm_producto.cdesprod ) AS cdesprod,
                                                        tb_unimed.cabrevia,
                                                        COUNT(*),
                                                        tb_proyectos.ccodproy,
                                                        tb_proyectos.cdesproy,
                                                        CONCAT_WS('',rrhh.tabla_aquarius.apellidos,rrhh.tabla_aquarius.nombres ) AS usuario 
                                                    FROM
                                                        alm_consumo
                                                        LEFT JOIN cm_producto ON alm_consumo.idprod = cm_producto.id_cprod
                                                        LEFT JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                        LEFT JOIN tb_proyectos ON alm_consumo.ncostos = tb_proyectos.nidreg
                                                        LEFT JOIN rrhh.tabla_aquarius ON ibis.alm_consumo.nrodoc = rrhh.tabla_aquarius.dni 
                                                    WHERE
                                                        cm_producto.cdesprod LIKE :descripcion 
                                                        AND alm_consumo.cserie LIKE :serie 
                                                        AND NOT ISNULL( alm_consumo.cserie ) 
                                                        AND alm_consumo.cserie <> '' 
                                                        AND alm_consumo.ncostos LIKE :costos 
                                                    GROUP BY
                                                        alm_consumo.idprod,
                                                        alm_consumo.fechasalida,
                                                        cm_producto.ccodprod,
                                                        alm_consumo.cantsalida,
                                                        alm_consumo.nhoja 
                                                    HAVING
                                                        COUNT(*) >= 1 
                                                    ORDER BY
                                                        alm_consumo.freg DESC");
                
                $sql->execute(["costos"=>$cc,"serie"=>$s,"descripcion"=>$d]);

                $rowCount = $sql->rowCount();
                $item = 1;
                $salida ="";

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()){

                        $marcado = $rs['flgdevolver'] == 1 ? "checked" : "";
                        $firma = "public/documentos/firmas/".$rs['cfirma'].".png";

                        $salida .= '<tr class="pointer" data-grabado="1" 
                                                        data-registrado="1" 
                                                        data-kardex = "'.$rs['nkardex'].'"
                                                        data-firma = "'.$rs['cfirma'].'"
                                                        data-devolucion = "'.$rs['fechadevolucion'].'"
                                                        data-firmadevolucion ="'.$rs['calmacen'].'">
                                        <td class="textoDerecha">'.$rowCount--.'</td>
                                        <td class="textoCentro">'.$rs['nrodoc'].'</td>
                                        <td class="pl5px">'.$rs['usuario'].'</td>
                                        <td class="pl5px">'.$rs['ccodproy'].'</td>
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="pl5px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoDerecha">'.$rs['cantsalida'].'</td>
                                        <td class="textoCentro">'.$rs['fechasalida'].'</td>
                                        <td class="textoCentro">'.$rs['fechadevolucion'].'</td>
                                        <td class="textoCentro">'.$rs['nhoja'].'</td>
                                        <td class="pl5px">'.$rs['cisometrico'].'</td>
                                        <td class="pl5px">'.$rs['cobserentrega'].'</td>
                                        <td class="pl5px">'.$rs['cserie'].'</td>
                                        <td class="textoCentro"><input type="checkbox" '.$marcado.'></td>
                                        <td class="pl5px">'.$rs['cestado'].'</td>
                                        <td class="textoCentro">
                                            <div style ="width:110px !important; text-align:center">
                                                <img src = '.$firma.' style ="width:100% !important">
                                            </div>
                                        </td>
                                    </tr>';
                                    /*$salida .= '<tr>
                                    <td colspan="15">'.$s.'</td>
                                </tr>';*/
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