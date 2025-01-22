document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const toggleContrastBtn = document.getElementById('toggle-contrast');
    const increaseFontBtn = document.getElementById('increase-font');
    const decreaseFontBtn = document.getElementById('decrease-font');

    // Domyślny rozmiar czcionki
    const defaultFontSize = 1; // 1rem
    const maxFontSize = 3; // Maksymalny rozmiar to 3rem
    let fontSize = parseFloat(localStorage.getItem('fontSize')) || defaultFontSize;

    // Ustawienie początkowego rozmiaru czcionki
    body.style.fontSize = `${fontSize}rem`;

    // Tryb wysokiego kontrastu
    if (localStorage.getItem('highContrast') === 'enabled') {
        body.classList.add('high-contrast');
    }

    if (toggleContrastBtn) {
        toggleContrastBtn.addEventListener('click', () => {
            body.classList.toggle('high-contrast');
            if (body.classList.contains('high-contrast')) {
                localStorage.setItem('highContrast', 'enabled');
            } else {
                localStorage.removeItem('highContrast');
            }
        });
    }

    // Zwiększanie czcionki
    if (increaseFontBtn) {
        increaseFontBtn.addEventListener('click', () => {
            if (fontSize < maxFontSize) {
                fontSize += 0.1;
                body.style.fontSize = `${fontSize.toFixed(1)}rem`;
                localStorage.setItem('fontSize', fontSize.toFixed(1));
            }
        });
    }

    // Zmniejszanie czcionki
    if (decreaseFontBtn) {
        decreaseFontBtn.addEventListener('click', () => {
            if (fontSize > defaultFontSize) {
                fontSize -= 0.1;
                body.style.fontSize = `${fontSize.toFixed(1)}rem`;
                localStorage.setItem('fontSize', fontSize.toFixed(1));
            }
        });
    }
});
