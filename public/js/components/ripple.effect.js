function createRippleEffect(event) {
    const button = event.currentTarget;
    
    const ripples = button.getElementsByClassName('ripple');
    while(ripples.length > 0) {
        ripples[0].remove();
    }
    
    const ripple = document.createElement('span');
    ripple.classList.add('ripple');
    
    button.appendChild(ripple);
    
    const diameter = Math.max(button.clientWidth, button.clientHeight);
    const radius = diameter / 2;
    
    const rect = button.getBoundingClientRect();
    const x = event.clientX - rect.left - radius;
    const y = event.clientY - rect.top - radius;
    
    // Aplicamos estilos al ripple
    ripple.style.width = ripple.style.height = `${diameter}px`;
    ripple.style.left = `${x}px`;
    ripple.style.top = `${y}px`;
    
    setTimeout(() => {
        ripple.remove();
    }, 600); // Coincide con la duración de la animación
}

document.querySelectorAll('.main-button, .social-button').forEach(button => {
    button.addEventListener('click', createRippleEffect);
});