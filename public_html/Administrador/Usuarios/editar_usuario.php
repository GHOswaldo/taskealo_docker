<?php
session_start();
if (!isset($_SESSION['IDUsuario']) || $_SESSION['Rol'] != 3) {
    header("Location: login.html");
    exit();
}

include '../../src/conexion_escritura.php';

// Asegúrate de que el ID es un entero
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Consulta para obtener el usuario
$sql = "SELECT * FROM USUARIOS WHERE IDUsuario = $id";
$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

$usuario = $result->fetch_assoc();

if (!$usuario) {
    die("Usuario no encontrado.");
}

// Obtener detalles del alumno
$detalles_sql = "SELECT * FROM DETALLES_ALUMNOS WHERE IDUsuario = $id";
$detalles_result = $conn->query($detalles_sql);

$detalles = [];
if ($detalles_result) {
    $detalles = $detalles_result->fetch_assoc();
}

$matricula = !empty($detalles['Matricula']) ? $detalles['Matricula'] : null;
$carrera_asignada = !empty($detalles['IDCarrera']) ? $detalles['IDCarrera'] : null;

// Consulta para obtener la lista de carreras
$carreras_sql = "SELECT IDCarrera, NombreCarrera FROM CARRERAS";
$carreras_result = $conn->query($carreras_sql);

$carreras = [];
if ($carreras_result) {
    while ($row = $carreras_result->fetch_assoc()) {
        $carreras[] = $row;
    }
}

// Consulta para obtener la lista de roles
$roles_sql = "SELECT IDRol, NombreRol FROM ROLES";
$roles_result = $conn->query($roles_sql);

$roles = [];
if ($roles_result) {
    while ($row = $roles_result->fetch_assoc()) {
        $roles[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - Taskealo</title>
    <link rel="stylesheet" href="../../src/styles/stylesA.css">
    <style>
        .form-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-column {
            flex: 1;
            min-width: 200px;
        }

        .form-column label,
        .form-column input,
        .form-column select {
            display: block;
            width: 100%;
            margin-bottom: 15px;
        }

        .button-container {
            display: flex;
            justify-content: flex-start;
            gap: 10px;
        }
    </style>
    <script>
        function mostrarOpcionesAlumno() {
            var rol = document.getElementById('rol').value;
            var carreraDiv = document.getElementById('divCarrera');
            var matriculaDiv = document.getElementById('divMatricula');
            if (rol == '1') { // Rol Alumno
                carreraDiv.style.display = 'block';
                matriculaDiv.style.display = 'block';
            } else {
                carreraDiv.style.display = 'none';
                matriculaDiv.style.display = 'none';
            }
        }

        window.onload = function () {
            mostrarOpcionesAlumno(); // Mostrar u ocultar los campos al cargar la página
        };
    </script>
</head>

<body>
    <?php include '../sidebar_administrador.php'; ?>
    <div class='content'>
        <h1>Editar Usuario</h1>
        <form action="procesar_editar_usuario.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuario['IDUsuario']); ?>">

            <div class="form-container">
                <!-- Columna 1 -->
                <div class="form-column">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['Nombre']); ?>" required>

                    <label for="curp">CURP:</label>
                    <input type="text" id="curp" name="curp" value="<?php echo htmlspecialchars($usuario['CURP']); ?>" required maxlength="18" pattern="[A-Z0-9]{18}">

                    <label for="rol">Rol:</label>
                    <select id="rol" name="rol" required onchange="mostrarOpcionesAlumno()">
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?php echo htmlspecialchars($rol['IDRol']); ?>" <?php echo ($usuario['IDRol'] == $rol['IDRol']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($rol['NombreRol']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Columna 2 -->
                <div class="form-column">
                    <label for="usuario">Correo/Usuario:</label>
                    <input type="email" id="usuario" name="usuario" value="<?php echo htmlspecialchars($usuario['Usuario']); ?>" required>

                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" placeholder="Ingresa la nueva contraseña" required>

                    <div class="button-container">
                        <button type="submit" id="submitButton" disabled>Actualizar Usuario</button>
                        <a href="Usuarios.php">
                            <button type="button">Volver a Usuarios</button>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const submitButton = document.getElementById('submitButton');

            // Deshabilitar el botón inicialmente
            submitButton.disabled = true;

            // Función para habilitar o deshabilitar el botón
            function validarPassword() {
                const password = passwordInput.value.trim();
                // Habilitar el botón solo si la contraseña no está vacía
                submitButton.disabled = password.length === 0;
            }

            // Escuchar cambios en el campo de contraseña
            passwordInput.addEventListener('input', validarPassword);
        });
    </script>
</body>

</html>
