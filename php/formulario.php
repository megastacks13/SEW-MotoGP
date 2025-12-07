<?php
require_once "configuracion.php";
require_once "cronometro.php";

session_start();

$config = new Configuracion();

$error = "";
$errores = [];
$valores = [];

// Inicializar valores
for ($i = 1; $i <= 10; $i++) {
    $valores["p$i"] = "";
}

// Si el usuario termina registro → inicia formulario
if (isset($_POST['finalizar_registro'])) {

    $resultado = $config->insertarUsuario(
            $_POST['profesion'],
            $_POST['edad'],
            $_POST['genero'],
            $_POST['pericia']
    );

    if ($resultado['success']) {
        // Iniciar cronómetro automáticamente
        $_SESSION['cronometro_formulario'] = new Cronometro();
        $_SESSION['cronometro_formulario']->arrancar();
        $_SESSION['formulario_iniciado'] = true;
    } else {
        $error = "Error al registrar el usuario: " . $resultado['error'];
    }
}


// Si se envían respuestas
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["enviar_respuestas"])) {

    for ($i = 1; $i <= 10; $i++) {

        if ($i == 2) {
            $valores["p$i"] = isset($_POST["p$i"]) ? implode(", ", $_POST["p$i"]) : "";
        } else {
            $valores["p$i"] = isset($_POST["p$i"]) ? $_POST["p$i"] : "";
        }

        if (empty($valores["p$i"])) {
            $errores["p$i"] = " * Esta pregunta es obligatoria";
        }
    }

    if (empty($errores)) {

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
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>MotoGP-Juegos</title>
    <link rel='stylesheet' href='../estilo/estilo.css'>
    <link rel='stylesheet' href='../estilo/layout.css'>
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

        <h2>Registro de usuario para realizar el formulario</h2>

        <form method="post">

            <p>
                <label for="profesion">¿Profesión?</label><br>
                <input id="profesion" type='text' name='profesion'
                       value="<?php echo isset($_POST['profesion']) ? htmlspecialchars($_POST['profesion']) : ''; ?>">
            </p>

            <p>
                <label for="edad">¿Edad?</label><br>
                <input id="edad" type='number' name='edad'
                       value="<?php echo isset($_POST['edad']) ? htmlspecialchars($_POST['edad']) : ''; ?>">
            </p>

            <fieldset>
                <legend>¿Género?</legend>

                <input id="genero_hombre" type='radio' name='genero' value='Hombre'
                        <?php echo (isset($_POST['genero']) && $_POST['genero'] == 'Hombre') ? 'checked' : ''; ?>>
                <label for="genero_hombre">Hombre</label><br>

                <input id="genero_mujer" type='radio' name='genero' value='Mujer'
                        <?php echo (isset($_POST['genero']) && $_POST['genero'] == 'Mujer') ? 'checked' : ''; ?>>
                <label for="genero_mujer">Mujer</label><br>

                <input id="genero_otro" type='radio' name='genero' value='Otro'
                        <?php echo (isset($_POST['genero']) && $_POST['genero'] == 'Otro') ? 'checked' : ''; ?>>
                <label for="genero_otro">Otro</label>
            </fieldset>

            <fieldset>
                <legend>¿Pericia informática? (0-10)</legend>

                <?php
                for ($i = 0; $i <= 10; $i++):
                    $checked = isset($_POST['pericia']) && $_POST['pericia'] == $i ? 'checked' : '';
                    ?>
                    <input id="pericia_<?php echo $i; ?>" type="radio" name="pericia" value="<?php echo $i; ?>" <?php echo $checked; ?>>
                    <label for="pericia_<?php echo $i; ?>"><?php echo $i; ?></label><br>
                <?php endfor; ?>
            </fieldset>

            <p>
                <input type='submit' name='finalizar_registro' value='Finalizar registro'>
            </p>

        </form>

    <?php else: ?>

        <p><strong>Instrucciones:</strong> Complete todas las preguntas. El tiempo está siendo medido.</p>

        <form method="post">

            <!-- P1 -->
            <p>
                <label for="p1">1. ¿Qué día hizo más frío en los entrenamientos?</label><br>
                <select id="p1" name="p1">
                    <option value="">Seleccione una opción</option>
                    <?php
                    $options = ["24-Sept", "25-Sept", "26-Sept", "27-Sept"];
                    foreach ($options as $op):
                        $sel = ($valores['p1'] == $op) ? "selected" : "";
                        echo "<option value='$op' $sel>$op</option>";
                    endforeach;
                    ?>
                </select><br>
                <?php echo isset($errores['p1']) ? $errores['p1'] : ''; ?>
            </p>

            <!-- P2 -->
            <p>
            <fieldset>
                <legend>2. ¿Qué tipos de archivos puede procesar la vista "Circuito"?</legend>

                <?php
                $files = ["KML","PNG","SVG","PDF","HTML"];
                foreach ($files as $f):
                    $id = "p2_" . strtolower($f);
                    $checked = (isset($valores['p2']) && strpos($valores['p2'], $f) !== false) ? "checked" : "";
                    ?>
                    <input id="<?php echo $id; ?>" type="checkbox" name="p2[]" value="<?php echo $f; ?>" <?php echo $checked; ?>>
                    <label for="<?php echo $id; ?>"><?php echo $f; ?></label><br>
                <?php endforeach; ?>

            </fieldset><br>
            <?php echo isset($errores['p2']) ? $errores['p2'] : ''; ?>
            </p>

            <!-- P3 -->
            <p>
                <label for="p3">3. ¿Qué información meteorológica se proporciona?</label><br>
                <select id="p3" name="p3">
                    <option value="">Seleccione una opción</option>
                    <?php
                    $opts = ["Temperatura","Humedad","Viento","Todas las anteriores"];
                    foreach ($opts as $op):
                        $sel = ($valores['p3'] == $op) ? "selected" : "";
                        echo "<option value='$op' $sel>$op</option>";
                    endforeach;
                    ?>
                </select><br>
                <?php echo isset($errores['p3']) ? $errores['p3'] : ''; ?>
            </p>

            <!-- P4 -->
            <p>
                <label for="p4">4. ¿Qué se muestra en "Clasificaciones"?</label><br>
                <select id="p4" name="p4">
                    <option value="">Seleccione una opción</option>
                    <?php
                    $ops = [
                            "La clasificación global después de la carrera",
                            "Los ganadores de la carrera",
                            "La clasificación al final de la temporada",
                            "La clasificación de la temporada pasada"
                    ];
                    foreach ($ops as $op):
                        $sel = ($valores['p4'] == $op) ? "selected" : "";
                        echo "<option value='$op' $sel>$op</option>";
                    endforeach;
                    ?>
                </select><br>
                <?php echo isset($errores['p4']) ? $errores['p4'] : ''; ?>
            </p>

            <!-- P5 -->
            <p>
            <fieldset>
                <legend>5. Diferencia entre cronómetro JS y PHP</legend>

                <?php
                $opciones = [
                        "JavaScript se ejecuta en el servidor y PHP en el cliente",
                        "PHP se ejecuta en el servidor y JavaScript en el cliente",
                        "JavaScript es más preciso",
                        "PHP necesita recargar la página"
                ];

                $i = 1;
                foreach ($opciones as $op):
                    $id = "p5_op$i";
                    $checked = ($valores['p5'] == $op) ? "checked" : "";
                    ?>
                    <input id="<?php echo $id; ?>" type="radio" name="p5" value="<?php echo $op; ?>" <?php echo $checked; ?>>
                    <label for="<?php echo $id; ?>"><?php echo $op; ?></label><br>
                    <?php $i++; endforeach; ?>

            </fieldset><br>
            <?php echo isset($errores['p5']) ? $errores['p5'] : ''; ?>
            </p>

            <!-- P6 -->
            <p>
                <label for="p6">6. ¿Nombre completo del piloto destacado?</label><br>
                <input id="p6" type="text" name="p6" value="<?php echo $valores['p6']; ?>"><br>
                <?php echo isset($errores['p6']) ? $errores['p6'] : ''; ?>
            </p>

            <!-- P7 -->
            <p>
            <fieldset>
                <legend>7. ¿En qué equipo corrió Luca Marini en 2024?</legend>

                <?php
                $equipos = ["Repsol Honda","Ducati Lenovo","Monster Energy Yamaha","Aprilia Racing"];
                $i = 1;
                foreach ($equipos as $eq):
                    $id = "p7_op$i";
                    $checked = ($valores['p7'] == $eq) ? "checked" : "";
                    ?>
                    <input id="<?php echo $id; ?>" type="radio" name="p7" value="<?php echo $eq; ?>" <?php echo $checked; ?>>
                    <label for="<?php echo $id; ?>"><?php echo $eq; ?></label><br>
                    <?php $i++; endforeach; ?>

            </fieldset><br>
            <?php echo isset($errores['p7']) ? $errores['p7'] : ''; ?>
            </p>

            <!-- P8 -->
            <p>
                <label for="p8">8. ¿Cuántos puntos obtuvo Luca Marini?</label><br>
                <input id="p8" type="number" name="p8" value="<?php echo $valores['p8']; ?>" min="0" max="500"><br>
                <?php echo isset($errores['p8']) ? $errores['p8'] : ''; ?>
            </p>

            <!-- P9 -->
            <p>
                <label for="p9">9. ¿Quién ganó la última carrera en Motegi?</label><br>
                <input id="p9" type="text" name="p9" value="<?php echo $valores['p9']; ?>"><br>
                <?php echo isset($errores['p9']) ? $errores['p9'] : ''; ?>
            </p>

            <!-- P10 -->
            <p>
                <label for="p10">10. ¿Quién lideraba el campeonato después de Motegi?</label><br>
                <select id="p10" name="p10">
                    <option value="">Seleccione una opción</option>
                    <?php
                    $opts = [
                            "Pecco Bagnaia - 514 puntos",
                            "Jorge Martín - 541 puntos",
                            "Marc Márquez - 541 puntos",
                            "Enea Bastianini - 560 puntos"
                    ];
                    foreach ($opts as $op):
                        $sel = ($valores['p10'] == $op) ? "selected" : "";
                        echo "<option value='$op' $sel>$op</option>";
                    endforeach;
                    ?>
                </select><br>
                <?php echo isset($errores['p10']) ? $errores['p10'] : ''; ?>
            </p>

            <p>
                <input type="submit" name="enviar_respuestas" value="Enviar respuestas">
            </p>

        </form>
    <?php endif; ?>
</main>

</body>
</html>