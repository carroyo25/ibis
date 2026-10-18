<?php
    class MovimientosModel extends Model{

        public function __construct()
        {
            parent::__construct();
        }

        public function listarStocks($parametros){
            try {
                $success = false;
                $datos   = [];

                $cc = $parametros['costosSearch'];
                $cp = $parametros['codigoBusqueda']    == "" ? "%" : "%" . $parametros['codigoBusqueda']    . "%";
                $de = $parametros['descripcionSearch'] == "" ? "%" : "%" . $parametros['descripcionSearch'] . "%";

                $conn = $this->db->connect();

                // Variable de sesión para el costo de obra
                $conn->prepare("SET @idCostoObra = :id")
                    ->execute([':id' => $cc]);

                $sql = $conn->prepare("
                    SELECT *
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
                            COALESCE(ts.total_traspaso_saliente, 0)   AS traspaso_saliente,
                            COALESCE(te.total_traspaso_entrante, 0)   AS traspaso_entrante,
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
                            + COALESCE(te.total_traspaso_entrante, 0)
                            ) - (
                                COALESCE(cons.consumos, 0)
                            + COALESCE(st.salida_transferencia, 0)
                            + COALESCE(ts.total_traspaso_saliente, 0)
                            ) AS saldo
                        FROM cm_producto p
                        LEFT JOIN tb_unimed  um ON um.ncodmed    = p.nund
                        LEFT JOIN tb_grupo   g  ON g.ncodgrupo   = p.ngrupo
                        LEFT JOIN tb_clase   c  ON c.ncodclase   = p.nclase
                        LEFT JOIN tb_familia f  ON f.ncodfamilia = p.nfam

                        /* 1. Recepción */
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

                        /* 3. Consumos y devoluciones */
                        LEFT JOIN (
                            SELECT idprod,
                                SUM(cantsalida)     AS consumos,
                                SUM(cantdevolucion) AS devoluciones
                            FROM alm_consumo
                            WHERE ncostos   = @idCostoObra
                            AND flgactivo = 1
                            GROUP BY idprod
                        ) cons ON cons.idprod = p.id_cprod

                        /* 4. Transferencias ANTIGUAS salientes */
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

                        /* 5. Transferencias ANTIGUAS entrantes */
                        LEFT JOIN (
                            SELECT d.idcprod, SUM(d.ncanti) AS ingreso_transferencia
                            FROM alm_transferdet d
                            INNER JOIN alm_transfercab cb ON cb.idreg = d.idtransfer
                            WHERE d.nflgactivo = 1
                            AND cb.idcd       = @idCostoObra
                            GROUP BY d.idcprod
                        ) it ON it.idcprod = p.id_cprod

                        /* 6. Stock mínimo */
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

                        /* 8. Traspasos NUEVOS salientes */
                        LEFT JOIN (
                            SELECT d.idcprod, SUM(d.ncanti) AS total_traspaso_saliente
                            FROM alm_traspasodet d
                            INNER JOIN alm_traspasocab c ON c.idreg = d.idtraspaso
                            WHERE d.nflgactivo = 1
                            AND c.nflgactivo = 1
                            AND d.idcc       = @idCostoObra
                            GROUP BY d.idcprod
                        ) ts ON ts.idcprod = p.id_cprod

                        /* 9. Traspasos NUEVOS entrantes */
                        LEFT JOIN (
                            SELECT d.idcprod, SUM(d.ncanti) AS total_traspaso_entrante
                            FROM alm_traspasodet d
                            INNER JOIN alm_traspasocab c ON c.idreg = d.idtraspaso
                            WHERE d.nflgactivo = 1
                            AND c.nflgactivo = 1
                            AND d.idcd       = @idCostoObra
                            GROUP BY d.idcprod
                        ) te ON te.idcprod = p.id_cprod

                        WHERE p.flgActivo = 1
                        AND p.ntipo     = 37
                        AND p.ccodprod  LIKE :codigo
                        AND p.cdesprod  LIKE :descripcion
                    ) AS t
                    WHERE t.saldo > 0
                    ORDER BY t.cdesprod ASC
                ");

                $sql->execute([
                    ":codigo"      => $cp,
                    ":descripcion" => $de
                ]);

                $datos = $sql->fetchAll(PDO::FETCH_ASSOC);

                if (count($datos) > 0) {
                    $success = true;
                }

                return ["datos" => $datos, "success" => $success];

            } catch (PDOException $th) {
                return ["error" => $th->getMessage(), "success" => false];
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

        public function buscarAlmacen($data){
            try {
                $sql = $this->db->connect()->prepare("SELECT
                                                    p.ccodproy,
                                                    p.nalmacen,
                                                    a.cdesalm nombre,
                                                    a.ctipovia direccion
                                                FROM
                                                    tb_proyectos p
                                                    LEFT JOIN tb_almacen a ON a.ncodalm = p.nalmacen
                                                WHERE
                                                    p.nflgactivo = 1
                                                    AND p.nidreg = :id");
                $sql->execute([":id"=>$data['id']]);

                $datos = $sql->fetchAll(PDO::FETCH_ASSOC);

                if (count($datos) > 0) {
                    $success = true;
                }

                return ["datos" => $datos[0], "success" => $success];
            } catch (Exception $e) {
                return ['success' => false, 'error' => $e->getMessage()];
            }     
        }

        public function buscarValores($data){
            try {
                $tipo = $data['tipo'] ?? '';

                switch ($tipo) {
                    case 'almacen':
                        $sql= $this->db->connect()->query("SELECT LPAD(ncodalm,4,0) AS codigo, UPPER(cdesalm) AS nombre, UPPER(ctipovia) AS direccion
                                                            FROM tb_almacen WHERE nflgactivo = 1 AND nflgSepcon = 1 ORDER BY cdesalm");
                        break;
                    case 'empresa':
                        $sql= $this->db->connect()->query("SELECT
                                                    LPAD(cm_entidad.id_centi,4,0) AS codigo, 
                                                    UPPER(cm_entidad.crazonsoc) AS nombre, 
                                                    cm_entidad.cnumdoc AS ruc,
                                                    UPPER(cm_entidad.cviadireccion) AS direccion,
                                                    cm_entidad.cdigcateg
                                                FROM
                                                    cm_entidad
                                                WHERE
                                                    cm_entidad.nflgactivo = 7
                                                    AND cm_entidad.cdigcateg IS NOT NULL
                                                ORDER BY 
                                                    cm_entidad.crazonsoc");
                        break;
                    case 'modalidad':
                        return ['success' => true, 'datos' => $this->parametros('14')]; 
                        break;
                    case 'tipoEnvio':
                        return ['success' => true, 'datos' => $this->parametros('08')];
                        break;
                    case 'persona':
                       $sql = $this->db->connect()->prepare("SELECT
                                            tb_user.iduser AS codigo,
                                            tb_user.cnombres AS nombre
                                        FROM
                                            tb_user 
                                        WHERE
                                            nrol =:rol 
                                            AND nflgactivo = 1 
                                            AND nestado = 7");
                        $sql->execute([':rol'=>4]);
                        break;
                    case 'conductor':
                        $sql = $this->db->connect()->query("SELECT LPAD(idreg,3,0) AS codigo ,UPPER(cnombres) AS nombre ,clicencia AS licencia,cnrodoc 
                                                        FROM cm_conductores
                                                        WHERE nflgactivo = 1
                                                        ORDER BY cnombres");
                        break;
                    case 'vehiculo':
                        return ['success' => true, 'datos' => $this->parametros('24')];
                        break;
                    case 'transporte':
                        return ['success' => true, 'datos' => $this->parametros('23')];
                        break;
                    default:
                        return ['success' => false, 'error' => "Tipo '$tipo' no válido"];

                }

                return ['success' => true, 'datos' => $sql->fetchAll(PDO::FETCH_ASSOC)];
            } catch (PDOException $e) {
                return ['success' => false, 'error' => $e->getMessage()];
            }
        }

        public function emitirGuia($data) {
            try {
                // 1. Validar
                if (empty($data['fechaEmision']) || empty($data['detalles'])) {
                    return ['success' => false, 'error' => 'Faltan datos obligatorios (fechas o detalles)'];
                }

                // 2. Obtener el número de guía y preparar datos
                $idGuia = $this->numeroGuia();
                $data['numero_guia_sunat'] = str_pad($idGuia, 8, '0', STR_PAD_LEFT);

                // 3. Guardar en la BD (llamada a la función secundaria)
                $guardado = $this->guardarGuiaBD($data, $idGuia);
                if (!$guardado) {
                    throw new Exception("No se pudo guardar el registro de la guía en la BD");
                }

                // 4. Generar el PDF (llamada a la función secundaria)
                $pdfResult = $this->generarGuiaPdf($data, $data['detalles'], $idGuia);
                if (!$pdfResult) {
                    throw new Exception("Error al generar el archivo PDF");
                }

                // 5. Retornar éxito
                return [
                    'success' => true,
                    'idGuia'  => $idGuia,
                    'mensaje' => 'Guía emitida y PDF generado correctamente',
                    'archivo' => $pdfResult['archivo']
                ];

            } catch (Exception $e) {
                return ['success' => false, 'error' => $e->getMessage()];
            }
        }

        //llamar valores de la tabla general al pasar un parametro
        private function parametros($clase) {
            try {
                $salida = "";
                $query = $this->db->connect()->prepare("SELECT LPAD(nidreg,3,0) AS codigo, cdescripcion AS nombre 
                                                        FROM tb_parametros
                                                        WHERE cclase=:clase
                                                            AND ccod != '00'
                                                            AND ( nactivo = 1 OR nactivo IS NULL)
                                                        ORDER BY cdescripcion");
                $query->execute(["clase" => $clase]);
                return $query->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                error_log("Error en parametros($clase): " . $e->getMessage());
                return [];
            }
        }

        private function generarGuiaPdf($cabecera, $detalles, $idGuia) {
            try {
                require_once("public/formatos/guiaremision.php");
                
                $archivo = "public/documentos/guias_remision/20504898173-09-T001-" . $cabecera['numero_guia_sunat'] . ".pdf";
                
                $fecha_emision  = date("d/m/Y", strtotime($cabecera['fechaEmision']));
                $fecha_traslado = !empty($cabecera['fechaTraslado']) ? date("d/m/Y", strtotime($cabecera['fechaTraslado'])) : "";
                $anio = explode('-', $cabecera['fechaEmision']);

                $pdf = new PDF(
                    $idGuia,
                    $fecha_emision,
                    $cabecera['rucDestinatario'],
                    $cabecera['razonSocialDest'],
                    $cabecera['direccionDest'],
                    $cabecera['empresaTransporte'],
                    $cabecera['rucEmpresa'],
                    $cabecera['direccionEmpresa'],
                    $cabecera['almacenOrigen'],
                    null, null, null,
                    $fecha_traslado,
                    $cabecera['tipoEnvio'],
                    $cabecera['direccionDestino'],
                    null, null, null, null,
                    $cabecera['placa'] ?? '',
                    $cabecera['conductor'] ?? '',
                    $cabecera['licencia'] ?? '',
                    $cabecera['tipoEnvio'],
                    '',
                    $cabecera['proyecto'] ?? '',
                    $anio[0],
                    $cabecera["observaciones"] ?? '',
                    $cabecera["destinatario"] ?? '',
                    null, '',
                    $cabecera['peso'] ?? 0,
                    ''
                );
                
                $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->SetWidths(array(10, 15, 15, 147));
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('Arial', '', 6);
                
                $lc = 0;
                $itemNumero = 1;

                foreach ($detalles as $item) {
                    // Aseguramos que los valores no sean nulos
                    $unidad    = strtoupper($item['unidad'] ?? 'UND'); 
                    $cantidad  = number_format((float)$item['cantidad'], 0); // Si usas decimales, cambia el 0 por 2
                    $codigo    = $item['codigo'] ?? '';
                    $desc      = $item['descprod'] ?? '';
                    
                    // Formato: "CÓDIGO DESCRIPCIÓN"
                    $descripcionCompleta = trim($codigo . " " . $desc);
                    
                    // Alineación: [R]ight para item, [R]ight para cantidad, [C]enter para unidad, [L]eft para descripción
                    $pdf->SetX(13);
                    $pdf->SetAligns(array("R", "R", "C", "L"));
                    
                    $pdf->Row(array(
                        str_pad($itemNumero++, 3, "0", STR_PAD_LEFT), // Item
                        $cantidad,                                    // Cantidad
                        $unidad,                                      // Unidad
                        $descripcionCompleta                          // Descripción
                    ));

                    $lc++;

                    if ($lc == 26) {
                        $pdf->AddPage();
                        $lc = 0;
                    }
                }

                $pdf->SetXY(167, 237);
                //$pdf->Cell(6, 20, 'NRO.GUIA INTERNA: ' . $idGuia, 0, 1);

                $pdf->Ln(2);
                $pdf->Output($archivo, 'F');

                return ["archivo" => $archivo];

            } catch (Exception $e) {
                error_log("Error al generar PDF: " . $e->getMessage());
                return false;
            }
        }

        private function guardarGuiaBD($data, $idGuia) {
            try {
                $conn = $this->db->connect();
                
                $sql = $conn->prepare("
                    INSERT INTO lg_guias (INSERT INTO lg_guias SET  cnumguia=:guia,
                                                                    corigen=:origen,
                                                                    cdirorigen=:direccion_origen,
                                                                    cdestino=:destino,
                                                                    cdirdest=:direccion_destino,
                                                                    centi=:entidad,
                                                                    centidir=:direccion_entidad,
                                                                    centiruc=:ruc_entidad,
                                                                    ctraslado=:traslado,
                                                                    cenvio=:envio,
                                                                    cautoriza=:autoriza,
                                                                    cdestinatario=:destinatario,
                                                                    cobserva=:observaciones,
                                                                    cnombre=:nombres,
                                                                    clicencia=:licencia,
                                                                    cplaca=:placa,
                                                                    ftraslado=:fecha_traslado,
                                                                    fguia=:fecha_guia,
                                                                    cserie=:serie");
                
                $sql->execute([
                    ':guia'                 => $idGuia,
                    ':origen'               => $data['almacenOrigen'],
                    ':direccion_origen'     => $data['direccionOrigen'],
                    ':destino'              => $data['almacenDestino'] ?? '',
                    ':direccion_destino'    => $data['direccionDestino'] ?? '',
                    ':entidad'              => $data['empresaTransporte'] ?? '',
                    ':direccion_entidad'    => $data['direccionEmpresa'] ?? '',
                    ':ruc_entidad'          => $data['rucEmpresa'] ?? '',
                    ':traslado'             => $data['modalidad'] ?? '',
                    ':envio'                => $data['tipoEnvio'] ?? '',
                    ':autoriza'             => $data['autoriza'] ?? '',
                    ':destinatario'         => $data['destinatario'] ?? '',
                    ':observaciones'        => $data['observaciones'] ?? '',
                    ':nombres'              => $data['conductor'] ?? 0,
                    ':licencia'             => $data['licencia'] ?? '',
                    ':placa'                => $data['placa'] ?? '',
                    ':fecha_traslado'       => $data['fechaTraslado'] ?? '',
                    ':fecha_guia'           => $data['fechaEmision'] ?? '',
                    ':serie'                => 'T001',
                ]);

                return true;
                
            } catch (PDOException $e) {
                error_log("Error al guardar Guía: " . $e->getMessage());
                return false;
            }
        }
    }

?>

