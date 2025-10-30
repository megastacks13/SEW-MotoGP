
class Cronometro {
    constructor() {
        this.inicio = 0
    }

    arrancar(){
        if (this.corriendo) return;
        try{
            this.inicio = Temporal.Now;
        }catch(e){
            this.inicio = Date.now();
        }
        /* Formato con bind */
        this.corriendo = setInterval(this.actualizar.bind(this), 100)
        /* Formato con arrow function */
        //setInterval( () => this.actualizar, 100)
    }

    actualizar(){
        let actualTime;
        try{
            actualTime = Temporal.Now;
        }catch(e){
            actualTime = Date.now();
        }
        this.tiempo = actualTime-this.inicio;
        this.mostrar();
    }

    mostrar(){
        const main = document.body.querySelector('main'); // Buscamos main
        const p = main.children[1]; // Primero está h2 y luego el p

        const minutos = Math.floor(this.tiempo / 60000);
        const segundos = Math.floor((this.tiempo % 60000) / 1000);
        const decimas = Math.floor((this.tiempo % 1000) / 10);

        const formato =
            String(minutos).padStart(2, "0") + ":" +
            String(segundos).padStart(2, "0") + "." +
            String(decimas).padStart(2, "0");

        p.textContent = formato;
    }

    parar(){
        clearInterval(this.corriendo);
    }

    reiniciar(){
        clearInterval(this.corriendo);
        this.corriendo = null;
        this.tiempo = 0;
        this.mostrar();
    }
}