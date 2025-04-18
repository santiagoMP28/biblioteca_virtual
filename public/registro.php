<?php
session_start();
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    $contraseña = $_POST["contraseña"];

    try {
        // Conexión a PostgreSQL en Render
        $conn = new PDO(
            "pgsql:host=dpg-d01a606uk2gs73dh2ft0-a;" .
            "dbname=bibliotecavi;" .
            "user=bibliotecavi_user;" .
            "password=D5uyZglk0uUCVy4aT41y5kRHnHlfkRsY;" .
            "sslmode=require"
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);

        // Hash de la contraseña (importante para seguridad)
        $contraseña_hash = password_hash($contraseña, PASSWORD_BCRYPT);

        // Consulta preparada para PostgreSQL
        $stmt = $conn->prepare("INSERT INTO usuarios (correo, contraseña, rol) VALUES (:correo, :contraseña, 'usuario')");
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':contraseña', $contraseña_hash);
        
        if ($stmt->execute()) {
            $mensaje = "<p class='mensaje-exito'>Registro exitoso. <a href='login.php' class='btn'>Iniciar sesión</a></p>";
        } else {
            $mensaje = "<p class='mensaje-error'>Error al registrar. El correo ya existe.</p>";
        }
    } catch(PDOException $e) {
        if ($e->getCode() == '23505') { // Código de error para duplicados en PostgreSQL
            $mensaje = "<p class='mensaje-error'>El correo electrónico ya está registrado.</p>";
        } else {
            $mensaje = "<p class='mensaje-error'>Error de conexión: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        .mensaje-exito {
            color: green;
            background: #e8f5e9;
            padding: 10px;
            border-radius: 5px;
        }
        .mensaje-error {
            color: #d32f2f;
            background: #ffebee;
            padding: 10px;
            border-radius: 5px;
        }
        .btn {
            color: #1e88e5;
            text-decoration: none;
            font-weight: bold;
        }
        .form-container {
            max-width: 500px;
            margin: 30px auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Registro de usuario</h2>

        <?php echo $mensaje; ?>

        <form method="POST">
            <label>Correo:</label>
            <input type="email" name="correo" required>

            <label>Contraseña:</label>
            <input type="password" name="contraseña" required minlength="6">

            <input type="submit" value="Registrarse">
        </form>

        <p style="text-align:center; margin-top:10px;">
            <a href="login.php" class="btn">¿Ya tienes cuenta? Iniciar sesión</a>
        </p>
    </div>
</body>
</html>