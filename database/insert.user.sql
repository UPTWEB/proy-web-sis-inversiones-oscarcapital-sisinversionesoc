-- Script to insert users into the database
INSERT INTO
    usuario (id, username, password, rol, email)
VALUES
    (
        1,
        'ncamac',
        crypt ('ncamac', gen_salt ('bf')),
        'admin',
        'cc2022074262@virtual.upt.pe'
    ) 

INSERT INTO
    usuario (id, username, password, rol, email)
VALUES
    (
        3,
        'srgcp',
        crypt ('srgcp', gen_salt ('bf')),
        'admin',
        'sergiocolque.tlv@gmail.com'
    ) 

INSERT INTO
    usuario (id, username, password, rol, email)
VALUES
    (
        6,
        'chicho',
        crypt ('chicho', gen_salt ('bf')),
        'admin',
        'ra2022073504@virtual.upt.pe'
    )


-- Session


INSERT INTO sesiones (id_usuario, ip, user_agent)
VALUES (1, '192.168.1.5', 'Mozilla/5.0');

UPDATE sesiones
SET fin = CURRENT_TIMESTAMP
WHERE id_sesion = 123;