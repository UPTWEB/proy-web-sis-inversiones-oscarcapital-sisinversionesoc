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
            <nav class="navbar navbar-expand px-3 border-bottom">
                <button class="btn" id="sidebar-toggle" type="button">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="navbar-collapse navbar">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a href="#" data-bs-toggle="dropdown" class="nav-icon pe-md-0">
                                <img src="/images/dashboard/profile.jpg" class="avatar img-fluid rounded" alt="">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="#" class="dropdown-item">Profile</a>
                                <a href="#" class="dropdown-item">Setting</a>
                                <a href="#" class="dropdown-item">Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
            <main class="content px-3 py-4">
                <div class="container mt-4">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Datos de la Inversión</h4>
                        <a href="/inversiones" class="btn btn-outline-secondary">Retroceder</a>
                    </div>

                    <!-- DATOS DE LA INVERSIÓN -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <strong>Datos de la Inversión</strong>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Cliente</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['dni']) ?></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Cliente</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['nombres']) . " " . htmlspecialchars($inversion['apellido_paterno']) . " " . htmlspecialchars($inversion['apellido_materno']) ?></div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Plan</label>
                                    <div class="form-control">
                                        <?php
                                        $plan = (int)$inversion['plan_inversion'];
                                        switch ($plan) {
                                            case 1:
                                                echo "Mensual";
                                                break;
                                            case 6:
                                                echo "Semestral";
                                                break;
                                            case 12:
                                                echo "Anual";
                                                break;
                                            default:
                                                echo $plan . " Meses";
                                                break;
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Porcentaje %</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['porcentaje']) ?></div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Moneda</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['moneda']) ?></div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Monto</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['monto']) ?></div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Meses</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['meses']) ?></div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Fecha Inicio</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['fecha_inicio']) ?></div>
                                </div>
                                <!-- <div class="col-md-2">
                                    <label class="form-label">Mes Calculado</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['meses']) ?></div>
                                </div> -->
                                <div class="col-md-3">
                                    <label class="form-label">Fecha Calculada</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['fecha_calculada']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MEDIOS DE PAGO -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <strong>Medios de Pago</strong>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Banco</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['nombre_banco']) ?></div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Cuenta Bancaria</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['cuenta_bancaria']) ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Código de Cuenta Interbancario (CCI)</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['cuenta_interbancaria']) ?></div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Billetera Móvil</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['billetera_movil']) ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nro de Celular</label>
                                    <div class="form-control"><?= htmlspecialchars($inversion['celular']) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

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