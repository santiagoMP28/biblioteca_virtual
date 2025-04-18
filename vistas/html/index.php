<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido - Biblioteca Virtual</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }

        .bienvenida-container {
            text-align: center;
            max-width: 600px;
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            animation: aparecer 1s ease-in-out;
        }

        .bienvenida-container h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .bienvenida-container p {
            margin-bottom: 30px;
            color: #666;
        }

        .bienvenida-container img {
            width: 200px;
            margin-bottom: 20px;
        }

        .bienvenida-container a {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .bienvenida-container a:hover {
            background-color: #0056b3;
        }

        @keyframes aparecer {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="bienvenida-container">
<img src="/img/logo.png" alt="Logo de la Biblioteca">

        <h1>Bienvenido a la Biblioteca Virtual</h1>
        <p>Explora, aprende y disfruta de nuestros recursos digitales, inicia sesión para continuar.</p>
        <a href="login.php">Iniciar sesión</a>
    </div>
</body>
</html>
