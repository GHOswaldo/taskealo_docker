<?php
// Iniciar sesión para verificar que el usuario esté logueado
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['IDUsuario'])) {
    die("Acceso denegado. Por favor, inicia sesión.");
}

// Incluir el archivo de conexión
include '../../src/conexion_escritura.php';

// Verificar si se ha enviado un ID a través del parámetro GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta para obtener los datos del aviso
    $sql = "SELECT AV.IDAviso, AV.Titulo, AV.Descripcion, AV.IDGrupo, G.NombreGrupo
            FROM AVISOS AV
            LEFT JOIN GRUPOS G ON AV.IDGrupo = G.IDGrupo
            WHERE AV.IDAviso = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se encontró el aviso
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No se encontró el aviso.";
        exit();
    }

    $stmt->close();
} else {
    echo "ID no especificado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Editar Aviso - Taskealo</title>
    <link rel="stylesheet" href="../../src/styles/stylesA.css"> <!-- Asegúrate de que stylesA.css esté presente -->
</head>
<body>
    <?php include '../sidebar_maestro.php'; ?> <!-- Incluir la barra lateral correctamente -->
    <div class='content'> <!-- Contenido principal -->
        <h1>Editar Aviso</h1>
        <form action="actualizar_aviso.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['IDAviso']); ?>">

            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($row['Titulo']); ?>" required><br><br>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($row['Descripcion']); ?></textarea><br><br>

            <label for="grupo">Grupo:</label>
            <select id="grupo" name="grupo" required>
                <option value="">Seleccionar Grupo</option>
                <?php
                // Consulta para obtener todos los grupos para el combobox
                $sqlGrupos = "SELECT IDGrupo, NombreGrupo FROM GRUPOS";
                $resultGrupos = $conn->query($sqlGrupos);

                if ($resultGrupos) {
                    while ($grupo = $resultGrupos->fetch_assoc()) {
                        $selected = ($grupo['IDGrupo'] == $row['IDGrupo']) ? 'selected' : '';
                        echo "<option value='{$grupo['IDGrupo']}' $selected>{$grupo['NombreGrupo']}</option>";
                    }
                } else {
                    echo "<option value=''>Error al cargar los grupos</option>";
                }
                ?>
            </select><br><br>

            <button type="submit">Actualizar Aviso</button>
        </form>
    </div>
</body>
</html>

<?php
// Cerrar la conexión
$conn->close();
?>
