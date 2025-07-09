<div id="chatbot-widget">
    <div id="chatbot-toggle" class="chatbot-toggle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            <circle cx="9" cy="10" r="1"/>
            <circle cx="15" cy="10" r="1"/>
        </svg>
        <span class="chatbot-badge" id="chatbot-badge">1</span>
    </div>

    <div id="chatbot-window" class="chatbot-window">
        <!-- Header del chat -->
        <div class="chatbot-header">
            <div class="chatbot-avatar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="5"/>
                    <path d="M3 21v-2a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v2"/>
                </svg>
            </div>
            <div class="chatbot-info">
                <h4>Asistente Virtual</h4>
                <span class="chatbot-status">En línea</span>
            </div>
            <button id="chatbot-close" class="chatbot-close">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <!-- mensajes -->
        <div id="chatbot-messages" class="chatbot-messages">
            <div class="chatbot-message chatbot-message-bot">
                <div class="chatbot-message-avatar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="5"/>
                        <path d="M3 21v-2a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v2"/>
                    </svg>
                </div>
                <div class="chatbot-message-content">
                    <div class="chatbot-message-bubble">
                        ¡Hola! Soy tu asistente virtual. Puedo ayudarte con información sobre clientes, inversiones, pagos y estadísticas. ¿Qué te gustaría consultar?
                    </div>
                    <div class="chatbot-message-time"><?php echo date('H:i'); ?></div>
                </div>
            </div>
        </div>

        <!-- escritura -->
        <div class="chatbot-input-area">
            <div class="chatbot-input-container">
                <input 
                    type="text" 
                    id="chatbot-input" 
                    class="chatbot-input" 
                    placeholder="Pregúntame sobre clientes, inversiones, pagos..."
                    maxlength="500"
                >
                <button id="chatbot-send" class="chatbot-send" type="button">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22,2 15,22 11,13 2,9 22,2"/>
                    </svg>
                </button>
            </div>
            <div class="chatbot-typing" id="chatbot-typing" style="display: none;">
                <div class="chatbot-typing-indicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <span>Consultando...</span>
            </div>
        </div>
    </div>
</div>

<style>
#chatbot-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.chatbot-toggle {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
    color: white;
    position: relative;
}

.chatbot-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
}

.chatbot-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ff4757;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.chatbot-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: none;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid #e1e8ed;
}

.chatbot-window.active {
    display: flex;
}

.chatbot-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 16px 20px;
    display: flex;
    align-items: center;
    color: white;
}

.chatbot-avatar {
    width: 32px;
    height: 32px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
}

.chatbot-info h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.chatbot-status {
    font-size: 12px;
    opacity: 0.8;
}

.chatbot-close {
    margin-left: auto;
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.chatbot-close:hover {
    opacity: 1;
}

/* mensajes */
.chatbot-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #f8fafc;
}

.chatbot-message {
    display: flex;
    margin-bottom: 16px;
    align-items: flex-end;
}

.chatbot-message-bot {
    justify-content: flex-start;
}

.chatbot-message-user {
    justify-content: flex-end;
}

.chatbot-message-avatar {
    width: 28px;
    height: 28px;
    background: #667eea;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 8px;
    flex-shrink: 0;
}

.chatbot-message-user .chatbot-message-avatar {
    background: #2c3e50;
    margin-right: 0;
    margin-left: 8px;
    order: 2;
}

.chatbot-message-content {
    max-width: 70%;
}

.chatbot-message-user .chatbot-message-content {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.chatbot-message-bubble {
    background: white;
    padding: 12px 16px;
    border-radius: 18px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    font-size: 14px;
    line-height: 1.4;
    word-wrap: break-word;
}

.chatbot-message-user .chatbot-message-bubble {
    background: #667eea;
    color: white;
}

.chatbot-message-time {
    font-size: 11px;
    color: #8995a0;
    margin-top: 4px;
    padding-left: 16px;
}

.chatbot-message-user .chatbot-message-time {
    padding-left: 0;
    padding-right: 16px;
    text-align: right;
}

/* input */
.chatbot-input-area {
    border-top: 1px solid #e1e8ed;
    padding: 16px 20px;
    background: white;
}

.chatbot-input-container {
    display: flex;
    align-items: center;
    background: #f1f3f4;
    border-radius: 24px;
    padding: 8px 16px;
}

.chatbot-input {
    flex: 1;
    border: none;
    background: none;
    outline: none;
    font-size: 14px;
    padding: 8px 0;
    color: #2c3e50;
}

.chatbot-input::placeholder {
    color: #8995a0;
}

.chatbot-send {
    background: #667eea;
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: white;
    transition: background 0.2s;
    margin-left: 8px;
}

.chatbot-send:hover {
    background: #5a6fd8;
}

.chatbot-send:disabled {
    background: #bdc3c7;
    cursor: not-allowed;
}

/* Indicador de escritura */
.chatbot-typing {
    display: flex;
    align-items: center;
    margin-top: 8px;
    color: #8995a0;
    font-size: 12px;
}

.chatbot-typing-indicator {
    display: flex;
    margin-right: 8px;
}

.chatbot-typing-indicator span {
    width: 4px;
    height: 4px;
    background: #8995a0;
    border-radius: 50%;
    margin-right: 2px;
    animation: typing 1.4s infinite;
}

.chatbot-typing-indicator span:nth-child(2) {
    animation-delay: 0.2s;
}

.chatbot-typing-indicator span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
        opacity: 0.4;
    }
    30% {
        transform: translateY(-10px);
        opacity: 1;
    }
}

