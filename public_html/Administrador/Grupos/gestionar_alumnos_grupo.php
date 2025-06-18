<?php 

include '../../src/conexion_escritura.php';

// Obtener el ID del grupo desde la URL
$id_grupo = isset($_GET['id_grupo']) ? (int)$_GET['id_grupo'] : 0;

// Obtener información del grupo
$sqlGrupo = "SELECT NombreGrupo FROM GRUPOS WHERE IDGrupo = $id_grupo";
$resultGrupo = $conn->query($sqlGrupo);

// Manejo de errores de la consulta
if (!$resultGrupo) {
    die("Error en la consulta de grupo: " . $conn->error);
}

$grupo = $resultGrupo->fetch_assoc();

if (!$grupo) {
    die("Grupo no encontrado.");
}

// Obtener alumnos que ya están en el grupo
$sqlAlumnosEnGrupo = "
    SELECT U.IDUsuario, U.Nombre 
    FROM USUARIOS U 
    JOIN GRUPOS_USUARIOS GU ON U.IDUsuario = GU.IDUsuario 
    WHERE GU.IDGrupo = $id_grupo AND U.IDRol = 1
";
$resultAlumnosEnGrupo = $conn->query($sqlAlumnosEnGrupo);

// Manejo de errores de la consulta
if (!$resultAlumnosEnGrupo) {
    die("Error en la consulta de alumnos en el grupo: " . $conn->error);
}

$alumnosEnGrupo = $resultAlumnosEnGrupo->fetch_all(MYSQLI_ASSOC);

// Obtener lista de todos los alumnos que NO están en el grupo
$sqlAlumnos = "SELECT IDUsuario, Nombre FROM USUARIOS WHERE IDRol = 1 AND IDUsuario NOT IN (SELECT IDUsuario FROM GRUPOS_USUARIOS WHERE IDGrupo = $id_grupo)";
$resultAlumnos = $conn->query($sqlAlumnos);

// Obtener el docente actual (si existe) para ocultarlo en la lista
$sqlDocenteActual = "
    SELECT U.IDUsuario, U.Nombre 
    FROM USUARIOS U 
    JOIN GRUPOS_USUARIOS GU ON U.IDUsuario = GU.IDUsuario 
    WHERE GU.IDGrupo = $id_grupo AND U.IDRol = 2
";
$resultDocenteActual = $conn->query($sqlDocenteActual);
$docenteActual = $resultDocenteActual->fetch_assoc();

// Obtener lista de todos los docentes que NO están asignados al grupo actual
$sqlDocentes = "SELECT IDUsuario, Nombre FROM USUARIOS WHERE IDRol = 2";
$resultDocentes = $conn->query($sqlDocentes);

// Manejo de errores de la consulta
if (!$resultDocentes) {
    die("Error en la consulta de docentes: " . $conn->error);
}

$docentes = [];
while ($docente = $resultDocentes->fetch_assoc()) {
    // Solo agregar docentes que no son el docente actual
    if ($docenteActual && $docente['IDUsuario'] !== $docenteActual['IDUsuario']) {
        $docentes[] = $docente;
    }
}

// Manejo de la solicitud para agregar o eliminar alumnos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['agregar'])) {
        $alumnosSeleccionados = $_POST['alumnos'] ?? [];
        foreach ($alumnosSeleccionados as $id_alumno) {
            $sqlAgregar = "INSERT INTO GRUPOS_USUARIOS (IDGrupo, IDUsuario) VALUES ('$id_grupo', '$id_alumno')";
            $conn->query($sqlAgregar);
        }
    } elseif (isset($_POST['eliminar'])) {
        $alumnosAEliminar = $_POST['alumnos'] ?? [];
        foreach ($alumnosAEliminar as $id_alumno) {
            $sqlEliminar = "DELETE FROM GRUPOS_USUARIOS WHERE IDGrupo = '$id_grupo' AND IDUsuario = '$id_alumno'";
            $conn->query($sqlEliminar);
        }
    } elseif (isset($_POST['cambiar_docente'])) {
        $nuevoDocenteId = $_POST['nuevo_docente'] ?? null;
        if ($nuevoDocenteId) {
            // Eliminar al docente actual
            if ($docenteActual) {
                $sqlEliminarDocente = "DELETE FROM GRUPOS_USUARIOS WHERE IDGrupo = '$id_grupo' AND IDUsuario = " . $docenteActual['IDUsuario'];
                $conn->query($sqlEliminarDocente);
            }
            // Asignar el nuevo docente
            $sqlAgregarDocente = "INSERT INTO GRUPOS_USUARIOS (IDGrupo, IDUsuario) VALUES ('$id_grupo', '$nuevoDocenteId')";
            $conn->query($sqlAgregarDocente);
        }
    }
    header("Location: gestionar_alumnos_grupo.php?id_grupo=$id_grupo");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Alumnos - <?php echo htmlspecialchars($grupo['NombreGrupo']); ?></title>
    <link rel="stylesheet" href="../../src/styles/stylesA.css">
</head>
<body>
    <?php include '../sidebar_administrador.php'; ?>

    <div class="content">
        <h1>Gestionar Alumnos - <?php echo htmlspecialchars($grupo['NombreGrupo']); ?></h1>

        <form action="" method="POST">
            <h2>Agregar Alumnos al Grupo:</h2>
            <?php if ($resultAlumnos->num_rows > 0): ?>
                <?php while ($row = $resultAlumnos->fetch_assoc()): ?>
                    <input type="checkbox" name="alumnos[]" value="<?php echo htmlspecialchars($row['IDUsuario']); ?>">
                    <?php echo htmlspecialchars($row['Nombre']); ?><br>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No hay alumnos disponibles para agregar.</p>
            <?php endif; ?>
            <button type="submit" name="agregar">Agregar Alumnos</button>
        </form>

        <h2>Alumnos en el Grupo:</h2>
        <form action="" method="POST">
            <table class="tabla-alumnos">
                <thead>
                    <tr>
                        <th>Nombre del Alumno</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alumnosEnGrupo as $alumno): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($alumno['Nombre']); ?></td>
                            <td>
                                <input type="checkbox" name="alumnos[]" value="<?php echo htmlspecialchars($alumno['IDUsuario']); ?>"> Quitar
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" name="eliminar">Eliminar Alumnos</button>
        </form>

        <h2>Cambiar Docente del Grupo:</h2>
        <form action="" method="POST">
            <label for="nuevo_docente">Seleccionar nuevo docente:</label>
            <select name="nuevo_docente" id="nuevo_docente" required>
                <option value="">Seleccione un docente</option>
                <?php foreach ($docentes as $docente): ?>
                    <option value="<?php echo htmlspecialchars($docente['IDUsuario']); ?>">
                        <?php echo htmlspecialchars($docente['Nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="cambiar_docente">Cambiar Docente</button>
        </form>

        <a href="Grupos.php">Volver a Grupos</a>
    </div>
</body>
</html>

<?php
// Cerrar conexión
$conn->close();
?>
