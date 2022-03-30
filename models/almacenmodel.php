<?php
    class AlmacenModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarAlmacen(){
            try {
                $salida = "";
                $sql = $this->db->connect()->query("SELECT ncodalm,cdesalm,ncubigeo FROM tb_almacen WHERE nflgactivo=1");
                $sql->execute();
                $rc = $sql->rowcount();
                $item = 1;

                if ($rc > 0){
                    while( $rs = $sql->fetch()) {
                        $salida .='<tr data-id="'.$rs['ncodalm'].'">
                                        <td class="textoCentro">'.str_pad($item,3,0,STR_PAD_LEFT).'</td>
                                        <td class="pl20px">'.strtoupper($rs['cdesalm']).'</td>
                                        <td class="textoCentro"><a href="'.$rs['ncodalm'].'"><i class="fas fa-trash-alt"></i></a></td>
                                    </tr>';
                        $item++;
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function insertarAlmacen($datos){
            $salida = false;
            $respuesta = false;
            $mensaje = "Error en el registro";
            $clase = "mensaje_error";

            if(!$this->verificarExiste($datos['descripcion'])){
                try { 
                    $sql=$this->db->connect()->prepare("INSERT INTO tb_almacen 
                                                        SET ncubigeo=:ubi,
                                                            cdesalm=:dea,
                                                            ctipovia=:vti,
                                                            cdesvia=:vin,
                                                            cnrovia=:num,
                                                            czonavia=:zon,
                                                            nflgactivo=:est");
                    $sql->execute([ "ubi"=>$datos['ubigeo'],
                                    "dea"=>$datos['descripcion'],
                                    "vti"=>$datos['vtipo'],
                                    "vin"=>$datos['vnombre'],
                                    "num"=>$datos['numero'],
                                    "zon"=>$datos['zona'],
                                    "est"=>1]);
                                    
                    $rc = $sql->rowcount();

                    if ($rc > 0) {
                        
                        $respuesta = true;
                        $mensaje = "Se registro correctamente";
                        $clase = "mensaje_correcto";
                    }

                     $salida = array("respuesta"=>$respuesta,
                                    "mensaje"=>$mensaje,
                                    "clase"=>$clase);
                } catch (PDOException $th) {
                    echo $th->getMessage();
                    return false;
                } 
            }else{
                $salida = array("respuesta"=>false,
                                "mensaje"=>"Ya se tiene registrado",
                                "clase"=>"mensaje_error");
            }

            return $salida;
        }

        public function  modificarAlmacen($datos){
            $salida = false;
            $respuesta = false;
            $mensaje = "Error al modifcar el registro";
            $clase = "mensaje_error";

            try {
                $sql=$this->db->connect()->prepare("UPDATE tb_almacen 
                                                        SET ncubigeo=:ubi,
                                                            cdesalm=:dea,
                                                            ctipovia=:vti,
                                                            cdesvia=:vin,
                                                            cnrovia=:num,
                                                            czonavia=:zon,
                                                            nflgactivo=:est
                                                        WHERE ncodalm=:id");
                $sql->execute([ "ubi"=>$datos['ubigeo'],
                                "dea"=>$datos['descripcion'],
                                "vti"=>$datos['vtipo'],
                                "vin"=>$datos['vnombre'],
                                "num"=>$datos['numero'],
                                "zon"=>$datos['zona'],
                                "est"=>1,
                                "id"=>$datos['codigo']]);
                
                                $rc = $sql->rowcount();  
                if ($rc > 0) {
                    $respuesta = true;
                    $mensaje = "Registro modificado";
                    $clase = "mensaje_correcto";
                }

                $salida = array("respuesta"=>$respuesta,
                                    "mensaje"=>$mensaje,
                                    "clase"=>$clase);
                
                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function idAlmacen($id){
            $salida = "";
            try {
                $sql=$this->db->connect()->prepare("SELECT
                                                    tb_almacen.ncodalm,
                                                    tb_almacen.ccodalm,
                                                    tb_almacen.cdesalm,
                                                    tb_almacen.ncubigeo,
                                                    tb_almacen.cdesprov,
                                                    tb_almacen.cdesdist,
                                                    tb_almacen.ctipovia,
                                                    tb_almacen.cdesvia,
                                                    tb_almacen.cnrovia,
                                                    tb_almacen.cintevia,
                                                    tb_almacen.czonavia,
                                                    distritos.cdubigeo AS distrito,
                                                    provincias.cdubigeo AS provincia,
                                                    dptos.cdubigeo AS departamento 
                                                FROM
                                                    tb_almacen
                                                    LEFT JOIN tb_ubigeo AS distritos ON tb_almacen.ncubigeo = distritos.ccubigeo
                                                    LEFT JOIN tb_ubigeo AS provincias ON SUBSTR( tb_almacen.ncubigeo, 1, 4 ) = provincias.ccubigeo
                                                    LEFT JOIN tb_ubigeo AS dptos ON SUBSTR( tb_almacen.ncubigeo, 1, 2 ) = dptos.ccubigeo 
                                                WHERE tb_almacen.ncodalm =:id");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }
                }

                return array("almacen"=>$docData);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function verificarExiste($nombre){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                    count( ncodalm ) AS numero 
                                                FROM
                                                    tb_almacen 
                                                WHERE
                                                    cdesalm = :alm");
                $sql->execute(["alm"=>$nombre]);
                $result = $sql->fetchAll();
    
                return $result[0]['numero'];

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }  
        }

        public function borrarAlmacen($id){
            try {
                $sql=$this->db->connect()->prepare("UPDATE tb_almacen SET nflgactivo = 0 WHERE ncodalm=:id");
                $sql->execute(["id"=>$id]);
                $rc = $sql->rowcount();

                if ($rc > 0) {
                    $salida = $this->listarAlmacen();
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>