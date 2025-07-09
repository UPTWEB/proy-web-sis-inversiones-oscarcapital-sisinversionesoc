<div class="h-100">
    <div class="sidebar-logo">
        <img onclick="window.location.href = '/inicio'" src="https://oscarcapitalperu.com/wp-content/uploads/2024/09/OscarCapital-blanco-1.png" alt="Logo">
    </div>
    <ul class="sidebar-nav">
        <li class="sidebar-header">
            Admin Elements
        </li>
        <li class="sidebar-item">
            <a href="/inicio" class="sidebar-link">
                <i class="fa-solid fa-house pe-2"></i>
                Inicio
            </a>
        </li>
        <li class="sidebar-item">
            <a href="/inversiones" class="sidebar-link"><i class="fa-solid fa-briefcase pe-2"></i>
                Inversiones
            </a>
        </li>
        <li class="sidebar-item">
            <a href="/abonos" class="sidebar-link collapsed" data-bs-target="#pagos" data-bs-toggle="collapse"
                aria-expanded="false"><i class="fa-solid fa-credit-card pe-2"></i>
                Abonos
            </a>
            <ul id="pagos" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="/abonos/pendientes" class="sidebar-link">Pendientes</a>
                </li>
                <li class="sidebar-item">
                    <a href="/abonos/efectuados" class="sidebar-link">Efectuados</a>
                </li>
            </ul>
        </li>
        <li class="sidebar-item">
            <a href="/reportes" class="sidebar-link"><i class="fa-regular fa-chart-pie pe-2"></i>
                Reportes
            </a>
        </li>
        
        <li class="sidebar-item">
            <a href="/calendario" class="sidebar-link "><i class="fa-regular fa-calendar-days pe-2"></i>
                Calendario
            </a>
        </li>
        <li class="sidebar-item">
            <a href="" class="sidebar-link "><i class="fa-regular fa-gear pe-2"></i>
                Configuracion
            </a>
        </li>
    </ul>
</div>
<script src="/js/admin/toogle.section.js"></script>