<?php
// Variables que se necesitan a la hora de imprimir los datos de manera más clara con las fechas
$fecha = strtotime("+21 day", strtotime($_POST['valorCaja1']));

$resultado = "Su segunda dosis sería el ".(date("d-m-Y", $fecha));
echo $resultado;
?>