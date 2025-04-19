<?php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] != "usuario") {
    header("Location: login.php");
    exit();
}



include(__DIR__ . '/../includes/conexion.php');

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Usuario</title>
    <link rel="stylesheet" href="../css/estilos.css">

</head>
<body>
    <h2>Bienvenido, Lector 👋</h2>
    <a href='logout.php'>Cerrar sesión</a>

    <h3>📚 Libros disponibles</h3>

    <!-- 🔍 Formulario de búsqueda -->
    <form method="GET" action="usuario.php">
        <input type="text" name="buscar" placeholder="Buscar por título o autor" value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
        <button type="submit">🔍 Buscar</button>
        <?php if (isset($_GET['buscar']) && $_GET['buscar'] !== ''): ?>
            <a href="usuario.php">❌ Limpiar</a>
        <?php endif; ?>
    </form>
    <br>

    <table border="1" cellpadding="10">
        <tr>
            <th>Título</th>
            <th>Autor</th>
            <th>Descripción</th>
            <th>Año</th>
            <th>Leer</th>
        </tr>

        <?php
        $filtro = "";
        if (isset($_GET['buscar']) && $_GET['buscar'] !== '') {
            $buscar = $conexion->real_escape_string($_GET['buscar']);
            $filtro = "WHERE titulo LIKE '%$buscar%' OR autor LIKE '%$buscar%'";
        }

        $resultado = $conexion->query("SELECT * FROM libros $filtro ORDER BY titulo ASC");
        if ($resultado->num_rows > 0) {
            while ($libro = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($libro['titulo']) . "</td>";
                echo "<td>" . htmlspecialchars($libro['autor']) . "</td>";
                echo "<td>" . htmlspecialchars($libro['descripcion']) . "</td>";
                echo "<td>" . htmlspecialchars($libro['fecha_publicacion']) . "</td>";
                echo "<td><a href='../../archivos/" . urlencode($libro['archivo']) . "' target='_blank'>📖 Leer</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No se encontraron libros.</td></tr>";
        }
        ?>
    </table>
</body>
</html>
