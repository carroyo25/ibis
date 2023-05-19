<?php
    class RepoagerModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarClasesReporte() {
            try {
                $sql = $this->db->connect()->query("SELECT
                                                        tb_clase.ncodclase,
                                                        tb_clase.ncodgrupo,
                                                        UPPER(tb_grupo.cdescrip) AS cdescrip,
                                                        tb_grupo.ccodcata,
                                                        COUNT( tb_clase.ncodgrupo ) AS repeticiones
                                                    FROM
                                                        tb_clase
                                                        INNER JOIN tb_grupo ON tb_clase.ncodgrupo = tb_grupo.ncodgrupo
                                                    WHERE
                                                        tb_clase.nflgactivo = 1
                                                    GROUP BY tb_grupo.cdescrip
                                                    HAVING COUNT( tb_clase.ncodgrupo ) > 0
                                                    ORDER BY tb_grupo.cdescrip");
                $sql->execute();
                $rowCount = $sql->rowCount();
                $salida = "";

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<option value ="'.$rs['ncodgrupo'].'">'.$rs['cdescrip'].'</option>';
                    }  
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function listarTipos($id) {
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    tb_clase.ncodclase, 
                                                    tb_clase.ccodcata, 
                                                    tb_clase.cdescrip, 
                                                    tb_clase.ncodgrupo
                                                FROM
                                                    tb_clase
                                                WHERE
                                                    tb_clase.nflgactivo = 1
                                                    AND tb_clase.ncodgrupo = :id");
                
                $sql->execute(['id'=>$id]);
                $salida = "";
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ($rs = $sql->fetch()) {
                        $salida .= '<option value ="'.$rs['ncodclase'].'">'.$rs['cdescrip'].'</option>';
                    }  
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>