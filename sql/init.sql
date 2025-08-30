-- Creación base y tabla (ajusta credenciales según necesites)
CREATE DATABASE prueba_php3;
\c prueba_php3;

CREATE TABLE tasks (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  status VARCHAR(20) NOT NULL DEFAULT 'pendiente',
  created_at TIMESTAMP WITHOUT TIME ZONE DEFAULT now(),
  updated_at TIMESTAMP WITHOUT TIME ZONE DEFAULT now()
);

-- usuario para la app (opcional)
CREATE ROLE api_user WITH LOGIN PASSWORD 'secret_password';
GRANT ALL PRIVILEGES ON DATABASE prueba_php3 TO api_user;
