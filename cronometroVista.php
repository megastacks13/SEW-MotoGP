<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset='UTF-8' />
    <meta name='author' content='Jaime Alonso Fernández'/>
    <meta name='description' content='Página de Juegos'/>
    <meta name='keywords' content='MotoGP, Moto, Motorbike, Juegos, Diversión'/>
    <meta name='keywords' content='Memoria, Cartas'/>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
    <title>MotoGP-Juegos</title>
    <link rel='stylesheet' type='text/css' href='estilo/estilo.css'/>
    <link rel='stylesheet' type='text/css' href='estilo/layout.css'/>
    <link rel='icon' href='multimedia/img/favicon.ico' type='image/x-icon'/>
</head>

<body>
<!-- Datos con el contenidos que aparece en el navegador -->
<header>
    <h1><a href='index.html'>Moto GP Desktop</a></h1>
    <nav>
        <a href='index.html'>Inicio</a>
        <a href='piloto.html'>Piloto</a>
        <a href='circuito.html'>Circuito</a>
        <a href='metereologia.html'>Metereología</a>
        <a href='clasificaciones.php'>Clasificaciones</a>
        <a href='juegos.html' class='active'>Juegos</a>
        <a href='ayuda.html'>Ayuda</a>
    </nav>
</header>
<!-- Desmigue de ubicación -->
<p>Estás en: <a href='index.html'>Inicio</a> >> <a href='juegos.html'>Juegos</a> >> <strong>Cronómetro</strong></p>
<main>
    <section>
        <h2>Cronómetro</h2>
        <form action='#' method='POST'>
            <button type='submit' name='iniciar'>Inciar</button>
            <button type='submit' name='parar'>Parar</button>
            <button type='submit' name='mostrar'>Mostrar</button>
        </form>
        <?php
        require_once "php/cronometro.php";
        if (count($_POST)>0) {
            session_start();

            if (isset($_SESSION['cronometro'])){
                $miCronometro = $_SESSION['cronometro'];
            }else{
                $miCronometro = new Cronometro();
                $_SESSION['cronometro'] = $miCronometro;
            }

            if (isset($_POST['iniciar'])) $miCronometro->arrancar();
            if (isset($_POST['parar'])) $miCronometro->parar();
            if (isset($_POST['mostrar'])) $miCronometro->mostrar();
        }
        ?>
    </section>
</main>
</body>
</html>




