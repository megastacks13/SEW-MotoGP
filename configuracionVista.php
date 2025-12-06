<?php
require_once 'php/configuracion.php';

$config = new Configuracion();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['generar_csv'])) {
        $resultado = $config->exportarCSV();
    }
    elseif (isset($_POST['eliminar_bbdd'])) {
        $resultado = $config->eliminarBaseDatos();
        if ($resultado['success']) {
            $mensaje = "Base de datos eliminada exitosamente";
        } else {
            $mensaje = "Error al eliminar base de datos: " . $resultado['error'];
        }
    }
    elseif (isset($_POST['vaciar_bbdd'])) {
        $resultado = $config->reiniciarBaseDatos();
        if ($resultado['success']) {
            $mensaje = "Base de datos vaciada exitosamente";
        } else {
            $mensaje = "Error al vaciar base de datos: " . $resultado['error'];
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
    <title>MotoGP-Configuración</title>
    <link rel='stylesheet' type='text/css' href='estilo/estilo.css'/>
    <link rel='stylesheet' type='text/css' href='estilo/layout.css'/>
    <link rel='icon' href='multimedia/img/favicon.ico' type='image/x-icon'/>
</head>

<body>
<header>
    <h1>Moto GP Desktop</h1>
</header>
<main>
    <h2>Configuración de BBDD</h2>

    <?php if ($mensaje): ?>
        <p><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <!-- Formulario para gestión de BBDD -->
    <form action='#' method='post'>
        <p>
            <input type='submit' name='generar_csv' value='Generar CSV'/>
            <input type='submit' name='eliminar_bbdd' value='Eliminar BBDD'/>
            <input type='submit' name='vaciar_bbdd' value='Vaciar BBDD'/>
        </p>
    </form>
</main>
</body>
</html>