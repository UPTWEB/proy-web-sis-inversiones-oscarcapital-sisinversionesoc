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

    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">

    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

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
                    <h4><?= isset($inversion) ? 'EDITAR INVERSIÓN' : 'NUEVA INVERSIÓN' ?></h4>

                    <form id="form-inversion" action="<?= isset($inversion) ? '/inversiones/' . urlencode($inversion['id']) . '/editar' : '/inversiones/crear' ?>" method="POST">
                        <?php if (isset($inversion)): ?>
                            <input type="hidden" name="id" value="<?= htmlspecialchars($inversion['id']) ?>">
                        <?php endif; ?>

                        <!-- Botones -->
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary"><?= isset($inversion) ? 'Actualizar' : 'Guardar' ?></button>
                            <a href="/inversiones" class="btn btn-secondary">Retroceder</a>
                        </div>

                        <!-- DATOS DE LA INVERSIÓN -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <strong>DATOS DE LA INVERSIÓN</strong>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <!-- Cliente -->
                                    <div class="col-md-3">
                                        <label>DNI</label>
                                        <select id="dniSelect" class="tomselect" name="cliente_id" placeholder="Seleccione">
                                            <?php if (isset($inversion)): ?>
                                                <option value="<?= $inversion['cliente_id'] ?>" selected>
                                                    <?= $inversion['dni'] ?>
                                                </option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Nombre</label>
                                        <input type="text" class="form-control" id="nombre" placeholder="Nombre del cliente" readonly>
                                    </div>
                                    <!-- Plan de inversión -->
                                    <div class="col-md-3">
                                        <label>Plan</label>
                                        <select class="form-select" id="planInversion">
                                            <option value="">Seleccione</option>
                                            <option value="1">Mensual</option>
                                            <option value="6">Semestral</option>
                                            <option value="12">Anual</option>
                                            <option value="personalizado">Personalizado</option>
                                        </select>
                                        <input type="hidden" name="plan_inversion" id="planInversionHidden" value="<?= htmlspecialchars($inversion['plan_inversion'] ?? '') ?>">
                                    </div>

                                    <!-- Personalizado -->
                                    <div class="col-md-2" id="grupoPersonalizado" style="display: none;">
                                        <label>Cada cuántos meses</label>
                                        <input type="number" class="form-control" id="frecuenciaPersonalizada" min="1" placeholder="Ej: 3">
                                    </div>


                                </div>

                                <div class="row mb-3">
                                    <!-- Porcentaje -->
                                    <div class="col-md-2">
                                        <label>Porcentaje %</label>
                                        <input type="number" class="form-control" name="porcentaje" placeholder="%" value="<?= htmlspecialchars($inversion['porcentaje'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Moneda</label>
                                        <select class="form-select" name="moneda">
                                            <?php
                                            $monedas = ['PEN', 'USD', 'CLP'];
                                            foreach ($monedas as $moneda) {
                                                $selected = (isset($inversion['moneda']) && $inversion['moneda'] == $moneda) ? 'selected' : '';
                                                echo "<option value=\"$moneda\" $selected>$moneda</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Monto</label>
                                        <input type="number" step="0.01" class="form-control" name="monto" placeholder="0.00" value="<?= htmlspecialchars($inversion['monto'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Meses</label>
                                        <input type="number" class="form-control" name="meses" id="mesesInput" placeholder="Múltiplo del plan" value="<?= htmlspecialchars($inversion['meses'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Fecha Inicio</label>
                                        <input type="date" class="form-control" name="fecha_inicio" id="fechaInicio" value="<?= htmlspecialchars($inversion['fecha_inicio'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Fecha Calculada</label>
                                        <input type="date" class="form-control" name="fecha_calculada" id="fechaCalculada" value="<?= htmlspecialchars($inversion['fecha_calculada'] ?? '') ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- MEDIOS DE PAGO -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <strong>MEDIOS DE PAGO</strong>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label>Banco</label>
                                        <input type="text" class="form-control" name="nombre_banco" placeholder="Nombre" value="<?= htmlspecialchars($inversion['nombre_banco'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Cuenta Bancaria</label>
                                        <input type="text" class="form-control" name="cuenta_bancaria" placeholder="Nro Cuenta Bancaria" value="<?= htmlspecialchars($inversion['cuenta_bancaria'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Código de Cuenta Interbancario (CCI)</label>
                                        <input type="text" class="form-control" name="cuenta_interbancaria" placeholder="Nro de CCI" value="<?= htmlspecialchars($inversion['cuenta_interbancaria'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label>Billetera Móvil</label>
                                        <select class="form-select" name="billetera_movil">
                                            <option value="">Seleccione</option>
                                            <?php
                                            $billeteras = ['Yape', 'Plin'];
                                            foreach ($billeteras as $billetera) {
                                                $selected = (isset($inversion['billetera_movil']) && $inversion['billetera_movil'] == $billetera) ? 'selected' : '';
                                                echo "<option value=\"$billetera\" $selected>$billetera</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Nro de Celular</label>
                                        <input type="text" class="form-control" name="celular" placeholder="Nro Celular" value="<?= htmlspecialchars($inversion['celular'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- DATOS DE BENEFICIARIO PRINCIPAL -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <strong>DATOS DEL BENEFICIARIO PRINCIPAL (OBLIGATORIO)</strong>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label>Documento</label>
                                        <input type="text" class="form-control" name="ben1_dni" placeholder="DNI" value="<?= htmlspecialchars($inversion['ben1_dni'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-primary w-100" id="reniecButtonBeneficiario1">RENIEC</button>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Paterno</label>
                                        <input readonly type="text" class="form-control" name="ben1_apellido_paterno" placeholder="Apellido Paterno" value="<?= htmlspecialchars($inversion['ben1_apellido_paterno'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Materno</label>
                                        <input readonly type="text" class="form-control" name="ben1_apellido_materno" placeholder="Apellido Materno" value="<?= htmlspecialchars($inversion['ben1_apellido_materno'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Nombres</label>
                                        <input readonly type="text" class="form-control" name="ben1_nombres" placeholder="Nombres" value="<?= htmlspecialchars($inversion['ben1_nombres'] ?? '') ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label>Dirección Actual</label>
                                        <input type="text" class="form-control" name="ben1_direccion" placeholder="Dirección" value="<?= htmlspecialchars($inversion['ben1_direccion'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Parentesco</label>
                                        <select class="form-select" name="parentesco1">
                                            <option value="">Seleccione</option>
                                            <?php
                                            $parentescos = ['Padre', 'Madre', 'Hijo', 'Hermano', 'Otro'];
                                            foreach ($parentescos as $parentezco) {
                                                $selected = (isset($inversion['parentesco_beneficiario1']) && $inversion['parentesco_beneficiario1'] == $parentezco) ? 'selected' : '';
                                                echo "<option $selected>$parentezco</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Celular 1</label>
                                        <input type="text" class="form-control" name="ben1_celular1" placeholder="Nro Celular" value="<?= htmlspecialchars($inversion['ben1_celular1'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Celular 2</label>
                                        <input type="text" class="form-control" name="ben1_celular2" placeholder="Nro Celular" value="<?= htmlspecialchars($inversion['ben1_celular2'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- DATOS DE BENEFICIARIO SECUNDARIO -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <strong>DATOS DEL BENEFICIARIO SECUNDARIO</strong>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label>Documento</label>
                                        <input type="text" class="form-control" name="ben2_dni" placeholder="DNI" value="<?= htmlspecialchars($inversion['ben2_dni'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-primary w-100" id="reniecButtonBeneficiario2">RENIEC</button>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Paterno</label>
                                        <input readonly type="text" class="form-control" name="ben2_apellido_paterno" placeholder="Apellido Paterno" value="<?= htmlspecialchars($inversion['ben2_apellido_paterno'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Materno</label>
                                        <input readonly type="text" class="form-control" name="ben2_apellido_materno" placeholder="Apellido Materno" value="<?= htmlspecialchars($inversion['ben2_apellido_materno'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Nombres</label>
                                        <input readonly type="text" class="form-control" name="ben2_nombres" placeholder="Nombres" value="<?= htmlspecialchars($inversion['ben2_nombres'] ?? '') ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label>Dirección Actual</label>
                                        <input type="text" class="form-control" name="ben2_direccion" placeholder="Dirección" value="<?= htmlspecialchars($inversion['ben2_direccion'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Parentesco</label>
                                        <select class="form-select" name="parentesco2">
                                            <option value="">Seleccione</option>
                                            <?php
                                            $parentescos = ['Padre', 'Madre', 'Hijo', 'Hermano', 'Otro'];
                                            foreach ($parentescos as $parentezco) {
                                                $selected = (isset($inversion['parentesco_beneficiario2']) && $inversion['parentesco_beneficiario2'] == $parentezco) ? 'selected' : '';
                                                echo "<option $selected>$parentezco</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Celular 1</label>
                                        <input type="text" class="form-control" name="ben2_celular1" placeholder="Nro Celular" value="<?= htmlspecialchars($inversion['ben2_celular1'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Celular 2</label>
                                        <input type="text" class="form-control" name="ben2_celular2" placeholder="Nro Celular" value="<?= htmlspecialchars($inversion['ben2_celular2'] ?? '') ?>">
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
    <script src="/js/admin/inversiones/script.js"></script>
    <script src="/js/admin/inversiones/crearinversion.js"></script>
</body>

</html>