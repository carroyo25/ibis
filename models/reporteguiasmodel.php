<?php
    class ReporteGuiasModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarGuias(){
            try {
                $sql = $this->db->connect()->prepare("SELECT
	lg_guias.cnumguia,
	lg_guias.corigen,
	lg_guias.cdestino,
	lg_guias.ftraslado,
	lg_guias.freg 
FROM
	lg_guias 
WHERE
	lg_guias.nflgActivo = 1 
	AND lg_guias.cdirorigen <> "" 
ORDER BY
	lg_guias.freg DESC");
                $sql->execute();

                $rowCount = $sql->rowCount();
                
                if ($rowCount) {
                    $respuesta = true;
                    $i = 0;
                    
                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("datos"=>$docData);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>