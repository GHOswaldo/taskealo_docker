<?php
// Iniciar sesión para obtener el ID del usuario logueado
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['IDUsuario'])) {
    die("Acceso denegado. Por favor, inicia sesión.");
}

// Incluir el archivo de conexión
include '../../src/conexion_escritura.php';

// Recoger datos del formulario
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$grupo = $_POST['grupo']; // ID del grupo seleccionado
$alumno_id = $_SESSION['IDUsuario']; // Obtener el ID del usuario logueado

// Validar que los campos obligatorios no estén vacíos
if (empty($titulo) || empty($descripcion) || empty($grupo)) {
    die("Todos los campos son obligatorios.");
}

// Insertar en la tabla AVISOS usando una consulta preparada
$sql = "INSERT INTO AVISOS (Titulo, Descripcion, IDMaestro, IDGrupo) 
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}

// Vincular los parámetros a la consulta
$stmt->bind_param('ssii', $titulo, $descripcion, $alumno_id, $grupo);

// Ejecutar la consulta y verificar si fue exitosa
if ($stmt->execute()) {
    // Redirigir de vuelta a la página de listar avisos
    header("Location: listar_avisos_maestro.php");
    exit();
} else {
    echo "Error al agregar el aviso: " . $stmt->error;
}

// Cerrar el statement y la conexión
$stmt->close();
$conn->close();
?>
