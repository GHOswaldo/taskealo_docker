<?php
// Iniciar sesión al principio del archivo
session_start();

// Verificar si el usuario ha iniciado sesión y es maestro (Rol = 2)
if (!isset($_SESSION['IDUsuario']) || $_SESSION['Rol'] != 2) {
    header("Location: login.html");
    exit();
}

// Incluir el archivo de conexión para lectura
include '../../src/conexion_lectura.php';

// Obtener el ID del maestro desde la sesión
$idMaestro = $_SESSION['IDUsuario'];

// Preparar la consulta para obtener los grupos asignados al maestro
$sql = "SELECT IDGrupo, NombreGrupo 
        FROM GRUPOS 
        WHERE IDGrupo IN (
            SELECT IDGrupo 
            FROM MAESTROS_GRUPOS 
            WHERE IDMaestro = ?
        )";
$stmt = $conn->prepare($sql);

// Verificar si la preparación del statement fue exitosa
if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}

// Vincular el parámetro y ejecutar la consulta
$stmt->bind_param('i', $idMaestro);
$stmt->execute();
$result = $stmt->get_result();

// Manejo de errores en la consulta
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// Cerrar la conexión ya que solo se necesitó para obtener los datos
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Aviso - Taskealo</title>
    <link rel="stylesheet" href="../../src/styles/stylesA.css">
</head>
<body>
    <!-- Incluir la barra lateral -->
    <?php include 'sidebar_maestro.php'; ?>

    <!-- Contenido principal -->
    <div class="content">
        <h1>Publicar Aviso</h1>
        <form action="procesar_publicar_aviso.php" method="POST">
            <!-- Campo para el título del aviso -->
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" required><br><br>

            <!-- Campo para la descripción del aviso -->
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea><br><br>

            <!-- Selección del grupo al que se publicará el aviso -->
            <label for="grupo">Grupo:</label>
            <select id="grupo" name="grupo" required>
                <option value="">Seleccione un grupo</option>
                <?php
                // Mostrar los grupos asignados al maestro
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['IDGrupo']) . "'>" . htmlspecialchars($row['NombreGrupo']) . "</option>";
                }
                ?>
            </select><br><br>

            <!-- Botón para publicar el aviso -->
            <button type="submit">Publicar Aviso</button>
        </form>
    </div>
</body>
</html>
