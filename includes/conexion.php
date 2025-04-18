<?php
$host = "dpg-d01a606uk2gs73dh2ft0-a.oregon-postgres.render.com";
$dbname = "bibliotecavi";
$user = "bibliotecavi_user";
$password = "D5uyZglk0uUCVy4aT41y5kRHnHlfkRsY";
$port = "5432";

$conn = new PDO(
    "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
    $user,
    $password
);
?>