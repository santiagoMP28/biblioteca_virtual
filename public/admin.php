<?php
session_start();
if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] != "admin") {
    header("Location: login.php");
    exit();
}

include(__DIR__ . '/../includes/conexion.php');

// Eliminar libro
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];

    $consulta = $conexion->prepare("SELECT archivo_pdf FROM libros WHERE id = :id");
    $consulta->execute(['id' => $id]);
    $fila = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($fila) {
        $archivo = $fila['archivo_pdf'];
        if (!empty($archivo) && file_exists(__DIR__ . "/../archivos/$archivo")) {
            unlink(__DIR__ . "/../archivos/$archivo");
        }
        
        $eliminar = $conexion->prepare("DELETE FROM libros WHERE id = :id");
        $eliminar->execute(['id' => $id]);

        header("Location: admin.php?mensaje=🗑️ Libro eliminado correctamente.");
        exit();
    }
}

// Subir libro
if (isset($_POST['subir'])) {
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $descripcion = $_POST['descripcion'];
    $anio_publicacion = (int)$_POST['fecha_publicacion']; 

    $verificar = $conexion->prepare("SELECT * FROM libros WHERE titulo = :titulo AND autor = :autor");
    $verificar->execute(['titulo' => $titulo, 'autor' => $autor]);

    if ($verificar->rowCount() > 0) {
        echo '<div class="mensaje-error">⚠️ El libro ya existe. <span class="cerrar" onclick="this.parentElement.remove()">×</span></div>';
    } else {
        $archivoNombreOriginal = $_FILES['archivo']['name'];
        $archivoTmp = $_FILES['archivo']['tmp_name'];
        $archivoNombre = time() . "_" . basename($archivoNombreOriginal);
        $destino = __DIR__ . "/../archivos/" . $archivoNombre;

        if (move_uploaded_file($archivoTmp, $destino)) {
            $sql = $conexion->prepare("INSERT INTO libros (titulo, autor, descripcion, anio_publicacion, archivo_pdf)
                           VALUES (:titulo, :autor, :descripcion, :anio, :archivo)");
            $sql->execute([
                'titulo' => $titulo,
                'autor' => $autor,
                'descripcion' => $descripcion,
                'anio' => $anio_publicacion,
                'archivo' => $archivoNombre
            ]);

            echo '<div class="mensaje-exito">✅ Libro subido correctamente. <span class="cerrar" onclick="this.parentElement.remove()">×</span></div>';
        } else {
            echo '<div class="mensaje-error">❌ Error al subir el archivo PDF. <span class="cerrar" onclick="this.parentElement.remove()">×</span></div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Administrador</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
        }

        .sidebar {
            width: 220px;
            background-color: #007bff;
            color: white;
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: #0056b3;
        }

        .content {
            flex-grow: 1;
            padding: 30px;
            background-color: #f5f7fa;
        }

        .form-section, .table-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        button {
            padding: 8px 14px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: darkred;
        }

        form input, form textarea, form button {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            box-sizing: border-box;
        }

        form button {
            background-color: green;
        }

        form button:hover {
            background-color: darkgreen;
        }

        /* Estilos para mensajes */
        .mensaje-exito {
            background-color: #d4edda;
            color: #155724;
            padding: 12px 20px;
            border-radius: 5px;
            margin: 15px 0;
            border: 1px solid #c3e6cb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            animation: fadeIn 0.3s ease-in-out;
        }

        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px 20px;
            border-radius: 5px;
            margin: 15px 0;
            border: 1px solid #f5c6cb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            animation: fadeIn 0.3s ease-in-out;
        }

        .mensaje-exito .cerrar,
        .mensaje-error .cerrar {
            cursor: pointer;
            font-size: 18px;
            margin-left: 15px;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .mensaje-exito .cerrar {
            color: #155724;
        }

        .mensaje-error .cerrar {
            color: #721c24;
        }

        .mensaje-exito .cerrar:hover,
        .mensaje-error .cerrar:hover {
            opacity: 1;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin</h2>
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="mensaje-exito">
                <span><?php echo htmlspecialchars($_GET['mensaje']); ?></span>
                <span class="cerrar" onclick="this.parentElement.remove()">×</span>
            </div>
        <?php endif; ?>

        <a href="gestionar_usuarios.php">👥 Gestionar usuarios</a>
        <a href="logout.php">🔒 Cerrar sesión</a>
    </div>

    <div class="content">
        <div class="form-section">
            <h3>📚 Subir nuevo libro</h3>
            <form method="POST" action="admin.php" enctype="multipart/form-data">
                <input type="text" name="titulo" placeholder="Título del libro" required>
                <input type="text" name="autor" placeholder="Autor" required>
                <textarea name="descripcion" placeholder="Descripción"></textarea>
                <input type="number" name="fecha_publicacion" placeholder="Año de publicación" min="1000" max="<?php echo date('Y'); ?>" required>
                <label>Archivo PDF del libro:</label>
                <input type="file" name="archivo" accept=".pdf" required>
                <button type="submit" name="subir">📤 Subir libro</button>
            </form>
        </div>

        <div class="table-section">
            <h3>📖 Lista de libros registrados</h3>
            <table>
                <tr>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Descripción</th>
                    <th>Año</th>
                    <th>Leer</th>
                    <th>Acción</th> 
                </tr>

                <?php
                $resultado = $conexion->query("SELECT * FROM libros");
                $libros = $resultado->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($libros) > 0) {
                    foreach ($libros as $libro) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($libro['titulo'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($libro['autor'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($libro['descripcion'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($libro['anio_publicacion'] ?? '—') . "</td>";
                        echo "<td>";
                        if (!empty($libro['archivo_pdf'])) {
                            echo "<a href='../archivos/" . htmlspecialchars($libro['archivo_pdf']) . "' target='_blank'>📄 Ver PDF</a>";
                        } else {
                            echo "—";
                        }
                        echo "</td>";
                        echo "<td>
                                <form method='POST' action='admin.php' onsubmit=\"return confirm('¿Estás seguro de eliminar este libro?');\">
                                    <input type='hidden' name='id' value='" . $libro['id'] . "'>
                                    <button type='submit' name='eliminar'>🗑️ Eliminar</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No hay libros registrados.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>

</body>
</html>