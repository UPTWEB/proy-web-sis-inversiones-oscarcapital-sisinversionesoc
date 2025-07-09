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
                <div class="container-fluid mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>CONTRATOS</h4>
                        <div>
                            <a href="/inversiones/crear" class="btn btn-primary">Nuevo</a>
                            <a href="#" class="btn btn-info text-white">CSV</a>
                            <a href="#" class="btn btn-success">EXCEL</a>
                            <a href="#" class="btn btn-danger">PDF</a>
                            <a href="#" class="btn btn-secondary">IMPRIMIR</a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <input type="text" class="form-control" placeholder="Buscar" id="search-table">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nro</th>
                                    <th>Cliente</th>
                                    <th>Inscripción</th>
                                    <th>Plan</th>
                                    <th>Meses</th>
                                    <th>Imprimir</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                <?php if (!empty($inversiones)): ?>
                                    <?php foreach ($inversiones as $i): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($i['id']) ?></td>
                                            <td><?= htmlspecialchars($i['nombres']) . " " . htmlspecialchars($i['apellido_paterno']) . " " . htmlspecialchars($i['apellido_materno']) ?></td>
                                            <td><?= htmlspecialchars($i['fecha_inicio']) ?></td>
                                            <td><?php
                                                $plan = (int)$i['plan_inversion'];
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
                                            </td>
                                            <td><?= htmlspecialchars($i['meses']) ?></td>
                                            <td>
                                                <a href="contratos/<?= htmlspecialchars($i['id']) ?>" class="btn btn-success btn-sm">Contrato</a>
                                                <a href="/contratos/<?= htmlspecialchars($i['id']) ?>/calendario" class="btn btn-primary btn-sm">Calendario</a>
                                                <a href="/contratos/<?= htmlspecialchars($i['id']) ?>/beneficiario" class="btn btn-danger btn-sm">Beneficiario</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8">No hay clientes registrados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center" id="pagination-controls">
                            <!-- controles con js-->
                        </ul>
                    </nav>
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
    <script src="/js/admin/pagination.table.js"></script>
</body>

</html>