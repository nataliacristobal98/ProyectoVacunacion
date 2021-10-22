<?php
// Iniciamos sesion
session_start();
require_once "config/configuracion.php";

// Si la sesión está activada, no tiene sentido tener acceso a esta página. Se le redirigirá a donde corresponda
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    // Comprobamos a que página tendríamos que mandar. En caso de no tener el formulario completado se le mandaría a este, y si no a segunda dosis
    // Si no hubiese un hospital en el registro, quiere decir que no ha hecho el formulario por lógica
    if($_SESSION["hospital"] == null){
        header("location: formulario.php");
    }else{
        header("location: segundaDosis.php");
    }
    exit;
}

// Definimos todas las variables necesarias desde la BD
$dni = $ss = $apellido = $hospital=  $vacuna = $brazo = $antecedentes = $riesgo = $fecha_vacunacion = "";
$dni_err = $ss_err = $apellido_err = "";

// Enviamos la información
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Valida que ningún campo este vacio de DNI, SS y apellido
    if(empty(trim($_POST["dni"]))){
        $dni_err = "Introduce tu DNI";
    } else{
        $dni = trim($_POST["dni"]);
    }

    if(empty(trim($_POST["ss"]))){
        $ss_err = "Introduce tu SS";
    } else{
        $ss = trim($_POST["ss"]);
    }

    if(empty(trim($_POST["apellido"]))){
        $apellido_err = "Introduce tu apellido";
    } else{
        $apellido = trim($_POST["apellido"]);
    }

    // Validamos la información introducida
    if(empty($dni_err) && empty($ss_err) && empty($apellido_err)){
        // Consulta en la BD, seleccionamos todos los datos del registro, ya que puede ser que no haya ninguno y tengamos que redirigir al usuario al formulario de registro.
        // Si hubiera datos, se le tendría que rederigir a una pantalla con sus datos ya registrados previos.
        $sql = "SELECT * FROM usuarios WHERE dni = ?";

        if($stmt = $mysqli->prepare($sql)){
            // Preparacion de parámetros, en este caso solo dni porque con uno solo nos basta para comprobarlo.
            $stmt->bind_param("s", $param_dni);

            // Declaración de parametros
            $param_dni = $dni;

            // Ejecutar los parámetros creados
            if($stmt->execute()){
                // Guardamos los resultados desde la BD
                $result = $stmt->get_result();

                // Si el DNI existe, verificamos que los demás datos coincidan con ese DNI.
                if($result->num_rows == 1){
                    $fila = $result->fetch_assoc();
                    if($ss == $fila["ss"] && $apellido == $fila["apellido"]){
                        // Si los demás datos asociados a ese DNI son correctos, iniciamos la sesión
                        if(!isset($_SESSION)) {
                            session_start();
                        }

                        // Guardamos en la sesión los datos solicitados, ya que nos servirá para imprimir la información por pantalla
                        // También guardamos los valores nulos, ya que sirven para determinar a que pantallas hay que llevar
                        $_SESSION["loggedin"] = true;
                        $_SESSION["dni"] = $fila["dni"];
                        $_SESSION["hospital"] = $fila["hospital"];
                        $_SESSION["vacuna"] = $fila["tipo_vacuna"];
                        $_SESSION["brazo"] = $fila["brazo_dom"];
                        $_SESSION["antecedentes"] = $fila["antecedentes"];
                        $_SESSION["riesgo"] = $fila["riesgo"];
                        $_SESSION["fecha_vacunacion"] = $fila["fecha_vacunacion"];

                        // Podemos guardar datos también en las cookies, en este caso los de inicio de sesión pueden ser utiles.
                        // Encriptamos los de DNI y el número de la SS por la delicadeza de estos.
                        setcookie('DNI',  password_hash($dni, PASSWORD_DEFAULT), time() + (86400), "/");
                        setcookie('SS',  password_hash($ss, PASSWORD_DEFAULT), time() + (86400), "/");
                        setcookie('APELLIDO',  $apellido, time() + (86400), "/");


                        /* Como el flujo de entrada implica tener el DNI, el SS y el apellido registrados ya de por sí, por lógica si
                        no hay cualquiera de los otros datos quiere decir que no has hecho el formulario de resgiatro de la cita.
                        Por lo tanto si hay uno de esos datos se tiene que redireccionar a otra pantalla distinta. */

                        // Vamos a usar como comprobación el dato de hospital, aunque podría ser cualquiera de los datos menos DNI, SS y apellido.
                        $hospital = $fila["hospital"];

                        // Si no hay hospital resgistrado, se le lleva al formulario y si está se le lleva a ver los datos ya tomados.
                        if($hospital == null){
                            header("location: formulario.php");
                        }else{
                            header("location: segundadosis.php");
                        }

                    } else{
                        // Si hay algun fallo en los datos, se rechaza la consulta
                        $login_err = "Algún dato ha sido mal introducido";
                    }

                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
    $mysqli->close();

}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Vacunación COVID - Información</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/cover/">

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/cover.css" rel="stylesheet">
    <link href="css/signin.css" rel="stylesheet">

    <!--Mensaje de aviso de cookies-->
    <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            setTimeout(function() {
                $(".cuadro").fadeOut(5000);
            },2000);
        });
    </script>

    <!--XMLHttpRequest perteneciente a AJAX-->
    <script>
        function mostrarSugerencia(str) {
            var xmlhttp;
            if (str.length==0) { document.getElementById("txtSugerencia").innerHTML=""; return; }

            xmlhttp=new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {

                if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                    document.getElementById("txtSugerencia").innerHTML=xmlhttp.responseText;
                }
            }
            xmlhttp.open("GET","listadoDatos.php?q="+str,true);
            xmlhttp.send();
        }
    </script>
