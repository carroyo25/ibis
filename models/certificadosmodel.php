<?php
    class CertificadosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listaCertificados($user,$orden){
            $orden = $orden == "" ? "%": $orden;

            $docData = [];

            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.ncodproy,
                                                        lg_ordencab.cnumero,
                                                        lg_ordencab.cper,
                                                        lg_ordencab.id_regmov,
                                                        lg_regdocumento.cmodulo,
                                                        lg_regdocumento.nflgactivo,
                                                        tb_proyectos.ccodproy,
                                                        COUNT( lg_regdocumento.cdocumento ) AS adjuntos,
                                                        lg_regdocumento.nidrefer
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN alm_recepcab ON lg_ordencab.id_regmov = alm_recepcab.idref_abas
                                                        INNER JOIN lg_regdocumento ON alm_recepcab.id_regalm = lg_regdocumento.nidrefer
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg 
                                                    WHERE
                                                        tb_costusu.nflgactivo = 1 
                                                        AND tb_costusu.id_cuser = :user
                                                        AND lg_ordencab.cnumero LIKE :orden
                                                        AND lg_regdocumento.cmodulo = 'NI' 
                                                        AND lg_regdocumento.nflgactivo = 1
                                                    GROUP BY
                                                        lg_ordencab.cnumero 
                                                    ORDER BY
                                                        tb_proyectos.ccodproy DESC");

                $sql->execute(["user"=>$user,"orden"=>$orden]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    $respuesta = true;
                    $i = 0;
                    
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("datos"=>$docData);

            }  catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function listarAdjuntos($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        lg_regdocumento.creferencia,
                                                        lg_regdocumento.cdocumento 
                                                    FROM
                                                        lg_regdocumento 
                                                    WHERE
                                                        lg_regdocumento.nidrefer = :id
                                                        AND lg_regdocumento.nflgactivo = 1 
                                                        AND lg_regdocumento.cmodulo = 'NI'");
                
                $sql->execute(["id"=>$id]);

                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    $respuesta = true;
                    $i = 0;
                    
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("adjuntos"=>$docData);
                
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>