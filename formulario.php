<?php
$errorFormulario = false;
$formularioPOST = "";
$mostrarFormulario = false;
$tiempoTranscurrido = "";

// Inicializar el cronómetro si existe la clase
if (class_exists('Cronometro')) {
    $cronometro = new Cronometro();
}

$errorP1 = "";
$errorP2 = "";
$errorP3 = "";
$errorP4 = "";
$errorP5 = "";
$errorP6 = "";
$errorP7 = "";
$errorP8 = "";
$errorP9 = "";
$errorP10 = "";

// Verificar si se ha presionado "Iniciar prueba"
if (isset($_POST['iniciar_prueba'])) {
    $mostrarFormulario = true;
    if (isset($cronometro)) {
        $cronometro->arrancar();
    }
}

// Solo se ejecutará si se han enviado los datos desde el formulario al pulsar el boton Finalizar
if (isset($_POST['finalizar_prueba'])) {
    $mostrarFormulario = true;
    $formularioPOST = $_POST;

    // Validar todas las preguntas
    if (empty($_POST["p1"])) {
        $errorP1 = " * Esta pregunta es obligatoria ";
        $errorFormulario = true;
    }

    if (empty($_POST["p2"])) {
        $errorP2 = " * Esta pregunta es obligatoria ";
        $errorFormulario = true;
    }

    if (empty($_POST["p3"])) {
        $errorP3 = " * Debes seleccionar una opción ";
        $errorFormulario = true;
    }

    if (empty($_POST["p4"])) {
        $errorP4 = " * Debes seleccionar una opción ";
        $errorFormulario = true;
    }

    if (empty($_POST["p5"])) {
        $errorP5 = " * Debes seleccionar una opción ";
        $errorFormulario = true;
    }

    if (empty($_POST["p6"])) {
        $errorP6 = " * Esta pregunta es obligatoria ";
        $errorFormulario = true;
    }

    if (empty($_POST["p7"])) {
        $errorP7 = " * Debes seleccionar una opción ";
        $errorFormulario = true;
    }

    if (empty($_POST["p8"])) {
        $errorP8 = " * Esta pregunta es obligatoria ";
        $errorFormulario = true;
    }

    if (empty($_POST["p9"])) {
        $errorP9 = " * Debes seleccionar una opción ";
        $errorFormulario = true;
    }

    if (empty($_POST["p10"])) {
        $errorP10 = " * Esta pregunta es obligatoria ";
        $errorFormulario = true;
    }

    // Si no hay errores, parar el cronómetro
    if (!$errorFormulario && isset($cronometro)) {
        $cronometro->parar();
        // Obtener el tiempo transcurrido si el método existe
        if (method_exists($cronometro, 'getTiempoTranscurrido')) {
            $tiempoTranscurrido = $cronometro->getTiempoTranscurrido();
        }
    }
}
?>

<?php if (!$mostrarFormulario): ?>
    <!-- Mostrar botón para iniciar la prueba -->
    <form action='#' method='post'>
        <input type='submit' name='iniciar_prueba' value='Iniciar prueba'/>
    </form>
