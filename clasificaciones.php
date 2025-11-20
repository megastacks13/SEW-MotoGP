
<?php

class Clasificaciones{
    function __construct(){
        $this-> documento = "xml/circuitoEsquema.xml";
    }

    function consultar(){
        $this-> datos = file_get_contents($this-> documento);
        if ($this->datos === FALSE) {
            echo "Error al cargar el archivo XML";
            return;
        }
        $this-> xml = new SimpleXMLElement($this->datos);
    }
    function mostrar_ganador() {
        $piloto = (string)$this->xml->resultados->piloto;
        $tiempoISO = (string)$this->xml->resultados->tiempo;

        preg_match('/PT(\d+)M(\d+\.\d+)S/', $tiempoISO, $matches);

        $min = (int)$matches[1];
        $segDecimal = (float)$matches[2];

        $seg = floor($segDecimal);
        $ms = round(($segDecimal - $seg) * 1000);

        $tiempoFormateado = sprintf("%02d:%02d.%03d", $min, $seg, $ms);

        echo "<p>$piloto - $tiempoFormateado</p>";
    }


    function mostrar_ranking() {
        echo "<table>
        <tr>
            <th id='tabPos' scope='col'>Posición</th>
            <th id='tabNom' scope='col'>Nombre</th>
            <th id='tabPuntos' scope='col'>Puntos</th>
        </tr>";

        $pos = 1;

        foreach ($this->xml->rankingMundial->piloto as $piloto) {
            $nombre = $piloto;
            $puntos = $piloto['puntos'];

            echo "<tr>
                <td headers='tabPos'>{$pos}</td>
                <td headers='tabNom'>{$nombre}</td>
                <td headers='tabPuntos'>{$puntos}</td>
              </tr>";

            $pos++;
        }

        echo "</table>";
    }

}
?>




<!DOCTYPE HTML>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="author" content="Jaime Alonso Fernández"/>
    <meta name="description" content="Página de clasificaciones"/>
    <meta name="keywords" content="MotoGP, Moto, Motorbike, Competición, Resultados, Clasificación"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>MotoGP-Clasificaciones</title>
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css"/>
    <link rel="stylesheet" type="text/css" href="estilo/layout.css"/>
    <link rel="icon" href="multimedia/img/favicon.ico" type="image/x-icon"/>
</head>

<body>
<!-- Datos con el contenidos que aparece en el navegador -->
<header>
    <h1><a href="index.html">Moto GP Desktop</a></h1>
    <nav>
        <a href="index.html">Inicio</a>
        <a href="piloto.html">Piloto</a>
        <a href="circuito.html">Circuito</a>
        <a href="metereologia.html">Metereología</a>
        <a href="clasificaciones.php" class="active">Clasificaciones</a>
        <a href="juegos.html">Juegos</a>
        <a href="ayuda.html">Ayuda</a>
    </nav>
</header>
    <!-- Desmigue de ubicación -->
    <p>Estás en: <a href="index.html">Inicio</a> >> <strong>Clasificaciones</strong></p>
<main>
    <h2>Ganador de la carrera</h2>
    <?php
        $clasificaciones = new Clasificaciones();
        $clasificaciones->consultar();
        $clasificaciones-> mostrar_ganador();
        echo "<h2>Estado del mundial tras la carrera</h2>";
        $clasificaciones-> mostrar_ranking();
    ?>
</main>
</body>
</html>