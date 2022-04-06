<?php
    class BienesModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarItems(){
            try {
                $salida = "";
                $sql = $this->db->connect()->query("SELECT
                                                    cm_producto.id_cprod,
                                                    cm_producto.ccodprod,
                                                    cm_producto.cdesprod,
                                                    cm_producto.flgActivo,
                                                    tb_parametros.cdescripcion AS tipo,
                                                    tb_unimed.cabrevia 
                                                FROM
                                                    cm_producto
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_parametros ON cm_producto.ntipo = tb_parametros.nidreg 
                                                WHERE
                                                    cm_producto.flgActivo = 1");
                $sql->execute();
                $rc = $sql->rowcount();
                $item = 1;

                if ($rc > 0){
                    while( $rs = $sql->fetch()) {
                        $salida .='<tr data-id="'.$rs['id_cprod'].'" class="pointer">
                                        <td class="textoCentro">'.$rs['ccodprod'].'</td>
                                        <td class="textoCentro '.strtolower($rs['tipo']).'">'.$rs['tipo'].'</td>
                                        <td class="pl20px">'.$rs['cdesprod'].'</td>
                                        <td class="textoCentro">'.$rs['cabrevia'].'</td>
                                        <td class="textoCentro"><a href="'.$rs['id_cprod'].'"><i class="fas fa-trash-alt"></i></a></td>
                                    </tr>';
                        $item++;
                    }
                }

                return $salida;

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        public function consultarId($id){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                    cm_producto.id_cprod,
                                                    cm_producto.ccodprod,
                                                    cm_producto.cdesprod,
                                                    cm_producto.ntipo,
                                                    cm_producto.flgActivo,
                                                    cm_producto.rfoto,
                                                    cm_producto.cnparte,
                                                    cm_producto.flgSerie, 
	                                                cm_producto.flgDetrac,
                                                    UPPER(CONCAT( tb_grupo.ccodcata, ' - ', tb_grupo.cdescrip )) AS grupo,
                                                    UPPER(CONCAT( tb_clase.ccodcata, ' - ', tb_clase.cdescrip )) AS clase,
                                                    UPPER(CONCAT( tb_familia.ccodcata, ' - ', tb_familia.cdescrip )) AS familia,
                                                    cm_producto.ngrupo,
                                                    cm_producto.nclase,
                                                    cm_producto.nfam,
                                                    cm_producto.nund,
                                                    tb_parametros.cdescripcion AS tipo,
                                                    UPPER(
                                                    CONCAT( tb_unimed.ccodmed, ' - ', tb_unimed.cdesmed )) AS unidad 
                                                FROM
                                                    cm_producto
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_grupo ON cm_producto.ngrupo = tb_grupo.ncodgrupo
                                                    INNER JOIN tb_clase ON cm_producto.nclase = tb_clase.ncodclase
                                                    INNER JOIN tb_familia ON cm_producto.nfam = tb_familia.ncodfamilia
                                                    INNER JOIN tb_parametros ON cm_producto.ntipo = tb_parametros.nidreg 
                                                WHERE
                                                    cm_producto.id_cprod = :id");
                 $sql->execute(["id"=>$id]);
                 $rowCount = $sql->rowCount();
                 
                 if ($rowCount > 0) {
                     $docData = array();
                     while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                         $docData[] = $row;
                     } 
                 }

                 return array("item"=>$docData);
            } catch (PDOException $th) {
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            } 
        }

        public function mostrarGrupos($id){
            $salida = "";
            try {
                $sql = $this->db->connect()->prepare("SELECT ncodgrupo,ccodcata,cdescrip 
                                                    FROM tb_grupo 
                                                    WHERE nflgactivo=1
                                                    AND  ntipclase =:id
                                                    ORDER BY cdescrip ASC");
                $sql->execute(["id"=>$id]);
                $rowCount = $sql->rowCount();

                if ($rowCount > 0) {
                    while ( $rs = $sql->fetch()){
                        $salida .='<li><a href="'.$rs['ncodgrupo'].'" data-catalogo="'.$rs['ccodcata'] .'">'.$rs['ccodcata'] .' - '.strtoupper($rs['cdescrip']).'</a></li>';
                    }
                }

                return $salida;
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        public function generarCodigo($codigo){
            try {
                $sql = $this->db->connect()->prepare("SELECT COUNT( cm_producto.ccodprod ) AS generado 
                                                    FROM cm_producto 
                                                    WHERE cm_producto.ccodprod LIKE :codigo");
                $sql->execute(["codigo"=>$codigo."%"]);
                $result = $sql->fetchAll();

                return $codigo.str_pad(($result[0]['generado']+1),4,0,STR_PAD_LEFT);

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }
        
        public function insertar($datos){
            $salida = false;
            $respuesta = false;
            $mensaje = "Error en el registro";
            $clase = "mensaje_error";

            if(!$this->verificar($datos['codigo'])){
                try {

                    $serie = array_key_exists('serie', $datos)? 1 : 0;
                    $detraccion = array_key_exists("detraccion",$datos)? 1 : 0;

                    $sql = $this->db->connect()->prepare("INSERT INTO cm_producto 
                                                            SET ccodprod=:cod,cdesprod=:descripcion,ntipo=:tipo,ngrupo=:grupo,
                                                                nclase=:clase,nfam=:familia,nund=:unidad,cnparte=:parte,flgSerie=:serie,
                                                                flgDetrac=:detrac,flgActivo=:activo,iduser=:user,rfoto=:tfoto");
                    $sql->execute(["cod"=>$datos['codigo'],
                                    "descripcion"=>$datos['descripcion'],
                                    "tipo"=>$datos['codigo_tipo'],
                                    "grupo"=>$datos['codigo_grupo'],
                                    "clase"=>$datos['codigo_clase'],
                                    "familia"=>$datos['codigo_familia'],
                                    "unidad"=>$datos['codigo_unidad'],
                                    "parte"=>$datos['nro_parte'],
                                    "serie"=>$serie,
                                    "detrac"=>$detraccion,
                                    "activo"=>1,
                                    "user"=>$_SESSION['iduser'],
                                    "tfoto"=>$datos['tipofoto']]);
                    $rowCount = $sql->rowCount();

                    if ($rowCount > 0){
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
                                "mensaje"=>"Codigo duplicadp",
                                "clase"=>"mensaje_error");
            }

            return $salida;
        }

        public function Modificar($datos){
            $salida = false;
            $respuesta = false;
            $mensaje = "Error en el registro";
            $clase = "mensaje_error";

            try {

                $serie = array_key_exists('serie', $datos)? 1 : 0;
                $detraccion = array_key_exists("detraccion",$datos)? 1 : 0;

                $sql = $this->db->connect()->prepare("UPDATE cm_producto 
                                                        SET cdesprod=:descripcion,nund=:unidad,cnparte=:parte,
                                                            flgSerie=:serie,flgDetrac=:detrac,iduser=:user,rfoto=:tfoto
                                                        WHERE id_cprod=:id");
                $sql->execute([ "descripcion"=>$datos['descripcion'],
                                "unidad"=>$datos['codigo_unidad'],
                                "parte"=>$datos['nro_parte'],
                                "serie"=>$serie,
                                "detrac"=>$detraccion,
                                "user"=>$_SESSION['iduser'],
                                "tfoto"=>$datos['tipofoto'],
                                "id"=>$datos['codigo_item']]);
                                
                $rowCount = $sql->rowCount();

                if ($rowCount > 0){
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

            return $salida;
        }

        private function verificar($codigo){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        COUNT( cm_producto.ccodprod ) AS numero 
                                                    FROM
                                                        cm_producto 
                                                    WHERE
                                                        cm_producto.ccodprod =:codigo");
                $sql->execute(["codigo"=>$codigo]);
                $result = $sql->fetchAll();
    
                return $result[0]['numero'];

            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }  
        }

        public function subirFoto($archivo,$codigo){
            $temporal	= $archivo['tmp_name'];
            $type       = $archivo['type'];

            
            if ($temporal !== ""){
                switch ($type) {
                    case 'image/jpeg':
                        $original = imagecreatefromjpeg($temporal);
                        $ext = '.jpg';
                        break;
                    case 'image/png':
                        $original 	= imagecreatefrompng($temporal);
                        $ext = '.png';
                        break;
                    case 'image/gif':
                        $original 	= imagecreatefromgif($temporal);
                        $ext = '.gif';
                        break;
                    default:
                        die("formato inválido");
                }

                $ancho_original	= imagesx($original);
                $alto_original	= imagesy($original);
    
                //crear el lienzo vacio 520*400s
                $ancho_nuevo 	= 520;
                $alto_nuevo		= 400; //round($ancho_nuevo * $alto_original / $ancho_original);
                 
                $copia = imagecreatetruecolor($ancho_nuevo,$alto_nuevo);
    
                //copiar original -> copia
                imagecopyresampled($copia, $original, 0, 0, 0, 0, $ancho_nuevo, 400, $ancho_original, $alto_original);
    
                //exportar guardar imagen
                imagejpeg($copia,"public/fotos/catalogo/".$codigo.$ext,50);
                 
                //elimina los datos temporales
                imagedestroy($original);
                imagedestroy($copia);
            }
        }

        public function eliminaItem($id){
            try {
                $sql = $this->db->connect()->prepare("UPDATE cm_producto SET flgActivo = 0 WHERE id_cprod=:id");
                $sql->execute([$id]);

                return $this->listarItems();
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            } 
        }
    }
?>