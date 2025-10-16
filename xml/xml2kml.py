import xml.etree.ElementTree as ET
import sys


def getTree(fileXML):
    try:
        tree = ET.parse(fileXML)
    except IOError:
        print("No se encuentra el archivo XML")
        exit()
    except ET.ParseError:
        print("Error en archivo xml")
        exit()
    return tree

def writeCoordinate(point:ET.Element, file):
    attributes = point.attrib
    latitude = attributes.get("latitud")
    longitude = attributes.get("longitud")
    altitude = attributes.get("altura")
    coordinate = longitude +  "," + latitude + "," + altitude + "\n"
    file.write(coordinate)

def traverseTreeAndAndDoKML(tree:ET.ElementTree, file):
    root = tree.getroot()
    
    plane = root.find("plano")
    startPoint = plane.find("punto")
    
    writeCoordinate(startPoint, file)
    
    phases = plane.find("tramos")
    
    for phase in phases.findall("tramo"):
        writeCoordinate(phase.find("punto"), file)


def prologueKML(file, filename):
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

def epilogueKML(file):
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
    
    
def generateKML(tree:ET.ElementTree, filename:str):
    try:
        file = open(filename, "w")
    except IOError:
        print("Archivo kml (args2) inválido")
        exit()
        
    prologueKML(file, filename)
    traverseTreeAndAndDoKML(tree, file)
    epilogueKML(file)
    file.close()
    
    
def main():
    fileXML = sys.argv[1]
    tree = getTree(fileXML=fileXML)
    fileKML = sys.argv[2]
    generateKML(tree=tree, filename=fileKML)
    
    
if __name__ == "__main__":
    main()