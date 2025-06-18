<?php
session_start();
if (!isset($_SESSION['IDUsuario']) || $_SESSION['Rol'] != 3) {
    header("Location: ../../login.php"); 
    exit();
}

include '../../src/conexion_escritura.php'; 

// Obtener el ID de la materia
$id = $_GET['id'];

// Consultar los datos de la materia
$sql = "SELECT * FROM MATERIAS WHERE IDMateria = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$materia = $result->fetch_assoc();
$stmt->close();

// Procesar la actualización de la materia
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_materia = $_POST['nombre_materia'];
    $sql = "UPDATE MATERIAS SET Nombre = ? WHERE IDMateria = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $nombre_materia, $id);
    if ($stmt->execute()) {
        header("Location: Materias.php"); 
        exit();
    } else {
        echo "Error al actualizar la materia.";
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
    <title>Editar Materia</title>
    <link rel="stylesheet" href="../../src/styles/stylesA.css">
</head>

<body>
    <?php include '../sidebar_administrador.php'; ?> 

    <div class="content"> <!-- Mover el contenido para que no quede oculto por el sidebar -->
        <h1>Editar Materia</h1>
        <form action="editar_materia.php?id=<?php echo $id; ?>" method="POST">
            <label for="nombre_materia">Nombre de la Materia:</label>
            <input type="text" id="nombre_materia" name="nombre_materia" value="<?php echo $materia['Nombre']; ?>"
                required><br><br>
            <button type="submit">Actualizar Materia</button>
            <!-- Botón de regreso -->
            <a href="Materias.php">
                <button type="button">Volver a Materias</button>
            </a>
        </form>
    </div>
</body>

</html>
