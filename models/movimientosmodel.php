<?php
    class MovimientosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarStocks($parametros){
            try {
                $success = false;
                $datos = [];
                $cc = $parametros['costosSearch'];
                $cp = $parametros['codigoBusqueda'] == "" ? "%" : "%".$parametros['codigoBusqueda']."%";
                $de = $parametros['descripcionSearch'] == "" ? "%" : "%".$parametros['descripcionSearch']."%";

                $conn = $this->db->connect();

                $conn->prepare("SET @idCostoObra = :id")->execute([':id' => $cc]);

            
                $sql = $conn->prepare("SELECT *
                                                        FROM (
                                                            SELECT
                                                                p.id_cprod,
                                                                p.ccodprod,
                                                                UPPER(p.cdesprod)                         AS cdesprod,
                                                                COALESCE(r.cantidad_obra, 0)              AS ingresos,
                                                                r.idreg,
                                                                inv.condicion,
                                                                COALESCE(inv.inventarios_cantidad, 0)     AS inventarios,
                                                                COALESCE(cons.consumos, 0)                AS consumos,
                                                                COALESCE(cons.devoluciones, 0)            AS devoluciones,
                                                                COALESCE(st.salida_transferencia, 0)      AS salidas_transferencia,
                                                                st.iditem,
                                                                COALESCE(it.ingreso_transferencia, 0)     AS ingresos_transferencias,
                                                                min_.cantidad_minima                      AS minimo,
                                                                um.cabrevia,
                                                                COALESCE(aj.ajustes_cantidad, 0)          AS ajustes,
                                                                UPPER(g.cdescrip)                         AS grupo,
                                                                UPPER(c.cdescrip)                         AS clase,
                                                                UPPER(f.cdescrip)                         AS familia,
                                                                (
                                                                    COALESCE(r.cantidad_obra, 0)
                                                                + COALESCE(inv.inventarios_cantidad, 0)
                                                                + COALESCE(it.ingreso_transferencia, 0)
                                                                + COALESCE(cons.devoluciones, 0)
                                                                + COALESCE(aj.ajustes_cantidad, 0)
                                                                ) - (
                                                                    COALESCE(cons.consumos, 0)
                                                                + COALESCE(st.salida_transferencia, 0)
                                                                ) AS saldo
                                                            FROM cm_producto p
                                                            LEFT JOIN tb_unimed  um ON um.ncodmed    = p.nund
                                                            LEFT JOIN tb_grupo   g  ON g.ncodgrupo   = p.ngrupo
                                                            LEFT JOIN tb_clase   c  ON c.ncodclase   = p.nclase
                                                            LEFT JOIN tb_familia f  ON f.ncodfamilia = p.nfam

                                                            /* 1. Recepción — CON GROUP BY ✅ */
                                                            LEFT JOIN (
                                                                SELECT e.codprod,
                                                                    SUM(e.cant_ingr) AS cantidad_obra,
                                                                    MAX(e.idreg)     AS idreg
                                                                FROM alm_existencia e
                                                                INNER JOIN alm_cabexist cb ON cb.idreg = e.idregistro
                                                                WHERE e.nflgActivo = 1
                                                                AND cb.idcostos  = @idCostoObra
                                                                GROUP BY e.codprod
                                                            ) r ON r.codprod = p.id_cprod

                                                            /* 2. Inventario */
                                                            LEFT JOIN (
                                                                SELECT d.codprod,
                                                                    SUM(d.cant_ingr) AS inventarios_cantidad,
                                                                    MAX(d.condicion) AS condicion
                                                                FROM alm_inventariodet d
                                                                INNER JOIN alm_inventariocab cb ON cb.idreg = d.idregistro
                                                                WHERE d.nflgActivo = 1
                                                                AND cb.idcostos  = @idCostoObra
                                                                GROUP BY d.codprod
                                                            ) inv ON inv.codprod = p.id_cprod

                                                            /* 3. Consumos */
                                                            LEFT JOIN (
                                                                SELECT idprod,
                                                                    SUM(cantsalida)     AS consumos,
                                                                    SUM(cantdevolucion) AS devoluciones
                                                                FROM alm_consumo
                                                                WHERE ncostos   = @idCostoObra
                                                                AND flgactivo = 1
                                                                GROUP BY idprod
                                                            ) cons ON cons.idprod = p.id_cprod

                                                            /* 4. Transferencias SALIENTES */
                                                            LEFT JOIN (
                                                                SELECT d.idcprod,
                                                                    SUM(d.ncanti) AS salida_transferencia,
                                                                    MAX(d.iditem) AS iditem
                                                                FROM alm_transferdet d
                                                                INNER JOIN alm_transfercab cb ON cb.idreg = d.idtransfer
                                                                WHERE d.nflgactivo = 1
                                                                AND cb.idcc       = @idCostoObra
                                                                GROUP BY d.idcprod
                                                            ) st ON st.idcprod = p.id_cprod

                                                            /* 5. Transferencias ENTRANTES */
                                                            LEFT JOIN (
                                                                SELECT d.idcprod, SUM(d.ncanti) AS ingreso_transferencia
                                                                FROM alm_transferdet d
                                                                INNER JOIN alm_transfercab cb ON cb.idreg = d.idtransfer
                                                                WHERE d.nflgactivo = 1
                                                                AND cb.idcd       = @idCostoObra
                                                                GROUP BY d.idcprod
                                                            ) it ON it.idcprod = p.id_cprod

                                                            /* 6. Mínimo */
                                                            LEFT JOIN (
                                                                SELECT idprod, MAX(ncantidad) AS cantidad_minima
                                                                FROM alm_minimo
                                                                WHERE ncostos = @idCostoObra
                                                                GROUP BY idprod
                                                            ) min_ ON min_.idprod = p.id_cprod

                                                            /* 7. Ajustes */
                                                            LEFT JOIN (
                                                                SELECT d.codprod, SUM(d.cant_ingr) AS ajustes_cantidad
                                                                FROM alm_ajustedet d
                                                                INNER JOIN alm_ajustecab cb ON cb.idreg = d.idregistro
                                                                WHERE d.nflgActivo = 1
                                                                AND cb.idcostos  = @idCostoObra
                                                                AND cb.idrecepciona IS NOT NULL
                                                                GROUP BY d.codprod
                                                            ) aj ON aj.codprod = p.id_cprod

                                                            WHERE p.flgActivo = 1
                                                            AND p.ntipo     = 37
                                                            AND p.ccodprod LIKE :codigo
                                                            AND p.cdesprod  LIKE :descripcion
                                                        ) AS t
                                                        WHERE t.saldo > 0
                                                        ORDER BY t.cdesprod ASC;");
                $sql->execute([ "codigo" =>$cp,
                                "descripcion" =>$de]);
                $rowCount = $sql->rowCount();
                
                $datos = $sql->fetchAll(PDO::FETCH_ASSOC);
                
                if ($rowCount > 0) {
                    $success = true;
                }

                return array("datos"=>$datos,"success"=>$success);

            } catch (PDOException $th) {
                return array("error: "=>$th->getMessage(),"success"=>false);
            }
        }

        public function transferir($data){
            try {
                // =====================================================
                // 1. VALIDAR DATOS DE ENTRADA
                // =====================================================
                $fecha       = $data['fecha']       ?? null;
                $autorizado  = $data['autorizado']  ?? null;
                $responsable = $data['responsable'] ?? null;
                $ccOrigen    = $data['ccOrigen']    ?? null;
                $ccDestino   = $data['ccDestino']   ?? null;
                $items       = $data['items']       ?? [];

                if (!$fecha || !$autorizado || !$responsable || !$ccOrigen || !$ccDestino) {
                    return ['success' => false, 'error' => 'Faltan datos obligatorios'];
                }

                if (empty($items)) {
                    return ['success' => false, 'error' => 'No hay ítems para traspasar'];
                }

                if ($ccOrigen == $ccDestino) {
                    return ['success' => false, 'error' => 'CC Origen y Destino no pueden ser iguales'];
                }

                // =====================================================
                // 2. INICIAR TRANSACCIÓN
                // =====================================================
                $conn = $this->db->connect();
                $conn->beginTransaction();
                    

                try {
                    // =================================================
                    // 3. INSERTAR CABECERA (alm_transfercab)
                    // =================================================
                    $sqlCab = $conn->prepare("
                        INSERT INTO alm_traspasocab
                            (dfecha, cautoriza, cresponsable, idcc, idcd, nflgactivo)
                        VALUES 
                            (:fecha, :autoriza, :responsable, :idcc, :idcd, 1)
                    ");
                    $sqlCab->execute([
                        ':fecha'       => $fecha,
                        ':autoriza'    => $autorizado,
                        ':responsable' => $responsable,
                        ':idcc'        => $ccOrigen,
                        ':idcd'        => $ccDestino
                    ]);

                    $idTransfer = $conn->lastInsertId();

                    // =================================================
                    // 4. INSERTAR DETALLE (alm_traspasodet)
                    // =================================================
                    $sqlDet = $conn->prepare("
                        INSERT INTO alm_traspasodet 
                            (idtraspaso, idcprod, ncanti, cobservacion, idcc, idcd, nflgactivo)
                        VALUES 
                            (:idtraspaso, :idcprod, :ncanti, :cobservacion, :idcc, :idcd, 1)
                    ");

                    foreach ($items as $item) {

                        $idprod      = $item['idprod']       ?? null;
                        $cantidad    = (float) ($item['cantidad']    ?? 0);
                        $observacion = $item['observacion']  ?? null;

                        if (!$idprod || $cantidad <= 0) {
                            continue;   // saltar ítems inválidos
                        }

                        $sqlDet->execute([
                            ':idtraspaso'   => $idTransfer,     // 👈 FK a la cabecera
                            ':idcprod'      => $idprod,
                            ':ncanti'       => $cantidad,
                            ':cobservacion' => $observacion,
                            ':idcc'         => $ccOrigen,        // 👈 CC origen (duplicado)
                            ':idcd'         => $ccDestino        // 👈 CC destino (duplicado)
                        ]);
                    }


                    // =================================================
                    // 5. CONFIRMAR TRANSACCIÓN
                    // =================================================
                    $conn->commit();

                    return [
                        'success'      => true,
                        'idtransfer'   => $idTransfer,
                        'total_items'  => count($items),
                        'mensaje'      => 'Traspaso registrado correctamente'
                    ];
                } catch (Exception $e) {
                    $conn->rollBack();
                    throw $e;
                }
                
            } catch (Exception $e) {
                return ['success' => false, 'error' => $e->getMessage()];
            }
        }
    }
?>