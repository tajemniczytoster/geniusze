document.addEventListener('DOMContentLoaded', () => {
    const button = document.querySelector('.crazy-button');
    let clickCount = 0;

    button.addEventListener('click', (e) => {
        e.preventDefault(); // Zablokowanie domyślnej akcji przycisku

        clickCount++;
        if (clickCount < 5) {
            // Generowanie losowej pozycji w obrębie widocznego ekranu
            const randomX = Math.random() * (window.innerWidth - button.offsetWidth);
            const randomY = Math.random() * (window.innerHeight - button.offsetHeight);

            button.style.position = 'absolute';
            button.style.left = `${Math.max(0, randomX)}px`;
            button.style.top = `${Math.max(0, randomY)}px`;
        } else {
            const downloadLink = document.createElement('a');
            downloadLink.href = 'wojna i okupacja.pdf'; // Ścieżka do pliku
            downloadLink.download = 'wojna i okupacja.pdf'; // Aktywacja pobierania
            downloadLink.style.display = 'none'; 
            document.body.appendChild(downloadLink);
            downloadLink.click(); 
            document.body.removeChild(downloadLink); 
        }
    });

    // Ciągły efekt Particle
    setInterval(() => {
        for (let i = 0; i < 10; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';

            // Ustawianie początkowych pozycji dla cząstek
            particle.style.left = `${Math.random() * window.innerWidth}px`;
            particle.style.top = `${Math.random() * window.innerHeight}px`;

            document.body.appendChild(particle);

            // Usuwanie cząstek po 5 sekundach
            setTimeout(() => particle.remove(), 5000);
        }
    }, 500); // Co 500ms generuje 10 cząstek
});
