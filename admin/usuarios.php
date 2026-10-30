<?php
require "../includes/auth.php";
require "../conexion.php";
exigir_admin();

$r = $conexion->query(
    "SELECT id, nombre, email, rol, activo, email_verificado, creado_en
     FROM usuarios
     ORDER BY id DESC"
);

require "../includes/header.php";
?>
<section class="hero">
    <div class="hero-kicker">Administración · Seguridad</div>
    <h2>Usuarios, roles y permisos</h2>
    <p>
        El administrador puede modificar el rol y bloquear/desbloquear usuarios.
        Las nuevas cuentas creadas desde el registro son clientes.
    </p>
</section>

<div class="info-box">
    <strong>Roles:</strong> administrador = gestiona el sistema;
    cliente = consulta la información disponible para usuarios.
</div>

<div class="table-card">
    <div class="table-wrap">
        <table>
            <tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th>Correo verificado</th><th>Creado</th><th>Acciones</th></tr>
            <?php while ($x = $r->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($x["nombre"]) ?></td>
                    <td><?= htmlspecialchars($x["email"]) ?></td>
                    <td>
                        <span class="badge <?= $x["rol"] === "admin" ? "admin" : "" ?>">
                            <?= htmlspecialchars($x["rol"]) ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?= !$x["activo"] ? "block" : "" ?>">
                            <?= $x["activo"] ? "Activo" : "Bloqueado" ?>
                        </span>
                    </td>
                    <td><?= $x["email_verificado"] ? "✅ Sí" : "⏳ Pendiente" ?></td>
                    <td><?= htmlspecialchars($x["creado_en"]) ?></td>
                    <td class="actions">
                        <a class="btn small" href="editar.php?tabla=usuarios&id=<?= $x["id"] ?>">Modificar</a>
                        <a class="btn danger small" href="eliminar.php?tabla=usuarios&id=<?= $x["id"] ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
<?php require "../includes/footer.php"; ?>
