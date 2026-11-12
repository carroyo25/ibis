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

        /*function listarMinimosPaginado($parametros, $page = 1, $limit = 5){
            $page  = max(1, (int)$page);
            $limit = max(1, (int)$limit);
            $offset = ($page - 1) * $limit;

            $cc = (int)$parametros['costos'];
            $cp = ($parametros['codigo']      === '') ? '%' : '%' . $parametros['codigo']      . '%';
            $de = ($parametros['descripcion'] === '') ? '%' : '%' . $parametros['descripcion'] . '%';

            try {
                $db = $this->db->connect();
                $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

                // Variable de sesión para el costo
                $db->prepare("SET @idCostoObra = :id")->execute([':id' => $cc]);

                // 1. CTE con TODOS los movimientos
                $cteMovimientos = "
                    WITH Movimientos AS (
                        SELECT e.codprod, cb.idcostos, cb.ffechadoc AS fecha_reg, SUM(e.cant_ingr) AS cantidad
                        FROM alm_existencia e
                        INNER JOIN alm_cabexist cb ON cb.idreg = e.idregistro
                        WHERE e.nflgActivo = 1 AND cb.idcostos = @idCostoObra
                        GROUP BY e.codprod, cb.idcostos, cb.ffechadoc
                        
                        UNION ALL
                        
                        SELECT d.codprod, cb.idcostos, cb.ffechaInv AS fecha_reg, SUM(d.cant_ingr) AS cantidad
                        FROM alm_inventariodet d
                        INNER JOIN alm_inventariocab cb ON cb.idreg = d.idregistro
                        WHERE d.nflgActivo = 1 AND cb.idcostos = @idCostoObra
                        GROUP BY d.codprod, cb.idcostos, cb.ffechaInv
                        
                        UNION ALL
                        
                        SELECT idprod, ncostos, fechasalida AS fecha_reg, SUM(cantsalida - cantdevolucion) AS cantidad
                        FROM alm_consumo
                        WHERE ncostos = @idCostoObra AND flgactivo = 1
                        GROUP BY idprod, ncostos, fechasalida
                        
                        UNION ALL
                        
                        SELECT d.codprod, cb.idcostos, cb.ffechadoc AS fecha_reg, SUM(d.cant_ingr) AS cantidad
                        FROM alm_ajustedet d
                        INNER JOIN alm_ajustecab cb ON cb.idreg = d.idregistro
                        WHERE d.nflgActivo = 1 AND cb.idcostos = @idCostoObra AND cb.idrecepciona IS NOT NULL
                        GROUP BY d.codprod, cb.idcostos, cb.ffechadoc
                        
                        UNION ALL
                        
                        SELECT d.idcprod, d.idcd, c.dfecha AS fecha_reg, SUM(d.ncanti) AS cantidad
                        FROM alm_traspasodet d
                        INNER JOIN alm_traspasocab c ON c.idreg = d.idtraspaso
                        WHERE d.nflgactivo = 1 AND c.nflgactivo = 1 AND d.idcd = @idCostoObra
                        GROUP BY d.idcprod, d.idcd, c.dfecha
                        
                        UNION ALL
                        
                        SELECT d.idcprod, d.idcc, c.dfecha AS fecha_reg, -SUM(d.ncanti) AS cantidad
                        FROM alm_traspasodet d
                        INNER JOIN alm_traspasocab c ON c.idreg = d.idtraspaso
                        WHERE d.nflgactivo = 1 AND c.nflgactivo = 1 AND d.idcc = @idCostoObra
                        GROUP BY d.idcprod, d.idcc, c.dfecha
                        
                        UNION ALL
                        
                        SELECT d.idcprod, cb.idcc, cb.ftraslado AS fecha_reg, -SUM(d.ncanti) AS cantidad
                        FROM alm_transferdet d
                        INNER JOIN alm_transfercab cb ON cb.idreg = d.idtransfer
                        WHERE d.nflgactivo = 1 AND cb.idcc = @idCostoObra
                        GROUP BY d.idcprod, cb.idcc, cb.ftraslado
                    ),
                    UltimoMinimo AS (
                        SELECT 
                            m.idprod, 
                            m.ncostos, 
                            m.ffecha, 
                            m.ntotal,
                            ROW_NUMBER() OVER (PARTITION BY m.idprod, m.ncostos ORDER BY m.ffecha DESC) as rn
                        FROM alm_minimo m
                        WHERE m.swactivo = 1 AND m.ncostos = @idCostoObra
                    )
                ";

                // ---------- COUNT ----------
                $sqlCount = "$cteMovimientos 
                            SELECT COUNT(*) AS total 
                            FROM UltimoMinimo m
                            INNER JOIN cm_producto p ON p.id_cprod = m.idprod
                            WHERE m.rn = 1
                            AND p.flgActivo = 1
                            AND p.ntipo = 37
                            AND p.ccodprod LIKE :codigo
                            AND p.cdesprod LIKE :descripcion";
                
                $stmt = $db->prepare($sqlCount);
                $stmt->execute([':codigo' => $cp, ':descripcion' => $de]);
                $total = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
                $totalPaginas = (int)ceil($total / $limit);

                // ---------- SELECT paginado ----------
                // Estructura exacta: SELECT -> FROM -> LEFT JOIN -> WHERE -> GROUP BY -> ORDER -> LIMIT
                $sql = "$cteMovimientos
                        SELECT
                            p.id_cprod,
                            p.ccodprod,
                            UPPER(p.cdesprod) AS cdesprod,
                            um.cabrevia AS unidad,
                            DATE_FORMAT(m.ffecha, '%d/%m/%Y') AS ffecha,
                            m.ntotal AS nminimo,
                            m.ncostos AS idcostos,
                            UPPER(py.ccodproy) AS proyecto,
                            COALESCE(SUM(mov.cantidad), 0) AS saldo,
                            CASE
                                WHEN m.ntotal > 0 AND COALESCE(SUM(mov.cantidad), 0) <= 0 THEN 'CRÍTICO'
                                WHEN m.ntotal > 0 AND COALESCE(SUM(mov.cantidad), 0) < m.ntotal THEN 'CRÍTICO'
                                WHEN m.ntotal > 0 AND COALESCE(SUM(mov.cantidad), 0) = m.ntotal THEN 'MÍNIMO'
                                WHEN m.ntotal = 0 AND COALESCE(SUM(mov.cantidad), 0) <= 0 THEN 'CRÍTICO'
                                WHEN m.ntotal = 0 AND COALESCE(SUM(mov.cantidad), 0) > 0 THEN 'SIN MÍNIMO'
                                WHEN COALESCE(SUM(mov.cantidad), 0) > (m.ntotal * 3) THEN 'EXCESO'
                                ELSE 'NORMAL'
                            END AS estado_stock
                        FROM UltimoMinimo m
                        INNER JOIN cm_producto p ON p.id_cprod = m.idprod
                        LEFT JOIN tb_unimed um ON um.ncodmed = p.nund
                        INNER JOIN tb_proyectos py ON m.ncostos = py.nidreg
                        LEFT JOIN Movimientos mov ON mov.codprod = m.idprod 
                                                AND mov.idcostos = m.ncostos 
                                                AND mov.fecha_reg <= m.ffecha
                        WHERE m.rn = 1
                        AND p.flgActivo = 1
                        AND p.ntipo = 37
                        AND p.ccodprod LIKE :codigo
                        AND p.cdesprod LIKE :descripcion
                        GROUP BY m.idprod, m.ncostos, m.ffecha, m.ntotal, p.ccodprod, p.cdesprod, um.cabrevia, py.ccodproy
                        ORDER BY m.ffecha DESC, p.cdesprod ASC
                        LIMIT :offset, :limit";

                $stmt2 = $db->prepare($sql);
                $stmt2->bindValue(':codigo',      $cp, PDO::PARAM_STR);
                $stmt2->bindValue(':descripcion', $de, PDO::PARAM_STR);
                $stmt2->bindValue(':offset',      $offset, PDO::PARAM_INT);
                $stmt2->bindValue(':limit',       $limit,  PDO::PARAM_INT);
                $stmt2->execute();

                $data = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                return [
                    'success'        => true,
                    'data'           => $data,
                    'total'          => $total,
                    'pagina'         => $page,
                    'total_paginas'  => $totalPaginas,
                    'costos'         => $cc
                ];

            } catch (PDOException $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }*/

        function listarMinimosPaginado($parametros, $page = 1, $limit = 5){
            $page  = max(1, (int)$page);
            $limit = max(1, (int)$limit);
            $offset = ($page - 1) * $limit;

            $cc = (int)$parametros['costos'];
            $cp = ($parametros['codigo']      === '') ? '%' : '%' . $parametros['codigo']      . '%';
            $de = ($parametros['descripcion'] === '') ? '%' : '%' . $parametros['descripcion'] . '%';

            try {
                $db = $this->db->connect();
                $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

                // Variable de sesión para el costo
                $db->prepare("SET @idCostoObra = :id")->execute([':id' => $cc]);

                // 1. CTE con TODOS los movimientos
                $cteMovimientos = "
                    WITH Movimientos AS (
                        SELECT e.codprod, cb.idcostos, cb.ffechadoc AS fecha_reg, SUM(e.cant_ingr) AS cantidad
                        FROM alm_existencia e
                        INNER JOIN alm_cabexist cb ON cb.idreg = e.idregistro
                        WHERE e.nflgActivo = 1 AND cb.idcostos = @idCostoObra
                        GROUP BY e.codprod, cb.idcostos, cb.ffechadoc
                        
                        UNION ALL
                        SELECT d.codprod, cb.idcostos, cb.ffechaInv AS fecha_reg, SUM(d.cant_ingr) AS cantidad
                        FROM alm_inventariodet d
                        INNER JOIN alm_inventariocab cb ON cb.idreg = d.idregistro
                        WHERE d.nflgActivo = 1 AND cb.idcostos = @idCostoObra
                        GROUP BY d.codprod, cb.idcostos, cb.ffechaInv
                        
                        UNION ALL
                        SELECT idprod, ncostos, fechasalida AS fecha_reg, SUM(cantsalida - cantdevolucion) AS cantidad
                        FROM alm_consumo
                        WHERE ncostos = @idCostoObra AND flgactivo = 1
                        GROUP BY idprod, ncostos, fechasalida
                        
                        UNION ALL
                        SELECT d.codprod, cb.idcostos, cb.ffechadoc AS fecha_reg, SUM(d.cant_ingr) AS cantidad
                        FROM alm_ajustedet d
                        INNER JOIN alm_ajustecab cb ON cb.idreg = d.idregistro
                        WHERE d.nflgActivo = 1 AND cb.idcostos = @idCostoObra AND cb.idrecepciona IS NOT NULL
                        GROUP BY d.codprod, cb.idcostos, cb.ffechadoc
                        
                        UNION ALL
                        SELECT d.idcprod, d.idcd, c.dfecha AS fecha_reg, SUM(d.ncanti) AS cantidad
                        FROM alm_traspasodet d
                        INNER JOIN alm_traspasocab c ON c.idreg = d.idtraspaso
                        WHERE d.nflgactivo = 1 AND c.nflgactivo = 1 AND d.idcd = @idCostoObra
                        GROUP BY d.idcprod, d.idcd, c.dfecha
                        
                        UNION ALL
                        SELECT d.idcprod, d.idcc, c.dfecha AS fecha_reg, -SUM(d.ncanti) AS cantidad
                        FROM alm_traspasodet d
                        INNER JOIN alm_traspasocab c ON c.idreg = d.idtraspaso
                        WHERE d.nflgactivo = 1 AND c.nflgactivo = 1 AND d.idcc = @idCostoObra
                        GROUP BY d.idcprod, d.idcc, c.dfecha
                        
                        UNION ALL
                        SELECT d.idcprod, cb.idcc, cb.ftraslado AS fecha_reg, -SUM(d.ncanti) AS cantidad
                        FROM alm_transferdet d
                        INNER JOIN alm_transfercab cb ON cb.idreg = d.idtransfer
                        WHERE d.nflgactivo = 1 AND cb.idcc = @idCostoObra
                        GROUP BY d.idcprod, cb.idcc, cb.ftraslado
                    ),
                    UltimoMinimo AS (
                        /* Ya NO filtramos por rn = 1 para traer todo el historial */
                        SELECT 
                            m.idprod, 
                            m.ncostos, 
                            m.ffecha, 
                            m.ntotal,
                            ROW_NUMBER() OVER (PARTITION BY m.idprod, m.ncostos ORDER BY m.ffecha DESC) as rn
                        FROM alm_minimo m
                        WHERE m.swactivo = 1 AND m.ncostos = @idCostoObra
                    )
                ";

                // ---------- COUNT ----------
                $sqlCount = "$cteMovimientos 
                            SELECT COUNT(*) AS total 
                            FROM UltimoMinimo m
                            INNER JOIN cm_producto p ON p.id_cprod = m.idprod
                            WHERE p.flgActivo = 1
                            AND p.ntipo = 37
                            AND p.ccodprod LIKE :codigo
                            AND p.cdesprod LIKE :descripcion";
                
                $stmt = $db->prepare($sqlCount);
                $stmt->execute([':codigo' => $cp, ':descripcion' => $de]);
                $total = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
                $totalPaginas = (int)ceil($total / $limit);

                // ---------- SELECT paginado ----------
                $sql = "$cteMovimientos
                        SELECT
                            p.id_cprod,
                            p.ccodprod,
                            UPPER(p.cdesprod) AS cdesprod,
                            um.cabrevia AS unidad,
                            DATE_FORMAT(m.ffecha, '%d/%m/%Y') AS ffecha,
                            m.ntotal AS nminimo,
                            m.ncostos AS idcostos,
                            UPPER(py.ccodproy) AS proyecto,
                            COALESCE(SUM(mov.cantidad), 0) AS saldo,
                            CASE
                                WHEN m.ntotal > 0 AND COALESCE(SUM(mov.cantidad), 0) <= 0 THEN 'CRÍTICO'
                                WHEN m.ntotal > 0 AND COALESCE(SUM(mov.cantidad), 0) < m.ntotal THEN 'CRÍTICO'
                                WHEN m.ntotal > 0 AND COALESCE(SUM(mov.cantidad), 0) = m.ntotal THEN 'MÍNIMO'
                                WHEN m.ntotal = 0 AND COALESCE(SUM(mov.cantidad), 0) <= 0 THEN 'CRÍTICO'
                                WHEN m.ntotal = 0 AND COALESCE(SUM(mov.cantidad), 0) > 0 THEN 'SIN MÍNIMO'
                                WHEN COALESCE(SUM(mov.cantidad), 0) > (m.ntotal * 3) THEN 'EXCESO'
                                ELSE 'NORMAL'
                            END AS estado_stock
                        FROM UltimoMinimo m
                        INNER JOIN cm_producto p ON p.id_cprod = m.idprod
                        LEFT JOIN tb_unimed um ON um.ncodmed = p.nund
                        INNER JOIN tb_proyectos py ON m.ncostos = py.nidreg
                        LEFT JOIN Movimientos mov ON mov.codprod = m.idprod 
                                                AND mov.idcostos = m.ncostos 
                                                AND mov.fecha_reg <= m.ffecha
                        WHERE p.flgActivo = 1
                        AND p.ntipo = 37
                        AND p.ccodprod LIKE :codigo
                        AND p.cdesprod LIKE :descripcion
                        GROUP BY m.idprod, m.ncostos, m.ffecha, m.ntotal, p.ccodprod, p.cdesprod, um.cabrevia, py.ccodproy
                        ORDER BY py.ccodproy ASC, p.cdesprod ASC, m.ffecha DESC
                        LIMIT :offset, :limit";

                $stmt2 = $db->prepare($sql);
                $stmt2->bindValue(':codigo',      $cp, PDO::PARAM_STR);
                $stmt2->bindValue(':descripcion', $de, PDO::PARAM_STR);
                $stmt2->bindValue(':offset',      $offset, PDO::PARAM_INT);
                $stmt2->bindValue(':limit',       $limit,  PDO::PARAM_INT);
                $stmt2->execute();

                $data = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                return [
                    'success'        => true,
                    'data'           => $data,
                    'total'          => $total,
                    'pagina'         => $page,
                    'total_paginas'  => $totalPaginas,
                    'costos'         => $cc
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

        public function listarMinimosExportar($costos, $codigo, $descripcion) {
            $costos = ($costos == '-1' || empty($costos)) ? '%' : (int)$costos;
            $codigo = empty($codigo) ? '%' : '%' . $codigo . '%';
            $descrip = empty($descripcion) ? '%' : '%' . $descripcion . '%';

            try {
                $db = $this->db->connect();
                $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

                // Si el costo es específico, seteamos la variable
                if ($costos !== '%') {
                    $db->prepare("SET @idCostoObra = :id")->execute([':id' => $costos]);
                } else {
                    // Si exportan todos, seteamos un comodín que luego manejamos en el WHERE
                    $db->prepare("SET @idCostoObra = 0")->execute();
                }

                // Para la exportación no ponemos LIMIT, pero sí filtramos por el costo si viene
                $filtroCosto = ($costos === '%') ? "" : "AND m.ncostos = @idCostoObra";

                $cteMovimientos = "
                    WITH Movimientos AS (
                        SELECT e.codprod, cb.idcostos, cb.ffechadoc AS fecha_reg, SUM(e.cant_ingr) AS cantidad
                        FROM alm_existencia e
                        INNER JOIN alm_cabexist cb ON cb.idreg = e.idregistro
                        WHERE e.nflgActivo = 1 " . ($costos === '%' ? "" : "AND cb.idcostos = @idCostoObra") . "
                        GROUP BY e.codprod, cb.idcostos, cb.ffechadoc
                        
                        UNION ALL
                        SELECT d.codprod, cb.idcostos, cb.ffechaInv AS fecha_reg, SUM(d.cant_ingr) AS cantidad
                        FROM alm_inventariodet d
                        INNER JOIN alm_inventariocab cb ON cb.idreg = d.idregistro
                        WHERE d.nflgActivo = 1 " . ($costos === '%' ? "" : "AND cb.idcostos = @idCostoObra") . "
                        GROUP BY d.codprod, cb.idcostos, cb.ffechaInv
                        
                        UNION ALL
                        SELECT idprod, ncostos, fechasalida AS fecha_reg, SUM(cantsalida - cantdevolucion) AS cantidad
                        FROM alm_consumo
                        WHERE flgactivo = 1 " . ($costos === '%' ? "" : "AND ncostos = @idCostoObra") . "
                        GROUP BY idprod, ncostos, fechasalida
                        
                        UNION ALL
                        SELECT d.codprod, cb.idcostos, cb.ffechadoc AS fecha_reg, SUM(d.cant_ingr) AS cantidad
                        FROM alm_ajustedet d
                        INNER JOIN alm_ajustecab cb ON cb.idreg = d.idregistro
                        WHERE d.nflgActivo = 1 AND cb.idrecepciona IS NOT NULL " . ($costos === '%' ? "" : "AND cb.idcostos = @idCostoObra") . "
                        GROUP BY d.codprod, cb.idcostos, cb.ffechadoc
                        
                        UNION ALL
                        SELECT d.idcprod, d.idcd, c.dfecha AS fecha_reg, SUM(d.ncanti) AS cantidad
                        FROM alm_traspasodet d
                        INNER JOIN alm_traspasocab c ON c.idreg = d.idtraspaso
                        WHERE d.nflgactivo = 1 AND c.nflgactivo = 1 " . ($costos === '%' ? "" : "AND d.idcd = @idCostoObra") . "
                        GROUP BY d.idcprod, d.idcd, c.dfecha
                        
                        UNION ALL
                        SELECT d.idcprod, d.idcc, c.dfecha AS fecha_reg, -SUM(d.ncanti) AS cantidad
                        FROM alm_traspasodet d
                        INNER JOIN alm_traspasocab c ON c.idreg = d.idtraspaso
                        WHERE d.nflgactivo = 1 AND c.nflgactivo = 1 " . ($costos === '%' ? "" : "AND d.idcc = @idCostoObra") . "
                        GROUP BY d.idcprod, d.idcc, c.dfecha
                        
                        UNION ALL
                        SELECT d.idcprod, cb.idcc, cb.ftraslado AS fecha_reg, -SUM(d.ncanti) AS cantidad
                        FROM alm_transferdet d
                        INNER JOIN alm_transfercab cb ON cb.idreg = d.idtransfer
                        WHERE d.nflgactivo = 1 " . ($costos === '%' ? "" : "AND cb.idcc = @idCostoObra") . "
                        GROUP BY d.idcprod, cb.idcc, cb.ftraslado
                    ),
                    UltimoMinimo AS (
                        SELECT 
                            m.idprod, 
                            m.ncostos, 
                            m.ffecha, 
                            m.ntotal,
                            ROW_NUMBER() OVER (PARTITION BY m.idprod, m.ncostos ORDER BY m.ffecha DESC) as rn
                        FROM alm_minimo m
                        WHERE m.swactivo = 1 $filtroCosto
                    )
                ";

                $sql = "$cteMovimientos
                        SELECT
                            p.id_cprod,
                            p.ccodprod,
                            UPPER(p.cdesprod) AS cdesprod,
                            um.cabrevia AS unidad,
                            DATE_FORMAT(m.ffecha, '%d/%m/%Y') AS ffecha,
                            m.ntotal AS nminimo,
                            m.ncostos AS idcostos,
                            UPPER(py.ccodproy) AS proyecto,
                            COALESCE(SUM(mov.cantidad), 0) AS saldo,
                            
                            CASE
                                WHEN m.ntotal > 0 AND COALESCE(SUM(mov.cantidad), 0) <= 0 THEN 'CRÍTICO'
                                WHEN m.ntotal > 0 AND COALESCE(SUM(mov.cantidad), 0) < m.ntotal THEN 'CRÍTICO'
                                WHEN m.ntotal > 0 AND COALESCE(SUM(mov.cantidad), 0) = m.ntotal THEN 'MÍNIMO'
                                WHEN m.ntotal = 0 AND COALESCE(SUM(mov.cantidad), 0) <= 0 THEN 'CRÍTICO'
                                WHEN m.ntotal = 0 AND COALESCE(SUM(mov.cantidad), 0) > 0 THEN 'SIN MÍNIMO'
                                WHEN COALESCE(SUM(mov.cantidad), 0) > (m.ntotal * 3) THEN 'EXCESO'
                                ELSE 'NORMAL'
                            END AS estado_stock
                        FROM UltimoMinimo m
                        INNER JOIN cm_producto p ON p.id_cprod = m.idprod
                        LEFT JOIN tb_unimed um ON um.ncodmed = p.nund
                        INNER JOIN tb_proyectos py ON m.ncostos = py.nidreg
                        LEFT JOIN Movimientos mov ON mov.codprod = m.idprod 
                                                AND mov.idcostos = m.ncostos 
                                                AND mov.fecha_reg <= m.ffecha
                        WHERE m.rn = 1
                        AND p.flgActivo = 1
                        AND p.ntipo = 37
                        AND p.ccodprod LIKE :codigo
                        AND p.cdesprod LIKE :descripcion
                        GROUP BY m.idprod, m.ncostos, m.ffecha, m.ntotal, p.ccodprod, p.cdesprod, um.cabrevia, py.ccodproy
                        ORDER BY py.ccodproy ASC, p.cdesprod ASC
                        ";

                $stmt = $db->prepare($sql);
                $stmt->execute([
                    ":codigo"      => $codigo,
                    ":descripcion" => $descrip
                ]);

                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

        public function buscarProducto($parametros){
            try {
                $codigo = $parametros['codigo'];
                $docData = [];

                $sql = $this->db->connect()->prepare("SELECT c.id_cprod,
                                                            c.ccodprod,
                                                            c.cdesprod,
                                                            u.cabrevia
                                                        FROM cm_producto c
                                                        LEFT JOIN tb_unimed u ON u.ncodmed = c.nund
                                                        WHERE C.flgVisible = 1
                                                        AND c.ccodprod = :codigo");
                $sql->execute(["codigo"=>$codigo]);

                while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                    $docData[] = $row;
                }

                return array("datos"=>$docData,'success' => true);
            } catch (PDOException $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
    }
?>