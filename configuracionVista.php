<?php
if (!class_exists('Configuracion')) {
    require_once 'configuracion.php';
}
$config = new Configuracion();
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
    <h2>Registro de usuario para realizar el formulario</h2>
    <!-- Mostrar formulario completo -->
    <form action='#' method='post' name='formularioRegistro'>
        <p>¿Profesión?</p>
        <p>
            <input type='text' name='profesion' value="<?php echo isset($_POST['profesion']) ? htmlspecialchars($_POST['profesion']) : ''; ?>"/>
        </p>

        <p>¿Edad?</p>
        <p>
            <input type='number' name='edad' value="<?php echo isset($_POST['edad']) ? htmlspecialchars($_POST['edad']) : ''; ?>"/>
        </p>

        <p>¿Género?</p>
        <p>
            <input type='radio' name='genero' value='Hombre' <?php echo (isset($_POST['genero']) && $_POST['genero'] == 'Hombre') ? 'checked' : ''; ?>/> Hombre<br>
            <input type='radio' name='genero' value='Mujer' <?php echo (isset($_POST['genero']) && $_POST['genero'] == 'Mujer') ? 'checked' : ''; ?>/> Mujer<br>
            <input type='radio' name='genero' value='Otro' <?php echo (isset($_POST['genero']) && $_POST['genero'] == 'Otro') ? 'checked' : ''; ?>/> Otro<br>
        </p>

        <p>¿Pericia Informática?</p>
        <p>
            <input type='radio' name='pericia' value='0' <?php echo (isset($_POST['pericia']) && $_POST['pericia'] == '0') ? 'checked' : ''; ?>/> 0<br>
            <input type='radio' name='pericia' value='1' <?php echo (isset($_POST['pericia']) && $_POST['pericia'] == '1') ? 'checked' : ''; ?>/> 1<br>
            <input type='radio' name='pericia' value='2' <?php echo (isset($_POST['pericia']) && $_POST['pericia'] == '2') ? 'checked' : ''; ?>/> 2<br>
            <input type='radio' name='pericia' value='3' <?php echo (isset($_POST['pericia']) && $_POST['pericia'] == '3') ? 'checked' : ''; ?>/> 3<br>
            <input type='radio' name='pericia' value='4' <?php echo (isset($_POST['pericia']) && $_POST['pericia'] == '4') ? 'checked' : ''; ?>/> 4<br>
            <input type='radio' name='pericia' value='5' <?php echo (isset($_POST['pericia']) && $_POST['pericia'] == '5') ? 'checked' : ''; ?>/> 5<br>
            <input type='radio' name='pericia' value='6' <?php echo (isset($_POST['pericia']) && $_POST['pericia'] == '6') ? 'checked' : ''; ?>/> 6<br>
            <input type='radio' name='pericia' value='7' <?php echo (isset($_POST['pericia']) && $_POST['pericia'] == '7') ? 'checked' : ''; ?>/> 7<br>
            <input type='radio' name='pericia' value='8' <?php echo (isset($_POST['pericia']) && $_POST['pericia'] == '8') ? 'checked' : ''; ?>/> 8<br>
            <input type='radio' name='pericia' value='9' <?php echo (isset($_POST['pericia']) && $_POST['pericia'] == '9') ? 'checked' : ''; ?>/> 9<br>
            <input type='radio' name='pericia' value='10' <?php echo (isset($_POST['pericia']) && $_POST['pericia'] == '10') ? 'checked' : ''; ?>/> 10
        </p>
        <p>
            <input type='submit' name='finalizar_registro' value='Finalizar registro'/>
        </p>
        <?php
        if (isset($_POST['finalizar_registro'])) {
            $resultado = $config->insertarUsuario($_POST['profesion'], $_POST['edad'], $_POST['genero'], $_POST['pericia']);
            if ($resultado['success']) {
                header("Location: juegos.html");
            } else {
                echo "<p'>Error al registrar el usuario: " . $resultado['error'] . "</p>";
            }
        }
        ?>
    </form>
</main>
</body>
</html>
