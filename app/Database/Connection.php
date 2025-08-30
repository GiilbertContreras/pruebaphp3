<?php
namespace App\Database;

use Dotenv\Dotenv;
use PDO;
use PDOException;

/**
 * Clase Connection
 * Maneja la conexión única (singleton) a la base de datos mediante PDO.
 */
class Connection {
    /** @var PDO|null $pdo Instancia única de conexión */
    private static ?PDO $pdo = null;

    /**
     * Devuelve la conexión activa a la base de datos.
     * @return PDO
     */
    public static function get(): PDO {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        // Cargar variables de entorno
        try {
            $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
            $dotenv->load();
        } catch (\Exception $e) {
            error_log("[ENV ERROR] No se pudo cargar .env: " . $e->getMessage() . " | Archivo: " . __FILE__);
            http_response_code(500);
            echo json_encode(["error" => "Error interno del servidor"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Variables de entorno
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $db   = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');

        // Validar existencia
        if (!$host || !$port || !$db || !$user || !$pass) {
            $faltantes = [];
            if (!$host) $faltantes[] = 'DB_HOST';
            if (!$port) $faltantes[] = 'DB_PORT';
            if (!$db)   $faltantes[] = 'DB_NAME';
            if (!$user) $faltantes[] = 'DB_USER';
            if (!$pass) $faltantes[] = 'DB_PASS';

            error_log("[DB CONFIG ERROR] Variables faltantes: " . implode(", ", $faltantes) . " | Archivo: " . __FILE__);
            http_response_code(500);
            echo json_encode(["error" => "En mantenimiento. Verifique más tarde."], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $dsn = "pgsql:host={$host};port={$port};dbname={$db};";

        // Crear conexión PDO
        try {
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT => false
            ]);
        } catch (PDOException $e) {
            error_log("[DB CONNECTION ERROR] " . $e->getMessage() . " | Archivo: " . __FILE__);
            http_response_code(500);
            echo json_encode(["error" => "Error interno del servidor"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        return self::$pdo;
    }
}
