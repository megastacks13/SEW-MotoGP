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

}