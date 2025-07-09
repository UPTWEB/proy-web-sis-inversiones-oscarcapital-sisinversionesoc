document.getElementById("reniecButtonCliente").addEventListener("click", function() {
    const dniInput = document.querySelector('input[name="dni"]');
    const dni = dniInput.value.trim();

    if (dni.length !== 8) {
        alert("Ingrese un DNI válido de 8 dígitos.");
        return;
    }

    fetch('/clientes/consultar_dni', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'dni=' + encodeURIComponent(dni)
        })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                const persona = response.data;
                console.log(persona); // Para debug

                // Rellenar los campos correctos
                document.querySelector('input[name="apellido_paterno"]').value = persona.apellido_paterno;
                document.querySelector('input[name="apellido_materno"]').value = persona.apellido_materno;
                document.querySelector('input[name="nombres"]').value = persona.nombres;
                document.querySelector('input[name="direccion"]').value = persona.direccion_completa;

            } else {
                alert("Error: " + response.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
});
document.getElementById("reniecButtonBeneficiario").addEventListener("click", function() {
    const dniInput = document.querySelector('input[name="ben_dni"]');
    const dni = dniInput.value.trim();

    if (dni.length !== 8) {
        alert("Ingrese un DNI válido de 8 dígitos.");
        return;
    }

    fetch('/clientes/consultar_dni', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'dni=' + encodeURIComponent(dni)
        })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                const persona = response.data;
                console.log(persona); // Para debug

                // Rellenar los campos correctos
                document.querySelector('input[name="ben_apellido_paterno"]').value = persona.apellido_paterno;
                document.querySelector('input[name="ben_apellido_materno"]').value = persona.apellido_materno;
                document.querySelector('input[name="ben_nombres"]').value = persona.nombres;
                document.querySelector('input[name="ben_direccion"]').value = persona.direccion_completa;

            } else {
                alert("Error: " + response.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
});
