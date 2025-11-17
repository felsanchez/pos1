<?php
/**
 * Script de diagnóstico del sistema de logging
 * Guarda este archivo como test-logging.php en la raíz del proyecto
 * Accede desde: http://localhost/pos/test-logging.php
 */

echo "<h1>Diagnóstico del Sistema de Logging</h1>";
echo "<pre>";

// 1. Verificar que config.php existe y se puede cargar
echo "1. Verificando config.php...\n";
if (file_exists(__DIR__ . '/config.php')) {
    echo "   ✓ config.php existe\n";
    try {
        require_once __DIR__ . '/config.php';
        echo "   ✓ config.php cargado correctamente\n";
    } catch (Exception $e) {
        echo "   ✗ Error al cargar config.php: " . $e->getMessage() . "\n";
        die();
    }
} else {
    echo "   ✗ config.php NO existe\n";
    die();
}

// 2. Verificar que Logger está disponible
echo "\n2. Verificando Logger...\n";
if (class_exists('Logger')) {
    echo "   ✓ Clase Logger disponible\n";
} else {
    echo "   ✗ Clase Logger NO disponible\n";
    die();
}

// 3. Verificar directorio storage/logs/
echo "\n3. Verificando directorio storage/logs/...\n";
$logDir = __DIR__ . '/storage/logs/';
if (file_exists($logDir)) {
    echo "   ✓ Directorio existe: $logDir\n";
} else {
    echo "   ✗ Directorio NO existe: $logDir\n";
    echo "   Creando directorio...\n";
    mkdir($logDir, 0755, true);
    if (file_exists($logDir)) {
        echo "   ✓ Directorio creado exitosamente\n";
    } else {
        echo "   ✗ No se pudo crear el directorio\n";
        die();
    }
}

// 4. Verificar permisos de escritura
echo "\n4. Verificando permisos de escritura...\n";
if (is_writable($logDir)) {
    echo "   ✓ Directorio tiene permisos de escritura\n";
} else {
    echo "   ✗ Directorio NO tiene permisos de escritura\n";
    echo "   Ruta: $logDir\n";
    echo "   Permisos actuales: " . substr(sprintf('%o', fileperms($logDir)), -4) . "\n";
    die();
}

// 5. Probar escribir un log
echo "\n5. Probando escribir logs...\n";
session_start();
$_SESSION['usuario'] = 'test-diagnostico';

try {
    Logger::error('TEST: Error de prueba desde diagnóstico', [
        'timestamp' => date('Y-m-d H:i:s'),
        'test' => true
    ]);
    echo "   ✓ Log ERROR escrito\n";

    Logger::warning('TEST: Advertencia de prueba', ['test' => true]);
    echo "   ✓ Log WARNING escrito\n";

    Logger::info('TEST: Info de prueba', ['test' => true]);
    echo "   ✓ Log INFO escrito\n";
} catch (Exception $e) {
    echo "   ✗ Error al escribir log: " . $e->getMessage() . "\n";
    die();
}

// 6. Verificar que el archivo de log se creó
echo "\n6. Verificando archivo de log...\n";
$logFile = $logDir . date('Y-m-d') . '.log';
if (file_exists($logFile)) {
    echo "   ✓ Archivo de log existe: $logFile\n";
    echo "   ✓ Tamaño: " . filesize($logFile) . " bytes\n";

    // Leer últimas 5 líneas
    $lines = file($logFile);
    $lastLines = array_slice($lines, -5);
    echo "\n   Últimas 5 líneas del log:\n";
    foreach ($lastLines as $line) {
        $entry = json_decode($line, true);
        if ($entry) {
            echo "   - [{$entry['level']}] {$entry['timestamp']}: {$entry['message']}\n";
        }
    }
} else {
    echo "   ✗ Archivo de log NO existe: $logFile\n";
}

// 7. Simular error de base de datos
echo "\n7. Simulando error de base de datos...\n";
try {
    // Intentar conectar con credenciales incorrectas
    $host = 'localhost';
    $dbname = 'base_de_datos_que_no_existe';
    $user = 'usuario_invalido';
    $pass = 'contraseña_invalida';

    $link = new PDO("mysql:host={$host};dbname={$dbname}", $user, $pass);
} catch (PDOException $e) {
    echo "   ✓ Error de BD capturado correctamente\n";
    Logger::error('TEST: Error simulado de base de datos', [
        'exception' => $e
    ]);
    echo "   ✓ Error registrado en log\n";
}

// 8. Leer estadísticas
echo "\n8. Estadísticas de logs del día:\n";
try {
    $stats = Logger::getStats(date('Y-m-d'));
    echo "   Total: {$stats['total']}\n";
    echo "   Errores: {$stats['errors']}\n";
    echo "   Advertencias: {$stats['warnings']}\n";
    echo "   Info: {$stats['info']}\n";
    echo "   Debug: {$stats['debug']}\n";
} catch (Exception $e) {
    echo "   ✗ Error al obtener estadísticas: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✓ DIAGNÓSTICO COMPLETADO\n";
echo str_repeat("=", 60) . "\n";
echo "\nSi todos los tests pasaron, el sistema de logging está funcionando.\n";
echo "Ahora puedes:\n";
echo "1. Ver los logs en: http://localhost/pos/?ruta=logs\n";
echo "2. O ver el archivo directamente en: storage/logs/" . date('Y-m-d') . ".log\n";
echo "\n";
echo "</pre>";
?>
