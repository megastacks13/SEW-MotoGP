<?php
require_once "configuracion.php";
require_once "claseCronometro.php";

session_start();

$config = new Configuracion();

$error = "";
$errores = [];
$valores = [];

// Inicializar valores para los inputs
for ($i = 1; $i <= 10; $i++) {
    $valores["p$i"] = "";
}

// Si se pulsó el botón "Iniciar formulario"
if (isset($_POST['iniciar_formulario'])) {
    $_SESSION['cronometro_formulario'] = new Cronometro();
    $_SESSION['cronometro_formulario']->arrancar();
    $_SESSION['formulario_iniciado'] = true;
}

// Si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["enviar_respuestas"])) {

    // Guardar valores para mantenerlos en el formulario
    for ($i = 1; $i <= 10; $i++) {
        // Manejar diferentes tipos de inputs
        if ($i == 2) {
            // Pregunta 2: checkbox múltiple
            $valores["p$i"] = isset($_POST["p$i"]) ? implode(", ", $_POST["p$i"]) : "";
        } else {
            $valores["p$i"] = isset($_POST["p$i"]) ? $_POST["p$i"] : "";
        }

        // Validar que no esté vacío
        if (empty($valores["p$i"])) {
            $errores["p$i"] = " * Esta pregunta es obligatoria";
        }
    }

    // No hay errores → Guardar en la BBDD
    if (empty($errores)) {
        // Parar el cronómetro y guardar el tiempo en sesión
        if (isset($_SESSION['cronometro_formulario'])) {
            $_SESSION['cronometro_formulario']->parar();
            $_SESSION['tiempo_formulario'] = $_SESSION['cronometro_formulario']->getTiempo();
        }

        $res = $config->insertarTestPreguntas(
                $valores["p1"],
                $valores["p2"],
                $valores["p3"],
                $valores["p4"],
                $valores["p5"],
                $valores["p6"],
                $valores["p7"],
                $valores["p8"],
                $valores["p9"],
                $valores["p10"]
        );

        if ($res["success"]) {
            header("Location: opiniones.php");
            exit();
        } else {
            $error = $res["error"];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset='UTF-8' />
    <meta name='author' content='Jaime Alonso Fernández'/>
    <meta name='description' content='Página de Juegos'/>
    <meta name='keywords' content='MotoGP, Moto, Motorbike, Usuario, Ingreso'/>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
    <title>MotoGP-Juegos</title>
    <link rel='stylesheet' type='text/css' href='estilo/estilo.css'/>
    <link rel='stylesheet' type='text/css' href='estilo/layout.css'/>
    <link rel='icon' href='multimedia/img/favicon.ico' type='image/x-icon'/>
</head>

<body>
<header>
    <h1>Moto GP Desktop</h1>
</header>

<main>
    <h2>Test de Preguntas Inicial</h2>

    <?php if ($error): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (!isset($_SESSION['formulario_iniciado'])): ?>
        <!-- Botón para iniciar el formulario -->
        <p>Pulse el botón para comenzar el test. Se medirá el tiempo que tarda en completarlo.</p>
        <form action="" method="post">
            <input type="submit" name="iniciar_formulario" value="Iniciar formulario">
        </form>
    <?php else: ?>
        <!-- Formulario de preguntas (solo visible después de iniciar) -->
        <p><strong>Instrucciones:</strong> Complete todas las preguntas. El tiempo está siendo medido desde que inició el test.</p>

        <form action="" method="post">
            <p>1. ¿Cuándo se disputó la carrera de Motegi en 2025?</p>
            <input type="date" name="p1" value="<?php echo isset($valores['p1']) ? $valores['p1'] : '' ?>">
            <?php echo isset($errores['p1']) ? $errores['p1'] : '' ?>

            <p>2. ¿Qué tipos de archivos puede procesar y renderizar la vista de "Circuito"? (Seleccione todos los que correspondan)</p>
            <input type="checkbox" name="p2[]" value="JPEG" <?php echo (isset($valores['p2']) && strpos($valores['p2'], 'JPEG') !== false) ? 'checked' : '' ?>> JPEG<br>
            <input type="checkbox" name="p2[]" value="PNG" <?php echo (isset($valores['p2']) && strpos($valores['p2'], 'PNG') !== false) ? 'checked' : '' ?>> PNG<br>
            <input type="checkbox" name="p2[]" value="SVG" <?php echo (isset($valores['p2']) && strpos($valores['p2'], 'SVG') !== false) ? 'checked' : '' ?>> SVG<br>
            <input type="checkbox" name="p2[]" value="PDF" <?php echo (isset($valores['p2']) && strpos($valores['p2'], 'PDF') !== false) ? 'checked' : '' ?>> PDF<br>
            <input type="checkbox" name="p2[]" value="MP4" <?php echo (isset($valores['p2']) && strpos($valores['p2'], 'MP4') !== false) ? 'checked' : '' ?>> MP4
            <?php echo isset($errores['p2']) ? $errores['p2'] : '' ?>

            <p>3. ¿Qué información meteorológica se proporciona específicamente para la carrera?</p>
            <select name="p3">
                <option value="">Seleccione una opción</option>
                <option value="Temperatura" <?php echo (isset($valores['p3']) && $valores['p3'] == 'Temperatura') ? 'selected' : '' ?>>Temperatura</option>
                <option value="Humedad" <?php echo (isset($valores['p3']) && $valores['p3'] == 'Humedad') ? 'selected' : '' ?>>Humedad</option>
                <option value="Viento" <?php echo (isset($valores['p3']) && $valores['p3'] == 'Viento') ? 'selected' : '' ?>>Viento</option>
                <option value="Todas las anteriores" <?php echo (isset($valores['p3']) && $valores['p3'] == 'Todas las anteriores') ? 'selected' : '' ?>>Todas las anteriores</option>
            </select>
            <?php echo isset($errores['p3']) ? $errores['p3'] : '' ?>

            <p>4. ¿Qué se muestra en el apartado de "Clasificaciones"?</p>
            <input type="text" name="p4" value="<?php echo isset($valores['p4']) ? $valores['p4'] : '' ?>" placeholder="Escriba su respuesta">
            <?php echo isset($errores['p4']) ? $errores['p4'] : '' ?>

            <p>5. ¿Cuál es la principal diferencia entre el cronómetro en JavaScript y el cronómetro en PHP?</p>
            <input type="radio" name="p5" value="JavaScript se ejecuta en el servido y PHP en el cliente" <?php echo (isset($valores['p5']) && $valores['p5'] == 'JavaScript se ejecuta en el cliente') ? 'checked' : '' ?>> JavaScript se ejecuta en el cliente<br>
            <input type="radio" name="p5" value="PHP se ejecuta en el servido y JavaScript en el cliente" <?php echo (isset($valores['p5']) && $valores['p5'] == 'PHP se ejecuta en el servidor') ? 'checked' : '' ?>> PHP se ejecuta en el servidor<br>
            <input type="radio" name="p5" value="JavaScript es más preciso" <?php echo (isset($valores['p5']) && $valores['p5'] == 'JavaScript es más preciso') ? 'checked' : '' ?>> JavaScript es más preciso<br>
            <input type="radio" name="p5" value="PHP necesita recargar la página" <?php echo (isset($valores['p5']) && $valores['p5'] == 'PHP necesita recargar la página') ? 'checked' : '' ?>> PHP necesita recargar la página
            <?php echo isset($errores['p5']) ? $errores['p5'] : '' ?>

            <p>6. ¿Cuál es el nombre completo del piloto destacado en la página "Piloto"?</p>
            <input type="text" name="p6" value="<?php echo isset($valores['p6']) ? $valores['p6'] : '' ?>">
            <?php echo isset($errores['p6']) ? $errores['p6'] : '' ?>

            <p>7. ¿En qué equipo corrió Luca Marini en el año 2024?</p>
            <input type="radio" name="p7" value="Repsol Honda" <?php echo (isset($valores['p7']) && $valores['p7'] == 'Repsol Honda') ? 'checked' : '' ?>> Repsol Honda<br>
            <input type="radio" name="p7" value="Ducati Lenovo" <?php echo (isset($valores['p7']) && $valores['p7'] == 'Ducati Lenovo') ? 'checked' : '' ?>> Ducati Lenovo<br>
            <input type="radio" name="p7" value="Monster Energy Yamaha" <?php echo (isset($valores['p7']) && $valores['p7'] == 'Monster Energy Yamaha') ? 'checked' : '' ?>> Monster Energy Yamaha<br>
            <input type="radio" name="p7" value="Aprilia Racing" <?php echo (isset($valores['p7']) && $valores['p7'] == 'Aprilia Racing') ? 'checked' : '' ?>> Aprilia Racing
            <?php echo isset($errores['p7']) ? $errores['p7'] : '' ?>

            <p>8. ¿Cuántos puntos obtuvo Luca Marini en la temporada 2024?</p>
            <input type="number" name="p8" value="<?php echo isset($valores['p8']) ? $valores['p8'] : '' ?>" min="0" max="500">
            <?php echo isset($errores['p8']) ? $errores['p8'] : '' ?>

            <p>9. ¿Quién ganó la última carrera en Motegi?</p>
            <input type="text" name="p9" value="<?php echo isset($valores['p9']) ? $valores['p9'] : '' ?>">
            <?php echo isset($errores['p9']) ? $errores['p9'] : '' ?>

            <p>10. ¿Quién lideraba el campeonato después de la carrera de Motegi y con cuántos puntos? (Seleccione la opción correcta)</p>
            <select name="p10">
                <option value="">Seleccione una opción</option>
                <option value="Pecco Bagnaia - 285 puntos" <?php echo (isset($valores['p10']) && $valores['p10'] == 'Pecco Bagnaia - 285 puntos') ? 'selected' : '' ?>>Pecco Bagnaia - 285 puntos</option>
                <option value="Jorge Martín - 275 puntos" <?php echo (isset($valores['p10']) && $valores['p10'] == 'Jorge Martín - 275 puntos') ? 'selected' : '' ?>>Jorge Martín - 275 puntos</option>
                <option value="Marc Márquez - 265 puntos" <?php echo (isset($valores['p10']) && $valores['p10'] == 'Marc Márquez - 265 puntos') ? 'selected' : '' ?>>Marc Márquez - 265 puntos</option>
                <option value="Enea Bastianini - 255 puntos" <?php echo (isset($valores['p10']) && $valores['p10'] == 'Enea Bastianini - 255 puntos') ? 'selected' : '' ?>>Enea Bastianini - 255 puntos</option>
            </select>
            <?php echo isset($errores['p10']) ? $errores['p10'] : '' ?>

            <p><input type="submit" name="enviar_respuestas" value="Enviar respuestas"></p>
        </form>
    <?php endif; ?>
</main>
</body>
</html>