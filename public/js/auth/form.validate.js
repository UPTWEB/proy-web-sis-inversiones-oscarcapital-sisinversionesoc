import { showLoader,hideLoader } from "./loader.effect.js";
import {addInputErrors,clearInputErrors } from "./input.js";


// event click

const btnLoginForm = document.getElementById('btn-form');
const btnText = btnLoginForm.querySelector('.btn-text');
const loader = btnLoginForm.querySelector('.loader');

btnLoginForm.addEventListener("click", function (event) {
    createRippleEffect(event);
    if (checkEmpty()){
        showLoader(btnLoginForm,loader,btnText,'#333');
        sendForm(event);
    } 
});

// for media buttons
document.querySelector('.social-media').addEventListener('click', function(event) {
    
    const elementButton = event.target.closest('.social-button');
    if (elementButton) {
        console.log('Se hizo clic en:', elementButton.id);
        
        const btnSocialMedia = document.getElementById(elementButton.id);
        const btnTextSM = btnSocialMedia.querySelector('.btn-text');
        const loaderSM = btnSocialMedia.querySelector('.loader');
        
        showLoader(btnSocialMedia,loaderSM,btnTextSM,'#ebebeb');
        
        if (elementButton.id === 'btn-google') {
          
            let popup;
            popup = window.open("/google-login", "_blank", "width=600,height=550");
            const timer = setInterval(() => {
                if (popup.closed) {
                    clearInterval(timer);
                    hideLoader(btnSocialMedia,loaderSM,btnTextSM,'#ebebeb');
                }
            }, 500);

            window.addEventListener("message", (event) => {
                if (!event.data || !event.data.status) return;
                message(event.data.message, event.data.status);

                if (event.data.status === "success") {
                    setTimeout(() => {
                        window.location.href = event.data.redirect;
                    }, 900);
                } 

            });
            
        } 
        // else if (elementButton.id === 'btn-facebook') {
            
            
            
        // }
    }
});

const inputUser = document.getElementById("username");
const inputPassword = document.getElementById("password");

async function sendForm(event) {

    event.preventDefault();

    const formData = {
        username: inputUser.value,
        password: inputPassword.value,
        ipclient: ipClient
    };

    let statusData = "";
    let messageData = "";

    try {
        const response = await fetch("/api/auth/Authlogin", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-Internal-Request": "1"
            },
            body: JSON.stringify(formData)
        });


        const data = await response.json();
        
        statusData = data.status || "error";
        messageData = data.message || "No se recibió respuesta válida del servidor.";

        if (data.status === "success") {
            // btnText.innerHTML = 'Bienvenido ' + data.usuario.username + ' (ID: ' + data.usuario.id + ')';
            // localStorage.setItem("token", data.token);
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        } 

    } catch (error) {
        console.error("⚠️ Error de red:", error);
        statusData = "error";
        messageData = "⚠️ Hubo un error en la solicitud.";
    } finally {
        message(messageData,statusData);
        setTimeout(() => {
            btnText.innerHTML = 'Iniciar Sesión';
            hideLoader(btnLoginForm,loader,btnText);
        }, 900);
    }
}

document.getElementById('form-Login').addEventListener('focusin', function (event) {
    const input = event.target;

    if (input.tagName === 'INPUT' && input.classList.contains('error') ) { 
        const iconFocus = document.getElementById(input.id + "-icon");
        clearInputErrors(input, iconFocus);
    }
});

function checkEmpty() {
    const iconUser = document.getElementById("username-icon");
    const iconPassword = document.getElementById("password-icon");

    let isValid = true;

    if (inputUser.value.trim() === "") {
        addInputErrors(inputUser, iconUser);
        isValid = false;
    } else {
        clearInputErrors(inputUser, iconUser);
    }
    
    if (inputPassword.value.trim() === "") {
        addInputErrors(inputPassword, iconPassword);
        isValid = false;
    } else {
        clearInputErrors(inputPassword, iconPassword);
    }

    return isValid;
}

const view_password = document.getElementById('view-password');
view_password.addEventListener('click', () => {

    const passwordInput = document.getElementById('password');
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        view_password.classList.remove('fa-eye');
        view_password.classList.add('fa-eye-slash');
        // view_password.style.font-size = '0.1px';

    } else {
        passwordInput.type = "password";
        view_password.classList.remove('fa-eye-slash');
        view_password.classList.add('fa-eye');
        // view_password.style.removeProperty('font-size');
    }

});

inputPassword.addEventListener('keydown', function(event) {
    if (event.key === ' ' || event.code === 'Space' || event.keyCode === 32) {
        event.preventDefault();
    }
});

inputPassword.addEventListener('input', () => { 

    const value = inputPassword.value;
    view_password.hidden = !value;
});