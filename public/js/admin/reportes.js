const ctx = document.getElementById("graficoLineaGeneral").getContext("2d");

const lineChart = new Chart(ctx, {
  type: "line",
  data: {
    labels: labels, // Etiquetas compartidas (meses)
    datasets: [
      {
        label: "Ingresos por Inversión",
        data: dataIngresos,
        fill: true,
        borderColor: "rgba(54, 162, 235, 1)",
        backgroundColor: "rgba(54, 162, 235, 0.2)",
        tension: 0.3,
        pointRadius: 4,
        pointBackgroundColor: "rgba(54, 162, 235, 1)",
      },
      {
        label: "Pagos Recaudados",
        data: dataPagos,
        fill: true,
        borderColor: "rgba(255, 99, 132, 1)",
        backgroundColor: "rgba(255, 99, 132, 0.2)",
        tension: 0.3,
        pointRadius: 4,
        pointBackgroundColor: "rgba(255, 99, 132, 1)",
      },
    ],
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: "top",
      },
      tooltip: {
        callbacks: {
          label: function (context) {
            return `S/ ${context.parsed.y.toFixed(2)}`;
          },
        },
      },
    },
    scales: {
      y: {
        beginAtZero: true,
        title: {
          display: true,
          text: "Monto (S/.)",
        },
      },
      x: {
        title: {
          display: true,
          text: "Mes",
        },
      },
    },
  },
});

const graficoBarrasClientes = new Chart(
  document.getElementById("graficoBarrasClientes"),
  {
    type: "bar",
    data: {
      labels: ["Cliente A", "Cliente B", "Cliente C"],
      datasets: [
        {
          label: "Monto Invertido",
          data: [5000, 3000, 7000],
          backgroundColor: "rgba(75, 192, 192, 0.7)",
        },
      ],
    },
    options: {
      responsive: true,
      indexAxis: "y",
      plugins: { legend: { display: false } },
    },
  }
);

new Chart(document.getElementById("graficoTopClientes").getContext("2d"), {
    type: 'bar',
    data: {
        labels: labelsTopClientes,
        datasets: [{
            label: 'Inversión total (PEN)',
            data: dataTopClientes,
            backgroundColor: 'rgba(75, 192, 192, 0.7)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        // plugins: {
        //     title: {
        //         display: true,
        //         text: 'Top 5 Clientes que más han invertido (histórico en PEN)'
        //     }
        // },
        scales: {
            x: {
                beginAtZero: true,
                title: { display: true, text: 'Monto (PEN)' }
            },
            y: {
                title: { display: true, text: 'Cliente' }
            }
        }
    }
});

const graficoPiePagos = new Chart(document.getElementById("graficoPiePagos"), {
  type: "pie",
  data: {
    labels: ["Pagos Efectuados", "Pagos Pendientes"],
    datasets: [
      {
        data: [
          dataEstadoPagos["Recaudado"] || 0,
          dataEstadoPagos["Pendiente"] || 0,
        ],
        backgroundColor: ["#28a745", "#ffc107"],
      },
    ],
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: "bottom",
      },
    },
  },
});
