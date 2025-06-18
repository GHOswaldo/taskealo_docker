<?php

include '../../src/conexion_escritura.php'; 

// Recoger datos del formulario
$nombre_grupo = $_POST['nombre_grupo'];
$id_maestro = $_POST['maestro'];
$alumnos = isset($_POST['alumnos']) ? $_POST['alumnos'] : [];

// Insertar el nuevo grupo en la tabla de grupos
$sqlGrupo = "INSERT INTO GRUPOS (NombreGrupo) VALUES ('$nombre_grupo')";
if ($conn->query($sqlGrupo) === TRUE) {
    $id_grupo = $conn->insert_id; // Obtener el ID del grupo recién creado

    // Insertar al maestro en la tabla GRUPOS_USUARIOS
    $sqlMaestro = "INSERT INTO GRUPOS_USUARIOS (IDGrupo, IDUsuario) VALUES ('$id_grupo', '$id_maestro')";
    if (!$conn->query($sqlMaestro)) {
        die("Error al insertar maestro: " . $conn->error);
    }

    // Insertar a los alumnos en la tabla GRUPOS_USUARIOS
    foreach ($alumnos as $id_alumno) {
        $sqlAlumno = "INSERT INTO GRUPOS_USUARIOS (IDGrupo, IDUsuario) VALUES ('$id_grupo', '$id_alumno')";
        if (!$conn->query($sqlAlumno)) {
            die("Error al insertar alumno: " . $conn->error);
        }
    }

    // Redirigir a la página de grupos después de crear el grupo
    header("Location: Grupos.php");
    exit(); // Asegurarse de que el script se detenga después de redirigir
} else {
    echo "Error al crear el grupo: " . $conn->error;
}

$conn->close();
?>
