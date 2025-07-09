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
            <?php require_once '../app/views/includes/cliente/sidebar.php'; ?>
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
                        <h4 class="mb-0">Datos del Pago</h4>
                        <?php
                        $origen = isset($_SESSION['origen']) ? $_SESSION['origen'] : '/abonos/efectuados';
                        unset($_SESSION['origen']); // Limpiar para evitar usarla incorrectamente después
                        ?>
                        <a href="<?= htmlspecialchars($origen) ?>" class="btn btn-outline-secondary">Retroceder</a>
                    </div>

                    <!-- DATOS DEL PAGO -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <strong>Datos del Pago</strong>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">DNI</label>
                                    <div class="form-control"><?= htmlspecialchars($pago['dni']) ?></div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Cliente</label>
                                    <div class="form-control"><?= htmlspecialchars($pago['nombres'] . ' ' . $pago['apellido_paterno'] . ' ' . $pago['apellido_materno']) ?></div>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Pago N°</label>
                                    <div class="form-control"><?= htmlspecialchars($pago['numero_pago']) ?></div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Fecha</label>
                                    <div class="form-control"><?= htmlspecialchars($pago['fecha']) ?></div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Monto</label>
                                    <div class="form-control"><?= htmlspecialchars($pago['monto']) . " " . htmlspecialchars($pago['moneda']) ?></div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Plan</label>
                                    <div class="form-control">
                                        <?php
                                        $plan = (int)$pago['plan_inversion'];
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
                                    <div class="form-control"><?= htmlspecialchars($pago['nombre_banco']) ?></div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Cuenta Bancaria</label>
                                    <div class="form-control"><?= htmlspecialchars($pago['cuenta_bancaria']) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Código de Cuenta Interbancario (CCI)</label>
                                    <div class="form-control"><?= htmlspecialchars($pago['cuenta_interbancaria']) ?></div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Billetera Móvil</label>
                                    <div class="form-control"><?= htmlspecialchars($pago['billetera_movil']) ?></div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nro de Celular</label>
                                    <div class="form-control"><?= htmlspecialchars($pago['celular']) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COMPROBANTE -->
                    <?php if (!empty($pago['comprobante'])): ?>
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-warning text-dark">
                                <strong>Comprobante de Pago</strong>
                            </div>
                            <div class="card-body text-center">
                                <img src="/<?= htmlspecialchars($pago['comprobante']) ?>" alt="Comprobante" class="img-fluid" style="max-height: 400px;">
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>