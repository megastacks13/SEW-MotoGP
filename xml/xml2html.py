"""
!/usr/bin/env python3
-*- coding: utf-8 -*-

@author: Jaime Alonso Fernández UO294024
@date: 2025-10-16
@description: Convierte un archivo XML que siga las pautas de circuito.xsd de en un formato HTML.

Para la ejecución de este código se emplea un comando similar al visto en xml2svg:
python xml2kml.py circuitoEsquema.xml salida.kml
"""

import typing
import xml.etree.ElementTree as ET
import sys
import logging

NAMESPACE = {"c": "http://uniovi.es/circuito"}
logging.basicConfig(level=logging.INFO, format="%(levelname)s: %(message)s")
logger = logging.getLogger(__name__)

# Macros
section_start = lambda file: file.write('<section>\n')
section_end = lambda file: file.write('</section>\n')
header = lambda file, number, title: file.write('<h%d>%s</h%d>\n' % (number, title, number))
parraf = lambda file, content: file.write('<p>%s</p>\n' % content)
img = lambda file, source, alt: file.write('<img src="%s" alt="%s">\n' % (source, alt))
aside_start = lambda file: file.write('<aside>')
aside_end = lambda file: file.write('</aside>\n')

def get_tree(file_xml):
    try:
        tree = ET.parse(file_xml)
    except IOError:
        print("No se encuentra el archivo XML")
        exit()
    except ET.ParseError:
        print("Error en archivo xml")
        exit()
    return tree


def generate_html_header(file:typing.TextIO):
    file.write('<!DOCTYPE html>\n')
    file.write('<html lang="es">\n')
    file.write('<head>\n')
    file.write('<meta charset="utf-8">\n')
    file.write('<meta name="viewport" content="width=device-width, initial-scale=1.0">\n')
    file.write('<meta name="author" content="Jaime Alonso Fernández">\n')
    file.write('<meta name="description" content="Página donde se detalla el circuito de Motegi">\n')
    file.write('<meta name="keywords" content="MotoGP, Moto">\n')
    file.write('<meta name="keywords" content="Motegi, circuito">\n')
    file.write('<title>MotoGP-Circuito</title>\n')
    file.write('<link rel="stylesheet" type="text/css" href="estilo/estilo.css">\n')
    file.write('<link rel="stylesheet" type="text/css" href="estilo/layout.css">\n')
    file.write('<link rel="icon" href="multimedia/img/favicon.ico" type="image/x-icon"/>')
    file.write('</head>\n<body>\n<header>\n')
    file.write('<h1><a href="index.html">Moto GP Desktop</a></h1>\n')
    file.write('<nav>\n')
    file.write('<a href="index.html">Inicio</a>\n')
    file.write('<a href="piloto.html">Piloto</a>\n')
    file.write('<a href="circuito.html" class="active">Circuito</a>\n')
    file.write('<a href="metereologia.html">Metereología</a>\n')
    file.write('<a href="clasificaciones.html">Clasificaciones</a>\n')
    file.write('<a href="juegos.html">Juegos</a>\n')
    file.write('<a href="ayuda.html">Ayuda</a>\n')
    file.write('</nav>\n</header>\n')
    file.write('<p>Estás en: <a href="index.html">Inicio</a> >> <strong>Circuito</strong></p>')
    file.write('<main>\n')

def generate_html_close_tags(file:typing.TextIO):
    file.write('</main>\n</body>\n</html>\n')

def generate_html_mid_content(tree:ET.ElementTree, file:typing.TextIO):
    root = tree.getroot()
    name = root.find('c:nombre', NAMESPACE).text
    galerias = root.find('c:galerias', NAMESPACE)
    images = galerias.find('c:galeriafotos', NAMESPACE).findall('c:foto', NAMESPACE)
    videos = galerias.find('c:galeriavideos', NAMESPACE).findall('c:video', NAMESPACE)

    pais = root.find('c:geografia', NAMESPACE).find('c:pais', NAMESPACE)
    localidad = root.find('c:geografia', NAMESPACE).find('c:localidad', NAMESPACE)

    distancia = root.find('c:dimensiones', NAMESPACE).find('c:longitud', NAMESPACE)
    unidad = distancia.attrib.get('unidades')

    anchura = root.find('c:dimensiones', NAMESPACE).find('c:anchura', NAMESPACE)
    unidad_anchura = anchura.attrib.get('unidades')

    section_start(file)
    header(file, 2, name)
    parraf(file, f"Situado en {pais.text} a las afuera de {localidad.text}, este circuito cuenta con una longitud "
                 f"aproximada de {distancia.text} {unidad} y una anchura media de unos {anchura.text} {unidad_anchura}.")

    src = images[0].attrib.get('src')
    alt = images[0].attrib.get('alt')
    img(file, src, alt)
    section_end(file)

    section_start(file)
    header(file, 3, "Resultados")
    ganador = root.find('c:resultados', NAMESPACE).find('c:piloto', NAMESPACE).text
    tiempo = root.find('c:resultados', NAMESPACE).find('c:tiempo', NAMESPACE).text
    parraf(file, f"Tras esta competicion, el piloto <strong>{ganador}</strong> se garantizó el escalón más alto "
                 f"del podio con un tiempo de {tiempo}.")
    parraf(file, "Dicho esto, el top 3 del mundial al finalizar esta competencia, tomaba la siguiente forma.")
    ranking = root.find('c:rankingMundial', NAMESPACE)
    pilotos = ranking.findall('c:piloto', NAMESPACE)

    file.write('<table>\n')
    file.write('<tr>\n')
    file.write('<th>Posición</th>\n')
    file.write('<th>Nombre</th>\n')
    file.write('<th>Puntos</th>\n')
    file.write('</tr>\n')
    for i, piloto in enumerate(pilotos):
        file.write('<tr>\n')
        file.write('<td>%d</td>\n' % (i+1))
        file.write('<td>%s</td>\n' % piloto.text)
        file.write('<td>%s</td>\n' % piloto.attrib.get('puntos'))
        file.write('</tr>\n')

    file.write('</table>\n')
    section_end(file)

    bibliography = root.find('c:bibliografia', NAMESPACE)
    references = bibliography.findall('c:referencia', NAMESPACE)


    aside_start(file)
    for reference in references:
        file.write('<a href="%s">%s</a>\n'%(reference.attrib.get('src'), reference.text))
    aside_end(file)


def generate_html(tree: ET.ElementTree, filename: str):
    with open(filename, "w") as file:
        generate_html_header(file)
        generate_html_mid_content(tree, file)
        generate_html_close_tags(file)


def main():
    file_xml = sys.argv[1]
    tree = get_tree(file_xml=file_xml)
    file_html = sys.argv[2]
    generate_html(tree, file_html)
    logger.info(f"Archivo HTML {file_html} generado con éxito")


if __name__ == "__main__":
    main()