"use strict";

class Memoria {
    constructor() {
        // Todos los articles son cards...
        const articles = document.querySelectorAll("main article");
        // Entonces para cada uno le ponemos un listener
        for (let article of articles) {
            // Arrow function para mantener el "this" sin usar bind
            article.addEventListener("click", () => {
                this.voltearCarta(article);
            });
        }
    }

    // Voltea la carta añadiendo la propiedad data-estado="revelada"
    voltearCarta(card) {
        card.dataset.estado = "revelada";
    }
}
