<?php
// test_original.php - Probar la consulta original
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("c:/xampp/htdocs/ibis/public/cotizacion/connect.php");

if (!$pdo) {
    die("Error de conexión a la base de datos");
}

// La consulta original con el problema
$sql = "SELECT 
            m.nrodoc,
            m.fentrega,
            m.cserie,
            m.idcostos,
            m.idprod,
            CONCAT(a.NOM_TRABAJADOR, ' ', a.APE_PATERNO, ' ', a.APE_MATERNO) AS nombre,
            a.ESTADO,
            m.flgactivo,
            m.flgestado,
            m.fmtto,
            m.ntipo,
            DATEDIFF(m.fmtto, CURDATE()) AS dias_diferencia,
            UPPER(p.cdesprod) AS cdesprod,
            m.idreg,
            e.chdd,
            e.cprocesador,
            e.cram,
            e.totros,
            e.nestado,
            UPPER(u.cnameuser) AS cnameuser,
            y.ccodproy 
        FROM ti_mmttos m
        LEFT JOIN linked_data.vw_personal_ultimo_ingreso_aquarius a ON a.NUM_DOC_IDENTIDAD = m.nrodoc
        LEFT JOIN cm_producto p ON p.id_cprod = m.idprod
        LEFT JOIN tb_tiespec e ON e.cserie = m.cserie COLLATE utf8_spanish2_ci
        LEFT JOIN tb_user u ON u.iduser = m.iduser COLLATE utf8_spanish2_ci
        LEFT JOIN tb_proyectos y ON y.nidreg = m.idcostos 
        WHERE m.flgactivo = 1 
            AND a.ESTADO != 'CESADO' 
            AND m.ntipo != 0 
            AND (p.ccodprod LIKE '%B05010002%' OR p.ccodprod LIKE '%B05010006%' OR p.ccodprod LIKE '%B05010005%') 
            AND m.flgestado = 0 
            AND DATEDIFF(m.fmtto, CURDATE()) BETWEEN 0 AND 30 
        ORDER BY dias_diferencia ASC";

// Ejecutar y verificar errores
$stmt = $pdo->prepare($sql);
if (!$stmt) {
    die("Error al preparar la consulta: " . print_r($pdo->errorInfo(), true));
}

$executed = $stmt->execute();
if (!$executed) {
    die("Error al ejecutar la consulta: " . print_r($stmt->errorInfo(), true));
}

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Número de registros encontrados: " . count($results) . "<br><br>";

if (count($results) > 0) {
    echo "<pre>";
    print_r(array_slice($results, 0, 3)); // Mostrar primeros 3 registros
    echo "</pre>";
} else {
    echo "No se encontraron registros con los criterios especificados.";
}
?>