<?php
// ============================================================
// VETERINARIA ALEM - CONEXIÓN A MYSQL
// XAMPP normalmente utiliza:
// servidor: localhost
// usuario: root
// contraseña: vacía
// base de datos: veterinariaalem
// ============================================================

require_once __DIR__ . "/config.php";

// Hora de Uruguay en PHP y en MySQL (para turnos y facturas).
date_default_timezone_set("America/Montevideo");

// Que los errores de MySQL se informen como excepciones (igual en todas las versiones de PHP).
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "veterinariaalem";

try {
    $conexion = new mysqli($host, $usuario, $password, $base_datos);
} catch (mysqli_sql_exception $e) {
    die("Error de conexión con MySQL. Verifica que MySQL esté encendido en XAMPP y que la base 'veterinariaalem' esté importada.");
}

$conexion->query("SET time_zone = '-03:00'");

$conexion->set_charset("utf8mb4");
?>
