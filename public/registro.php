<?php
$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    $contraseña = $_POST["contraseña"];

    $conn = new mysqli("localhost", "root", "", "bibliotecaV");

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO usuarios (correo, contraseña) VALUES (?, ?)");
    $stmt->bind_param("ss", $correo, $contraseña);

    if ($stmt->execute()) {
        $mensaje = "<p class='mensaje-exito'>Registro exitoso. <a href='login.php' class='btn'>Iniciar sesión</a></p>";
    } else {
        $mensaje = "<p class='mensaje-error'>Error al registrar: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="../css/estilos.css">
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
