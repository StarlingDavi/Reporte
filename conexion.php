<?php
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "bd_facturacionpruebas";

$conexion = mysqli_connect($servidor, $usuario, $password, $base_datos);

if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
} else {
    echo "Conexión exitosa a la base de datos.";
}
?>