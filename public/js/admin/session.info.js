async function getClientIp() {
  try {
    const response = await fetch('https://api.ipify.org');
    const ip = await response.text();
    return ip;
  } catch (error) {
    console.error('Error fetching IP:', error);
    return '127.0.0.1';
  }
}

if (localStorage.getItem('ipClient')) {
  document.getElementById("ipLoad").innerHTML = localStorage.getItem('ipClient');
} else {
  getClientIp().then((ip) => {
    document.getElementById("ipLoad").innerHTML = ip;
  });
}

let segundosC = 0;
let intervaloC;

// function iniciarCronometro(inicio = (localStorage.getItem('cronometroCount') != null) ? parseInt(localStorage.getItem('cronometroCount')) : 0 ) {
//   segundosC = inicio;
//   clearInterval(intervaloC);
//   intervaloC = setInterval(() => {
//     const h = String(Math.floor(segundosC / 3600)).padStart(2, '0');
//     const m = String(Math.floor((segundosC % 3600) / 60)).padStart(2, '0');
//     const s = String(segundosC % 60).padStart(2, '0');
//     // console.log(`${h}:${m}:${s}`);
//     segundosC++;
//     console.log(segundosC);
//     const elapsedTimeElement = document.getElementById("elapsed-Time");
//     if (elapsedTimeElement) {
//         elapsedTimeElement.innerHTML = h+':'+m+':'+s;
//     }
//     // localStorage.setItem('cronometroCount', segundosC);
//   }, 1000);
// }



async function iniciarCronometro() {
  try {
    const response = await fetch("/api/auth/TimeSesion", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Internal-Request": "1"
      }
    });

    const result = await response.json();

    if (result.status === 'success') {
      const tiempo = result.time; 
      const [horas, minutos, segundos] = tiempo.split(':').map(Number);
      segundosC = (horas * 3600) + (minutos * 60) + segundos;
    } else {
      segundosC = 0;
    }

  } catch (error) {
    console.error("Error al obtener el tiempo de sesión:", error);
    segundosC = 0;
  }

  clearInterval(intervaloC);
  intervaloC = setInterval(() => {
    const h = String(Math.floor(segundosC / 3600)).padStart(2, '0');
    const m = String(Math.floor((segundosC % 3600) / 60)).padStart(2, '0');
    const s = String(segundosC % 60).padStart(2, '0');

    const elapsedTimeElement = document.getElementById("elapsed-Time");
    if (elapsedTimeElement) {
      elapsedTimeElement.innerHTML = `${h}:${m}:${s}`;
    }

    segundosC++;
    console.log(segundosC);
  }, 1000);
}





// function reiniciarCronometro() {
//   localStorage.setItem('cronometroCount', 0);
// }

iniciarCronometro();


let sesionCerradaManual = false;

window.addEventListener('beforeunload', () => {
  
  // localStorage.setItem('cronometroCount', segundosC);
  if (!sesionCerradaManual) {
    disconnect();
  }

});

function disconnect() {
  const data = {
    // ip: localStorage.getItem('ipClient'),
    tiempo: segundosC
  };

  const blob = new Blob([JSON.stringify(data)], { type: 'application/json' });

  navigator.sendBeacon("/api/auth/AuthlogoutDisconnect", blob);

  //reiniciarCronometro();
}

setInterval(() => {
  fetch("/api/auth/PingSesion", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-Internal-Request": "1"
    },
    body: JSON.stringify({
      tiempo: segundosC 
    })
  });
}, 60000); 

document.getElementById('logoutSesion').addEventListener('click', function () {
  sesionCerradaManual = true;

  fetch("/api/auth/Authlogout", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-Internal-Request": "1"
    },
    body: JSON.stringify({
      tiempo: segundosC
    })
  }).then(() => {
    // reiniciarCronometro();
    window.location.href = "/auth/index";
  });
});