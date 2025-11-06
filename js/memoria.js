"use strict";

class Memoria {
    constructor() {
        this.tableroBloqueado = true;
        this.primera_carta = null;
        this.segunda_carta = null;

        this.#crearCartasDeArticles();
        this.#barajarCartas()
        this.tableroBloqueado = false;

        this.cronometro = new Cronometro();
        this.cronometro.arrancar()
    }

    // Voltea la carta añadiendo la propiedad data-estado="volteada"
    voltearCarta(card) {
        // Comprobamos las cartas
        if (card.dataset.estado === "revelada" || card.dataset.estado === "volteada" || this.tableroBloqueado) return;

        card.dataset.estado = "volteada";

        // Almacenamos las cartas
        if (this.primera_carta) {
            this.segunda_carta = card;
            this.#comprobarPareja();
        }
        else{
            this.primera_carta = card;
        }
    }

    #barajarCartas() {
        const main = document.querySelector('main');
        const elementos = Array.from(main.children);

        // Hacemos una lista que no incluya el h2
        const [h2,p,  ...cartas] = elementos;

        // Barajamos las cartas
        for (let i = cartas.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (cartas.length - 1));
            [cartas[i], cartas[j]] = [cartas[j], cartas[i]];
        }

        // Reinsertamos las cartas barajadas
        cartas.forEach(carta => main.appendChild(carta));
    }

    #reiniciarAtributos(){
        this.primera_carta = null;
        this.segunda_carta = null;
        this.tableroBloqueado = false;
    }

    #deshabilitarCartas(){
        this.primera_carta.dataset.estado = "revelada";
        this.segunda_carta.dataset.estado = "revelada";
        this.#reiniciarAtributos();
        this.#comprobarJuego();
    }

    #comprobarJuego(){
        let articles = document.getElementsByTagName('article');
        for (let article of articles) {
            if (article.dataset.estado !== "revelada") return false;
        }
        this.cronometro.parar();
        return true;
    }

    #cubrirCartas(){
        // Bloqueamos el tablero
        this.tableroBloqueado = true;

        /* Con bind
        setTimeout(this.reiniciarAtributos.bind(this), 1500);
        this.primera_carta.dataset.estado = "";
        this.segunda_carta.dataset.estado = "";
        */

        // Con arrow functions
        setTimeout(() =>{
            // Reiniciamos el estado
            this.primera_carta.dataset.estado = "";
            this.segunda_carta.dataset.estado = "";

            // Y los atributos
            this.#reiniciarAtributos();
        }, 1500)
    }

    #comprobarPareja(){
        // Estructura de cartas article -> h3[0], img[1]
        let alt_primera_carta = this.primera_carta.children[1].alt
        let alt_segunda_carta = this.segunda_carta.children[1].alt
        // Operador ternario
        alt_primera_carta === alt_segunda_carta ? this.#deshabilitarCartas() : this.#cubrirCartas();
    }

    #crearCartasDeArticles(){
        const articles = document.querySelectorAll("article");
        articles.forEach((article) => {
            article.addEventListener("click", (e) => {this.voltearCarta(article)})
        })
    }

}

new Memoria();