<?php
session_start();
require_once "config/configuracion.php";

//Comprobar que el usuario está en sesión, si no se le lleva al login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

//Si la sesión está iniciada pero ya no hay que rellenar el formulario se redirecciona a segundaDosis.php para no interferir en la BD
if($_SESSION["hospital"] != null){
    header("location: segundaDosis.php");
    exit;
}

//Cogemos una variante de el login para poder añadir los siguientes datos en la BD en el usuario correspondiente
$dni = ($_SESSION["dni"]);

// Definimos todas las variables necesarias desde la BD
$hospital = $vacuna = $brazo = $antecedentes = $riesgo = $fecha = $hora = "";
$hospital_err = $vacuna_err = $brazo_err = $antecedentes_err = $riesgo_err = $fecha_err = $hora_err = "";

    // Enviamos la información del formulario recogido
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // Si el hospital está marcado, y según su value en el formulario se define su contenido en la variable $hospital
        if (isset($_POST['hospital']) && $_POST['hospital'] == '1'){
            $hospital = "Isabel Zendal";
        }else if(isset($_POST['hospital']) && $_POST['hospital'] == '2'){
            $hospital = "Wizink Center";
        }else if(isset($_POST['hospital']) && $_POST['hospital'] == '3'){
            $hospital = "Wanda Metropolitano";
        }else if(isset($_POST['hospital']) && $_POST['hospital'] == '4'){
            $hospital = "Hospital de Henares";
        }else if(isset($_POST['hospital']) && $_POST['hospital'] == '5'){
            $hospital = "Hospital del Sureste";
        }else{
            $hospital_err = "Error";
        }

        // Si la vacuna está marcado, y según su value en el formulario se define su contenido en la variable $vacuna
        if(isset($_POST['vacuna']) && $_POST['vacuna'] == 'Pfizer'){
            $vacuna = "Pfizer";
        }else if(isset($_POST['vacuna']) && $_POST['vacuna'] == 'Moderna'){
            $vacuna = "Moderna";
        }else if(isset($_POST['vacuna']) && $_POST['vacuna'] == 'Astra Zeneca'){
            $vacuna = "Astra Zeneca";
        }else if(isset($_POST['vacuna']) && $_POST['vacuna'] == 'Janssen'){
            $vacuna = "Janssen";
        }else{
            $vacuna_err = "Error";
        }

        // Si el brazo está marcado, y según su value en el formulario se define su contenido en la variable $brazo
        if(isset($_POST['brazo']) && $_POST['brazo'] == 'derecho'){
            $brazo = "derecho";
        }else if (isset($_POST['brazo']) && $_POST['brazo'] == 'izquierdo'){
            $brazo = "izquierdo";
        }else {
            $brazo_err = "Error";
        }

        // Si el antecedentes está marcado, y según su value en el formulario se define su contenido en la variable $antecedentes
        if(isset($_POST['antecedentes']) && $_POST['antecedentes'] == 'si'){
            $antecedentes = "Si";
        }else if (isset($_POST['antecedentes']) && $_POST['antecedentes'] == 'no'){
            $antecedentes = "No";
        }else {
            $antecedentes_err = "Error";
        }

        // Si el riesgo está marcado, y según su value en el formulario se define su contenido en la variable $riesgo
        if(isset($_POST['riesgo']) && $_POST['riesgo'] == 'si'){
            $riesgo = "Si";
        }else if (isset($_POST['riesgo']) && $_POST['riesgo'] == 'no'){
            $riesgo = "No";
        }else {
            $riesgo_err = "Error";
        }


        // Si la fecha está marcado, y según su value en el formulario se define su contenido en la variable $fecha
        if(empty(trim($_POST["fecha"]))){
            $fecha_err = "Introduce la fecha";
        } else{
            $fecha = trim($_POST["fecha"]);
        }
        // Si la hora l está marcado, y según su value en el formulario se define su contenido en la variable $hora
        if(empty(trim($_POST["hora"]))){
            $hora_err = "Introduce la hora";
        } else{
            $hora = trim($_POST["hora"]);
        }
        // La fecha y la hora se tendrían que combinar en una para que se pueda añadir como DATETIME en la BD
        $fecha_hora = $fecha . " ". $hora. ":00"; // Concatenamos en un string
        $fecha_vacunacion = date($fecha_hora); //Le damos formato de fecha con date()


        // Si no hay ningún error, se pasa a actualizar la BD
        if(empty($hospital_err) && empty($vacuna_err) && empty($brazo_err) && empty($antecedentes_err) && empty($riesgo_err) && empty($fecha_err) && empty($hora_err)){
            // En este caso, al ser un usuario ya registrado todos sus datos son nulos, por ello habría que hacer un UPDATE
            // También añadimos que la fecha de alta tome el valor de la fecha en el momento del registro.
            // Usamos el dni como identificador del usuario ya que es un valor único y no fallará
            $sql = "UPDATE usuarios SET fecha_alta = CURRENT_TIMESTAMP, hospital = ?, tipo_vacuna = ?, brazo_dom = ?, antecedentes = ?, riesgo = ?, fecha_vacunacion = ? WHERE dni = ?";

            if($stmt = $mysqli->prepare($sql)){
                // Preparacion de parámetros, no hace falta que pongamos fecha_alta porque no es relevante para el usuario.
                $stmt->bind_param("sssssss", $hospital, $vacuna, $brazo, $antecedentes, $riesgo, $fecha_vacunacion, $dni);

                // Actualizamos valores de la sesión, ya que nos servirá para imprimir la información por pantalla
                $_SESSION["hospital"] = $hospital;
                $_SESSION["vacuna"] = $vacuna;
                $_SESSION["brazo"] = $brazo;
                $_SESSION["antecedentes"] = $antecedentes;
                $_SESSION["riesgo"] = $riesgo;
                $_SESSION["fecha_vacunacion"] = $fecha_vacunacion;

                //Ejecutamos la consulta/update
                if($stmt->execute()){
                    // Redireccionamos a la pantalla de registro
                    header("location: registro.php");
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }
                $stmt->close();
            }
        }
    }
    $mysqli->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Vacunación COVID - Formulario</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/cover/">

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/cover.css" rel="stylesheet">
    <link href="css/signin.css" rel="stylesheet">
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
        <h1 class="cover-heading">Vacunación del Covid-19 Comunidad de Madrid</h1> </br>
        <p class="lead">Bienvenido, <b><?php echo htmlspecialchars($_COOKIE['APELLIDO']); ?></b>. Rellene los datos:</p>
        <form action="<?php echo htmlspecialchars($_SERVER["SCRIPT_NAME"]); ?>" method="post">
        <h5 class="mb-3">Hospital:</h5>
        <!--Hospital-->
            <select name="hospital" id="hospital" class="d-block w-100 form-control <?php echo (!empty($hospital_err)) ? 'is-invalid' : ''; ?>" required>
            <option value="1">Isabel Zendal</option>
            <option value="2">Wizink Center</option>
            <option value="3">Wanda Metropolitano</option>
            <option value="4">Hospital de Henares</option>
            <option value="5">Hospital del Sureste</option>
        </select>
            <span class="invalid-feedback"><?php echo $hospital_err;?></span>

        <div class="row">
            <!--Vacuna-->
            <div class="col">
                <div class="d-block my-3">
                    <div class="custom-control custom-radio">
                        <h5 class="mb-3">Tipo de vacuna:</h5>
                        <input id="pfizer" name="vacuna" value="Pfizer" type="radio" class="custom-control-input form-control <?php echo (!empty($vacuna_err)) ? 'is-invalid' : ''; ?>">
                        <label class="custom-control-label" for="pfizer">Pfizer</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input id="moderna" name="vacuna" value="Moderna" type="radio" class="custom-control-input form-control <?php echo (!empty($vacuna_err)) ? 'is-invalid' : ''; ?>">
                        <label class="custom-control-label" for="moderna">Moderna</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input id="astrazeneca" name="vacuna" value="Astra Zeneca" type="radio" class="custom-control-input form-control <?php echo (!empty($vacuna_err)) ? 'is-invalid' : ''; ?>">
                        <label class="custom-control-label" for="astrazeneca">Astra Zeneca</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input id="janssen" name="vacuna" value="Janssen" type="radio" class="custom-control-input form-control <?php echo (!empty($vacuna_err)) ? 'is-invalid' : ''; ?>">
                        <label class="custom-control-label" for="janssen">Janssen</label>
                    </div>
                </div>
                <span class="invalid-feedback"><?php echo $vacuna_err;?></span>
            </div>
            <!--Brazo-->
            <div class="col">
                <div class="d-block my-3">
                    <div class="custom-control custom-radio">
                        <h5 class="mb-3">Brazo dominante:</h5>
                        <input id="derecho" name="brazo" value="derecho" type="radio" class="custom-control-input form-control <?php echo (!empty($brazo_err)) ? 'is-invalid' : ''; ?>">
                        <label class="custom-control-label" for="derecho">Derecho</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input id="izquierdo" name="brazo" value="izquierdo" type="radio" class="custom-control-input form-control <?php echo (!empty($brazo_err)) ? 'is-invalid' : ''; ?>">
                        <label class="custom-control-label" for="izquierdo">Izquierdo</label>
                    </div>
                </div>
                <span class="invalid-feedback"><?php echo $brazo_err;?></span>
            </div>
        </div>

        <div class="row">
            <!--Antecedentes-->
            <div class="col">
                <div class="d-block my-3">
                    <div class="custom-control custom-radio">
                        <h5 class="mb-3">Antecedentes de COVID-19:</h5>
                        <input id="antecedentessi" name="antecedentes" value="si" type="radio" class="custom-control-input form-control <?php echo (!empty($antecedentes_err)) ? 'is-invalid' : ''; ?>">
                        <label class="custom-control-label" for="antecedentessi">Sí, he pasado el COVID</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input id="antecedentesno" name="antecedentes" value="no" type="radio" class="custom-control-input form-control <?php echo (!empty($antecedentes_err)) ? 'is-invalid' : ''; ?>">
                        <label class="custom-control-label" for="antecedentesno">No, no he pasado el COVID</label>
                    </div>
                </div>
                <span class="invalid-feedback"><?php echo $antecedentes_err;?></span>
            </div>
            <!--Riesgo-->
            <div class="col">
                <div class="d-block my-3">
                    <div class="custom-control custom-radio">
                        <h5 class="mb-3">Persona de riesgo:</h5>
                        <input id="riesgosi" name="riesgo" value="si" type="radio" class="custom-control-input form-control <?php echo (!empty($riesgo_err)) ? 'is-invalid' : ''; ?>">
                        <label class="custom-control-label" for="riesgosi">Sí, soy persona de riesgo</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input id="riesgono" name="riesgo" value="no" type="radio" class="custom-control-input form-control <?php echo (!empty($riesgo_err)) ? 'is-invalid' : ''; ?>">
                        <label class="custom-control-label" for="riesgono">No, no soy persona de riesgo</label>
                    </div>
                </div>
                <span class="invalid-feedback"><?php echo $riesgo_err;?></span>
            </div>
        </div>

        <div class="row">
            <!--Fecha-->
            <div class="col">
                    <div class="d-block my-3">
                        <h5 class="mb-3">Fecha de vacunación:</h5>
                        <input type="date" id="start" name="fecha" class="form-control <?php echo (!empty($fecha_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $fecha; ?>"
                               min="2021-01-01" max="2029-12-31">
                    </div>
                    <span class="invalid-feedback"><?php echo $fecha_err;?></span>
            </div>
                <!--Hora-->
            <div class="col">
                    <div class="d-block my-3">
                        <h5 class="mb-3">Hora de la cita:</h5>
                        <input type="time" name="hora" class="form-control <?php echo (!empty($hora_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $hora; ?>">
                    </div>
                    <span class="invalid-feedback"><?php echo $hora_err;?></span>
            </div>
        </div>

            <div class="form-group">
                <input type="submit" class="btn btn-lg btn-block btn-secondary" value="Enviar">
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
