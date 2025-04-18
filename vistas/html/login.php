<?php
session_start();
$mensaje = ""; // Inicializa para evitar el warning

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    $contraseña = $_POST["contraseña"];

    // Conexión a la base de datos
    $conn = new mysqli("localhost", "root", "", "bibliotecaV");

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Consulta segura con prepared statements
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo=? AND contraseña=?");
    $stmt->bind_param("ss", $correo, $contraseña);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        $_SESSION["id_usuario"] = $usuario["id"];
        $_SESSION["rol"] = $usuario["rol"];

        // Redirección según el rol
        if ($usuario["rol"] == "admin") {
            header("Location: admin.php");
        } else {
            header("Location: usuario.php");
        }
        exit;
    } else {
        $mensaje = "<p class='mensaje-error'>Correo o contraseña incorrectos.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <div class="form-container">
        <h2>Iniciar Sesión</h2>

        <?php echo $mensaje; ?>

        <form method="POST">
            <label for="correo">Correo:</label>
            <input type="email" name="correo" required>

            <label for="contraseña">Contraseña:</label>
            <input type="password" name="contraseña" required>

            <input type="submit" value="Iniciar sesión">
        </form>

        <p style="text-align:center; margin-top:10px;">
            ¿No tienes cuenta? <a href="registro.php" class="btn">Registrarse</a>
        </p>
    </div>
</body>
</html>
