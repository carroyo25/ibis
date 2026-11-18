<?php
    class BienesModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarItemsPaginado($criterio, $page, $porPagina) {
            try {
                $offset = ($page - 1) * $porPagina;
                $palabra = $criterio == "" ? "%" : "%" . $criterio . "%";

                // 1. Contar el total de registros para el paginador
                $sqlCount = $this->db->connect()->prepare("SELECT COUNT(cm_producto.id_cprod) as total 
                                                        FROM cm_producto 
                                                        WHERE cm_producto.cdesprod LIKE :criterio 
                                                        AND cm_producto.flgVisible = 1");
                $sqlCount->execute(["criterio" => $palabra]);
                $total = $sqlCount->fetch(PDO::FETCH_ASSOC)['total'];

                // 2. Traer los datos de la página actual
                $sql = $this->db->connect()->prepare("SELECT
                                                        cm_producto.id_cprod,
                                                        cm_producto.ccodprod,
                                                        UPPER(cm_producto.cdesprod) AS cdesprod,
                                                        cm_producto.flgActivo,
                                                        tb_parametros.cdescripcion AS tipo,
                                                        tb_unimed.cabrevia 
                                                    FROM cm_producto
                                                    INNER JOIN tb_unimed ON cm_producto.nund = tb_unimed.ncodmed
                                                    INNER JOIN tb_parametros ON cm_producto.ntipo = tb_parametros.nidreg 
                                                    WHERE cm_producto.cdesprod LIKE :criterio
                                                    AND cm_producto.flgVisible = 1
                                                    ORDER BY id_cprod ASC
                                                    LIMIT :limit OFFSET :offset");
                
                $sql->bindValue(':criterio', $palabra, PDO::PARAM_STR);
                $sql->bindValue(':limit', $porPagina, PDO::PARAM_INT);
                $sql->bindValue(':offset', $offset, PDO::PARAM_INT);
                $sql->execute();
                
                $items = $sql->fetchAll(PDO::FETCH_ASSOC);

                // Devolvemos el JSON estructurado
                return array(
                    "total" => $total,
                    "items" => $items
                );

            } catch (PDOException $th) {
                echo "Error: " . $th->getMessage();
                return false;
            }
        }

        public function obtenerJerarquiaCompleta() {
            try {
                // 1. Traer Tipos
                $stmt = $this->db->connect()->query("SELECT nidreg AS id, cdescripcion AS nombre, cdescripcion AS codigo 
                                                        FROM tb_parametros 
                                                        WHERE cclase = '07'
                                                        AND cdescripcion IN ('BIENES', 'SERVICIOS')");
                $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // 2. Traer Unidades
                $stmt = $this->db->connect()->query("SELECT ncodmed AS id, cdesmed AS nombre, ccodmed AS codigo FROM tb_unimed");
                $unidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // 3. Traer Grupos, Clases y Familias
                $stmt = $this->db->connect()->query("SELECT ncodgrupo AS id, ccodcata AS codigo, cdescrip AS nombre FROM tb_grupo WHERE nflgactivo = 1");
                $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($grupos as &$grupo) {
                    $stmtC = $this->db->connect()->prepare("SELECT ncodclase AS id, ccodcata AS codigo, cdescrip AS nombre FROM tb_clase WHERE ncodgrupo = :id");
                    $stmtC->execute(['id' => $grupo['id']]);
                    $grupo['clases'] = $stmtC->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($grupo['clases'] as &$clase) {
                        $stmtF = $this->db->connect()->prepare("SELECT ncodfamilia AS id, ccodcata AS codigo, cdescrip AS nombre FROM tb_familia WHERE ncodclase = :id");
                        $stmtF->execute(['id' => $clase['id']]);
                        $clase['familias'] = $stmtF->fetchAll(PDO::FETCH_ASSOC);
                    }
                }

                return array(
                    "tipos" => $tipos,
                    "unidades" => $unidades,
                    "grupos" => $grupos
                );

            } catch (PDOException $th) {
                return array("error" => $th->getMessage());
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
    }
?>