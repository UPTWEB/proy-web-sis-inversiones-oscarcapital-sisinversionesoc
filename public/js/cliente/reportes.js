
const ctxGeneral = document.getElementById("graficoLineaGeneral").getContext("2d");
new Chart(ctxGeneral, {
  type: "line",
  data: {
    labels: labels,
    datasets: [
      {
        label: "Inversiones",
        data: dataIngresos,
        fill: true,
        borderColor: "rgba(54, 162, 235, 1)",
        backgroundColor: "rgba(54, 162, 235, 0.2)",
        tension: 0.3,
        pointRadius: 4,
      },
      {
        label: "Abonos",
        data: dataPagos,
        fill: true,
        borderColor: "rgba(255, 99, 132, 1)",
        backgroundColor: "rgba(255, 99, 132, 0.2)",
        tension: 0.3,
        pointRadius: 4,
      },
    ],
  },
  options: {
    responsive: true,
    plugins: {
      legend: { position: "top" },
      tooltip: {
        callbacks: {
          label: context => `S/ ${context.parsed.y.toFixed(2)}`
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        title: { display: true, text: "Monto (S/.)" }
      },
      x: {
        title: { display: true, text: "Mes" }
      }
    }
  }
});

const ctxInversiones = document.getElementById("graficoInversionesMensuales").getContext("2d");
new Chart(ctxInversiones, {
  type: 'bar',
  data: {
    labels: labels,
    datasets: [{
      label: 'Inversiones por mes (S/.)',
      data: dataIngresos,
      backgroundColor: 'rgba(75, 192, 192, 0.6)',
      borderColor: 'rgba(75, 192, 192, 1)',
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true,
        title: { display: true, text: 'Monto (S/.)' }
      },
      x: {
        title: { display: true, text: 'Mes' }
      }
    }
  }
});

const ctxPagos = document.getElementById("graficoPagosMensuales").getContext("2d");
new Chart(ctxPagos, {
  type: 'bar',
  data: {
    labels: labels,
    datasets: [{
      label: 'Pagos recibidos por mes (S/.)',
      data: dataPagos,
      backgroundColor: 'rgba(255, 159, 64, 0.6)',
      borderColor: 'rgba(255, 159, 64, 1)',
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true,
        title: { display: true, text: 'Monto (S/.)' }
      },
      x: {
        title: { display: true, text: 'Mes' }
      }
    }
  }
});

