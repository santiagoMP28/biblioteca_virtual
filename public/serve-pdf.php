<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isset($_SESSION["usuario"])) {
    header("HTTP/1.1 401 Unauthorized");
    exit("Acceso no autorizado");
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    header("HTTP/1.1 400 Bad Request");
    exit("ID inválido");
}

$stmt = $conexion->prepare("SELECT archivo_pdf FROM libros WHERE id = ?");
$stmt->execute([$id]);
$archivo = $stmt->fetchColumn();

if (!$archivo) {
    header("HTTP/1.1 404 Not Found");
    exit("Libro no encontrado");
}

$ruta = '/tmp/archivos/' . basename($archivo);
if (!file_exists($ruta)) {
    header("HTTP/1.1 404 Not Found");
    exit("PDF no encontrado");
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($ruta) . '"');
header('Content-Length: ' . filesize($ruta));
readfile($ruta);
exit;