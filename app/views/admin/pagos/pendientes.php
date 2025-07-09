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
                        <h4>PAGOS PENDIENTES <?= isset($verTodos) && $verTodos ? '(TODOS)' : '(PRÓXIMOS)' ?></h4>
                        <div>
                            <?php if (isset($verTodos) && $verTodos): ?>
                                <a href="/pagos/pendientes" class="btn btn-warning me-2">
                                    <i class="fas fa-filter"></i> Solo Próximos
                                </a>
                            <?php else: ?>
                                <a href="/pagos/pendientes?todos=1" class="btn btn-info me-2">
                                    <i class="fas fa-list"></i> Ver Todos
                                </a>
                            <?php endif; ?>
                            <a href="/export/pagosPendientes/csv<?= isset($verTodos) && $verTodos ? '?todos=1' : '' ?>" class="btn btn-info text-white">CSV</a>
                            <a href="/export/pagosPendientes/excel<?= isset($verTodos) && $verTodos ? '?todos=1' : '' ?>" class="btn btn-success">EXCEL</a>
                            <a href="#" class="btn btn-danger">PDF</a>
                            <a href="#" class="btn btn-secondary">IMPRIMIR</a>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $_SESSION['error'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <div class="mb-3">
                        <input type="text" class="form-control" placeholder="Buscar" id="search-table">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center align-middle" id="tabla-pagos">
                            <thead class="table-light">
                                <tr>
                                    <th>Nro</th>
                                    <th>Cliente</th>
                                    <th>Monto</th>
                                    <th>N° Pago</th>
                                    <th>fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                <?php if (!empty($pagos)): ?>
                                    <?php foreach ($pagos as $i): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($i['id']) ?></td>
                                            <td><?= htmlspecialchars($i['nombres']) . " " . htmlspecialchars($i['apellido_paterno']) . " " . htmlspecialchars($i['apellido_materno']) ?></td>
                                            <td><?= htmlspecialchars($i['monto']) . " " . htmlspecialchars($i['moneda']) ?></td>
                                            <td>
                                                <?= htmlspecialchars($i['numero_pago']) ?>
                                                <?php if (!isset($verTodos) || !$verTodos): ?>
                                                    <span class="badge bg-success ms-1">Próximo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($i['fecha']) ?></td>
                                            <td>
                                                <a href="/pagos/<?= htmlspecialchars($i['id'])?>" class="btn btn-success btn-sm w-25">Ver</a>
                                                <a href="/pagos/<?= htmlspecialchars($i['id'])?>/registrar" class="btn btn-primary btn-sm">Registrar</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6">
                                            <?= isset($verTodos) && $verTodos ? 'No hay pagos pendientes.' : 'No hay próximos pagos pendientes.' ?>
                                        </td>
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
    <script src="/js/admin/script.js"></script>
    <script src="/js/admin/pagination.table.js"></script>
</body>

</html>