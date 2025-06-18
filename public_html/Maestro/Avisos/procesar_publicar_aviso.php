<?php
// Iniciar sesión al principio del archivo
session_start();

// Verificar que el usuario haya iniciado sesión y tenga el rol de maestro (Rol = 2)
if (!isset($_SESSION['IDUsuario']) || $_SESSION['Rol'] != 2) {
    header("Location: ../../login.php");
    exit();
}

// Incluir el archivo de conexión para escritura
include '../../src/conexion_escritura.php';

// Verificar el método de la solicitud
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger y sanitizar datos del formulario
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $idGrupo = (int) $_POST['grupo'];
    $idMaestro = $_SESSION['IDUsuario'];  // ID del maestro que publica el aviso

    // Validar que los campos no estén vacíos
    if (empty($titulo) || empty($descripcion) || empty($idGrupo)) {
        die("Todos los campos son obligatorios.");
    }

    // Preparar la consulta para insertar el aviso en la base de datos
    $sql = "INSERT INTO AVISOS (Titulo, Descripcion, IDMaestro, IDGrupo) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Verificar si la preparación del statement fue exitosa
    if ($stmt === false) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    // Vincular los parámetros a la consulta preparada
    $stmt->bind_param('ssii', $titulo, $descripcion, $idMaestro, $idGrupo);

    // Ejecutar la consulta y verificar el resultado
    if ($stmt->execute()) {
        // Redirigir de vuelta a la página de listar avisos con un mensaje de éxito
        header("Location: listar_avisos_maestro.php?mensaje=publicado");
        exit();
    } else {
        // Mostrar error si la inserción falla
        echo "Error al publicar el aviso: " . htmlspecialchars($stmt->error);
    }

    // Cerrar el statement
    $stmt->close();
}

// Cerrar la conexión
$conn->close();
?>
