<?php
?>
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
                        <h4>CLIENTES</h4>
                        <div>
                            <a href="/clientes/crear" class="btn btn-primary">Nuevo</a>
                            <a href="/export/clientes/csv" class="btn btn-info text-white">CSV</a>
                            <a href="/export/clientes/excel" class="btn btn-success">EXCEL</a>
                            <a href="#" class="btn btn-danger">PDF</a>
                            <a href="#" class="btn btn-secondary">IMPRIMIR</a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <input type="text" class="form-control" placeholder="Buscar" id="search-table">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center align-middle" id="tabla-clientes">
                            <thead class="table-light">
                                <tr>
                                    <th>DNI</th>
                                    <th>Cliente</th>
                                    <th>Direccion</th>
                                    <th>Celular</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                <?php if (!empty($clientes)): ?>
                                    <?php foreach ($clientes as $c): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($c['dni']) ?></td>
                                            <td><?= htmlspecialchars($c['nombres']) . " " . htmlspecialchars($c['apellido_paterno']) . " " . htmlspecialchars($c['apellido_materno']) ?>
                                            </td>
                                            <td><?= htmlspecialchars($c['direccion']) ?></td>
                                            <td><?= htmlspecialchars($c['celular1']) ?></td>
                                            <td><?= htmlspecialchars($c['estado_civil']) ?></td>
                                            <td>
                                                <a href="/clientes/<?= htmlspecialchars($c['id']) ?>" class="btn btn-success btn-sm">Ver</a>
                                                <a href="/clientes/<?= htmlspecialchars($c['id']) ?>/editar" class="btn btn-primary btn-sm">Editar</a>
                                                <form action="/clientes/eliminar" method="POST" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?= htmlspecialchars($c['id']) ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro que deseas eliminar?');">Eliminar</button>
                                                </form>
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
                            <!-- Los controles de paginación se generarán dinámicamente con JavaScript -->
                        </ul>
                    
                        <!-- <ul class="pagination justify-content-center">
                            <li class="page-item disabled"><a class="page-link" href="#">«</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">4</a></li>
                        </ul> -->
                        
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