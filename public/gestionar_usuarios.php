<?php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] != "admin") {
    header("Location: login.php");
    exit();
}

include(__DIR__ . '/../includes/conexion.php');

// Eliminar usuario
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    if ($id == $_SESSION['id_usuario']) {
        echo "<p style='color:red; font-weight: bold;'>⚠️ No puedes eliminar tu propio usuario.</p>";
    } else {
        $conexion->query("DELETE FROM usuarios WHERE id = $id");
        echo "<p style='color:green; font-weight: bold;'>✅ Usuario eliminado correctamente.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>👥 Gestión de Usuarios</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <h2>👥 Gestión de Usuarios del Sistema</h2>
    
    
    <nav>
        <a href='admin.php'>🔙 Volver al panel principal</a> | 
        <a href='logout.php'>🚪 Cerrar sesión</a>
    </nav>
    <br>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead style="background-color: #f0f0f0;">
            <tr>
                <th>📧 Correo electrónico</th>
                <th>🔑 Rol</th>
                <th>⚙️ Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $resultado = $conexion->query("SELECT * FROM usuarios ORDER BY correo ASC");
            if ($resultado->rowCount() > 0) {
                while ($usuario = $resultado->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($usuario['correo']) . "</td>";
                    echo "<td>" . htmlspecialchars($usuario['rol']) . "</td>";
                    echo "<td>";

                    if ($usuario['id'] != $_SESSION['id_usuario']) {
                        echo "<form method='POST' action='gestionar_usuarios.php' onsubmit=\"return confirm('¿Estás seguro de eliminar este usuario?');\">
                                <input type='hidden' name='id' value='" . $usuario['id'] . "'>
                                <button type='submit' name='eliminar'>🗑️ Eliminar</button>
                              </form>";
                    } else {
                        echo "👤 (Tú)";
                    }

                    echo "</td></tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No hay usuarios registrados en el sistema.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
