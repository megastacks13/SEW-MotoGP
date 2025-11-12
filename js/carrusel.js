"use strict";


class Carrusel {
    constructor() {
        this.busqueda = null;
        this.actual = 0;
        this.maximo = 5;
        this.#procesarJSONFotografias();
    }

    #getFotografias() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "https://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?",
                dataType: "jsonp",
                method: "GET",
                data: {
                    tags: "Motegi, MotoGP",
                    tagmode: "all",
                    format: "json"
                },
                success: function(data) {
                    data.items.forEach(item =>{
                        item.media.m = item.media.m.replace("_m.", "_z.")
                    })
                    resolve(data.items);
                },
                error: function() {
                    console.error("Error al realizar la llamada AJAX al servicio de Flickr.");
                    reject("Error al cargar las fotos");
                }
            });
        });
    }

    // Recibimos las imágenes
    async #procesarJSONFotografias(){
        const fotos = await this.#getFotografias();
        this.busqueda =  fotos.slice(0,this.maximo);
    }

    #mostrarFotografias(){
        this.actual++;
        if (this.actual >= this.maximo) {this.actual = 0}
        // Lo hago así para trabajar de distintas formas con jQuery y de paso evitar un contenedor de imagen vacío
        // Verificamos que no exista ya la imagen
        const img = $('article img');

        // Si existe lo dejamos
        if (img.length > 0) {
            img
                .attr("src", this.busqueda[this.actual].media.m)
                .attr("alt", this.busqueda[this.actual].title);
        // Si no, la creamos
        } else {
            $('<img>')
                .attr("src", this.busqueda[this.actual].media.m)
                .attr("alt", this.busqueda[this.actual].title)
                .appendTo('article');
        }
    }

    async cambiarFotografia(){
        setInterval(() => {this.#mostrarFotografias();}, 3000);
    }
}

$(document).ready(async () =>{
    let carrusel = new Carrusel();
    await carrusel.cambiarFotografia();
});
