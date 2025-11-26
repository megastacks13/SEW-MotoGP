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
            $(domElement).parent().after(mainChildren[i]);
        }
    }

}

/* Preguntar*/
class CargadorSVG {
    leerArchivoSVG(archivo, domElement) {
        if (archivo && archivo.type !== 'image/svg+xml') {
            alert("El archivo no es un .svg");
            return;
        }

        const lector = new FileReader();
        lector.onload = (e) => this.insertarSVG(e.target.result, domElement);
        lector.readAsText(archivo);
    }

    insertarSVG(archivo, domElement) {
        const parser = new DOMParser();
        $("<svg>").innerHTML(parser.parseFromString(archivo, "image/svg+xml")).after(domElement);
    }
}

/*class CargadorSVG {
    leerArchivoSVG(archivo, domElement) {
        this.archivo = URL.createObjectURL(archivo);
        this.insertarSVG(domElement);
    }

    insertarSVG(domElement) {
        let grafico = $("<svg>").innerHTML(this.archivo);
        $(domElement).parent().after(grafico);
    }
}*/

class CargadorKML {
    constructor() {
        mapboxgl.accessToken = "pk.eyJ1IjoibWVnYXN0YWNrczEzIiwiYSI6ImNtaTY4MWpuZzA5dHgyaXM5aG9mY2dqa3EifQ.uDa5e4mt3QnTKK__FPgvmA";

        const divMapa = document.querySelector('main > div');

        this.map = new mapboxgl.Map({
            container: divMapa,
            style: 'mapbox://styles/mapbox/streets-v12',
            center: [-0.5, 40.4],
            zoom: 6
        });

        this.geojson = null;
    }

    leerArchivoKML(archivo) {
        const reader = new FileReader();

        reader.onload = (e) => {
            const text = e.target.result;

            // Convertir KML a GeoJSON
            const parser = new DOMParser();
            const kml = parser.parseFromString(text, "text/xml");
            this.geojson = toGeoJSON.kml(kml);

            // Insertar capa en el mapa
            this.insertarCapaKML();
        };

        reader.readAsText(archivo);
    }

    insertarCapaKML() {
        if (!this.geojson) {
            console.error("No se ha leído ningún KML");
            return;
        }

        // Actualizar fuente si ya existe
        if (this.map.getSource('kml-data')) {
            this.map.getSource('kml-data').setData(this.geojson);
        } else {
            // Añadir fuente y capa
            this.map.addSource('kml-data', {
                type: 'geojson',
                data: this.geojson
            });

            this.map.addLayer({
                id: 'kml-layer',
                type: 'line',
                source: 'kml-data',
                paint: {
                    'line-color': '#FF0000',
                    'line-width': 3
                }
            });
        }

        // Ajustar vista al contenido
        const bounds = new mapboxgl.LngLatBounds();
        this.geojson.features.forEach(f => {
            f.geometry.coordinates.forEach(c => bounds.extend(c));
        });
        this.map.fitBounds(bounds, { padding: 20 });
    }
}


$(document).ready(() => {
    let circuito = new Circuito();
    let cargadorSVG = new CargadorSVG();
    let cargadorKML = new CargadorKML();

    // Asociar inputs
    $("input").each((index, element) => {
        const $element = $(element);
        switch (index) {
            case 0:
                $element.on("change", e => {
                    if (e.target.files[0]) circuito.leerArchivoHTML(e.target.files[0], $element);
                });
                break;
            case 1:
                $element.on("change", e => {
                    if (e.target.files[0]) cargadorSVG.leerArchivoSVG(e.target.files[0], $element);
                });
                break;
            case 2:
                $element.on("change", e => {
                    if (e.target.files[0]) cargadorKML.leerArchivoKML(e.target.files[0], $element);
                });
                break;
        }
    });
});
