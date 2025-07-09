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
                    <!-- Gráfico de barras ancho completo -->
                    <div class="row mb-3">
                        <div class="col-12 mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Resumen General de Inversiones</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="graficoLineaGeneral" height="150" width="500"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dos gráficos debajo: barras y pie -->
                    <div class="row mb-4">
                        <!-- Gráfico de barras (más ancho) -->
                        <div class="col-md-6 mb-4 mb-md-0"> <!-- Aumentado a 8 columnas -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Inversiones Realizadas</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="graficoInversionesMensuales" height="140"></canvas> <!-- Altura aumentada -->
                                </div>
                            </div>
                        </div>

                        <!-- Gráfico de pastel (más compacto) -->
                        <div class="col-md-6"> <!-- Reducido a 4 columnas -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Pagos Recibidos</h5>
                                </div>
                                <div class="card-body text-center">
                                    <canvas id="graficoPagosMensuales"></canvas>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const labels = <?= json_encode($labels) ?>;
        const dataIngresos = <?= json_encode($dataIngresos) ?>;
        const dataPagos = <?= json_encode($dataPagos) ?>;

    </script>
    <script src="/js/cliente/reportes.js"></script>
</body>

</html>