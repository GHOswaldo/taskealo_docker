<?php
// Iniciar sesión al principio del archivo
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['IDUsuario'])) {
    // Si no está logueado, redirigir al inicio de sesión
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario logueado desde la sesión
$usuarioID = $_SESSION['IDUsuario'];

// Incluir archivo de conexión a la base de datos
include '../../src/conexion_escritura.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Tarea - Taskealo</title>
    <link rel="stylesheet" href="../../src/styles/stylesA.css">
</head>
<body>
    <?php include '../sidebar_maestro.php'; ?> <!-- Incluir la barra lateral -->

    <div class="content">
        <h1>Agregar Tarea</h1>
        <form action="agregar_tarea.php" method="POST">
            <!-- Campo para el título de la tarea -->
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" required><br><br>

            <!-- Campo para la descripción de la tarea -->
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea><br><br>

            <!-- Selección de la materia -->
            <label for="materia">Materia:</label>
            <select id="materia" name="materia" required>
                <option value="">Seleccione una materia</option>
                <?php
                // Obtener la lista de materias desde la base de datos
                $sqlMaterias = "SELECT IDMateria, Nombre FROM MATERIAS";
                $resultMaterias = $conn->query($sqlMaterias);
                
                // Verificar si la consulta fue exitosa
                if ($resultMaterias) {
                    while ($row = $resultMaterias->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['IDMateria']) . "'>" . htmlspecialchars($row['Nombre']) . "</option>";
                    }
                } else {
                    echo "<option disabled>No se encontraron materias</option>";
                }
                ?>
            </select><br><br>

            <!-- Selección del grupo del usuario -->
            <label for="grupo">Grupo:</label>
            <select id="grupo" name="grupo" required>
                <option value="">Seleccione un grupo</option>
                <?php
                // Obtener los grupos a los que el usuario pertenece
                $sqlGruposUsuario = "SELECT G.IDGrupo, G.NombreGrupo 
                                     FROM GRUPOS G
                                     INNER JOIN GRUPOS_USUARIOS GU ON G.IDGrupo = GU.IDGrupo
                                     WHERE GU.IDUsuario = ?";
                
                // Preparar y ejecutar la consulta
                if ($stmt = $conn->prepare($sqlGruposUsuario)) {
                    $stmt->bind_param('i', $usuarioID);
                    $stmt->execute();
                    $resultGrupos = $stmt->get_result();

                    // Mostrar los grupos a los que pertenece el usuario
                    while ($rowGrupo = $resultGrupos->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($rowGrupo['IDGrupo']) . "'>" . htmlspecialchars($rowGrupo['NombreGrupo']) . "</option>";
                    }

                    $stmt->close();
                }
                ?>
            </select><br><br>

            <!-- Campo para la fecha de creación -->
            <label for="fecha_creacion">Fecha de Creación:</label>
            <input type="date" id="fecha_creacion" name="fecha_creacion" required><br><br>

            <!-- Campo para la fecha límite -->
            <label for="fecha_limite">Fecha Límite:</label>
            <input type="date" id="fecha_limite" name="fecha_limite" required><br><br>

            <!-- Botón para agregar la tarea -->
            <button type="submit">Agregar Tarea</button>
        </form>
    </div>

    <!-- Cerrar la conexión a la base de datos -->
    <?php $conn->close(); ?>
</body>
</html>