</head>

<body class="text-center">

<!--Cuadro de info de cookies-->
    <div class="cuadro">
         <p>Utilizamos <strong>cookies</strong> en nuestra web con una finalidad funcional y analítica.
         Las <strong>cookies</strong> nos ayudan a mejorar tu experiencia de navegación y a mostrarte contenidos personalizados.</p>
    </div>

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
        <h1 class="cover-heading">Vacunación del Covid-19 Comunidad de Madrid</h1> </br>
        <p class="lead">Bienvenido al sistema de citación contra el SARS-CoV-2(COVID 19)</p>
        <p class="lead">Acceda con su número de soporte del DNI, número de la Seguridad Social y su primer apellido.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["SCRIPT_NAME"]); ?>" method="post">
            <h5 class="font-weight-normal" >Número de soporte DNI</h5>
            <label for="inputDNI" class="sr-only">Num soporte DNI</label>
            <input type="dni" id="inputDNI" name="dni" onkeyup="mostrarSugerencia(this.value)"  class="form-control <?php echo (!empty($dni_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $dni; ?>" placeholder="Num soporte DNI (IDESP)" required autofocus> </br>
            <span class="invalid-feedback"><?php echo $dni_err; ?></span>

            <h5 class="font-weight-normal" >Número de la Seguridad Social</h5>
            <label for="inputSS" class="sr-only">Num SS</label>
            <input type="SS" id="inputSS" name="ss" onkeyup="mostrarSugerencia(this.value)" class="form-control <?php echo (!empty($ss_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $ss; ?>" placeholder="Num SS" required autofocus> </br>
            <span class="invalid-feedback"><?php echo $ss_err; ?></span>
            <h5 class="font-weight-normal" >Primer Apellido</h5>

            <label for="inputApellido" class="sr-only">Primer apellido</label>
            <input type="apellido" id="inputApellido" name="apellido" onkeyup="mostrarSugerencia(this.value)" class="form-control <?php echo (!empty($apellido_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $apellido; ?>" placeholder="Primer apellido" required autofocus>
            <span class="invalid-feedback"><?php echo $apellido_err; ?></span>
            </br>
            <div class="form-group">
                <input type="submit" class="btn btn-lg btn-block btn-secondary" value="Login">
            </div>
        </form>
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

