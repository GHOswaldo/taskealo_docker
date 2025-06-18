<?php
session_start();
if (!isset($_SESSION['IDUsuario']) || $_SESSION['Rol'] != 3) {  // Verificar si es administrador
    header("Location: ../../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Taskealo</title>
    <link rel="stylesheet" href="../../src/styles/stylesA.css"> 
</head>

<body>
    <?php include 'sidebar_administrador.php'; ?>
    <div class='content'>
        <h1>Panel de Administración</h1>
    </div>
</body>

</html>
