document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("ingresosChart");
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ingresosLabels,
                datasets: [{
                    label: 'Ingresos por mes (S/.)',
                    data: ingresosData,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Monto (S/.)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Fecha'
                        }
                    }
                }
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        events: `/inicio/eventosCalendario/${id_cliente}`, // ✅ ruta dinámica
        eventClick: function (info) {
            info.jsEvent.preventDefault();
            if (info.event.url) {
                window.location.href = info.event.url;
            }
        }
    });

    calendar.render();
});
