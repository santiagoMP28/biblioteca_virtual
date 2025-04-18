<?php
session_start();

// Inicializa todas las variables que se usarán en la vista
$mensaje = "";
$error = "";

// Verificación del driver PostgreSQL
if (!extension_loaded('pdo_pgsql')) {
    die("Error: El sistema no está disponible temporalmente. Por favor, inténtalo más tarde.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"] ?? '';
    $contraseña = $_POST["contraseña"] ?? '';

    try {
        $conn = new PDO(
            "pgsql:host=dpg-d01a606uk2gs73dh2ft0-a;" .
            "dbname=bibliotecavi;" .
            "user=bibliotecavi_user;" .
            "password=D5uyZglk0uUCVy4aT41y5kRHnHlfkRsY",
            null,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $stmt = $conn->prepare("SELECT id, correo, contraseña, rol FROM usuarios WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() === 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificación de contraseña (compatible con texto plano y hash)
            if ($contraseña === $usuario['contraseña'] || password_verify($contraseña, $usuario['contraseña'])) {
                $_SESSION['usuario'] = [
                    'id' => $usuario['id'],
                    'correo' => $usuario['correo'],
                    'rol' => $usuario['rol']
                ];
                
                header('Location: ' . ($usuario['rol'] === 'admin' ? 'admin.php' : 'usuario.php'));
                exit;
            }
        }
        
        $error = "Credenciales incorrectas";
        
    } catch (PDOException $e) {
        $error = "Error temporal. Por favor, inténtalo nuevamente.";
        error_log("Error de conexión: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="/css/estilos.css">
    <style>
        .mensaje-error {
            color: #d32f2f;
            background: #ffebee;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Iniciar Sesión</h2>
        
        <?php if (!empty($error)): ?>
            <div class="mensaje-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" required value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="contraseña">Contraseña:</label>
                <input type="password" id="contraseña" name="contraseña" required>
            </div>
            
            <button type="submit">Ingresar</button>
        </form>
        
        <div class="form-footer">
            ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
        </div>
    </div>
</body>
</html>