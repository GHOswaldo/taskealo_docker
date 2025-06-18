<?php
// Iniciar sesión para obtener el ID del usuario logueado
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['IDUsuario'])) {
    die("Acceso denegado. Por favor, inicia sesión.");
}

// Incluir archivo de conexión a la base de datos
include '../../src/conexion_escritura.php';

// Recoger datos del formulario
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$materia = $_POST['materia']; // ID de la materia seleccionada
$grupo = $_POST['grupo']; // ID del grupo seleccionado
$fecha_creacion = $_POST['fecha_creacion']; // Fecha de creación seleccionada por el usuario
$fecha_limite = $_POST['fecha_limite'];
$alumno_id = $_SESSION['IDUsuario']; // Obtener el ID del usuario logueado

// Validar que los campos obligatorios no estén vacíos
if (empty($titulo) || empty($descripcion) || empty($materia) || empty($grupo) || empty($fecha_creacion) || empty($fecha_limite)) {
    die("Todos los campos son obligatorios.");
}

// Insertar la tarea en la tabla TAREAS usando una consulta preparada
$sql = "INSERT INTO TAREAS (Titulo, Descripcion, FechaCreacion, FechaLimite, IDMateria, IDGrupo, IDAlumno) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

// Verificar si la preparación de la consulta fue exitosa
if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}

// Vincular los parámetros a la consulta
$stmt->bind_param('ssssiii', $titulo, $descripcion, $fecha_creacion, $fecha_limite, $materia, $grupo, $alumno_id);

// Ejecutar la consulta
if ($stmt->execute()) {
    // Redirigir de vuelta a la página de tareas
    header("Location: listar_tareas_maestro.php"); 
    exit();
} else {
    // Si hay un error, mostrarlo
    echo "Error al agregar la tarea: " . $stmt->error;
}

// Cerrar la consulta y la conexión
$stmt->close();
$conn->close();
?>
