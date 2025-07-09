// message("Sesion iniciada", "success");
// message("dasdsa", "success");
// message("Hubo un error en la solicitud", "error");
// message("Error: " + error.message, "error");
        

let currentTimeoutId = null;
function message(message, action) {
    const messageElement = document.querySelector("MESSAGE");
    if (action === "error") {
        messageElement.classList.remove('success'); 
        messageElement.classList.add('error'); 
    }

    if (action === "success") { 
        messageElement.classList.remove('error');
        messageElement.classList.add('success'); 
    }

    messageElement.innerHTML = message;
    messageElement.style.display = "flex";
    messageElement.style.animation = "messageIn 0.7s cubic-bezier(0.6, -0.14, 0.02, 1.29)";
    if (currentTimeoutId) { clearTimeout(currentTimeoutId); }
    currentTimeoutId = setTimeout(() => {
        messageElement.style.animation = "messageOut 0.8s";
        setTimeout(() => {
            messageElement.style.display = "none";
            currentTimeoutId = null;
            messageElement.className = "";
        }, 700);
    }, 4000);
}