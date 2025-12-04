<?php
class Cronometro {

    private $inicio = 0;
    private $tiempo = 0;

    function arrancar() {
        $this->inicio = microtime(true);
    }

    function parar() {
        $this->tiempo = microtime(true) - $this->inicio;
    }

    function getTiempo() {
        return $this->tiempo;
    }

    function mostrar() {
        $minutos = floor($this->tiempo / 60);
        $segundos = floor(fmod($this->tiempo, 60));
        $centesimas = floor(($this->tiempo - floor($this->tiempo)) * 100);

        $tiempo_formateado = sprintf("%02d:%02d.%02d", $minutos, $segundos, $centesimas);

        echo "<p>".$tiempo_formateado."</p>";
    }
}
?>
