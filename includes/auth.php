<?php
// ============================================================
// AUTENTICACIÓN Y PERMISOS
// Utiliza sesiones de PHP. No utiliza JavaScript.
// ============================================================

require_once __DIR__ . "/../config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        "httponly" => true,
        "samesite" => "Lax",
    ]);
    session_start();
}

require_once __DIR__ . "/helpers.php";

function usuario_logueado() {
    return isset($_SESSION["usuario_id"]);
}

function es_admin() {
    return ($_SESSION["rol"] ?? "") === "admin";
}

function exigir_login() {
    if (!usuario_logueado()) {
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }
}

function exigir_admin() {
    exigir_login();

    if (!es_admin()) {
        header("Location: " . BASE_URL . "/index.php");
        exit;
    }
}
