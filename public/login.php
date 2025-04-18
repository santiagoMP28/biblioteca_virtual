<?php
session_start();

// Verificación crítica del driver (añade esto al inicio)
if (!extension_loaded('pdo_pgsql')) {
    die("Error: El controlador PostgreSQL no está instalado. Contacta al administrador del servidor.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    $contraseña = $_POST["contraseña"];

    // Conexión directa (versión simplificada)
    try {
        $conn = new PDO(
            "pgsql:host=dpg-d01a606uk2gs73dh2ft0-a;" .
            "dbname=bibliotecavi",
            "bibliotecavi_user",
            "D5uyZglk0uUCVy4aT41y5kRHnHlfkRsY"
        );
        
        // Consulta de login
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch();
        
        if ($usuario && ($contraseña === $usuario['contraseña'] || password_verify($contraseña, $usuario['contraseña']))) {
            $_SESSION['usuario'] = $usuario;
            header("Location: " . ($usuario['rol'] == 'admin' ? 'admin.php' : 'usuario.php'));
            exit;
        } else {
            $error = "Credenciales incorrectas";
        }
    } catch (PDOException $e) {
        $error = "Error de conexión: " . $e->getMessage();
    }
}
?>

<!-- Mantén tu HTML actual -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="/css/estilos.css">
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