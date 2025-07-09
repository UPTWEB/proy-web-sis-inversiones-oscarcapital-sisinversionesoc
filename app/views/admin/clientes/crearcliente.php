<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/main.css">
</head>

<body>
    <div class="wrapper">
        <aside id="sidebar" class="js-sidebar">
            <!-- Content For Sidebar -->
            <?php require_once '../app/views/includes/admin/sidebar.php'; ?>
        </aside>
        <div class="main">
            <?php require_once '../app/views/includes/navbar.php'; ?>
            <main class="content px-3 py-2">
                <div class="container mt-4">
                    <h4><?= isset($cliente) ? 'EDITAR CLIENTE' : 'NUEVO CLIENTE' ?></h4>

                    <form action="<?= isset($cliente) ? '/clientes/' . urlencode($cliente['id']) . '/editar' : '/clientes/crear' ?>" method="POST">
                        <!-- Campo oculto para ID de cliente (solo si es edición) -->
                        <?php if (isset($cliente)): ?>
                            <input type="hidden" name="id" value="<?= htmlspecialchars($cliente['id']) ?>">
                        <?php endif; ?>

                        <!-- Botones -->
                        <div class="mb-3">
                            <button class="btn btn-primary"><?= isset($cliente) ? 'Actualizar' : 'Guardar' ?></button>
                            <a href="/clientes" class="btn btn-secondary">Retroceder</a>
                        </div>

                        <!-- DATOS PERSONALES -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <strong>DATOS PERSONALES</strong>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label>Documento</label>
                                        <input type="text" class="form-control" name="dni" placeholder="DNI" value="<?= htmlspecialchars($cliente['dni'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-primary w-100" id="reniecButtonCliente">RENIEC</button>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Paterno</label>
                                        <input readonly type="text" class="form-control" name="apellido_paterno" placeholder="Apellido Paterno" value="<?= htmlspecialchars($cliente['apellido_paterno'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Materno</label>
                                        <input readonly type="text" class="form-control" name="apellido_materno" placeholder="Apellido Materno" value="<?= htmlspecialchars($cliente['apellido_materno'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Nombres</label>
                                        <input readonly type="text" class="form-control" name="nombres" placeholder="Nombres" value="<?= htmlspecialchars($cliente['nombres'] ?? '') ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label>Dirección Actual</label>
                                        <input type="text" class="form-control" name="direccion" placeholder="Dirección" value="<?= htmlspecialchars($cliente['direccion'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Estado Civil</label>
                                        <select class="form-select" name="estado_civil">
                                            <option value="">Seleccione</option>
                                            <?php
                                            $estados = ['Soltero', 'Casado', 'Divorciado', 'Viudo'];
                                            foreach ($estados as $estado) {
                                                $selected = (isset($cliente['estado_civil']) && $cliente['estado_civil'] == $estado) ? 'selected' : '';
                                                echo "<option $selected>$estado</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Celular 1</label>
                                        <input type="text" class="form-control" name="celular1" placeholder="Nro Celular" value="<?= htmlspecialchars($cliente['celular1'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Celular 2</label>
                                        <input type="text" class="form-control" name="celular2" placeholder="Nro Celular" value="<?= htmlspecialchars($cliente['celular2'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>


                    </form>
                </div>
            </main>

            <a href="#" class="theme-toggle">
                <i class="fa-regular fa-moon"></i>
                <i class="fa-regular fa-sun"></i>
            </a>
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row text-muted">
                        <div class="col-6 text-start">
                            <p class="mb-0">
                                <a href="#" class="text-muted">
                                    <strong>NextMind</strong>
                                </a>
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="#" class="text-muted">Contact</a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="#" class="text-muted">About Us</a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="#" class="text-muted">Terms</a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="#" class="text-muted">Booking</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <?php include_once '../app/views/includes/chatbot.php'; ?>
    <script src="/js/admin/clientes/script.js"></script>
</body>

</html>