/* Responsive */
@media (max-width: 480px) {
    .chatbot-window {
        width: calc(100vw - 40px);
        height: 70vh;
        bottom: 80px;
        right: 20px;
        left: 20px;
    }
    
    #chatbot-widget {
        right: 20px;
        bottom: 20px;
    }
}

/* Scrollbar personalizado */
.chatbot-messages::-webkit-scrollbar {
    width: 4px;
}

.chatbot-messages::-webkit-scrollbar-track {
    background: transparent;
}

.chatbot-messages::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 2px;
}

.chatbot-messages::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('chatbot-toggle');
    const window = document.getElementById('chatbot-window');
    const close = document.getElementById('chatbot-close');
    const input = document.getElementById('chatbot-input');
    const send = document.getElementById('chatbot-send');
    const messages = document.getElementById('chatbot-messages');
    const typing = document.getElementById('chatbot-typing');
    const badge = document.getElementById('chatbot-badge');
    
    let isOpen = false;
    let messageCount = 1;

    toggle.addEventListener('click', function() {
        isOpen = !isOpen;
        window.classList.toggle('active', isOpen);
        if (isOpen) {
            input.focus();
            badge.style.display = 'none';
        }
    });

    close.addEventListener('click', function() {
        isOpen = false;
        window.classList.remove('active');
    });

    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    send.addEventListener('click', sendMessage);

    function sendMessage() {
        const message = input.value.trim();
        if (!message) return;

        addMessage(message, 'user');
        input.value = '';
        showTyping();

        // Llamada al backend con mejor manejo de errores
        fetch('/api/chatbot/processMessage?action=process', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => {
            // Verificar si la respuesta es JSON válida
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('La respuesta del servidor no es JSON válida');
            }
            return response.json();
        })
        .then(data => {
            hideTyping();
            if (data.error) {
                addMessage('❌ ' + data.error, 'bot');
            } else {
                addMessage(data.response, 'bot');
            }
        })
        .catch(error => {
            hideTyping();
            console.error('Error completo:', error);
            addMessage('❌ Error de conexión. Verifica que estés autenticado y vuelve a intentar.', 'bot');
        });
    }

    function addMessage(text, sender) {
        const time = new Date().toLocaleTimeString('es-ES', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `chatbot-message chatbot-message-${sender}`;
        
        // Convertir markdown básico a HTML
        const formattedText = formatMarkdown(text);
        
        messageDiv.innerHTML = `
            <div class="chatbot-message-avatar">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="5"/>
                    <path d="M3 21v-2a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v2"/>
                </svg>
            </div>
            <div class="chatbot-message-content">
                <div class="chatbot-message-bubble">
                    ${formattedText}
                </div>
                <div class="chatbot-message-time">${time}</div>
            </div>
        `;
        
        messages.appendChild(messageDiv);
        messages.scrollTop = messages.scrollHeight;
    }

    function formatMarkdown(text) {
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // **bold**
            .replace(/\*(.*?)\*/g, '<em>$1</em>') // *italic*
            .replace(/\n/g, '<br>') // line breaks
            .replace(/•/g, '•'); // bullet points
    }

    function showTyping() {
        typing.style.display = 'flex';
        messages.scrollTop = messages.scrollHeight;
    }

    function hideTyping() {
        typing.style.display = 'none';
    }

    function showNotification() {
        if (!isOpen) {
            messageCount++;
            badge.textContent = messageCount;
            badge.style.display = 'flex';
        }
    }

    window.addBotMessage = function(message) {
        addMessage(message, 'bot');
        showNotification();
    };
});
</script>