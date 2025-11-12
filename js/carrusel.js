"use strict";


class Carrusel {
    constructor() {
        this.busqueda = null;
        this.actual = 0;
        this.maximo = 5;
    }

    getFotografias() {
        $.ajax({
            url: "https://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?",
            dataType: "jsonp",
            method: "GET",
            data: {
                tags: "Motegi, MotoGP",
                tagmode: "all",
                format: "json"
            },
            success: (data) => {
                data.items.forEach(item =>{
                    item.media.m = item.media.m.replace("_m.", "_z.")
                })
                this.#procesarJSONFotografias(data.items);
            },
            error: function() {
                console.error("Error al realizar la llamada AJAX al servicio de Flickr.");
            }
        });
    }

    // Recibimos las imágenes
    #procesarJSONFotografias(fotos){
        this.busqueda =  fotos.slice(0,this.maximo);
        this.cambiarFotografia();
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
        this.#mostrarFotografias();
        setInterval(() => {this.#mostrarFotografias();}, 3000);
    }
}

$(document).ready(async () =>{
    let carrusel = new Carrusel();
    await carrusel.getFotografias();
});
