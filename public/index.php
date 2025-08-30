<?php
/**
 * ==============================================================
 *  FRONT CONTROLLER - index.php
 * ==============================================================
 * Punto de entrada único para la API REST.
 * - Carga dependencias (Composer).
 * - Inicializa router.
 * - Define rutas de la API.
 * - Aplica normalización de URIs.
 * - Despacha la petición al controlador correspondiente.
 * ==============================================================
 */

declare(strict_types=1);

// ----------------- AUTOLOAD (Composer) -----------------
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\TaskController;
use App\Services\AuthService;

// ----------------- CABECERAS GLOBALES -----------------
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Ajustar en producción a dominios confiables
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejo de preflight CORS (solo responde sin procesar más)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ----------------- CARGAR VARIABLES DE ENTORNO -----------------
$dotenvPath = __DIR__ . '/../.env';
if (file_exists($dotenvPath)) {
    $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Ignora comentarios
        putenv(trim($line));
    }
}

// ----------------- INICIALIZAR ROUTER -----------------
$router = new Router();

/**
 * ==============================================================
 *                  RUTAS DISPONIBLES EN LA API
 * ==============================================================
 */

// ---- TASKS CRUD ----
$router->get('/api/tasks', [TaskController::class, 'index']);     // Listar todas las tareas
$router->get('/api/tasks/{id}', [TaskController::class, 'show']); // Mostrar tarea por ID
$router->post('/api/tasks', [TaskController::class, 'store']);    // Crear tarea
$router->put('/api/tasks/{id}', [TaskController::class, 'update']); // Actualizar tarea
$router->delete('/api/tasks/{id}', [TaskController::class, 'destroy']); // Eliminar tarea

// ---- LOGIN (Generación de JWT) ----
$router->post('/api/login', function () {
    $body = json_decode(file_get_contents('php://input'), true);

    // Validación mínima
    if (!isset($body['username']) || !isset($body['password'])) {
        http_response_code(400);
        echo json_encode(["error" => "username y password requeridos"]);
        return;
    }

    $auth = new AuthService();
    $token = $auth->attempt($body['username'], $body['password']);

    if (!$token) {
        http_response_code(401);
        echo json_encode(["error" => "Credenciales inválidas"]);
        return;
    }

    echo json_encode(["token" => $token]);
});

// =============================================================
// NORMALIZAR REQUEST (para quitar base path del proyecto local)
// =============================================================
$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])); // ej: /prueba_php3/public
$requestUri = strtok($_SERVER['REQUEST_URI'], '?');                   // URI sin query string
$path = '/' . ltrim(str_replace($basePath, '', $requestUri), '/');    // Normalizar

// =============================================================
// DESPACHAR PETICIÓN
// =============================================================
try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $path);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "error"   => "Error interno del servidor",
        "detalle" => $e->getMessage()
    ]);
}
