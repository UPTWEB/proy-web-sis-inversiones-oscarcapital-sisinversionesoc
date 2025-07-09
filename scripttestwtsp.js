// Configuración desde variables de entorno
const WHATSAPP_CONFIG = {
    token: process.env.WHATSAPP_TOKEN || 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIzODYwMyIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6ImNvbnN1bHRvciJ9.c6rXTQYdo-4rBfgtO8QaskrTu3PpbDX3Lglbge4gZ3c', // Fallback para desarrollo
    instancia: process.env.WHATSAPP_INSTANCIA || 'NTE5NjM2NTM3Mzc='
};

/**
 * Envía un mensaje de WhatsApp con o sin archivo
 * @param {string} numero - Número de teléfono (ej: "51987654321")
 * @param {string} mensaje - Mensaje de texto
 * @param {File|null} file - Archivo a enviar (opcional)
 * @returns {Promise} - Promesa con la respuesta de la API
 */
async function sendWhatsAppMessage(numero, mensaje, file = null) {
    try {
        // Validaciones básicas
        if (!numero || !mensaje) {
            throw new Error('Número y mensaje son requeridos');
        }

        if (!WHATSAPP_CONFIG.token || !WHATSAPP_CONFIG.instancia) {
            throw new Error('Token e instancia deben estar configurados en .env');
        }

        // Si hay archivo, enviar como media
        if (file) {
            return await sendMediaMessage(numero, mensaje, file);
        } else {
            return await sendTextMessage(numero, mensaje);
        }
    } catch (error) {
        console.error('Error enviando mensaje:', error);
        throw error;
    }
}

/**
 * Envía mensaje de texto
 */
async function sendTextMessage(numero, mensaje) {
    const options = {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${WHATSAPP_CONFIG.token}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            number: numero,
            text: mensaje
        })
    };

    const response = await fetch(`https://apiwsp.factiliza.com/v1/message/sendtext/${WHATSAPP_CONFIG.instancia}`, options);
    return await response.json();
}

/**
 * Envía mensaje con archivo
 */
async function sendMediaMessage(numero, mensaje, file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        
        reader.onload = async function() {
            try {
                const base64 = reader.result.split(',')[1];
                
                const options = {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${WHATSAPP_CONFIG.token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        number: numero,
                        mediatype: "image",
                        media: base64,
                        caption: mensaje
                    })
                };

                const response = await fetch(`https://apiwsp.factiliza.com/v1/message/sendmedia/${WHATSAPP_CONFIG.instancia}`, options);
                const result = await response.json();
                resolve(result);
            } catch (error) {
                reject(error);
            }
        };
        
        reader.onerror = () => reject(new Error('Error leyendo archivo'));
        reader.readAsDataURL(file);
    });
}

// Exportar para uso en otros módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { sendWhatsAppMessage };
}