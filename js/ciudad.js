"use strict";

class Ciudad {

    constructor(nombre, pais, gentilicio) {
        this.nombre = nombre;
        this.pais = pais;
        this.gentilicio = gentilicio;
    }

    fill(poblacion, coordenadas_centro){
        this.poblacion = poblacion;
        this.coordenadas_centro = coordenadas_centro;
    }

    getNombreCiudad(){
        return this.nombre.toString();
    }

    getPaisCiudad(){
        return this.pais.toString();
    }

    getInformacionSecundaria(){
        return `<ul>
            <li>Gentilicio: ${this.gentilicio.toString()}</li>
            <li>Población(2021): ${this.poblacion.toString()}</li>
            </ul>`;
    }

    writeCoordenadas(){
        const seccion2 = document.querySelectorAll('main > section')[0];
        const p = document.createElement("p");
        p.textContent = this.coordenadas_centro.toString();
        seccion2.appendChild(p);
    }

    getMetereologiaCarrera(callback){
        $.ajax({
            url:"https://archive-api.open-meteo.com/v1/archive?latitude=36.533611&longitude=140.2275&start_date=2025-09-28&end_date=2025-09-28&daily=sunrise,sunset&hourly=temperature_2m,apparent_temperature,rain,relative_humidity_2m,wind_speed_10m,wind_direction_10m&timezone=Europe%2FLondon",
            type:"json",
            method:"GET",
            success: (data) => {
                this.procesarJsonCarrera(data);
                if (callback) callback();
            },
            error: function(data){

            }
        })
    }

    getMetereologiaEntrenos(){
        $.ajax({
            url:"https://archive-api.open-meteo.com/v1/archive?latitude=36.533611&longitude=140.2275&start_date=2025-09-25&end_date=2025-09-27&hourly=temperature_2m,rain,relative_humidity_2m,wind_speed_10m&timezone=Europe%2FLondon",
            type:"json",
            method:"GET",
            success: (data) => {
                this.procesarJSONEntrenos(data);
            },
            error: function(data){

            }
        })
    }

    procesarJsonCarrera(jsonCarrera) {
        const fecha = jsonCarrera.daily.time[0].split("T")[0];
        const salidaSol = jsonCarrera.daily.sunrise[0].split("T")[1];
        const puestaSol = jsonCarrera.daily.sunset[0].split("T")[1];
        const hourly = jsonCarrera.hourly;

        let section = $("<section>")

        $("<h3>").text(`Día de la carrera (${fecha}):`).appendTo(section);
        $("<pre>").text(`Salida del sol: ${salidaSol}\nPuesta de sol: ${puestaSol}\n `)
            .appendTo(section);
        this.#renderHourlyRace(hourly, section);

        section.appendTo("main");

    }

    procesarJSONEntrenos(jsonEntrenos){
        const hourly = jsonEntrenos.hourly;

        const mediasTemperatura = this.#mediasDiarias(hourly.temperature_2m);
        const mediasLluvia = this.#mediasDiarias(hourly.rain).map(r => Number((r * 100).toFixed(2)));
        const mediasHumedad = this.#mediasDiarias(hourly.relative_humidity_2m);
        const mediasViento = this.#mediasDiarias(hourly.wind_speed_10m);

        let section = $("<section>")
        $("<h3>").text(`Entrenamientos (${hourly.time[0].split("T")[0]}-${hourly.time[hourly.time.length-1].split("T")[0]}):`).appendTo(section);

        for (let i = 0; i < mediasTemperatura.length; i++) {
            const fecha = hourly.time[i*24].split("T")[0];
            $("<pre>").text(`Día: ${fecha}
            -Temperatura: ${mediasTemperatura[i]}ºC
            -Porcentaje de lluvia: ${mediasLluvia[i]}%
            -Humedad relativa: ${mediasHumedad[i]}%
            -Velocidad del viento: ${mediasViento[i]} km/h`)
                .appendTo(section);
        }
        section.appendTo("main");
    }


    #renderHourlyRace(hourly, appendTo){

        for(let i = 0; i < hourly.time.length; i++){
            $("<pre>").text(`Hora: ${hourly.time[i].split("T")[1]}
            -Temperatura: ${hourly.temperature_2m[i]}ºC
            -Sensación térmica: ${hourly.apparent_temperature[i]}ºC
            -Porcentaje de lluvia: ${hourly.rain[i]*100}%
            -Humedad relativa: ${hourly.relative_humidity_2m[i]}%
            -Velocidad del viento: ${hourly.wind_speed_10m[i]}km/h
            -Dirección del viento: ${hourly.wind_direction_10m[i]}º`)
                .appendTo(appendTo);
        }
    }

    #mediasDiarias(array) {
        return Array.from({ length: 3 }, (_, dia) => {
            const bloque = array.slice(dia * 24, (dia + 1) * 24);
            const media = bloque.reduce((sum, val) => sum + val, 0) / bloque.length;
            return Number(media.toFixed(2));
        });
    }


}

