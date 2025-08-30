<?php
    /* Activa tipos estrictos: fuerza coincidencia exacta de tipos en argumentos y retornos. */
    declare(strict_types=1);

    /* AUTOLOAD (Composer), Carga automáticamente todas las dependencias Composer */
    require_once __DIR__ . '/../vendor/autoload.php'; 

    /* Importar clases necesarias para la aplicación */
    use App\Core\Router;
    use App\Controllers\TaskController;
    use App\Services\AuthService;

    /* Cabeceras Globales: Configura cabeceras HTTP para la API: formato JSON, CORS y métodos permitidos */
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *'); //En producción, restringir a dominios confiables
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    /* Manejo de preflight CORS (solo responde sin procesar más) */
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(http_response_code(200));

    /* Variables de Entorno: Lee el archivo .env y carga cada variable, ignorando comentarios y líneas vacías */
    $dotenvPath = __DIR__ . '/../.env';
    if (file_exists($dotenvPath)) {
        $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue; //Ignora comentarios
            putenv(trim($line)); //Asigna la variable al entorno
        }
    }

    /* ROUTER: Crea una instancia del router para gestionar rutas y solicitudes HTTP */
    $router = new Router();

    /* RUTAS: Disponibes en la API */
    $router->get('/api/tasks', [TaskController::class, 'index']); //Listar todas las tareas
    $router->get('/api/tasks/{id}', [TaskController::class, 'show']); //Mostrar tarea por ID
    $router->post('/api/tasks', [TaskController::class, 'store']); //Crear tarea
    $router->put('/api/tasks/{id}', [TaskController::class, 'update']); //Actualizar tarea
    $router->delete('/api/tasks/{id}', [TaskController::class, 'destroy']); //Eliminar tarea

    /* LOGIN (Generación de JWT) */
    $router->post('/api/login', function () {
        $body = json_decode(file_get_contents('php://input'), true);

        // Validación mínima al usuario
        if (!isset($body['username']) || !isset($body['password'])) {
            http_response_code(400);
            echo json_encode(["error" => "username y password requeridos"]);
            return;
        }

        $auth = new AuthService(); //Crea instancia del servicio de autenticación
        $token = $auth->attempt($body['username'], $body['password']); //Intenta generar un token JWT con las credenciales proporcionadas

        /* Bloque de Autenticacion al usuario */
        if (!$token) {
            http_response_code(401);
            echo json_encode(["error" => "Credenciales inválidas"]);
            return;
        }

        /* Responder con el token JWT generado al cliente en formato JSON */
        echo json_encode(["token" => $token]);
    });

    /* Tratar REQUEST (para quitar base path del proyecto local) */
    $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])); //ej: /prueba_php3/public
    $requestUri = strtok($_SERVER['REQUEST_URI'], '?'); //URI sin query string
    $path = '/' . ltrim(str_replace($basePath, '', $requestUri), '/'); //Normalizar

    /* REDIRECCIÓN A index.html SI NO HAY RUTA */
    if ($path === '/' || $path === '') {
        header('Location: index.html');
        exit;
    }

    /* Procesar Peticion */
    try {
        $router->dispatch($_SERVER['REQUEST_METHOD'], $path);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            "error"   => "Error interno del servidor",
            "detalle" => $e->getMessage()
        ]);
    }

?>