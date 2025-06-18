<?php
// Conexión a la base de datos
include '../../src/conexion_escritura.php';

// Recoger datos del formulario
$nombre = $_POST['nombre'];
$usuario = $_POST['usuario'];
$password = $_POST['password']; // Sin cifrado
$curp = $_POST['curp'];
$rol = $_POST['rol'];
$matricula = isset($_POST['matricula']) ? $_POST['matricula'] : null;
$carrera = isset($_POST['carrera']) ? $_POST['carrera'] : null; // Aquí se espera que 'carrera' sea la ID

// Insertar en la tabla USUARIOS (sin cifrar la contraseña)
$sql = "INSERT INTO USUARIOS (Nombre, Usuario, Password, CURP, IDRol) 
        VALUES ('$nombre', '$usuario', '$password', '$curp', '$rol')";

if ($conn->query($sql) === TRUE) {
    $userId = $conn->insert_id; // Obtener el ID del nuevo usuario insertado

    // Si el rol es Alumno, insertar en DETALLES_ALUMNOS
    if ($rol == '1') { // Alumno
        // Cambia 'Carrera' a 'IDCarrera' en la consulta
        $detalles_sql = "INSERT INTO DETALLES_ALUMNOS (IDUsuario, Matricula, IDCarrera) 
                         VALUES ('$userId', '$matricula', '$carrera')";

        if ($conn->query($detalles_sql) !== TRUE) {
            echo "Error al insertar detalles: " . $conn->error;
        }
    }

    // Redirigir de vuelta a la página de usuarios
    header("Location: Usuarios.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
