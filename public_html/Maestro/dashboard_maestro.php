<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['IDUsuario'])) {
    header("Location: ../../login.php");
    exit();
}

// Mostrar información del usuario logueado
?>
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Dashboard - Taskealo</title>
    <link rel="stylesheet" href="../../src/styles/stylesA.css">
</head>
<body>
    <?php include 'sidebar_maestro.php'; ?>
    <div class='content'>
        <h1>Bienvenido, <?php echo $_SESSION['Nombre']; ?></h1>
        <p>Tu rol es: <?php echo $_SESSION['Rol']; ?></p>


        <?php
        // Acciones específicas para diferentes roles
        if ($_SESSION['Rol'] == 1) {  // Si el rol es Alumno
            echo "<p>Acceso a funcionalidades de alumnos</p>";
        } elseif ($_SESSION['Rol'] == 2) {  // Si el rol es Maestro
            echo "<p>Acceso a funcionalidades de maestros</p>";
        } else {
            echo "<p>Acceso estándar.</p>";
        }
        ?>
    </div>
</body>
</html>
