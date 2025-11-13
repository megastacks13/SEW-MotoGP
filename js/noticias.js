class Noticias {
    constructor() {
        this.busqueda = new URLSearchParams({
            api_token: "gBzNhZAM3xkCdZ5lCYXAnkCQiIAk6OzB0j8ijjoG",
            search: "MotoGP",
            language: "es",
            categories: "sports",
            limit: 5,
            published_after: "2024-01-01"
        });
        this.url = "https://api.thenewsapi.com/v1/news/all?";
    }

    #buscar() {
        return fetch(this.url + this.busqueda);
    }

    procesarInformacion() {
        this.#buscar()
            .then(respuesta => respuesta.json())
            .then(datos => {

                if (!datos.data || datos.data.length === 0) {
                    $("<p>").text("No se encontraron noticias para esta búsqueda.")
                        .appendTo("section");
                    return;
                }

                datos.data.forEach(noticia => {
                    $("<article>")
                        .append($("<h4>").text(noticia.title))
                        .append($("<p>").text(noticia.description || "Sin descripción"))
                        .append($("<a>")
                            .attr("href", noticia.url)
                            .attr("target", "_blank")
                            .text(noticia.source))
                        .appendTo("section");
                });
            })
            .catch(error => console.error("Error al obtener las noticias:", error));
    }
}

new Noticias().procesarInformacion();
