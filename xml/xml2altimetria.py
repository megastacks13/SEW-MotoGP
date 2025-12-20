"""
!/usr/bin/env python3
-*- coding: utf-8 -*-

@author: Jaime Alonso Fernández UO294024
@date: 2025-10-16
@description: Convierte un archivo XML que siga las pautas de circuito.xsd en un altímetro svg.

Para la ejecución de este código se emplea un comando similar al visto en xml2svg:
python xml2altimetria.py circuitoEsquema.xml salida.svg
"""
import xml.etree.ElementTree as ET
import sys
import logging

NAMESPACE = {"c": "http://uniovi.es/circuito"}
logging.basicConfig(level=logging.INFO, format="%(levelname)s: %(message)s")
logger = logging.getLogger(__name__)


class Svg(object):
    """
    Genera archivos SVG con rectángulos, círculos, líneas, polilíneas y texto
    @version 1.0 18/Octubre/2024
    @author: Juan Manuel Cueva Lovelle. Universidad de Oviedo
    """

    def __init__(self):
        """
        Crea el elemento raíz, el espacio de nombres y la versión
        """
        self.raiz = ET.Element('svg', xmlns="http://www.w3.org/2000/svg", version="1.1", viewBox="0 0 1700 800")

    def addRect(self, x, y, width, height, fill, strokeWidth, stroke):
        """
        Añade un elemento rect
        """
        ET.SubElement(self.raiz, 'rect',
                      x=x,
                      y=y,
                      width=width,
                      height=height,
                      fill=fill,
                      **{'stroke-width':strokeWidth},
                      stroke=stroke)

    def addCircle(self, cx, cy, r, fill):
        """
        Añade un elemento circle
        """
        ET.SubElement(self.raiz, 'circle',
                      cx=cx,
                      cy=cy,
                      r=r,
                      fill=fill)

    def addLine(self, x1, y1, x2, y2, stroke, strokeWidth):
        """
        Añade un elemento line
        """
        ET.SubElement(self.raiz, 'line',
                      x1=x1,
                      y1=y1,
                      x2=x2,
                      y2=y2,
                      stroke=stroke,
                      **{'stroke-width':strokeWidth})

    def addPolyline(self, points, stroke, strokeWidth, fill):
        """
        Añade un elemento polyline
        """
        ET.SubElement(self.raiz, 'polyline',
                      points=points,
                      stroke=stroke,
                      **{'stroke-width':strokeWidth},
                      fill=fill)

    def addText(self, texto, x, y, fontFamily, fontSize, style):
        """
        Añade un elemento texto
        """
        ET.SubElement(self.raiz, 'text',
                      x=x,
                      y=y,
                      **{'font-family': fontFamily},
                      **{'font-size': fontSize},
                      style=style).text = texto

    def escribir(self, nombreArchivoSVG):
        """ de
        Escribe el archivo SVG con declaración y codificación
        """
        arbol = ET.ElementTree(self.raiz)

        """
        Introduce indentación y saltos de línea
        para generar XML en modo texto
        """
        ET.indent(arbol)

        arbol.write(nombreArchivoSVG,
                    encoding='utf-8',
                    xml_declaration=True
                    )

    def ver(self):
        """
        Muestra el archivo SVG. Se utiliza para depurar
        """
        print("\nElemento raiz = ", self.raiz.tag)

        if self.raiz.text != None:
            print("Contenido = ", self.raiz.text.strip('\n'))  # strip() elimina los '\n' del string
        else:
            print("Contenido = ", self.raiz.text)

        print("Atributos = ", self.raiz.attrib)

        # Recorrido de los elementos del árbol
        for hijo in self.raiz.findall('.//'):  # Expresión XPath
            print("\nElemento = ", hijo.tag)
            if hijo.text != None:
                print("Contenido = ", hijo.text.strip('\n'))  # strip() elimina los '\n' del string
            else:
                print("Contenido = ", hijo.text)
            print("Atributos = ", hijo.attrib)

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


def get_heights_from_tree(tree: ET.ElementTree):
    """ Recorre el árbol y manda escribir los puntos en el archivo de salida """
    root = tree.getroot()
    plane = root.find("c:plano", NAMESPACE)
    heights = {}

    start_point = plane.find("c:punto", NAMESPACE)
    heights[0] = start_point.attrib.get("altura")

    phases = plane.find("c:tramos", NAMESPACE)
    for phase in phases.findall("c:tramo", NAMESPACE):
        index = phase.attrib.get("numeroSector")
        value = phase.find("c:punto", NAMESPACE).attrib.get("altura")
        heights[index] = float(value)

    return heights

def draw_svg(heights: dict, filename: str):
    """
    Dada la lista de puntos dibuja la linea que que une todos los puntos, despues los puntos los remarca
     con una circunferencia, etiqueta su altura al lado de la misma y añade en el eje x el tramo del que se trata.

     El punto 0 y el 27 son el mismo (se parte y finaliza en el inicio).
     """
    svg_instance = Svg()

    # Scaling parameters
    x_scale = 60
    y_scale = 3
    margin_left = 10
    base_y = 700

    points = []
    for i, h in enumerate(heights.values()):
        x = margin_left + i * x_scale
        y = base_y - float(h) * y_scale  # invertimos Y para que "altura" suba
        points.append(f"{x},{y}")

    svg_instance.addPolyline(
        points=" ".join(points),
        stroke="black",
        strokeWidth="2",
        fill="none"
    )

    # We draw here the base line
    svg_instance.addLine(
        x1=str(margin_left),
        y1=str(base_y),
        x2=str(margin_left + (len(heights) - 1) * x_scale),
        y2=str(base_y),
        stroke="gray",
        strokeWidth="1"
    )

    # Draw the points with the labels
    for i, h in enumerate(heights.values()):

        x = margin_left + i * x_scale
        y = base_y - float(h) * y_scale

        # Draw the point as a circle
        svg_instance.addCircle(
            cx=str(x),
            cy=str(y),
            r="4",
            fill="red"
        )

        # Height text
        svg_instance.addText(
            texto=f"{float(h):.1f}",
            x=str(x - 10),
            y=str(y - 10),
            fontFamily="Arial",
            fontSize="12",
            style="fill:blue;"
        )
        # Index text (on x axis)
        svg_instance.addText(
            texto=str(i),
            x=str(x - 5),
            y=str(base_y + 20),
            fontFamily="Arial",
            fontSize="12",
            style="fill:black;"
        )

    # Text under base
    svg_instance.addText(
        texto="Tramo",
        x=str((len(heights) - 1) * x_scale /2),
        y=str(base_y + 60),
        fontFamily="Arial",
        fontSize="14",
        style="fill:black; font-weight:bold;"
    )

    svg_instance.escribir(filename)


def main():
    file_xml = sys.argv[1]
    tree = get_tree(file_xml=file_xml)
    file_svg = sys.argv[2]
    heights = get_heights_from_tree(tree)
    draw_svg(heights, file_svg)
    logger.info(f"Archivo SVG {file_svg} generado con éxito")


if __name__ == "__main__":
    main()