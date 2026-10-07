<?php
    class MovimientosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarStocks($parametros){
            try {
                $success = false;
                $datos = [];
                $cc = $parametros['costosSearch'];
                $cp = $parametros['codigoBusqueda'] == "" ? "%" : "%".$parametros['codigoBusqueda']."%";
                $de = $parametros['descripcionSearch'] == "" ? "%" : "%".$parametros['descripcionSearch']."%";

            
                $sql = $this->db->connect()->prepare("SELECT
                                            cm_producto.id_cprod,
                                            cm_producto.ccodprod,
                                            UPPER( cm_producto.cdesprod ) AS cdesprod,
                                            recepcion.cantidad_obra AS ingresos,
                                            recepcion.idreg,
                                            inventarios.condicion,
                                            inventarios.inventarios_cantidad AS inventarios,
                                            SUM( consumo.cantsalida ) AS consumos,
                                            SUM( consumo.cantdevolucion ) AS devoluciones,
                                            sal_trans.salida_transferencia AS salidas_transferencia,
                                            sal_trans.iditem,
                                            ing_trans.ingreso_transferencia AS ingresos_transferencias,
                                            minimo.cantidad_minima AS minimo,
                                            tb_unimed.cabrevia,
                                            ajustes.ajustes_cantidad AS ajustes,
                                            UPPER( tb_grupo.cdescrip ) AS grupo,
                                            UPPER( tb_clase.cdescrip ) AS clase,
                                            UPPER( tb_familia.cdescrip ) AS familia 
                                        FROM
                                            cm_producto
                                            LEFT JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                            LEFT JOIN (
                                            SELECT
                                                COUNT( alm_existencia.cant_ingr ) AS ingresos_obra,
                                                SUM( alm_existencia.cant_ingr ) AS cantidad_obra,
                                                alm_existencia.codprod,
                                                alm_existencia.idreg
                                            FROM
                                                alm_existencia
                                                LEFT JOIN alm_cabexist ON alm_cabexist.idreg = alm_existencia.idregistro 
                                            WHERE
                                                alm_existencia.nflgActivo = 1 
                                                AND alm_cabexist.idcostos = :cingreso  
                                            GROUP BY
                                                alm_existencia.codprod 
                                            ) AS recepcion ON recepcion.codprod = cm_producto.id_cprod
                                            LEFT JOIN (
                                            SELECT
                                                COUNT( alm_inventariodet.cant_ingr ) AS inventarios_registros,
                                                SUM( alm_inventariodet.cant_ingr ) AS inventarios_cantidad,
                                                alm_inventariocab.idcostos,
                                                alm_inventariodet.codprod,
                                                alm_inventariodet.condicion 
                                            FROM
                                                alm_inventariodet
                                                INNER JOIN alm_inventariocab ON alm_inventariodet.idregistro = alm_inventariocab.idreg 
                                            WHERE
                                                alm_inventariodet.nflgActivo = 1 
                                                AND alm_inventariocab.idcostos = :cinventario 
                                            GROUP BY
                                                alm_inventariodet.codprod 
                                            ) AS inventarios ON inventarios.codprod = cm_producto.id_cprod
                                            LEFT JOIN (
                                            SELECT
                                                SUM( alm_consumo.cantsalida ) AS cantsalida,
                                                SUM(alm_consumo.cantdevolucion) AS cantdevolucion,
                                                alm_consumo.idprod 
                                            FROM
                                                alm_consumo 
                                            WHERE
                                                alm_consumo.ncostos = :csalida
                                                AND alm_consumo.flgactivo = 1 
                                            GROUP BY
                                                alm_consumo.idprod
                                            ) AS consumo ON consumo.idprod = cm_producto.id_cprod
                                            LEFT JOIN (
                                            SELECT
                                                SUM( alm_transferdet.ncanti ) AS salida_transferencia,
                                                alm_transferdet.idcprod,
                                                alm_transferdet.iditem 
                                            FROM
                                                alm_transferdet
                                                LEFT JOIN alm_transfercab ON alm_transferdet.idtransfer = alm_transfercab.idreg 
                                            WHERE
                                                alm_transferdet.nflgactivo = 1 
                                                AND alm_transfercab.idcc = :ctransfsalida 
                                            GROUP BY
                                                alm_transferdet.idcprod 
                                            ) AS sal_trans ON sal_trans.idcprod = cm_producto.id_cprod
                                            LEFT JOIN (
                                            SELECT
                                                SUM( alm_transferdet.ncanti ) AS ingreso_transferencia,
                                                alm_transferdet.idcprod 
                                            FROM
                                                alm_transferdet
                                                LEFT JOIN alm_transfercab ON alm_transferdet.idtransfer = alm_transfercab.idreg 
                                            WHERE
                                                alm_transferdet.nflgactivo = 1 
                                                AND alm_transfercab.idcd = :ctransfingreso 
                                            GROUP BY
                                                alm_transferdet.idcprod 
                                            ) AS ing_trans ON ing_trans.idcprod = cm_producto.id_cprod
                                            LEFT JOIN (
                                            SELECT
                                                alm_minimo.dfecha,
                                                alm_minimo.idprod,
                                                alm_minimo.ncantidad AS cantidad_minima 
                                            FROM
                                                alm_minimo 
                                            WHERE
                                                alm_minimo.ncostos = :cminimo  
                                            GROUP BY
                                                alm_minimo.idprod,
                                                alm_minimo.dfecha 
                                            ) AS minimo ON minimo.idprod = cm_producto.id_cprod
                                            LEFT JOIN (
                                            SELECT
                                                COUNT( alm_ajustedet.cant_ingr ) AS ajustes_registros,
                                                SUM( alm_ajustedet.cant_ingr ) AS ajustes_cantidad,
                                                alm_ajustecab.idcostos,
                                                alm_ajustedet.codprod,
                                                alm_ajustedet.condicion 
                                            FROM
                                                alm_ajustedet
                                                INNER JOIN alm_ajustecab ON alm_ajustedet.idregistro = alm_ajustecab.idreg 
                                            WHERE
                                                alm_ajustedet.nflgActivo = 1 
                                                AND alm_ajustecab.idcostos = :cajuste 
                                                AND NOT ISNULL( alm_ajustecab.idrecepciona ) 
                                            GROUP BY
                                                alm_ajustedet.codprod 
                                            ) AS ajustes ON ajustes.codprod = cm_producto.id_cprod
                                            LEFT JOIN tb_grupo ON cm_producto.ngrupo = tb_grupo.ncodgrupo
                                            LEFT JOIN tb_clase ON cm_producto.nclase = tb_clase.ncodclase
                                            LEFT JOIN tb_familia ON cm_producto.nfam = tb_familia.ncodfamilia 
                                        WHERE
                                            cm_producto.flgActivo = 1 
                                            AND cm_producto.ntipo = 37 
                                            AND cm_producto.ccodprod LIKE :codigo 
                                            AND cm_producto.cdesprod LIKE :descripcion 
                                            AND ( NOT ISNULL( recepcion.cantidad_obra ) OR NOT ISNULL( inventarios.inventarios_cantidad ) OR NOT ISNULL( sal_trans.salida_transferencia ) ) 
                                        GROUP BY
                                            cm_producto.id_cprod 
                                        ORDER BY
                                            cm_producto.cdesprod ASC
                                        LIMIT 0,10");
                $sql->execute(["cingreso" =>$cc,
                                "cinventario" =>$cc,
                                "csalida" =>$cc,
                                "ctransfsalida" =>$cc,
                                "ctransfingreso" =>$cc,
                                "cajuste" =>$cc,
                                "cminimo" =>$cc,
                                "codigo" =>$cp,
                                "descripcion" =>$de]);
                $rowCount = $sql->rowCount();
                
                $datos = $sql->fetchAll(PDO::FETCH_ASSOC);
                
                if ($rowCount > 0) {
                    $success = true;
                }
                
                return array("datos"=>$datos,"success"=>$success);

            } catch (PDOException $th) {
                return array("error: "->$th->getMessage(),"success"->false);
            }
        }
    }
?>