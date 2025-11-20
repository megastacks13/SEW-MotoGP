
class Cronometro {
    constructor() {
        this.inicio = 0

        this.#pruebaPonerBotones();
    }

    arrancar(){
        if (this.corriendo) return;
        try{
            this.inicio = Temporal.Now.instant().epochMilliseconds;
        }catch(e){
            this.inicio = Date.now();
        }
        /* Formato con bind */
        this.corriendo = setInterval(this.#actualizar.bind(this), 100)
        /* Formato con arrow function */
        //setInterval( () => this.actualizar, 100)
    }

    #actualizar(){
        let actualTime;
        try{
            actualTime = Temporal.Now.instant().epochMilliseconds;
        }catch(e){
            actualTime = Date.now();
        }
        this.tiempo = actualTime-this.inicio;
        this.#mostrar();
    }

    #mostrar(){
        const main = document.body.querySelector('main'); // Buscamos main
        const p = main.querySelector('p');

        const minutos = Math.floor(this.tiempo / 60000);
        const segundos = Math.floor((this.tiempo % 60000) / 1000);
        const decimas = Math.floor((this.tiempo % 1000) / 10);

        p.textContent = String(minutos).padStart(2, "0") + ":" +
            String(segundos).padStart(2, "0") + "." +
            String(decimas).padStart(2, "0");
    }

    parar(){
        clearInterval(this.corriendo);
    }

    reiniciar(){
        clearInterval(this.corriendo);
        this.corriendo = null;
        this.tiempo = 0;
        this.#mostrar();
    }

    #pruebaPonerBotones(){
        const section = document.body.querySelector('main');
        const buttons = section.querySelectorAll('button');

        if (buttons.length){
            buttons[0].addEventListener('click', _ => this.arrancar());
            buttons[1].addEventListener('click', _ => this.parar());
            buttons[2].addEventListener('click', _ =>this.reiniciar());
        }

    }
}

new Cronometro();