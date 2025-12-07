<?php

class Clasificaciones{
    private $documento;
    private $xml;

    function __construct(){
        $this->documento = $_SERVER['DOCUMENT_ROOT'] . '/xml/circuitoEsquema.xml';
    }

    function consultar(){
        if (!file_exists($this->documento)) {
            return false;
        }

        $content = file_get_contents($this->documento);
        if ($content === false) {
            return false;
        }

        libxml_use_internal_errors(true);
        $this->xml = simplexml_load_string($content);

        if ($this->xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            return false;
        }

        return true;
    }

    function mostrar_ganador() {
        if (!$this->xml) {
            echo "<p>Error cargando datos</p>";
            return;
        }

        $this->xml->registerXPathNamespace('c', 'http://uniovi.es/circuito');
        $resultados = $this->xml->xpath('//c:resultados');

        if (empty($resultados)) {
            echo "<p>No se encontraron resultados</p>";
            return;
        }

        $piloto = (string)$resultados[0]->piloto;
        $tiempoISO = (string)$resultados[0]->tiempo;

        preg_match('/PT(\d+)M(\d+\.\d+)S/', $tiempoISO, $matches);

        if (isset($matches[1]) && isset($matches[2])) {
            $min = (int)$matches[1];
            $segDecimal = (float)$matches[2];
            $seg = floor($segDecimal);
            $ms = round(($segDecimal - $seg) * 1000);
            $tiempoFormateado = sprintf("%02d:%02d.%03d", $min, $seg, $ms);
            echo "<p>$piloto - $tiempoFormateado</p>";
        } else {
            echo "<p>$piloto - $tiempoISO</p>";
        }
    }

    function mostrar_ranking() {
        if (!$this->xml) {
            echo "<p>Error cargando datos del ranking</p>";
            return;
        }

        $this->xml->registerXPathNamespace('c', 'http://uniovi.es/circuito');
        $pilotos = $this->xml->xpath('//c:rankingMundial/c:piloto');

        if (empty($pilotos)) {
            echo "<p>No se encontraron datos del ranking</p>";
            return;
        }

        echo "<table>
        <tr>
            <th id='tabPos' scope='col'>Posición</th>
            <th id='tabNom' scope='col'>Nombre</th>
            <th id='tabPuntos' scope='col'>Puntos</th>
        </tr>";

        $pos = 1;
        foreach ($pilotos as $piloto) {
            $nombre = (string)$piloto;
            $puntos = isset($piloto['puntos']) ? (string)$piloto['puntos'] : '0';

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

<p>Estás en: <a href="index.html">Inicio</a> >> <strong>Clasificaciones</strong></p>

<main>
    <h2>Ganador de la carrera</h2>
    <?php
    $clasificaciones = new Clasificaciones();
    if ($clasificaciones->consultar()) {
        $clasificaciones->mostrar_ganador();
        echo "<h2>Estado del mundial tras la carrera</h2>";
        $clasificaciones->mostrar_ranking();
    } else {
        echo "<p>Error al cargar los datos de clasificación.</p>";
    }
    ?>
</main>
</body>
</html>