<?php
    class MinimosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarMinimos($parametros){
            $docData = [];
            $respuesta = false;

            $costos  = $parametros['costos'] == '-1' ? '%':$parametros['costos'];
            $codigo  = $parametros['codigo'] == '' ? '%':$parametros['codigo'];
            $descrip = $parametros['descripcion'] == '' ? '%':$parametros['descripcion'];

            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                    alm_existencia.idreg,
                                                    alm_existencia.idpedido,
                                                    alm_existencia.idorden,
                                                    alm_existencia.idprod,
                                                    alm_existencia.codprod,
                                                    alm_cabexist.idcostos,
                                                    cm_producto.ccodprod,
                                                    UPPER( cm_producto.cdesprod ) AS cdesprod,
                                                    cm_producto.ngrupo,
                                                    cm_producto.nclase,
                                                    cm_producto.nfam,
                                                    FORMAT(SUM( alm_existencia.cant_ingr ),2) AS ingresos,
                                                    tb_unimed.cabrevia 
                                                FROM
                                                    alm_existencia
                                                    INNER JOIN alm_cabexist ON alm_existencia.idregistro = alm_cabexist.idreg
                                                    INNER JOIN cm_producto ON alm_existencia.codprod = cm_producto.id_cprod
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed 
                                                WHERE
                                                    alm_existencia.nflgActivo 
                                                    AND cm_producto.flgActivo 
                                                    AND cm_producto.ngrupo = 17 
                                                    AND cm_producto.nclase = 56 
                                                    AND alm_cabexist.idcostos LIKE :costos 
                                                    AND cm_producto.cdesprod LIKE :descripcion
                                                    AND alm_existencia.codprod LIKE :codigo
                                                GROUP BY
                                                    alm_existencia.codprod,
                                                    alm_cabexist.idcostos 
                                                ORDER BY
                                                    cm_producto.cdesprod ASC");

                $sql->execute(["costos"=>$costos,"codigo"=>$codigo,"descripcion"=>$descrip]);

                while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                return array($docData);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>