<?php
    class MinimosModel extends Model{
        function __construct()
        {
            parent::__construct();
        }

        function listarMinimos($parametros){
            $docData = [];
            $respuesta = false;

            $costos  = $parametros['costos'] == '-1' ? '%' : $parametros['costos'];
            $codigo  = $parametros['codigo'] == '' ? '%' : '%' . $parametros['codigo'] . '%';
            $descrip = $parametros['descripcion'] == '' ? '%' : '%' . $parametros['descripcion'] . '%';

            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                        e.idprod,
                                                        e.codprod,
                                                        c.idcostos,
                                                        MAX(p.ccodprod) AS ccodprod,
                                                        UPPER(MAX(p.cdesprod)) AS cdesprod,
                                                        MAX(p.ngrupo) AS ngrupo,
                                                        MAX(p.nclase) AS nclase,
                                                        MAX(p.nfam) AS nfam,
                                                        FORMAT(SUM(e.cant_ingr), 2) AS ingresos,
                                                        MAX(u.cabrevia) AS cabrevia,
                                                        FORMAT(
                                                            COALESCE((
                                                                SELECT SUM(cc.cantsalida)
                                                                FROM alm_consumo cc
                                                                WHERE cc.ncostos = c.idcostos
                                                                    AND cc.idprod = e.codprod
                                                                    AND cc.flgactivo = 1
                                                            ), 0),
                                                            2
                                                        ) AS consumos,
                                                        DATE_FORMAT(MAX(m2.ffecha), '%d/%m/%Y') AS ffecha,
                                                        FORMAT(MAX(m2.ntotal), 2) AS ntotal
                                                    FROM
                                                        alm_existencia e
                                                        LEFT JOIN alm_cabexist c ON e.idregistro = c.idreg
                                                        LEFT JOIN cm_producto p ON e.codprod = p.id_cprod
                                                        LEFT JOIN tb_unimed u ON p.nund = u.ncodmed
                                                        LEFT JOIN (SELECT m.idprod, m.ncostos, m.ffecha, m.ntotal FROM alm_minimo m ORDER BY m.ffecha DESC) m2
                                                            ON m2.idprod = e.codprod AND m2.ncostos = c.idcostos
                                                    WHERE
                                                        e.nflgActivo = 1
                                                        AND p.flgActivo = 1
                                                        AND p.ngrupo = 17
                                                        AND c.idcostos = :costos
                                                        AND p.cdesprod LIKE :descripcion
                                                        AND p.ccodprod LIKE :codigo
                                                    GROUP BY
                                                        e.idprod,
                                                        e.codprod,
                                                        c.idcostos");

                $sql->execute(["costos" => $costos, "codigo" => $codigo, "descripcion" => $descrip]);

                while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                    $docData[] = $row;
                }

                return array($docData);
            } catch (PDOException $th) {
                echo $th->getMessage();
                return false;
            }
        }

        function listarMinimosPaginado($parametros, $page = 1, $limit = 5){
            $offset = ($page - 1) * $limit;
            
            $costos = $parametros['costos'];
            $codigo = $parametros['codigo'] == '' ? '%' : '%' . $parametros['codigo'] . '%';
            $descrip = $parametros['descripcion'] == '' ? '%' : '%' . $parametros['descripcion'] . '%';

            try {
                $db = $this->db->connect();
                
                // Contar total
                $sqlCount = "SELECT COUNT(DISTINCT e.idreg) as total
                    FROM alm_existencia e
                    LEFT JOIN alm_cabexist c ON e.idregistro = c.idreg
                    LEFT JOIN cm_producto p ON e.codprod = p.id_cprod
                    WHERE e.nflgActivo = 1
                        AND p.flgActivo = 1
                        AND p.ngrupo = 17
                        AND c.idcostos = :costos
                        AND p.cdesprod LIKE :descripcion
                        AND p.ccodprod LIKE :codigo";
                
                $stmt = $db->prepare($sqlCount);
                $stmt->execute(["costos" => $costos, "codigo" => $codigo, "descripcion" => $descrip]);
                $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                $totalPaginas = ceil($total / $limit);

                // Consulta con LIMIT
                $sql = "SELECT
                    e.idprod,
                    e.codprod,
                    c.idcostos,
                    MAX(p.ccodprod) AS ccodprod,
                    UPPER(MAX(p.cdesprod)) AS cdesprod,
                    MAX(p.ngrupo) AS ngrupo,
                    MAX(p.nclase) AS nclase,
                    MAX(p.nfam) AS nfam,
                    FORMAT(SUM(e.cant_ingr), 2) AS ingresos,
                    MAX(u.cabrevia) AS cabrevia,
                    FORMAT(
                        COALESCE((
                            SELECT SUM(cc.cantsalida)
                            FROM alm_consumo cc
                            WHERE cc.ncostos = c.idcostos
                                AND cc.idprod = e.codprod
                                AND cc.flgactivo = 1
                        ), 0),
                        2
                    ) AS consumos,
                    DATE_FORMAT(MAX(m2.ffecha), '%d/%m/%Y') AS ffecha,
                    FORMAT(MAX(m2.ntotal), 2) AS ntotal
                FROM alm_existencia e
                LEFT JOIN alm_cabexist c ON e.idregistro = c.idreg
                LEFT JOIN cm_producto p ON e.codprod = p.id_cprod
                LEFT JOIN tb_unimed u ON p.nund = u.ncodmed
                LEFT JOIN (
                    SELECT m.idprod, m.ncostos, m.ffecha, m.ntotal 
                    FROM alm_minimo m
                    ORDER BY m.ffecha DESC
                ) m2 ON m2.idprod = e.codprod AND m2.ncostos = c.idcostos
                WHERE e.nflgActivo = 1
                    AND p.flgActivo = 1
                    AND p.ngrupo = 17
                    AND c.idcostos = :costos
                    AND p.cdesprod LIKE :descripcion
                    AND p.ccodprod LIKE :codigo
                GROUP BY e.idprod, e.codprod, c.idcostos
                ORDER BY p.cdesprod ASC
                LIMIT :offset, :limit";
                
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':costos', $costos);
                $stmt->bindParam(':codigo', $codigo);
                $stmt->bindParam(':descripcion', $descrip);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                
                $data = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $data[] = $row;
                }

                return [
                    'success' => true,
                    'data' => $data,
                    'total' => intval($total),
                    'pagina' => $page,
                    'total_paginas' => $totalPaginas,
                    'costos' =>$costos
                ];
                
            } catch (PDOException $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        function regristrarMinimo($parametros){
            try {
                $sql = $this->db->connect()->prepare("INSERT INTO alm_minimo
                        SET iduser = :usuario,
                            idprod = :producto,
                            ncostos = :costos,
                            nporcentaje = :porcentaje,
                            npersonal = :personal,
                            ntotal = :total,
                            cobserva = :observa,
                            ffecha = :fecha");

                $sql->execute([
                    "usuario" => $parametros['registra'],
                    "producto" => $parametros['idprod'],
                    "costos" => $parametros['costos'],
                    "porcentaje" => $parametros['porcentaje'],
                    "total" => $parametros['total'],
                    "personal" => $parametros['personal'],
                    "observa" => $parametros['observaciones'],
                    "fecha" => $parametros['fecha']
                ]);

                if ($sql->rowCount() > 0) {
                    return array("error" => 1, "mensaje" => "Correctamente registrado");
                } else {
                    return array("error" => 0, "mensaje" => "Error en el registro");
                }
            } catch (PDOException $th) {
                return array("error" => $th->getMessage());
            }
        }

        public function verificarPermiso($parametros){
                try{
                    $docData = [];

                    $sql = $this->db->connect()->prepare("SELECT 
                                                            m.agrega,
                                                            m.modifica,
                                                            m.elimina
                                                        FROM tb_usermod m
                                                        WHERE m.ncodmod =:modulo
                                                        AND m.iduser =:usuario
                                                        AND m.flgactivo = 1");

                    $sql->execute(["modulo"=>$parametros['modulo'],"usuario"=>$parametros['user']]);

                    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                        $docData[] = $row;
                    }

                    return array("datos"=>$docData);

                }catch (PDOException $th) {
                    echo "Error: ".$th->getMessage();
                    return false;
                }
        }

        public function listarMinimosExportar($costos, $codigo, $descripcion){
            $costos = $costos == '-1' ? '%' : $costos;
            $codigo = empty($codigo) ? '%' : '%' . $codigo . '%';
            $descrip = empty($descripcion) ? '%' : '%' . $descripcion . '%';

            try {
                $db = $this->db->connect();
                
                // Consulta SIN LIMIT para obtener TODOS los datos
                $sql = "SELECT
                    e.idprod,
                    e.codprod,
                    c.idcostos,
                    MAX(p.ccodprod) AS ccodprod,
                    UPPER(MAX(p.cdesprod)) AS cdesprod,
                    MAX(p.ngrupo) AS ngrupo,
                    MAX(p.nclase) AS nclase,
                    MAX(p.nfam) AS nfam,
                    FORMAT(SUM(e.cant_ingr), 2) AS ingresos,
                    MAX(u.cabrevia) AS cabrevia,
                    FORMAT(
                        COALESCE((
                            SELECT SUM(cc.cantsalida)
                            FROM alm_consumo cc
                            WHERE cc.ncostos = c.idcostos
                                AND cc.idprod = e.codprod
                                AND cc.flgactivo = 1
                        ), 0),
                        2
                    ) AS consumos,
                    DATE_FORMAT(MAX(m2.ffecha), '%d/%m/%Y') AS ffecha,
                    FORMAT(MAX(m2.ntotal), 2) AS ntotal
                FROM alm_existencia e
                LEFT JOIN alm_cabexist c ON e.idregistro = c.idreg
                LEFT JOIN cm_producto p ON e.codprod = p.id_cprod
                LEFT JOIN tb_unimed u ON p.nund = u.ncodmed
                LEFT JOIN (
                    SELECT m.idprod, m.ncostos, m.ffecha, m.ntotal 
                    FROM alm_minimo m
                    ORDER BY m.ffecha DESC
                ) m2 ON m2.idprod = e.codprod AND m2.ncostos = c.idcostos
                WHERE e.nflgActivo = 1
                    AND p.flgActivo = 1
                    AND p.ngrupo = 17
                    AND c.idcostos = :costos
                    AND p.cdesprod LIKE :descripcion
                    AND p.ccodprod LIKE :codigo
                GROUP BY e.idprod, e.codprod, c.idcostos
                ORDER BY p.cdesprod ASC";
                
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':costos', $costos);
                $stmt->bindParam(':codigo', $codigo);
                $stmt->bindParam(':descripcion', $descrip);
                $stmt->execute();
                
                $data = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // Calcular estado
                    $ingresos = floatval(str_replace(',', '', $row['ingresos']));
                    $consumos = floatval(str_replace(',', '', $row['consumos']));
                    $saldo = $ingresos - $consumos;
                    $stockMinimo = floatval(str_replace(',', '', $row['ntotal'] ?? 0));
                    
                    if ($saldo <= 0) {
                        $estado = "CRÍTICO";
                    } else if ($stockMinimo > 0 && $saldo <= $stockMinimo) {
                        $estado = "MÍNIMO";
                    } else if ($saldo > 0 && $saldo <= 50) {
                        $estado = "BAJO";
                    } else if ($saldo > 200) {
                        $estado = "EXCESO";
                    } else {
                        $estado = "NORMAL";
                    }
                    
                    $row['estado'] = $estado;
                    $data[] = $row;
                }

                return [
                    'success' => true,
                    'data' => $data,
                    'total' => count($data)
                ];
                
            } catch (PDOException $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
    }
?>