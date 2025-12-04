<?php
session_start();
require_once "configuracion.php";

$config = new Configuracion();
$errores = [];
$mensaje = "";
$mostrar_observaciones = false; // Controla qué formulario mostrar

// --------------- Lógica de POST ----------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Guardar test de usabilidad
    if (isset($_POST["guardar_usabilidad"])) {
        $dispositivo = isset($_POST["dispositivo"]) ? $_POST["dispositivo"] : "";
        $tiempo = isset($_SESSION['tiempo_formulario']) ? round($_SESSION['tiempo_formulario']) : 0;
        $tarea = isset($_POST["tarea"]) ? 1 : 0;
        $comentarios = isset($_POST["comentarios"]) ? $_POST["comentarios"] : "";
        $mejora = isset($_POST["mejora"]) ? $_POST["mejora"] : "";
        $valoracion = isset($_POST["valoracion"]) ? $_POST["valoracion"] : "";

        if ($valoracion === "" || $valoracion < 0 || $valoracion > 10) {
            $errores["valoracion"] = "La valoración debe estar entre 0 y 10";
        }

        if (empty($errores)) {
            $res = $config->insertarTestUsabilidad($dispositivo, $tiempo, $tarea, $comentarios, $mejora, $valoracion);

            if (isset($res["success"]) && $res["success"]) {
                $mensaje = "Test de usabilidad guardado. Ahora añade las observaciones del facilitador.";
                $mostrar_observaciones = true; // mostrar formulario de observaciones
            } else {
                $errores["general"] = isset($res["error"]) ? $res["error"] : "";
            }
        }
    }

    // Guardar observación del facilitador
    if (isset($_POST["guardar_observacion"])) {
        $obs = isset($_POST["observacion"]) ? $_POST["observacion"] : "";
        $res = $config->insertarObservacionFacilitador(isset($obs)? $obs : "Sin Comentarios");

        if (isset($res["success"]) && $res["success"]) {
            // Limpiar variables de sesión
            if (isset($_SESSION['cronometro_formulario'])) unset($_SESSION['cronometro_formulario']);
            if (isset($_SESSION['formulario_iniciado'])) unset($_SESSION['formulario_iniciado']);
            if (isset($_SESSION['tiempo_formulario'])) unset($_SESSION['tiempo_formulario']);

            // ----------------- Página de agradecimiento -----------------
            echo '<!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8" />
                <meta name="author" content="Jaime Alonso Fernández"/>
                <meta name="description" content="Página de Juegos"/>
                <meta name="keywords" content="MotoGP, Moto, Motorbike, Usuario, Ingreso"/>
                <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                <title>MotoGP-Juegos - Gracias</title>
                <link rel="stylesheet" type="text/css" href="../estilo/estilo.css"/>
                <link rel="stylesheet" type="text/css" href="../estilo/layout.css"/>
                <link rel="icon" href="../multimedia/img/favicon.ico" type="image/x-icon"/>
            </head>
            
            <body>
                <header>
                    <h1>Moto GP Desktop</h1>
                </header>
                
                <main>
                    <h2>¡Gracias por tu participación!</h2>
                    <p>Tu observación ha sido registrada correctamente.</p>
                    <form action="../index.html">
                        <button type="submit">Volver al inicio</button>
                    </form>
                </main>
            </body>
            </html>';
            exit();
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
    <link rel='stylesheet' type='text/css' href='../estilo/estilo.css'/>
    <link rel='stylesheet' type='text/css' href='../estilo/layout.css'/>
    <link rel='icon' href='../multimedia/img/favicon.ico' type='image/x-icon'/>
</head>

<body>
<header>
    <h1>Moto GP Desktop</h1>
</header>

<main>
    <h2>Test de Opiniones - Usabilidad</h2>

    <?php if ($mensaje): ?>
        <p><?php echo $mensaje; ?></p>
    <?php endif; ?>

    <?php if (!$mostrar_observaciones): ?>
        <!-- Formulario de usabilidad -->
        <form method="post">
            <p>Dispositivo utilizado:</p>
            <select name="dispositivo">
                <option value="PC">PC</option>
                <option value="Portátil">Portátil</option>
                <option value="Tablet">Tablet</option>
                <option value="Móvil">Móvil</option>
            </select>

            <p>¿Completó la tarea?</p>
            <input type="checkbox" name="tarea"> Sí

            <p>Comentarios del usuario:</p>
            <textarea name="comentarios"></textarea>

            <p>Propuestas de mejora:</p>
            <textarea name="mejora"></textarea>

            <p>Valoración general (0–10):</p>
            <input type="number" name="valoracion" min="0" max="10">
            <span><?php echo isset($errores["valoracion"]) ? $errores["valoracion"] : "" ?></span>

            <p><input type="submit" name="guardar_usabilidad" value="Guardar usabilidad"></p>
        </form>

    <?php else: ?>
        <!-- Formulario de observaciones del facilitador -->
        <h3>Observaciones del facilitador</h3>

        <form method="post">
            <textarea name="observacion"></textarea>
            <span><?php echo isset($errores["obs"]) ? $errores["obs"] : "" ?></span>

            <p><input type="submit" name="guardar_observacion" value="Guardar observación"></p>
        </form>
    <?php endif; ?>

</main>
</body>
</html>
