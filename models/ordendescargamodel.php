<?php
    class OrdenDescargaModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function descargarPdf($parametros){
            $oc = $parametros['oc'];
            $codigo = "%".$parametros['codigo']."%";
            $docData = [];
            $archivo = "";
            
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                    d.id_regmov,
                                    p.ccodprod,
                                    p.cdesprod,
                                    o.cnumero,
                                    y.ccodproy
                                FROM
                                    lg_ordendet d
                                    LEFT JOIN cm_producto p ON p.id_cprod = d.id_cprod
                                    LEFT JOIN lg_ordencab o ON  o.id_regmov = d.id_regmov
                                    LEFT JOIN tb_proyectos y ON y.nidreg = d.ncodcos
                                WHERE 
                                    d.nestado = 1
                                    AND p.ccodprod LIKE :codigo
                                    AND o.cnumero = :orden");

                $sql->execute(["codigo"=>$codigo,"orden"=>$oc]);

                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    $docData[] = $row;
                }

                $ordenCant = count($docData);

                if ($ordenCant > 0){
                    $archivo = $this->generarDescargaOrden($docData[0]['id_regmov']);
                }

                return array("datos"=>$docData,"existe"=>$ordenCant,"archivo"=>$archivo);

            } catch (PDOException $th) {
                return array('error'->$th->getMessage());
            }
        }
        
    }
?>