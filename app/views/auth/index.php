<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - Oscar Capital</title>
    <link rel="stylesheet" href="/css/auth/auth.css">
    <?php require_once '../app/views/includes/header.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>

<body>

    <form id="form-Login" class="login-container">
        <img src="https://oscarcapitalperu.com/wp-content/uploads/2024/09/OscarCapital-blanco-1.png" alt="Logo">

        <h2>Bienvenido</h2>
        <p>Confirma tu identidad para continuar</p>

        <div class="input-login">
            <label for="username">Usuario</label>
            <i class="fa-solid fa-address-card icon-left" id="username-icon"></i>
            <input id="username" type="text" name="username" placeholder="Ingresa tu usuario">
        </div>

        <div class="input-login">
            <label for="password">Contraseña</label>
            <i class="fa-solid fa-key icon-left" id="password-icon"></i>
            <i class="fa-solid fa-eye icon-right" id="view-password" hidden></i>
            <input id="password" type="password" name="password" placeholder="Ingresa tu contraseña">
        </div>
        
        <button class="main-button ripple_effect" id="btn-form" type="submit">
            <span class="btn-text">Iniciar Sesión</span>
            <span class="loader" hidden></span>
        </button>

        
        <p class="separator"><span>O continúa con</span></p>

        <ul class="social-media">
            <button class="social-button" type="button" id="btn-google">
                <div class="btn-text" >
                    <img src="/svg/google.svg" alt="Google">
                    <span>Iniciar con Google</span>
                </div>
                <span class="loader" style="border-top-color:#aeaeae;" hidden></span>
            </button>
        </ul>
    </form>
    <?php include_once '../app/views/includes/message.php'; ?>
    
    <script src="/js/ip.info.js"></script>   
    <script src="/js/auth/form.validate.js" type="module"></script>   
    <script src="/js/components/ripple.effect.js"></script>   
</body>

</html>