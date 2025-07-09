
new TomSelect('#dniSelect', {
  valueField: 'id',
  labelField: 'dni',
  searchField: ['dni'],
  load: function(query, callback) {
    if (query.length < 2) return callback();
    fetch('/inversiones/select_cliente/' + encodeURIComponent(query))
      .then(response => response.json())
      .then(callback)
      .catch(() => callback());
  },
  render: {
    option: function(item) {
      return `<div>${item.dni}</div>`;
    },
    item: function(item) {
      return `<div>${item.dni}</div>`;
    }
  }
});

document.getElementById('dniSelect').addEventListener('change', function () {
  const dni = this.value;
  if (!dni) return;

  fetch('/clientes/' + dni + '/verAjax')
    .then(response => response.json())
    .then(data => {
      if (data.nombres) {
        document.getElementById('nombre').value = data.apellido_paterno + ' ' + data.apellido_materno + ', ' + data.nombres;
      } else {
        document.getElementById('nombre').value = "No encontrado";
      }
    })
    .catch(error => {
      console.error("Error:", error);
      document.getElementById('nombre').value = "Error de conexión";
    });
});
