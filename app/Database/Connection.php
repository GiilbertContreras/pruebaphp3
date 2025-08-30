<?php
    namespace App\Database;

    use Dotenv\Dotenv;
    use PDO;
    use PDOException;

    /* Clase Connection **/
    /* Maneja la conexión única (singleton) a la base de datos mediante PDO. Utiliza variables de entorno cargadas desde el archivo .env ubicado en la raíz del proyecto. */
    class Connection {
        /** @var PDO|null $pdo Instancia única de conexión */
        private static ?PDO $pdo = null;

        /* Devuelve la conexión activa a la base de datos. **/
        /* Si no existe, la crea utilizando los datos del archivo .env. */
        /** @return PDO Conexión activa */
        public static function get(): PDO {
            if (self::$pdo instanceof PDO) {
                return self::$pdo; // Reutiliza conexión existente
            }

            // Cargar variables de entorno desde el archivo .env
            try {
                // dirname(__DIR__, 3) => sube 3 niveles desde /app/Database hasta la raíz del proyecto
                $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
                $dotenv->load();
            } catch (\Exception $e) {
                error_log("[ENV ERROR] No se pudo cargar el archivo .env: " . $e->getMessage() . " | Archivo: " . __FILE__);
                http_response_code(500);
                echo json_encode([ "error" => "Error interno del servidor" ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ======================================================
            // Variables de entorno obligatorias
            // ======================================================
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

                error_log("[DB CONFIG ERROR] Variables de entorno faltantes: " . implode(", ", $faltantes) . " | Archivo: " . __FILE__);
                http_response_code(500);
                echo json_encode([ "error" => "En Mantenimiento. Verifique más tarde." ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // ======================================================
            // Construcción del DSN para PostgreSQL
            // ======================================================
            $dsn = "pgsql:host={$host};port={$port};dbname={$db};";

            // ======================================================
            // Intentar conexión PDO
            // ======================================================
            try {
        // dirname(__DIR__, 2) => sube 2 niveles hasta la raíz del proyecto
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();
    } catch (\Exception $e) {
        error_log("[ENV ERROR] No se pudo cargar el archivo .env: " . $e->getMessage() . " | Archivo: " . __FILE__);
        http_response_code(500);
        echo json_encode([ "error" => "Error interno del servidor" ], JSON_UNESCAPED_UNICODE);
        exit;
    }


            return self::$pdo;
        }
    }
?>