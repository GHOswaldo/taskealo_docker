<?php
session_start();
if (!isset($_SESSION['IDUsuario']) || $_SESSION['Rol'] != 3) {
    header("Location: ../../login.php"); 
    exit();
}

include '../../src/conexion_escritura.php'; 

$id = $_GET['id'];
$sql = "SELECT * FROM CARRERAS WHERE IDCarrera = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$carrera = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_carrera = $_POST['nombre_carrera'];
    $sql = "UPDATE CARRERAS SET NombreCarrera = ? WHERE IDCarrera = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $nombre_carrera, $id);
    if ($stmt->execute()) {
        header("Location: Carreras.php");
        exit();
    } else {
        echo "Error al actualizar la carrera.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Carrera</title>
    <link rel="stylesheet" href="../../src/styles/stylesA.css">

<body>
    <?php include '../sidebar_administrador.php'; ?>

    <div class="content">
        <h1>Editar Carrera</h1>
        <form action="editar_carrera.php?id=<?php echo $id; ?>" method="POST">
            <label for="nombre_carrera">Nombre de la Carrera:</label>
            <input type="text" id="nombre_carrera" name="nombre_carrera"
                value="<?php echo $carrera['NombreCarrera']; ?>" required><br><br>
            <button type="submit">Actualizar Carrera</button>
            <!-- Botón de regreso -->
            <a href="Carreras.php">
                <button type="button">Volver a Carreras</button>
            </a>

        </form>
    </div>
</body>

</html>
