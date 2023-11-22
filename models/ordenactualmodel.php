<?php
    class OrdenActualModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarOrdenActualScroll($pagina,$cantidad){
            try {
                $inicio = ($pagina - 1) * $cantidad;
                $limite = $this->contarItems();

                $sql = $this->db->connect()->prepare("SELECT
                                                        tb_costusu.ncodcos,
                                                        tb_costusu.ncodproy,
                                                        tb_costusu.id_cuser,
                                                        lg_ordencab.id_regmov,
                                                        LPAD(lg_ordencab.cnumero,6,0) AS cnumero,
                                                        DATE_FORMAT(lg_ordencab.ffechadoc,'%d/%m/%Y') AS emision,
                                                        lg_ordencab.nNivAten,
                                                        lg_ordencab.nEstadoDoc,
                                                        lg_ordencab.ncodpago,
                                                        lg_ordencab.nplazo,
                                                        lg_ordencab.cdocPDF,
                                                        FORMAT(lg_ordencab.ntotal,2) AS ntotal,
                                                        lg_ordencab.ncodmon,
                                                        UPPER( lg_ordencab.cObservacion ) AS concepto,
                                                        UPPER( tb_pedidocab.detalle ) AS detalle,
                                                        UPPER(
                                                        CONCAT_WS( tb_area.ccodarea, tb_area.cdesarea )) AS area,
                                                        UPPER(
                                                        CONCAT_WS( tb_proyectos.ccodproy, tb_proyectos.cdesproy )) AS costos,
                                                        tb_proyectos.ccodproy,
                                                        lg_ordencab.nfirmaLog,
                                                        lg_ordencab.nfirmaFin,
                                                        lg_ordencab.nfirmaOpe,
                                                        tb_parametros.cdescripcion AS atencion,
                                                        UPPER(cm_entidad.crazonsoc) AS proveedor,
                                                        IF(ISNULL(lg_ordencab.nfirmaLog),0,1) AS logistica,
                                                        IF(ISNULL(lg_ordencab.nfirmaFin),0,1) AS finanzas,
                                                        IF(ISNULL(lg_ordencab.nfirmaOpe),0,1) AS operaciones,
                                                        IF(lg_ordencab.nEstadoDoc = 59,'resaltado_firma','-') AS resaltado,
                                                        estados.cdescripcion AS estado 
                                                    FROM
                                                        tb_costusu
                                                        INNER JOIN lg_ordencab ON tb_costusu.ncodproy = lg_ordencab.ncodpry
                                                        INNER JOIN tb_pedidocab ON lg_ordencab.id_refpedi = tb_pedidocab.idreg
                                                        INNER JOIN tb_area ON lg_ordencab.ncodarea = tb_area.ncodarea
                                                        INNER JOIN tb_proyectos ON lg_ordencab.ncodpry = tb_proyectos.nidreg
                                                        INNER JOIN tb_parametros ON lg_ordencab.nNivAten = tb_parametros.nidreg
                                                        INNER JOIN cm_entidad ON lg_ordencab.id_centi = cm_entidad.id_centi
                                                        INNER JOIN tb_parametros AS estados ON lg_ordencab.nEstadoDoc = estados.nidreg 
                                                    WHERE
                                                        tb_costusu.id_cuser = :user 
                                                        AND tb_costusu.nflgactivo = 1
                                                        AND (lg_ordencab.nEstadoDoc = 60 OR lg_ordencab.nEstadoDoc = 62)
                                                    ORDER BY lg_ordencab.id_regmov DESC
                                                    LIMIT $inicio,$cantidad");
                
                $sql->execute(["user"=>$_SESSION['iduser']]);

                $rc = $sql->rowcount();
                $item = 1;

                if ($rc > 0){
                    while( $rs = $sql->fetch()) {
                        $datos[] = $rs;
                    }
                }

                return array("filas"=>$datos,
                            'quedan'=>($inicio + $cantidad) < $limite);

            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }

        private function contarItems(){
            try {
                $sql = $this->db->connect()->query("SELECT COUNT(id_regmov) AS regs FROM lg_ordencab WHERE nflgactivo = 1");
                $sql->execute();
                $filas = $sql->fetch();

                return $filas['regs'];
            } catch (PDOException $th) {
                echo "Error: ".$th->getMessage();
                return false;
            }
        }
    }
?>