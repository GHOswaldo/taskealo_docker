<?php
session_start();

// Datos de conexión
$host = 'db_master';  // Cambia si es necesario
$username = 'root';   // Usuario MySQL
$password = '12345';  // Contraseña MySQL
$dbname = 'taskealo'; // Nombre de tu base de datos

// Obtener la clave de cifrado desde el archivo en src/
$encryption_key = require __DIR__ . '/src/encryption_key.php';

// Conectar a la base de datos
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Procesar solicitud de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Consulta para verificar el usuario y obtener la contraseña desencriptada
    $sql = "SELECT IDUsuario, Nombre, Usuario, AES_DECRYPT(Password, ?) AS Password, IDRol 
            FROM USUARIOS 
            WHERE Usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $encryption_key, $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Comparar la contraseña ingresada con la desencriptada
        if ($password === $row['Password']) {
            // Guardar información del usuario en la sesión
            $_SESSION['IDUsuario'] = $row['IDUsuario'];
            $_SESSION['Nombre'] = $row['Nombre'];
            $_SESSION['Usuario'] = $row['Usuario'];
            $_SESSION['Rol'] = $row['IDRol'];

            // Redirigir según el rol
            if ($_SESSION['Rol'] == 1) {
                header("Location: Alumno/dashboard_alumno.php");
            } elseif ($_SESSION['Rol'] == 2) {
                header("Location: Maestro/dashboard_maestro.php");
            } elseif ($_SESSION['Rol'] == 3) {  
                header("Location: Administrador/dashboard_administrador.php");
            }
            exit();
        } else {
            // Si la contraseña es incorrecta
            echo "Usuario o contraseña incorrectos.";
        }
    } else {
        // Si el usuario no existe
        echo "Usuario o contraseña incorrectos.";
    }

    $stmt->close();
}

$conn->close();
?>

