<?php
session_start();
if (!isset($_SESSION['IDUsuario']) || $_SESSION['Rol'] != 3) {
    header("Location: ../../login.php");
    exit();
}

include '../../src/conexion_escritura.php';

// Obtener el ID del rol
$id = $_GET['id'];
$sql = "SELECT * FROM ROLES WHERE IDRol = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$rol = $result->fetch_assoc();
$stmt->close();

// Procesar la actualización del rol
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_rol = $_POST['nombre_rol'];
    $sql = "UPDATE ROLES SET NombreRol = ? WHERE IDRol = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $nombre_rol, $id);
    if ($stmt->execute()) {
        header("Location: Roles.php");
        exit();
    } else {
        echo "Error al actualizar el rol.";
    }
    $stmt->close();
}

// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Rol</title>
    <link rel="stylesheet" href="../../src/styles/stylesA.css">
</head>

<body>
    <?php include '../sidebar_administrador.php'; ?>

    <div class="content"> <!-- Mover el contenido para que no quede oculto por el sidebar -->
        <h1>Editar Rol</h1>
        <form action="editar_rol.php?id=<?php echo $id; ?>" method="POST">
            <label for="nombre_rol">Nombre del Rol:</label>
            <input type="text" id="nombre_rol" name="nombre_rol" value="<?php echo $rol['NombreRol']; ?>" required><br><br>
            <button type="submit">Actualizar Rol</button>
            <!-- Botón de regreso -->
            <a href="Roles.php">
                <button type="button">Volver a Roles</button>
            </a>

        </form>
    </div>
</body>

</html>
