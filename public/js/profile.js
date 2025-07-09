// Definir las referencias a los elementos del formulario
const inputUpdateUser = document.getElementById('username');
const inputUpdatePassword = document.getElementById('actualPassword');
const inputUpdateNewPassword = document.getElementById('newPassword');
const inputUpdateEmail = document.getElementById('email');
const inputProfilePhoto = document.getElementById('profilePhoto');

async function sendForm(event) {
    event.preventDefault();

    if (!inputUpdatePassword.value.trim()) {
        message("La contraseña actual es obligatoria", "error");
        return;
    }

    let statusData = "";
    let messageData = "";

    try {
        const hasPhoto = inputProfilePhoto.files.length > 0;
        
        if (hasPhoto) {
            const formData = new FormData();
            formData.append('username', inputUpdateUser.value);
            formData.append('password', inputUpdatePassword.value);
            formData.append('newPassword', inputUpdateNewPassword.value);
            formData.append('email', inputUpdateEmail.value);
            formData.append('profilePhoto', inputProfilePhoto.files[0]);

            const response = await fetch("/api/user/UpdateUser", {
                method: "POST",
                headers: {
                    "X-Internal-Request": "1"
                },
                body: formData
            });

            const data = await response.json();
            statusData = data.status || "error";
            messageData = data.message || "No se recibió respuesta válida del servidor.";
        } else {
            const jsonData = {
                username: inputUpdateUser.value,
                password: inputUpdatePassword.value,
                newPassword: inputUpdateNewPassword.value,
                email: inputUpdateEmail.value,
            };

            const response = await fetch("/api/user/UpdateUser", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Internal-Request": "1"
                },
                body: JSON.stringify(jsonData)
            });

            const data = await response.json();
            statusData = data.status || "error";
            messageData = data.message || "No se recibió respuesta válida del servidor.";
        }

    } catch (error) {
        console.error("⚠️ Error de red:", error);
        statusData = "error";
        messageData = "⚠️ Hubo un error en la solicitud.";
    } finally {
        message(messageData, statusData);
        inputUpdatePassword.value = '';
        
        if (statusData === "success" && inputProfilePhoto.files.length > 0) {
            const photoPreview = document.getElementById('photoPreview');
            if (photoPreview) {
                const file = inputProfilePhoto.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }

        }
        if (statusData === "success") {
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
        
        closeEditProfileModal();
    }
}

if (inputProfilePhoto) {
    inputProfilePhoto.addEventListener('change', function(event) {
        const file = event.target.files[0];
        const photoPreview = document.getElementById('photoPreview');
        
        if (file && photoPreview) {
            const reader = new FileReader();
            reader.onload = function(e) {
                photoPreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
}