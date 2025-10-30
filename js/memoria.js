"use strict";

class Memoria {
    constructor() {
        this.tableroBloqueado = true;
        this.primera_carta = null;
        this.segunda_carta = null;
    }

    // Voltea la carta añadiendo la propiedad data-estado="revelada"
    voltearCarta(card) {
        card.dataset.estado = "revelada";
    }

    barajarCartas() {
        const main = document.querySelector('main');
        const elementos = Array.from(main.children);

        // Hacemos una lista que no incluya el h2
        const [h2, ...cartas] = elementos;

        // Barajamos las cartas
        for (let i = cartas.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            // Método de Fisher-Yates para barajar eficientemente
            [cartas[i], cartas[j]] = [cartas[j], cartas[i]];
        }

        // Reinsertamos las cartas barajadas
        cartas.forEach(carta => main.appendChild(carta));
    }


}
