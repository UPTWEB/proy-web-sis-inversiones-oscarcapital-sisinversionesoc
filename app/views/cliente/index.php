<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Admin Dashboard</title>
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

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
            <?php require_once '../app/views/includes/navbar.php'; ?>
            <main class="content px-3 py-2">
                <div class="container-fluid">
                    <!-- Dashboard Cards -->
                    <div class="row mt-4 mb-4">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Ganancias</h5>
                                    <h2 class="mb-0"><?= $ganancias ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Inversiones</h5>
                                    <h2 class="mb-0"><?= $cantidadInversiones ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Monto Invertido</h5>
                                    <h2 class="mb-0"><?= $montoInvertido ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-warning text-dark h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Monto Ganado</h5>
                                    <h2 class="mb-0"><?= $montoGanado ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deposits Section -->
                    <div class="row mb-4 d-flex flex-column flex-md-row align-items-stretch">
                        <!-- Chart Section -->
                        <div class="col-md-7 d-flex" style="flex: 0 0 65%;">
                            <div class="card flex-fill">
                                <div class="card-header">
                                    <h5 class="card-title">Total Depósitos Mensuales</h5>
                                </div>
                                <div class="card-body d-flex align-items-center">
                                    <div class="chart-container w-100">
                                        <canvas id="ingresosChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Calendar Section -->
                        <div class="col-md-5 d-flex" style="flex: 0 0 35%;">
                            <div class="card flex-fill">
                                <div class="card-header">
                                    <h5 class="card-title">Calendario</h5>
                                </div>
                                <div class="card-body d-flex align-items-center">
                                    <div id="calendar" class="w-100"></div>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const id_cliente = <?= $id ?>;
        const ingresosLabels = <?= json_encode($labelsIngresos) ?>;
        const ingresosData = <?= json_encode($dataIngresos) ?>;
    </script>
    <script src="/js/cliente/index.js"></script>
    <script src="/js/admin/script.js"></script>
    <script src="/js/admin/pagination.table.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>