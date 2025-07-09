const planSelect = document.getElementById("planInversion");
const grupoPersonalizado = document.getElementById("grupoPersonalizado");
const frecuenciaInput = document.getElementById("frecuenciaPersonalizada");
const planInversionHidden = document.getElementById("planInversionHidden");
const mesesInput = document.getElementById("mesesInput");
const fechaInicioInput = document.getElementById("fechaInicio");
const fechaCalculadaInput = document.getElementById("fechaCalculada");

function actualizarEstadoFrecuencia() {
  const valor = planSelect.value;
  if (valor === "personalizado") {
    grupoPersonalizado.style.display = "block";
    frecuenciaInput.disabled = false;
    planInversionHidden.value = frecuenciaInput.value || "";
  } else {
    grupoPersonalizado.style.display = "none";
    frecuenciaInput.value = "";
    frecuenciaInput.disabled = true;
    planInversionHidden.value = valor;
  }
}

function sincronizarFrecuenciaPersonalizada() {
  if (planSelect.value === "personalizado") {
    planInversionHidden.value = frecuenciaInput.value || "";
  }
  validarMultiplo();
}

function validarMultiplo() {
  const frecuencia = parseInt(planInversionHidden.value || 0);
  const meses = parseInt(mesesInput.value || 0);
  if (frecuencia > 0 && meses % frecuencia !== 0) {
    mesesInput.setCustomValidity(
      `El número de meses debe ser múltiplo de ${frecuencia}.`
    );
  } else {
    mesesInput.setCustomValidity("");
  }
}

function calcularFechaFinal() {
  const fechaInicio = new Date(fechaInicioInput.value);
  const meses = parseInt(mesesInput.value || 0);
  if (!isNaN(fechaInicio.getTime()) && meses > 0) {
    const fechaCalculada = new Date(fechaInicio);
    fechaCalculada.setMonth(fechaCalculada.getMonth() + meses);
    fechaCalculadaInput.value = fechaCalculada.toISOString().split("T")[0];
  }
}

planSelect.addEventListener("change", () => {
  actualizarEstadoFrecuencia();
  validarMultiplo();
});

frecuenciaInput.addEventListener("input", sincronizarFrecuenciaPersonalizada);
mesesInput.addEventListener("input", () => {
  validarMultiplo();
  calcularFechaFinal();
});
fechaInicioInput.addEventListener("input", calcularFechaFinal);

// Inicializar estado
actualizarEstadoFrecuencia();

//RENIEC
// RENIEC para beneficiario principal (ben1_*)
document
  .getElementById("reniecButtonBeneficiario1")
  .addEventListener("click", function () {
    const cardBody = this.closest(".card-body");
    const dniInput = cardBody.querySelector('input[name="ben1_dni"]');
    const dni = dniInput.value.trim();

    if (dni.length !== 8) {
      alert("Ingrese un DNI válido de 8 dígitos.");
      return;
    }

    fetch("/clientes/consultar_dni", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "dni=" + encodeURIComponent(dni),
    })
      .then((response) => response.json())
      .then((response) => {
        if (response.success) {
          const persona = response.data;

          cardBody.querySelector('input[name="ben1_apellido_paterno"]').value =
            persona.apellido_paterno;
          cardBody.querySelector('input[name="ben1_apellido_materno"]').value =
            persona.apellido_materno;
          cardBody.querySelector('input[name="ben1_nombres"]').value =
            persona.nombres;
          cardBody.querySelector('input[name="ben1_direccion"]').value =
            persona.direccion_completa;
        } else {
          alert("Error: " + response.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  });

// RENIEC para beneficiario secundario (ben2_*)
document
  .getElementById("reniecButtonBeneficiario2")
  .addEventListener("click", function () {
    const cardBody = this.closest(".card-body");
    const dniInput = cardBody.querySelector('input[name="ben2_dni"]');
    const dni = dniInput.value.trim();

    if (dni.length !== 8) {
      alert("Ingrese un DNI válido de 8 dígitos.");
      return;
    }

    fetch("/clientes/consultar_dni", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "dni=" + encodeURIComponent(dni),
    })
      .then((response) => response.json())
      .then((response) => {
        if (response.success) {
          const persona = response.data;

          cardBody.querySelector('input[name="ben2_apellido_paterno"]').value =
            persona.apellido_paterno;
          cardBody.querySelector('input[name="ben2_apellido_materno"]').value =
            persona.apellido_materno;
          cardBody.querySelector('input[name="ben2_nombres"]').value =
            persona.nombres;
          cardBody.querySelector('input[name="ben2_direccion"]').value =
            persona.direccion_completa;
        } else {
          alert("Error: " + response.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  });