<?php else: ?>
    <!-- Mostrar formulario completo -->
    <form action='#' method='post' name='formulario'>
        <?php if ($tiempoTranscurrido): ?>
            <p><strong>Tiempo empleado: <?php echo htmlspecialchars($tiempoTranscurrido); ?></strong></p>
        <?php endif; ?>

        <p>1. ¿Cuándo se disputó la carrera de Motegi en 2025?</p>
        <p>
            <input type='text' name='p1' value="<?php echo isset($_POST['p1']) ? htmlspecialchars($_POST['p1']) : ''; ?>"/>
            <span><?php echo $errorP1; ?></span>
        </p>

        <p>2. ¿Qué tres tipos de archivos puede procesar y renderizar la vista de "Circuito"?</p>
        <p>
            <input type='text' name='p2' value="<?php echo isset($_POST['p2']) ? htmlspecialchars($_POST['p2']) : ''; ?>"/>
            <span><?php echo $errorP2; ?></span>
        </p>

        <p>3. ¿Qué información meteorológica se proporciona específicamente para la carrera?</p>
        <p>
            <input type='radio' name='p3' value='Temperatura media diaria' <?php echo (isset($_POST['p3']) && $_POST['p3'] == 'Temperatura media diaria') ? 'checked' : ''; ?>/> Temperatura media diaria<br>
            <input type='radio' name='p3' value='Información detallada en la hora de la carrera' <?php echo (isset($_POST['p3']) && $_POST['p3'] == 'Información detallada en la hora de la carrera') ? 'checked' : ''; ?>/> Información detallada en la hora de la carrera<br>
            <input type='radio' name='p3' value='Pronóstico semanal completo' <?php echo (isset($_POST['p3']) && $_POST['p3'] == 'Pronóstico semanal completo') ? 'checked' : ''; ?>/> Pronóstico semanal completo<br>
            <input type='radio' name='p3' value='Solo probabilidad de lluvia' <?php echo (isset($_POST['p3']) && $_POST['p3'] == 'Solo probabilidad de lluvia') ? 'checked' : ''; ?>/> Solo probabilidad de lluvia
            <span><?php echo $errorP3; ?></span>
        </p>

        <p>4. ¿Qué se muestra en el apartado de "Clasificaciones"?</p>
        <p>
            <input type='radio' name='p4' value='Solo el ganador de la carrera' <?php echo (isset($_POST['p4']) && $_POST['p4'] == 'Solo el ganador de la carrera') ? 'checked' : ''; ?>/> Solo el ganador de la carrera<br>
            <input type='radio' name='p4' value='Ganador de la carrera y clasificación del mundial' <?php echo (isset($_POST['p4']) && $_POST['p4'] == 'Ganador de la carrera y clasificación del mundial') ? 'checked' : ''; ?>/> Ganador de la carrera y clasificación del mundial<br>
            <input type='radio' name='p4' value='Resultados de todas las temporadas' <?php echo (isset($_POST['p4']) && $_POST['p4'] == 'Resultados de todas las temporadas') ? 'checked' : ''; ?>/> Resultados de todas las temporadas<br>
            <input type='radio' name='p4' value='Estadísticas de los pilotos' <?php echo (isset($_POST['p4']) && $_POST['p4'] == 'Estadísticas de los pilotos') ? 'checked' : ''; ?>/> Estadísticas de los pilotos
            <span><?php echo $errorP4; ?></span>
        </p>

        <p>5. ¿Cuál es la principal diferencia entre el cronómetro en JavaScript y el cronómetro en PHP?</p>
        <p>
            <input type='radio' name='p5' value='JavaScript es más preciso' <?php echo (isset($_POST['p5']) && $_POST['p5'] == 'JavaScript es más preciso') ? 'checked' : ''; ?>/> JavaScript es más preciso<br>
            <input type='radio' name='p5' value='JavaScript se ejecuta en cliente, PHP en servidor' <?php echo (isset($_POST['p5']) && $_POST['p5'] == 'JavaScript se ejecuta en cliente, PHP en servidor') ? 'checked' : ''; ?>/> JavaScript se ejecuta en cliente, PHP en servidor<br>
            <input type='radio' name='p5' value='PHP tiene más funciones' <?php echo (isset($_POST['p5']) && $_POST['p5'] == 'PHP tiene más funciones') ? 'checked' : ''; ?>/> PHP tiene más funciones<br>
            <input type='radio' name='p5' value='No hay diferencia' <?php echo (isset($_POST['p5']) && $_POST['p5'] == 'No hay diferencia') ? 'checked' : ''; ?>/> No hay diferencia
            <span><?php echo $errorP5; ?></span>
        </p>

        <p>6. ¿Cuál es el nombre completo del piloto destacado en la página "Piloto"?</p>
        <p>
            <input type='text' name='p6' value="<?php echo isset($_POST['p6']) ? htmlspecialchars($_POST['p6']) : ''; ?>"/>
            <span><?php echo $errorP6; ?></span>
        </p>

        <p>7. ¿En qué equipo corrió Luca Marini en el año 2024?</p>
        <p>
            <select name='p7'>
                <option value='' <?php echo (!isset($_POST['p7']) || $_POST['p7'] == '') ? 'selected' : ''; ?>>Selecciona una opción</option>
                <option value='Ducati Corse' <?php echo (isset($_POST['p7']) && $_POST['p7'] == 'Ducati Corse') ? 'selected' : ''; ?>>Ducati Corse</option>
                <option value='Honda Repsol Team' <?php echo (isset($_POST['p7']) && $_POST['p7'] == 'Honda Repsol Team') ? 'selected' : ''; ?>>Honda Repsol Team</option>
                <option value='Yamaha Factory Racing' <?php echo (isset($_POST['p7']) && $_POST['p7'] == 'Yamaha Factory Racing') ? 'selected' : ''; ?>>Yamaha Factory Racing</option>
                <option value='Aprilia Racing' <?php echo (isset($_POST['p7']) && $_POST['p7'] == 'Aprilia Racing') ? 'selected' : ''; ?>>Aprilia Racing</option>
            </select>
            <span><?php echo $errorP7; ?></span>
        </p>

        <p>8. ¿Cuántos puntos obtuvo Luca Marini en la temporada 2024?</p>
        <p>
            <input type='text' name='p8' value="<?php echo isset($_POST['p8']) ? htmlspecialchars($_POST['p8']) : ''; ?>"/>
            <span><?php echo $errorP8; ?></span>
        </p>

        <p>9. ¿Quién ganó la última carrera en Motegi?</p>
        <p>
            <select name='p9'>
                <option value='' <?php echo (!isset($_POST['p9']) || $_POST['p9'] == '') ? 'selected' : ''; ?>>Selecciona una opción</option>
                <option value='Marc Marquez' <?php echo (isset($_POST['p9']) && $_POST['p9'] == 'Marc Marquez') ? 'selected' : ''; ?>>Marc Marquez</option>
                <option value='Francesco Bagnaia' <?php echo (isset($_POST['p9']) && $_POST['p9'] == 'Francesco Bagnaia') ? 'selected' : ''; ?>>Francesco Bagnaia</option>
                <option value='Alex Marquez' <?php echo (isset($_POST['p9']) && $_POST['p9'] == 'Alex Marquez') ? 'selected' : ''; ?>>Alex Marquez</option>
                <option value='Luca Marini' <?php echo (isset($_POST['p9']) && $_POST['p9'] == 'Luca Marini') ? 'selected' : ''; ?>>Luca Marini</option>
            </select>
            <span><?php echo $errorP9; ?></span>
        </p>

        <p>10. ¿Quién lideraba el campeonato después de la carrera de Motegi y con cuántos puntos?</p>
        <p>
            <input type='text' name='p10' value="<?php echo isset($_POST['p10']) ? htmlspecialchars($_POST['p10']) : ''; ?>"/>
            <span><?php echo $errorP10; ?></span>
        </p>

        <p>
            <input type='submit' name='finalizar_prueba' value='Finalizar prueba'/>
        </p>
    </form>

    <?php
    if (isset($_POST['finalizar_prueba'])) {
        echo "<h3>Array asociativo enviado por POST</h3>";
        echo "<pre>";
        print_r($formularioPOST);
        echo "</pre>";

        if ($errorFormulario) {
            echo "<h4>Formulario NO PROCESADO en el servidor</h4>";
            echo "<p>Por favor, responde todas las preguntas.</p>";
        } else {
            echo "<h4>Formulario PROCESADO correctamente</h4>";
            if ($tiempoTranscurrido) {
                echo "<p>Tiempo empleado para completar la prueba: " . htmlspecialchars($tiempoTranscurrido) . "</p>";
            }
        }
    }
    ?>
<?php endif; ?>