<?php
session_start();
require_once "config/configuracion.php";

// Hay que comprobar que la sesión está en uso, si no se le lleva a la pantalla de login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Esta pantalla en verdad solo se visionará UNA VEZ, después del relleno de formulario. Por lo tanto seguirá la misma lógica que la pantalla de la segunda dosis.
// Si la sesión está iniciada pero el formulario no está rellenado, habría que denegar el acceso a esta página y mandarlo al formulario
// Por lógica si no está el valor de hospital, el formulario no está rellenado. Por eso comprobamos si el valor es nulo
if($_SESSION["hospital"] == null){
    header("location: formulario.php");
    exit;
}

// Variables que se necesitan a la hora de imprimir los datos de manera más clara con las fechas
$fecha = strtotime($_SESSION["fecha_vacunacion"]);
$hora = date('H', strtotime($_SESSION["fecha_vacunacion"]));
$min = date('i', strtotime($_SESSION["fecha_vacunacion"]));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Vacunación COVID - Registro</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/cover/">

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/cover.css" rel="stylesheet">
</head>
<body class="text-center">

<?php
if(!empty($login_err)){
    echo '<div class="alert alert-danger">' . $login_err . '</div>';
}
?>

<div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
    <header class="masthead mb-auto">
        <div class="inner">
            <h4 class="masthead-brand">Vacunación Comunidad de Madrid</h4>
            <nav class="nav nav-masthead justify-content-center">
                <a class="nav-link" href="login.php">Login</a>
                <a class="nav-link" href="logout.php">Logout</a>
                <a class="nav-link" href="información.html">Información</a>
            </nav>
        </div>
    </header>

    <main role="main" class="inner cover">
        <h2 class="cover-heading"><b><?php echo htmlspecialchars($_COOKIE['APELLIDO']); ?></b>, tu cita se ha registrado correctamente.</h2>
        <p class="lead">Tus datos son los siguientes:</p>
        <ul>
            <li>El hospital al que deberá acudir es <b><?php echo htmlspecialchars($_SESSION["hospital"]); ?></b></li>
            <li>La vacuna elegida es <b><?php echo htmlspecialchars($_SESSION["vacuna"]); ?></b></li>
            <li>Tu brazo dominante es <b><?php echo htmlspecialchars($_SESSION["brazo"]); ?></b>, por lo que se te inyectará la vacuna en el contrario.</li>
            <li><b><?php echo htmlspecialchars($_SESSION["antecedentes"]); ?></b> has tenido antecedentes de COVID.</li>
            <li><b><?php echo htmlspecialchars($_SESSION["riesgo"]); ?></b> tienes mayores riesgos al contraer el virus.</li>
            <li>Tu cita será el <b><?php echo htmlspecialchars(date("d-m-Y", $fecha)); ?></b>,
                a las <b><?php echo htmlspecialchars($hora); ?></b>:<b><?php echo htmlspecialchars($min); ?></b>.
        </ul>
    </main>
    </br>
    <footer class="mastfoot mt-auto">
        <div class="inner">
            <p style="text-align: center">Registro de Vacunación de la Comunidad de Madrid</p>
        </div>
    </footer>
</div>
</body>
</html>
