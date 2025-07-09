let ipClient;

fetch('https://api.ipify.org')
    .then((response) => response.text())
    .then((ip) => {
        ipClient = ip;
        localStorage.setItem('ipClient', ipClient);
    })
    .catch((error) => console.error('Error fetching IP:', error));