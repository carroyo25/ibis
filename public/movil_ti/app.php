<?php
    // Establecer zona horaria de Lima
    date_default_timezone_set('America/Lima');
        
    header('Content-Type: application/json');

    require_once("c:/xampp/htdocs/ibis/public/cotizacion/connect.php");

    if(isset($_POST['funcion'])){
        if($_POST['funcion'] == "obtenerProximos"){
            echo json_encode(obtenerProximos($pdo));
        }
    }

    function obtenerProximos($pdo) {
    try {
        // SQL optimizado con mejores prácticas
        $sql = "SELECT 
                    m.nrodoc,
                    m.fentrega,
                    m.cserie,
                    m.idcostos,
                    m.idprod,
                    CONCAT(TRIM(a.NOM_TRABAJADOR), ' ', TRIM(a.APE_PATERNO), ' ', TRIM(a.APE_MATERNO)) AS nombre,
                    a.ESTADO,
                    m.flgactivo,
                    m.flgestado,
                    m.fmtto,
                    m.ntipo,
                    DATEDIFF(m.fmtto, CURDATE()) AS dias_diferencia,
                    UPPER(TRIM(p.cdesprod)) AS cdesprod,
                    m.idreg,
                    e.chdd,
                    e.cprocesador,
                    e.cram,
                    e.totros,
                    e.nestado,
                    UPPER(TRIM(u.cnameuser)) AS cnameuser,
                    y.ccodproy 
                FROM 
                    ti_mmttos m
                    LEFT JOIN linked_data.vw_personal_ultimo_ingreso_aquarius a 
                        ON a.NUM_DOC_IDENTIDAD = m.nrodoc
                    LEFT JOIN cm_producto p 
                        ON p.id_cprod = m.idprod
                    LEFT JOIN tb_tiespec e 
                        ON e.cserie = m.cserie COLLATE utf8_spanish2_ci
                    LEFT JOIN tb_user u 
                        ON u.iduser = m.iduser COLLATE utf8_spanish2_ci
                    LEFT JOIN tb_proyectos y 
                        ON y.nidreg = m.idcostos 
                WHERE 
                    m.flgactivo = 1 
                    AND COALESCE(a.ESTADO, '') != 'CESADO'
                    AND m.ntipo != 0 
                    AND (p.ccodprod LIKE '%B05010002%' 
                         OR p.ccodprod LIKE '%B05010006%' 
                         OR p.ccodprod LIKE '%B05010005%')
                    AND m.flgestado = 0 
                    AND DATEDIFF(m.fmtto, CURDATE()) BETWEEN 0 AND 30
                ORDER BY 
                    dias_diferencia ASC";
        
        // Preparar y ejecutar la consulta
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        // Obtener resultados
        $docData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Si no hay resultados, retornar array vacío
        if (empty($docData)) {
            return [];
        }
        
    
        return $docData;
        
    } catch (PDOException $e) {
        // Registrar error en log del servidor
        error_log("Error en obtenerProximos: " . $e->getMessage());
        error_log("Código de error: " . $e->getCode());
        
        // Retornar array con información de error (pero manteniendo formato JSON válido)
        return [
            "error" => true,
            "mensaje" => "Error al obtener los mantenimientos próximos",
            "detalle" => $e->getMessage() // Solo para desarrollo, quitar en producción
        ];
    } catch (Exception $e) {
        // Capturar cualquier otro tipo de error
        error_log("Error general en obtenerProximos: " . $e->getMessage());
        return [
            "error" => true,
            "mensaje" => "Error inesperado al procesar la solicitud"
        ];
    }
}

?>