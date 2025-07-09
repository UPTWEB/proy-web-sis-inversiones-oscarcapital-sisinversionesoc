













CREATE TABLE usuario (
    id SERIAL PRIMARY KEY, 
    username VARCHAR(100) NOT NULL UNIQUE,
    password TEXT NOT NULL,
    rol VARCHAR(20) NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    email VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE intentos_login (
    id SERIAL PRIMARY KEY,
    id_usuario INTEGER REFERENCES usuario(id),
    ip VARCHAR(45),
    user_agent TEXT,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    exito BOOLEAN NOT NULL
);

CREATE TABLE sesiones (
    id_sesion SERIAL PRIMARY KEY,
    id_usuario INTEGER REFERENCES usuario(id),
    ip VARCHAR(45),
    user_agent TEXT,
    inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fin TIMESTAMP,
    duracion INTERVAL DEFAULT '00:00:00'::interval,
    tipo VARCHAR(15)
);