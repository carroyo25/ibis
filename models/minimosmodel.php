<?php
    class MinimosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarMinimos($parametros){
            $docData = [];
            $respuesta = false;

            $costos  = $parametros['costos'] == '-1' ? '%': $parametros['costos'];
            $codigo  = $parametros['codigo'] == '' ? '%': '%'.$parametros['codigo'].'%';
            $descrip = $parametros['descripcion'] == '' ? '%': '%'.$parametros['descripcion'].'%';

            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                    e.idprod,
                                                    e.codprod,
                                                    c.idcostos,
                                                    MAX( p.ccodprod ) AS ccodprod,
                                                    UPPER(
                                                    MAX( p.cdesprod )) AS cdesprod,
                                                    MAX( p.ngrupo ) AS ngrupo,
                                                    MAX( p.nclase ) AS nclase,
                                                    MAX( p.nfam ) AS nfam,
                                                    FORMAT( SUM( e.cant_ingr ), 2 ) AS ingresos,
                                                    MAX( u.cabrevia ) AS cabrevia,
                                                    FORMAT(
                                                        COALESCE ((
                                                            SELECT
                                                                SUM( cc.cantsalida ) 
                                                            FROM
                                                                alm_consumo cc 
                                                            WHERE
                                                                cc.ncostos = c.idcostos
                                                                AND cc.idprod = e.codprod 
                                                                AND cc.flgactivo = 1 
                                                                ),
                                                            0 
                                                        ),
                                                        2 
                                                    ) AS consumos,
                                                    DATE_FORMAT(MAX(m2.ffecha),'%d/%m/%Y') AS ffecha,
                                                    FORMAT(MAX(m2.ntotal),2) AS ntotal
                                                FROM
                                                    alm_existencia e
                                                    LEFT JOIN alm_cabexist c ON e.idregistro = c.idreg
                                                    LEFT JOIN cm_producto p ON e.codprod = p.id_cprod
                                                    LEFT JOIN tb_unimed u ON p.nund = u.ncodmed
                                                    LEFT JOIN (SELECT m.idprod, m.ncostos, m.ffecha, m.ntotal FROM alm_minimo m ORDER BY m.ffecha ASC) m2
	                                                    ON m2.idprod = e.codprod AND m2.ncostos = c.idcostos 
                                                WHERE
                                                    e.nflgActivo = 1 
                                                    AND p.flgActivo = 1 
                                                    AND p.ngrupo = 17 
                                                    AND c.idcostos =:costos 
                                                    AND p.cdesprod LIKE :descripcion
                                                    AND p.ccodprod LIKE :codigo
                                                GROUP BY
                                                    e.idprod,
                                                    e.codprod,
                                                    c.idcostos");

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

        public function regristrarMinimo($parametros){
            try {
                $sql = $this->db->connect()->prepare("INSERT INTO alm_minimo
                        SET iduser=:usuario,
                            idprod=:producto,
                            ncostos=:costos,
                            nporcentaje=:porcentaje,
                            npersonal=:personal,
                            ntotal=:total,
                            cobserva=:observa,
                            ffecha=:fecha");

                $sql->execute(["usuario"    =>$parametros['registra'],
                                "producto"  =>$parametros['idprod'],
                                "costos"    =>$parametros['costos'],
                                "porcentaje"=>$parametros['porcentaje'],
                                "total"     =>$parametros['total'],
                                "personal"  =>$parametros['personal'],
                                "observa"   =>$parametros['observaciones'],
                                "fecha"     =>$parametros['fecha']]);

                if ($sql->rowCount()> 1){
                    return array("error"=>0,"mensaje"=>"Correctamente registrado");
                }else{
                    return array("error"=>1,"mensaje"=>"Error en el registro");
                }
                
            } catch (PDOException $th) {
                return array("error"=> $th->getMessage());
            }
        }
    }
?>