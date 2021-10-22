<?php
// Este escrito se usa para el botón de logout, sirve para cerrar la sesión ya creada.
session_start();

// Para cerrar y destruir la sesión
session_unset();
session_destroy();

// Una vez se cierra la sesión, llevamos al usuario de nuevo a la pantalla de login
header("location: login.php");
exit;

