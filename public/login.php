<?php
session_start();

// Inicializa variables
$error = "";

// Verificación del driver
if (!extension_loaded('pdo_pgsql')) {
    die("Error: El sistema no está disponible temporalmente. Por favor, inténtalo más tarde.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"] ?? '';
    $contraseña = $_POST["contraseña"] ?? '';

    try {
        // Conexión corregida
        $conn = new PDO(
            "pgsql:host=dpg-d01a606uk2gs73dh2ft0-a;" .
            "dbname=bibliotecavi",
            "bibliotecavi_user",
            "D5uyZglk0uUCVy4aT41y5kRHnHlfkRsY"
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT id, correo, contraseña, rol FROM usuarios WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() === 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificación de contraseña
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
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        
        h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 500;
        }
        
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        
        button[type="submit"] {
            width: 100%;
            padding: 0.75rem;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 1rem;
        }
        
        button[type="submit"]:hover {
            background-color: #2980b9;
        }
        
        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #7f8c8d;
        }
        
        .form-footer a {
            color: #3498db;
            text-decoration: none;
        }
        
        .form-footer a:hover {
            text-decoration: underline;
        }
        
        .mensaje-error {
            color: #e74c3c;
            background-color: #fadbd8;
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
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
                <input type="email" id="correo" name="correo" required value="<?php echo htmlspecialchars($correo); ?>">
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