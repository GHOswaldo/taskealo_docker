<?php
// Iniciar sesión para obtener el ID del usuario logueado
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['IDUsuario'])) {
    die("Acceso denegado. Por favor, inicia sesión.");
}

// Incluir archivo de conexión a la base de datos
include '../../src/conexion_escritura.php';

// Obtener el ID de la tarea desde la URL
$idTarea = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idTarea == 0) {
    die("ID de tarea inválido.");
}

// Obtener los datos de la tarea actual utilizando consulta preparada
$sql = "SELECT * FROM TAREAS WHERE IDTarea = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $idTarea);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("No se encontró la tarea.");
}

$tarea = $result->fetch_assoc();

// Si se envía el formulario para editar la tarea
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fechaCreacion = $_POST['fecha_creacion'];
    $fechaLimite = $_POST['fecha_limite'];
    $materia = $_POST['materia'];

    // Validar que los campos obligatorios no estén vacíos
    if (empty($titulo) || empty($descripcion) || empty($fechaCreacion) || empty($fechaLimite) || empty($materia)) {
        die("Todos los campos son obligatorios.");
    }

    // Actualizar la tarea en la base de datos utilizando consulta preparada
    $sqlUpdate = "UPDATE TAREAS SET 
                  Titulo = ?, 
                  Descripcion = ?, 
                  FechaCreacion = ?, 
                  FechaLimite = ?, 
                  IDMateria = ? 
                  WHERE IDTarea = ?";

    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param('ssssii', $titulo, $descripcion, $fechaCreacion, $fechaLimite, $materia, $idTarea);

    // Ejecutar la actualización
    if ($stmtUpdate->execute()) {
        header("Location: listar_tareas_maestro.php"); // Redirige a la lista de tareas después de actualizar
        exit();
    } else {
        echo "Error al actualizar la tarea: " . $stmtUpdate->error;
    }

    // Cerrar la consulta de actualización
    $stmtUpdate->close();
}

// Obtener la lista de materias para el dropdown utilizando consulta preparada
$sqlMaterias = "SELECT IDMateria, Nombre FROM MATERIAS";
$resultMaterias = $conn->query($sqlMaterias);

?>

<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Editar Tarea - Taskealo</title>
    <link rel="stylesheet" href="../../src/styles/stylesA.css">
</head>
<body>
    <?php include '../sidebar_maestro.php'; ?>
    <div class='content'>
        <h1>Editar Tarea</h1>
        <form action="editar_tarea.php?id=<?php echo $idTarea; ?>" method="POST">
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($tarea['Titulo']); ?>" required><br><br>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($tarea['Descripcion']); ?></textarea><br><br>

            <label for="fecha_creacion">Fecha de Creación:</label>
            <input type="date" id="fecha_creacion" name="fecha_creacion" value="<?php echo $tarea['FechaCreacion']; ?>" required><br><br>

            <label for="fecha_limite">Fecha Límite:</label>
            <input type="date" id="fecha_limite" name="fecha_limite" value="<?php echo $tarea['FechaLimite']; ?>" required><br><br>

            <label for="materia">Materia:</label>
            <select id="materia" name="materia" required>
                <?php
                while ($row = $resultMaterias->fetch_assoc()) {
                    $selected = $row['IDMateria'] == $tarea['IDMateria'] ? 'selected' : '';
                    echo "<option value='{$row['IDMateria']}' $selected>{$row['Nombre']}</option>";
                }
                ?>
            </select><br><br>

            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>

<?php
// Cerrar la conexión
$conn->close();
?>
