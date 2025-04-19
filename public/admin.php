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

        $_SESSION['mensaje'] = ['texto' => '🗑️ Libro eliminado correctamente', 'tipo' => 'exito'];
        header("Location: admin.php");
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
        $_SESSION['mensaje'] = ['texto' => '⚠️ El libro ya existe', 'tipo' => 'error'];
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

            $_SESSION['mensaje'] = ['texto' => '✅ Libro subido correctamente', 'tipo' => 'exito'];
        } else {
            $_SESSION['mensaje'] = ['texto' => '❌ Error al subir el archivo PDF', 'tipo' => 'error'];
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
            position: relative;
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
        .mensaje-flotante {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: slideIn 0.3s ease-out;
            max-width: 400px;
        }

        .mensaje-exito {
            background-color: #4CAF50;
            color: white;
            border-left: 5px solid #2E7D32;
        }

        .mensaje-error {
            background-color: #F44336;
            color: white;
            border-left: 5px solid #C62828;
        }

        .cerrar-mensaje {
            cursor: pointer;
            margin-left: 15px;
            font-weight: bold;
            font-size: 20px;
            opacity: 0.8;
        }

        .cerrar-mensaje:hover {
            opacity: 1;
        }

        @keyframes slideIn {
            from { 
                opacity: 0;
                transform: translateX(100%);
            }
            to { 
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideOut {
            from { 
                opacity: 1;
                transform: translateX(0);
            }
            to { 
                opacity: 0;
                transform: translateX(100%);
            }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin</h2>
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

    <?php
    // Mostrar mensaje flotante si existe
    if (isset($_SESSION['mensaje'])) {
        $mensaje = $_SESSION['mensaje'];
        unset($_SESSION['mensaje']);
        echo "
        <div class='mensaje-flotante mensaje-{$mensaje['tipo']}'>
            <span>{$mensaje['texto']}</span>
            <span class='cerrar-mensaje' onclick='cerrarMensaje(this)'>×</span>
        </div>
        <script>
            function cerrarMensaje(elemento) {
                elemento.parentElement.style.animation = 'slideOut 0.3s forwards';
                setTimeout(() => elemento.parentElement.remove(), 300);
            }
            
            // Auto-cierre después de 5 segundos
            setTimeout(() => {
                const mensaje = document.querySelector('.mensaje-flotante');
                if (mensaje) {
                    mensaje.style.animation = 'slideOut 0.3s forwards';
                    setTimeout(() => mensaje.remove(), 300);
                }
            }, 5000);
        </script>
        ";
    }
    ?>
</body>
</html>