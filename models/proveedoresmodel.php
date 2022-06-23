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
                                                        WHERE cm_entidad.nflgactivo = 7");
                $query->execute();

                $rowcount = $query->rowcount();

                if ($rowcount > 0 ){
                    $contador = 1;
                    while ($row = $query->fetch()) {


                        $salida .= '<tr class="pointer" data-id="'.$row['id_centi'].'">
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

            $agente = array_key_exists('agente', $datos)? 1 : 0;
           
            if ( $this->existeProveedor($datos['nrodoc'])){
                $salida = array("respuesta"=>false,
                                "clase"=>"mensaje_error",
                                "mensaje"=>"Ya se registro el proveedor");
            }else{
                $sql = $this->db->connect()->prepare("INSERT INTO cm_entidad SET ctipdoc=:tdoc,cnumdoc=:nrodoc,crazonsoc=:razon,
                                                                                cviadireccion=:direccion,ncodpais=:pais,ctelefono=:fono,
                                                                                nagenret=:retencion,cemail=:correo,nflgactivo=:estado,
                                                                                ctipper=:persona,nrubro=:rubro");
                $sql->execute(["tdoc"=>$datos["codigo_documento"],
                                "nrodoc"=>$datos["nrodoc"],
                                "razon"=>$datos["razon"],
                                "direccion"=>$datos["direccion"],
                                "pais"=>$datos["codigo_pais"],
                                "fono"=>$datos["telefono"],
                                "retencion"=>$datos["agente"],
                                "correo"=>$datos["correo"],
                                "estado"=>$datos['codigo_estado'],
                                "persona"=>$datos["codigo_tipo"],
                                "rubro" => $datos["codigo_rubro"]]);
                
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

        public function modificar($datos,$bancos,$contactos){
            $salida = false;

            $sql = $this->db->connect()->prepare("UPDATE cm_entidad SET ctipdoc=:tdoc,cnumdoc=:nrodoc,crazonsoc=:razon,
                                                                                cviadireccion=:direccion,ncodpais=:pais,ctelefono=:fono,
                                                                                nagenret=:retencion,cemail=:correo,nflgactivo=:estado,
                                                                                ctipper=:persona
                                                                        WHERE id_centi=:id");
                $sql->execute(["tdoc"=>$datos["codigo_documento"],
                                "nrodoc"=>$datos["nrodoc"],
                                "razon"=>$datos["razon"],
                                "direccion"=>$datos["direccion"],
                                "pais"=>$datos["codigo_pais"],
                                "fono"=>$datos["telefono"],
                                "retencion"=>$datos["agente"],
                                "correo"=>$datos["correo"],
                                "estado"=>$datos['codigo_estado'],
                                "persona"=>$datos["codigo_tipo"],
                                "id"=>$datos['codigo_entidad']]);

            $this->grabarBancos($datos["codigo_entidad"],$bancos);
            $this->grabarContactos($datos["codigo_entidad"],$contactos);

            $salida = array("respuesta"=>true,
                            "clase"=>"mensaje_correcto",
                            "mensaje"=>"Modifcado correctamente");
            
            return $salida;
        }

        public function consultarDatos($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        cm_entidad.id_centi,
                                                        cm_entidad.ctipdoc,
                                                        cm_entidad.ctipper,
                                                        cm_entidad.cnumdoc,
                                                        cm_entidad.crazonsoc,
                                                        cm_entidad.cviadireccion,
                                                        cm_entidad.ctelefono,
                                                        cm_entidad.nagenret,
                                                        cm_entidad.cemail,
                                                        cm_entidad.nflgactivo,
                                                        documentos.cdescripcion AS documento,
                                                        tipo_personas.cdescripcion AS tipo_persona,
                                                        tb_pais.cdespais,
                                                    IF
                                                        ( cm_entidad.nflgactivo = 7, 'ACTIVO', 'INACTIVO' ) AS estado,
                                                        cm_entidad.ncodpais,
                                                        cm_entidad.nrubro,
                                                        rubros.cdescripcion AS rubro 
                                                    FROM
                                                        cm_entidad
                                                        INNER JOIN tb_parametros AS documentos ON cm_entidad.ctipdoc = documentos.nidreg
                                                        INNER JOIN tb_parametros AS tipo_personas ON cm_entidad.ctipper = tipo_personas.nidreg
                                                        INNER JOIN tb_pais ON cm_entidad.ncodpais = tb_pais.ncodpais
                                                        INNER JOIN tb_parametros AS rubros ON cm_entidad.nrubro = rubros.nidreg 
                                                    WHERE
                                                        cm_entidad.id_centi =:id");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();
                
                if ($rowCount > 0) {
                    $docData = array();
                    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    } 
                }

                return array("proveedor"=>$docData,
                            "contactos"=>$this->consultarContactos($id),
                            "bancos"=>$this->consultarBancos($id));
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function borrarProveedor($id){
            try {
                $sql=$this->db->connect()->prepare("UPDATE cm_entidad SET nflgactivo = 0 WHERE id_centi=:id");
                $sql->execute(["id"=>$id]);
                $rc = $sql->rowcount();

                if ($rc > 0) {
                    $salida = $this->listarProveedores();
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
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
                                                                ndefault=:activo");
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
                                                                ndefault=:activo");
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

        private function consultarContactos($id){
            try {
                $salida = "";
                $contador = 1;
                $sql = $this->db->connect()->prepare("SELECT
                                                    cm_entidadcon.cnombres, 
                                                    cm_entidadcon.cemail, 
                                                    cm_entidadcon.ctelefono1, 
                                                    cm_entidadcon.nflgactivo, 
                                                    cm_entidadcon.id_centi,
                                                    cm_entidadcon.nidcontact
                                                FROM
                                                    cm_entidadcon
                                                WHERE
                                                    cm_entidadcon.id_centi = :id
                                                AND cm_entidadcon.nflgactivo = 7");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $sw = $rs['nflgactivo'] == 1 ? "checked":"";
                        $salida .= '<tr data-grabado="1">
                                        <td class="textoCentro"><a href="'.$rs['nidcontact'].'"><i class="far fa-trash-alt"></i></a></td>
                                        <td class="textoCentro">'.str_pad($contador++,2,0,STR_PAD_LEFT).'</td>
                                        <td class="pl20px">'.strtoupper($rs['cnombres']).'</td>
                                        <td class="textoCentro">'.$rs['ctelefono1'].'</td>
                                        <td class="pl20px">'.$rs['cemail'].'</td>
                                        <td class="textoCentro"><input type="checkbox" '.$sw.'></td>
                                    </tr>';
                    }

                    return $salida;
                }

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        private function consultarBancos($id){
            try {
                $salida = "";
                $contador = 1;
                $sql = $this->db->connect()->prepare("SELECT
                                                        cm_entidadbco.ncodbco,
                                                        cm_entidadbco.cnrocta,
                                                        cm_entidadbco.cmoneda,
                                                        cm_entidadbco.nflgactivo,
                                                        cm_entidadbco.id_centi,
                                                        bancos.cdescripcion AS banco,
                                                        monedas.cdescripcion AS moneda,
                                                        monedas.cabrevia 
                                                    FROM
                                                        cm_entidadbco
                                                        INNER JOIN tb_parametros AS bancos ON cm_entidadbco.ncodbco = bancos.nidreg
                                                        INNER JOIN tb_parametros AS monedas ON cm_entidadbco.cmoneda = monedas.nidreg 
                                                    WHERE
                                                        cm_entidadbco.id_centi = :id 
                                                        AND cm_entidadbco.nflgactivo = 7");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
                    while ($rs = $sql->fetch()){
                        $sw = $rs['nflgactivo'] == 1 ? "checked":"";
                        $salida .= '<tr data-grabado="1">
                                    <td class="textoCentro"><a href="'.$rs['ncodbco'].'"><i class="far fa-trash-alt"></i></a></td>
                                    <td class="textoCentro">'.str_pad($contador++,2,0,STR_PAD_LEFT).'</td>
                                    <td class="pl20px">'.strtoupper($rs['banco']).'</td>
                                    <td class="pl20px">'.strtoupper($rs['moneda']).'</td>
                                    <td class="textoCentro">'.strtoupper($rs['cnrocta']).'</td>
                                    <td class="textoCentro"><input type="checkbox" '.$sw.'></td>
                                </tr>';
                    }

                    return $salida;
                }
            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        
    }
?>