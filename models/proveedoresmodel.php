<?php
    class ProveedoresModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarProveedores(){
            try {
                $salida = "";
                $query = $this->db->connect()->query("SELECT cm_entidad.crazonsoc,
                                                             cm_entidad.cnumdoc,
                                                             cm_entidad.ctelefono,
                                                             cm_entidad.cemail,
                                                             cm_entidad.id_centi
                                                        FROM cm_entidad 
                                                        WHERE cm_entidad.nflgactivo = 1");
                $query->execute();

                $rowcount = $query->rowcount();

                if ($rowcount > 0 ){
                    $contador = 1;
                    while ($row = $query->fetch()) {


                        $salida .= '<tr class="pointertr" data-id="'.$row['id_centi'].'">
                                        <td class="textoCentro">'.str_pad($contador++,3,0,STR_PAD_LEFT).'</td>
                                        <td class="textoCentro">'.strtoupper($row['cnumdoc']).'</td>
                                        <td class="pl20px">'.strtoupper($row['crazonsoc']).'</td>
                                        <td class="textoCentro">'.strtoupper($row['ctelefono']).'</td>
                                        <td class="pl20px">'.strtolower($row['cemail']).'</td>
                                        <td class="textoCentro"><a href="'.$row['id_centi'].'"><i class="far fa-trash-alt"></i></a></td>
                                    </tr>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
        
        public function insertar($datos,$bancos,$contactos){
            $salida = false;
           
            if ( $this->existeProveedor($datos['nrodoc'])){
                $salida = array("respuesta"=>false,
                                "clase"=>"mensaje_error",
                                "mensaje"=>"Ya se registro el proveedor");
            }else{
                $sql = $this->db->connect()->prepare("INSERT INTO cm_entidad SET ctipdoc=:tdoc,cnumdoc=:nrodoc,crazonsoc=:razon,
                                                                                cviadireccion=:direccion,ncodpais=:pais,ctelefono=:fono,
                                                                                nagenret=:retencion,cemail=:correo,nflgactivo=:estado,
                                                                                ctipper=:persona");
                $sql->execute(["tdoc"=>$datos["codigo_documento"],
                                "nrodoc"=>$datos["nrodoc"],
                                "razon"=>$datos["razon"],
                                "direccion"=>$datos["direccion"],
                                "pais"=>$datos["codigo_pais"],
                                "fono"=>$datos["telefono"],
                                "retencion"=>$datos["agente"],
                                "correo"=>$datos["correo"],
                                "estado"=>1,
                                "persona"=>$datos["codigo_tipo"]]);
                
                $rowCount = $sql->rowcount();

                if ($rowCount > 0) {
                    
                   
                     $id = $this->obtenerId($datos['nrodoc']);

                     if ($id !== 0){
                         $this->grabarBancos($id,$bancos);
                         $this->grabarContactos($id,$contactos);

                         $salida = array("respuesta"=>true,
                                "clase"=>"mensaje_correcto",
                                "mensaje"=>"Grabado correctamente");
                     }
                }
            }
            
            return $salida;
        }

        private function grabarBancos($codigo,$bancos){
            $datos = json_decode($bancos);
                
            for ($i=0; $i < count($datos); $i++) {  
                try {
                    $sql = $this->db->connect()->prepare("INSERT INTO cm_entidadbco 
                                                          SET   id_centi=:codigo,
                                                                ncodbco=:codigobanco,
                                                                cnrocta=:cuenta,
                                                                cmoneda=:moneda,
                                                                nflgactivo=:activo");
                    $sql->execute(["codigo"      => $codigo,
                                    "codigobanco"=> $datos[$i]->nombre,
                                    "cuenta"     => $datos[$i]->numero,
                                    "moneda"     => $datos[$i]->cuenta,
                                    "activo"     => $datos[$i]->activo
                                    ]);
                } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }        
            }
        }

        private function grabarContactos($codigo,$contactos){
            $datos = json_decode($contactos);
                
            for ($i=0; $i < count($datos); $i++) { 
                try {
                    $sql = $this->db->connect()->prepare("INSERT INTO cm_entidadcon 
                                                          SET   id_centi=:codigo,
                                                                cnombres=:nombre,
                                                                cemail=:correo,
                                                                ctelefono1=:telefono,
                                                                nflgactivo=:activo");
                    $sql->execute(["codigo"    => $codigo,
                                    "nombre"   => $datos[$i]->nombre,
                                    "correo"   => $datos[$i]->correo,
                                    "telefono" => $datos[$i]->telefono,
                                    "activo"   => $datos[$i]->activo
                                    ]);
                } catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }        
            }
        }

        private function obtenerId($ruc){
            try {
                $sql = $this->db->connect()->prepare("SELECT id_centi FROM cm_entidad WHERE cnumdoc =:ruc");
                $sql->execute(["ruc"=>$ruc]);
                $result = $sql->fetchAll();

                return $result[0]['id_centi'];
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        private function existeProveedor($ruc){
            try {
                $sql = $this->db->connect()->prepare("SELECT cnumdoc FROM cm_entidad WHERE cnumdoc =:ruc");
                $sql->execute(["ruc"=>$ruc]);
                $rowcount = $sql->rowcount();

                if ($rowcount > 0) {
                    return true;
                }else {
                    return false;
                }
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
    }
?>