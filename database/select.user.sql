-- Login authentication
SELECT * FROM usuario WHERE (username = 'user' or  email = 'user') AND password = crypt('pass', password)

-- Check if the user is blocked
SELECT COUNT(*) FROM intentos_login WHERE id_usuario = ? AND exito = FALSE AND fecha_hora > (NOW() - INTERVAL '10 minutes')

