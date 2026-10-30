<?php
// ============================================================
// FUNCIONES COMUNES (escape de HTML, dinero, CSRF, dueño actual)
// ============================================================

function h($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8");
}

function dinero($n) {
    return "$ " . number_format((float)$n, 2, ",", ".");
}

function url_absoluta($ruta) {
    $https = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off");
    $host = $_SERVER["HTTP_HOST"] ?? "localhost";
    if (!preg_match('/^[A-Za-z0-9.\-:\[\]]+$/', $host)) $host = "localhost";
    return ($https ? "https://" : "http://") . $host . BASE_URL . "/" . ltrim($ruta, "/");
}

// ---- Protección CSRF para formularios nuevos ----
function csrf_token() {
    if (empty($_SESSION["csrf"])) {
        $_SESSION["csrf"] = bin2hex(random_bytes(16));
    }
    return $_SESSION["csrf"];
}

function csrf_field() {
    return '<input type="hidden" name="csrf" value="' . h(csrf_token()) . '">';
}

function csrf_validar() {
    return hash_equals($_SESSION["csrf"] ?? "", (string)($_POST["csrf"] ?? ""));
}

// ---- Dueño (cliente) asociado al usuario que inició sesión ----
// Cada cuenta de cliente tiene su ficha en la tabla "duenos".
// Si por algún motivo no existe (cuentas viejas), se crea acá.
function dueno_id_actual($conexion) {
    $uid = (int)($_SESSION["usuario_id"] ?? 0);
    if ($uid <= 0) return 0;

    $stmt = $conexion->prepare("SELECT nombre, email, id_dueno FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
    if (!$u) return 0;
    if (!empty($u["id_dueno"])) return (int)$u["id_dueno"];

    $ins = $conexion->prepare("INSERT INTO duenos(nombre, email) VALUES (?, ?)");
    $ins->bind_param("ss", $u["nombre"], $u["email"]);
    $ins->execute();
    $id = (int)$conexion->insert_id;

    $upd = $conexion->prepare("UPDATE usuarios SET id_dueno = ? WHERE id = ?");
    $upd->bind_param("ii", $id, $uid);
    $upd->execute();
    return $id;
}
