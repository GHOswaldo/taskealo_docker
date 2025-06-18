<?php
session_start(); // Iniciar sesión si no está iniciado

// Incluir el archivo de conexión
include '../../src/conexion_escritura.php';

// Verificar si el usuario ha iniciado sesión (opcional, dependiendo de tu lógica de acceso)
if (!isset($_SESSION['IDUsuario'])) {
    header("Location: ../../login.php");
    exit();
}

// Recoger datos del formulario
$id = $_POST['id'];
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$grupo = $_POST['grupo'];

// Actualizar en la tabla AVISOS
$sql = "UPDATE AVISOS SET Titulo = ?, Descripcion = ?, IDGrupo = ? WHERE IDAviso = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $titulo, $descripcion, $grupo, $id);

if ($stmt->execute()) {
    // Redirigir de vuelta a la página de listar avisos
    header("Location: listar_avisos_maestro.php");
    exit();
} else {
    echo "Error al actualizar el aviso: " . $stmt->error;
}

// Cerrar el statement y la conexión
$stmt->close();
$conn->close();
?>
