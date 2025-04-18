<?php
session_start();
$mensaje = ""; // Inicializa para evitar el warning

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    $contraseña = $_POST["contraseña"];

    // Conexión a PostgreSQL en Render (usa variables de entorno en producción)
    try {
        $conn = new PDO(
            "pgsql:host=dpg-d01a606uk2gs73dh2ft0-a.oregon-postgres.render.com;" .
            "dbname=bibliotecavi;" .
            "sslmode=require",
            'bibliotecavi_user',
            'D5uyZglk0uUCVy4aT41y5kRHnHlfkRsY'
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);

        // Consulta segura con prepared statement
        $stmt = $conn->prepare("SELECT id, correo, contraseña, rol FROM usuarios WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Verificar contraseña (asumiendo que está hasheada)
            if (password_verify($contraseña, $usuario['contraseña'])) {
                $_SESSION["id_usuario"] = $usuario["id"];
                $_SESSION["rol"] = $usuario["rol"];
                $_SESSION["correo"] = $usuario["correo"];

                // Redirección según el rol
                header("Location: " . ($usuario["rol"] == "admin" ? "admin.php" : "usuario.php"));
                exit;
            } else {
                $mensaje = "<p class='mensaje-error'>Correo o contraseña incorrectos.</p>";
            }
        } else {
            $mensaje = "<p class='mensaje-error'>Correo o contraseña incorrectos.</p>";
        }
    } catch(PDOException $e) {
        $mensaje = "<p class='mensaje-error'>Error de conexión: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

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