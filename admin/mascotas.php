<?php
require "../includes/auth.php";
require "../includes/captcha.php";
require "../conexion.php";
exigir_admin();

$error = "";
$ok = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["accion"] ?? "") === "agregar") {
    if (!validar_captcha($_POST["captcha"] ?? "")) {
        $error = MENSAJE_CAPTCHA;
    } else {
        $nombre = trim($_POST["nombre"] ?? "");
        $especie = trim($_POST["especie"] ?? "");
        $raza = trim($_POST["raza"] ?? "");
        $sexo = trim($_POST["sexo"] ?? "");
        $fecha = $_POST["fecha_nacimiento"] ?? null;
        $peso = ($_POST["peso"] ?? "") === "" ? null : (float)$_POST["peso"];
        $id_dueno = (int)($_POST["id_dueno"] ?? 0);

        $stmt = $conexion->prepare(
            "INSERT INTO mascotas(nombre, especie, raza, sexo, fecha_nacimiento, peso, id_dueno)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssssdi", $nombre, $especie, $raza, $sexo, $fecha, $peso, $id_dueno);

        if ($stmt->execute()) $ok = "Mascota agregada correctamente.";
        else $error = "No se pudo agregar: " . $stmt->error;
    }
}

$cap = nuevo_captcha();
$duenos = $conexion->query("SELECT id, nombre FROM duenos ORDER BY nombre");
$r = $conexion->query(
    "SELECT m.*, d.nombre AS dueno
     FROM mascotas m
     JOIN duenos d ON d.id = m.id_dueno
     ORDER BY m.id DESC"
);
require "../includes/header.php";
?>
<section class="hero">
    <div class="hero-kicker">Administración · Pacientes</div>
    <h2>Mascotas</h2>
    <p>Registra los pacientes y relaciónalos con su dueño.</p>
</section>

<div class="form-card">
    <?php if ($error): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($ok): ?><div class="alert success"><?= htmlspecialchars($ok) ?></div><?php endif; ?>

    <h3>Agregar mascota</h3>
    <form method="post">
        <input type="hidden" name="accion" value="agregar">
        <div class="form-grid">
            <div class="field"><label>Nombre</label><input name="nombre" required></div>
            <div class="field"><label>Especie</label><input name="especie" placeholder="Perro, gato..." required></div>
            <div class="field"><label>Raza</label><input name="raza"></div>
            <div class="field"><label>Sexo</label><select name="sexo"><option>Macho</option><option>Hembra</option></select></div>
            <div class="field"><label>Fecha de nacimiento</label><input type="date" name="fecha_nacimiento"></div>
            <div class="field"><label>Peso (kg)</label><input type="number" step="0.01" name="peso" min="0"></div>
            <div class="field"><label>Dueño</label>
                <select name="id_dueno" required>
                    <?php while ($d = $duenos->fetch_assoc()): ?>
                        <option value="<?= $d["id"] ?>"><?= htmlspecialchars($d["nombre"]) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <br>
        <?= captcha_html() ?>
        <div class="form-actions"><button class="btn" type="submit">Agregar mascota</button></div>
    </form>
</div>

<div class="table-card">
    <h3>Listado de mascotas</h3>
    <div class="table-wrap">
        <table>
            <tr><th>Nombre</th><th>Especie</th><th>Raza</th><th>Sexo</th><th>Nacimiento</th><th>Peso</th><th>Dueño</th><th>Acciones</th></tr>
            <?php while ($x = $r->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($x["nombre"]) ?></td>
                    <td><?= htmlspecialchars($x["especie"]) ?></td>
                    <td><?= htmlspecialchars($x["raza"]) ?></td>
                    <td><?= htmlspecialchars($x["sexo"]) ?></td>
                    <td><?= htmlspecialchars($x["fecha_nacimiento"]) ?></td>
                    <td><?= htmlspecialchars($x["peso"]) ?></td>
                    <td><?= htmlspecialchars($x["dueno"]) ?></td>
                    <td class="actions">
                        <a class="btn small" href="editar.php?tabla=mascotas&id=<?= $x["id"] ?>">Modificar</a>
                        <a class="btn danger small" href="eliminar.php?tabla=mascotas&id=<?= $x["id"] ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
<?php require "../includes/footer.php"; ?>
