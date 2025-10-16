"""
!/usr/bin/env python3
-*- coding: utf-8 -*-

@author: Jaime Alonso Fernández UO294024
@date: 2025-10-16
@description: Convierte un archivo XML que siga las pautas de circuito.xsd de en un formato KML.

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

def write_coordinate(point:ET.Element, file:typing.TextIO):
    """ Escribe en el archivo de salida las coordenadas del punto que recibe """

    attributes = point.attrib
    latitude = attributes.get("latitud")
    longitude = attributes.get("longitud")
    altitude = attributes.get("altura")
    coordinate = f"{longitude},{latitude},{altitude}\n"
    file.write(coordinate)

def traverse_tree_and_and_do_kml(tree:ET.ElementTree, file:typing.TextIO):
    """ Recorre el árbol y manda escribir los puntos en el archivo de salida """

    root = tree.getroot()
    plane = root.find("c:plano", NAMESPACE)
    start_point = plane.find("c:punto", NAMESPACE)
    
    write_coordinate(start_point, file)
    
    phases = plane.find("c:tramos", NAMESPACE)
    for phase in phases.findall("c:tramo", NAMESPACE):
        write_coordinate(phase.find("c:punto", NAMESPACE), file)


def prologue_kml(file:typing.TextIO, filename:str):
    """ Escribe en el archivo de salida el prólogo del archivo KML"""

    file.write('<?xml version="1.0" encoding="UTF-8"?>\n')
    file.write('<kml xmlns="http://www.opengis.net/kml/2.2">\n')
    file.write("<Document>\n")
    file.write("<Placemark>\n")
    file.write("<name>"+filename+"</name>\n")    
    file.write("<LineString>\n")
    #la etiqueta <extrude> extiende la línea hasta el suelo 
    file.write("<extrude>1</extrude>\n")
    # La etiqueta <tessellate> descompone la línea en porciones pequeñas
    file.write("<tessellate>1</tessellate>\n")
    file.write("<coordinates>\n")

def epilogue_kml(file:typing.TextIO):
    """ Escribe en el archivo de salida el epílogo del archivo KML"""

    file.write("</coordinates>\n")
    file.write("<altitudeMode>relativeToGround</altitudeMode>\n")
    file.write("</LineString>\n")
    file.write("<Style> id='lineaRoja'>\n") 
    file.write("<LineStyle>\n") 
    file.write("<color>#ff0000ff</color>\n")
    file.write("<width>5</width>\n")
    file.write("</LineStyle>\n")
    file.write("</Style>\n")
    file.write("</Placemark>\n")
    file.write("</Document>\n")
    file.write("</kml>\n")
    
    
def generate_kml(tree:ET.ElementTree, filename:str):
    """ Suponiendo que el archivo exista, condensa las 3 partes de la escritura """

    with open(filename, "w") as file:
        prologue_kml(file, filename)
        traverse_tree_and_and_do_kml(tree, file)
        epilogue_kml(file)


def main():
    file_xml = sys.argv[1]
    tree = get_tree(file_xml=file_xml)
    file_kml = sys.argv[2]
    generate_kml(tree=tree, filename=file_kml)
    logger.info(f"Archivo KML {file_kml} generado con éxito")

    
if __name__ == "__main__":
    main()