"use strict";

class Circuito {
    constructor() {
        this.#comprobarAPIFile()
    }

    #comprobarAPIFile() {
        if (!(window.File && window.FileReader && window.FileList && window.Blob))
            alert("Este navegador no soporta la API File.");
    }

    leerArchivoHTML(archivo, domElement) {
        let tipoTexto = /\.html$/;
        if (!archivo.name.match(tipoTexto)) {
            alert("El archivo no es un .html");
            return;
        }

        let lector = new FileReader();

        lector.onload = (evento) => {
            this.#processHTML(evento.target.result, domElement);
        };

        lector.readAsText(archivo);
    }

    #processHTML(resultadoHTML, domElement) {
        const doc = new DOMParser().parseFromString(resultadoHTML, "text/html");

        const mainChildren = doc.querySelectorAll("main > *");
        
        for (let i = mainChildren.length - 1; i >= 0; i--) {
            $(domElement).after(mainChildren[i]);
        }
    }

}

let circuito = new Circuito();

$(document).ready(() => {
    $("input").each((index, element) => {
        const $element = $(element); // lo envolvemos en jQuery
        switch (index) {
            case 0:
                $element.on("change", e => {
                    if (e.target.files[0]) circuito.leerArchivoHTML(e.target.files[0], $element);
                });
                break;
            case 1:
                console.log("Input Altimetria");
                break;
            case 2:
                console.log("Input Mapa");
                break;
        }
    });
});
