# Prueba Práctica PHP 3 - API Tasks

Instrucciones rápidas:
1. Ajusta variables de entorno: DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS, JWT_SECRET.
2. Ejecuta `composer install` en la raíz para instalar dependencias (firebase/php-jwt).
3. Crea la base de datos con `psql -f sql/init.sql` (o ajusta según tu setup).
4. Levanta el servidor apuntando `public/` como document root, o usa:
   `php -S 127.0.0.1:8000 -t public`
5. Probar flujo básico:
   - POST /api/login  { "username": "admin", "password": "password" } -> { token }
   - Usar token en Authorization: Bearer <token> para POST/PUT/DELETE en /api/tasks

Endpoints:
- POST /api/login
- GET /api/tasks
- GET /api/tasks/{id}
- POST /api/tasks
- PUT /api/tasks/{id}
- DELETE /api/tasks/{id}

Notas de seguridad: cambiar JWT_SECRET, usar HTTPS en producción, hashear passwords, etc.
