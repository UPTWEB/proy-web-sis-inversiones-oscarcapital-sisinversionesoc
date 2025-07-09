<link rel="stylesheet" href="/css/components/dialog.css">
<nav class="navbar navbar-expand px-3 border-bottom">
    <button class="btn" id="sidebar-toggle" type="button">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-collapse navbar">
        <ul class="navbar-nav">
            <div class="info pe-2">
                <i class="fa-solid fa-flag"></i>
                <span class="pe-2" id="ipLoad"></span>
                <i class="fa-solid fa-clock"></i>
                <span>Transcurrido:</span>
                <span id="elapsed-Time"></span>
            </div>
            <li class="nav-item dropdown profile-settings">
                <a onclick="toggleDialog('dialog-messages')" class="nav-icon pe-md-0">
                    <img src="<?= $_SESSION['foto']?>" class="avatar img-fluid rounded" alt="">
                </a>
                <dialog id="dialog-messages">
                    <button class="close-button" onclick="toggleDialog()">❌</button>
                    <div class="profile-settings-dialog-content">
                        <div class="profile-settings-avatar-section">
                            <div class="profile-settings-avatar-container">
                                <img src="<?= $_SESSION['foto']?>" class="profile-settings-avatar" alt="Avatar">
                                <div class="profile-settings-status-indicator"></div>
                            </div>
                            <h4 class="profile-settings-main-title"><?= $_SESSION['username'] ?></h4>
                        </div>
                        
                        <div class="profile-settings-info-section">
                            <div class="profile-settings-info-item">
                                <label class="profile-settings-info-label">Correo</label>
                                <span class="profile-settings-info-value"><?= $_SESSION['email']?></span>
                            </div>
                            
                            <div class="profile-settings-info-item">
                                <label class="profile-settings-info-label">Nombre de usuario</label>
                                <span class="profile-settings-info-value"><?= $_SESSION['username']?></span>
                            </div>
                        </div>
                        
                        <div class="profile-settings-actions">
                            <button class="profile-settings-btn profile-settings-btn-primary" onclick="openEditProfileModal()">
                                <i class="fa-solid fa-user-edit"></i>
                                Editar Perfil
                            </button>
                            <button class="profile-settings-btn profile-settings-btn-danger" id="logoutSesion">
                                <i class="fa-solid fa-sign-out-alt"></i>
                                Cerrar Sesión
                            </button>
                        </div>
                    </div>
                </dialog>
                
                <div id="editProfileModal" class="profile-settings-edit-modal">
                    <div class="profile-settings-edit-modal-content">
                        <div class="profile-settings-edit-modal-header">
                            <h3>Editar Perfil</h3>
                            <button class="profile-settings-edit-close-btn" onclick="closeEditProfileModal()">×</button>
                        </div>
                        <div class="profile-settings-edit-modal-body">
                            <form id="editProfileForm">
                                <div class="profile-settings-form-group">
                                    <label class="profile-settings-form-label">Foto de Perfil</label>
                                    <div class="profile-settings-photo-upload">
                                        <img src="<?= $_SESSION['foto']?>" class="profile-settings-preview-img" id="photoPreview" alt="Preview">
                                        <input type="file" id="profilePhoto" class="profile-settings-file-input" accept="image/*">
                                        <label for="profilePhoto" class="profile-settings-upload-btn">
                                            <i class="fa-solid fa-camera"></i>
                                            Cambiar Foto
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="profile-settings-form-group">
                                    <label class="profile-settings-form-label">Nombre de Usuario</label>
                                    <input type="text" class="profile-settings-form-input" id="username" placeholder="Ingrese un nombre de usuario" value="<?= $_SESSION['username'] ?>">
                                </div>
                                
                                <div class="profile-settings-form-group">
                                    <label class="profile-settings-form-label">Correo Electrónico</label>
                                    <input type="email" class="profile-settings-form-input" id="email" placeholder="Ingrese un email" value="<?= $_SESSION['email']?>">
                                </div>
                                
                                <div class="profile-settings-form-group">
                                    <label class="profile-settings-form-label">Nueva Contraseña (Solo si deseas actualizarla)</label>
                                    <input type="password" class="profile-settings-form-input" id="newPassword" placeholder="Ingrese nueva contraseña">
                                </div>
                                
                                <div class="profile-settings-form-group">
                                    <label class="profile-settings-form-label">Confirmar Contraseña (Solo si deseas actualizarla)</label>
                                    <input type="password" class="profile-settings-form-input" id="confirmPassword" placeholder="Confirme la nueva contraseña">
                                </div>
                                
                                <div class="profile-settings-form-group">
                                    <label class="profile-settings-form-label">Ingresa la contraseña actual</label>
                                    <input type="password" class="profile-settings-form-input" id="actualPassword" placeholder="Ingresa la contraseña actual">
                                </div>

                                <div class="profile-settings-form-actions">
                                    <button type="button" class="profile-settings-btn profile-settings-btn-secondary" onclick="closeEditProfileModal()">Cancelar</button>
                                    <button type="submit" class="profile-settings-btn profile-settings-btn-primary">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>
<div class="message-dashboard">
<?php include_once '../app/views/includes/message.php'; ?>
</div>
<script>
function openEditProfileModal() {
    const modal = document.getElementById('editProfileModal');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    toggleDialog();
}

function closeEditProfileModal() {
    const modal = document.getElementById('editProfileModal');
    modal.classList.remove('show');
    document.body.style.overflow = 'auto';
}

document.getElementById('profilePhoto').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('editProfileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const actualPassword = document.getElementById('actualPassword').value;
    
    if (!actualPassword.trim()) {
        message("La contraseña actual es obligatoria para verificar tu identidad", "error");
        return;
    }
    
    if (newPassword || confirmPassword) {
        if (!newPassword && confirmPassword) {
            message("Debes ingresar la nueva contraseña", "error");
            return;
        }
        
        if (newPassword && !confirmPassword) {
            message("Debes confirmar la nueva contraseña", "error");
            return;
        }
        
        if (newPassword !== confirmPassword) {
            message("Las nuevas contraseñas no coinciden", "error");
            return;
        }
    }
    
    sendForm(e);
});
</script>

<script src="/js/profile.js"></script>
<script src="/js/components/dialog.js"></script>
<script src="/js/admin/session.info.js"></script>