<?php
$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    $contraseña = $_POST["contraseña"];

    // Sanitizar correo
    $correo = filter_var($correo, FILTER_SANITIZE_EMAIL);

    // Verificar si el correo es válido
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "<p class='mensaje-error'>Correo no válido. Por favor, ingresa un correo válido.</p>";
    } else {
        // Encripta la contraseña antes de guardarla
        $contraseñaHash = password_hash($contraseña, PASSWORD_DEFAULT);

        try {
            $conn = new PDO(
                "pgsql:host=dpg-d01a606uk2gs73dh2ft0-a.oregon-postgres.render.com;" . 
                "dbname=bibliotecavi;" . 
                "sslmode=require",
                'bibliotecavi_user',
                'D5uyZglk0uUCVy4aT41y5kRHnHlfkRsY'
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Verificar si el correo ya existe en la base de datos
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = :correo");
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $mensaje = "<p class='mensaje-error'>Este correo ya está registrado. Por favor, usa otro.</p>";
            } else {
                // Insertar nuevo usuario
                $stmt = $conn->prepare("INSERT INTO usuarios (correo, contraseña, rol) VALUES (:correo, :contraseña, 'usuario')");
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':password', $contraseñaHash);
                $stmt->execute();

                $mensaje = "<p class='mensaje-exito'>Registro exitoso. <a href='login.php' class='btn'>Iniciar sesión</a></p>";
            }
        } catch (PDOException $e) {
            $mensaje = "<p class='mensaje-error'>Error al registrar: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="/css/estilos.css">
</head>
<body>
    <div class="form-container">
        <h2>Registro de usuario</h2>

        <?php echo $mensaje; ?>

        <form method="POST">
            <label>Correo:</label>
            <input type="email" name="correo" required>

            <label>Contraseña:</label>
            <input type="password" name="contraseña" required>

            <input type="submit" value="Registrarse">
        </form>

        <p style="text-align:center; margin-top:10px;">
            <a href="login.php" class="btn">¿Ya tienes cuenta? Iniciar sesión</a>
        </p>
    </div>
</body>
</html>
