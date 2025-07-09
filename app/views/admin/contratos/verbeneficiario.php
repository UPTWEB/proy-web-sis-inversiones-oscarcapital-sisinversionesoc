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
            <main class="content px-3 py-4">
                <div class="container mt-4">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Datos de la Inversión</h4>
                        <a href="/contratos" class="btn btn-outline-secondary">Retroceder</a>
                    </div>

                    <!-- DATOS DE BENEFICIARIO PRINCIPAL -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <strong>DATOS DEL BENEFICIARIO PRINCIPAL</strong>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label>Documento</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['ben1_dni'] ?? '') ?></div>
                                </div>
                                <div class="col-md-3">
                                    <label>Apellido Paterno</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['ben1_apellido_paterno'] ?? '') ?></div>
                                </div>
                                <div class="col-md-3">
                                    <label>Apellido Materno</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['ben1_apellido_materno'] ?? '') ?></div>
                                </div>
                                <div class="col-md-3">
                                    <label>Nombres</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['ben1_nombres'] ?? '') ?></div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label>Dirección Actual</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['ben1_direccion'] ?? '') ?></div>
                                </div>
                                <div class="col-md-2">
                                    <label>Parentesco</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['parentesco_beneficiario1'] ?? '') ?></div>
                                </div>
                                <div class="col-md-2">
                                    <label>Celular 1</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['ben1_celular1'] ?? '') ?></div>
                                </div>
                                <?php if(!empty($inversion['ben1_celular2'])):?>
                                <div class="col-md-2">
                                    <label>Celular 2</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['ben1_celular2'] ?? '') ?></div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php if(!empty($inversion['ben2_dni'])):?>
                    <!-- DATOS DE BENEFICIARIO SECUNDARIO -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <strong>DATOS DEL BENEFICIARIO SECUNDARIO</strong>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label>Documento</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['ben2_dni'] ?? '') ?></div>
                                </div>
                                <div class="col-md-3">
                                    <label>Paterno</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['ben2_apellido_paterno'] ?? '') ?></div>
                                </div>
                                <div class="col-md-3">
                                    <label>Materno</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['ben2_apellido_materno'] ?? '') ?></div>
                                </div>
                                <div class="col-md-3">
                                    <label>Nombres</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['ben2_nombres'] ?? '') ?></div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label>Dirección Actual</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['ben2_direccion'] ?? '') ?></div>
                                </div>
                                <div class="col-md-2">
                                    <label>Parentesco</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['parentesco_beneficiario2'] ?? '') ?></div>
                                </div>
                                <div class="col-md-2">
                                    <label>Celular 1</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['ben2_celular1'] ?? '') ?></div>
                                </div>
                                <div class="col-md-2">
                                    <label>Celular 2</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['ben2_celular2'] ?? '') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>        
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
</body>

</html>