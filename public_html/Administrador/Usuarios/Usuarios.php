<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Taskealo</title>
    <link rel="stylesheet" href="../../src/styles/stylesA.css">

    <style>
        /* Estilo para los botones */
        .boton-editar {
            background-color: #28a745; /* Verde */
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none; /* Sin subrayado para enlaces */
        }

        .boton-editar:hover {
            background-color: #218838; /* Verde más oscuro al pasar el ratón */
        }

        .boton-eliminar {
            background-color: #dc3545; /* Rojo */
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .boton-eliminar:hover {
            background-color: #c82333; /* Rojo más oscuro al pasar el ratón */
        }

        .rol-section {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
        }
    </style>
    <script>
        function mostrarCamposAlumno() {
            var rol = document.getElementById('rol').value;
            var divMatricula = document.getElementById('divMatricula');
            var divCarrera = document.getElementById('divCarrera');
            var matricula = document.getElementById('matricula');
            var carrera = document.getElementById('carrera');

            if (rol == '1') { // Rol Alumno
                divMatricula.style.display = 'block';
                divCarrera.style.display = 'block';
                matricula.setAttribute('required', 'required');
                carrera.setAttribute('required', 'required');
            } else {
                divMatricula.style.display = 'none';
                divCarrera.style.display = 'none';
                matricula.removeAttribute('required');
                carrera.removeAttribute('required');
            }
        }
    </script>
</head>

<body>
    <?php include '../sidebar_administrador.php'; ?>
    <div class='content'>
        <h1>Usuarios</h1>
        <h2>Agregar Usuario</h2>
        <form action="agregar_usuario.php" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required><br><br>

            <label for="usuario">Correo/Usuario:</label>
            <input type="email" id="usuario" name="usuario" required><br><br>

            <label for="password">Contraseña:</label>
            <input type="text" id="password" name="password" required><br><br>

            <label for="curp">CURP:</label>
            <input type="text" id="curp" name="curp" required maxlength="18" pattern="[A-Z0-9]{18}"><br><br>

            <label for="rol">Rol:</label>
            <select id="rol" name="rol" required onchange="mostrarCamposAlumno()">
                <option value="0">Seleccione un Rol</option>
                <?php
                // Conexión a la base de datos
                include '../../src/conexion_lectura.php';

                // Obtener roles
                $sql = "SELECT IDRol, NombreRol FROM ROLES";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['IDRol']}'>{$row['NombreRol']}</option>";
                }
                ?>
            </select><br><br>

            <div id="divMatricula" style="display:none;">
                <label for="matricula">Matrícula:</label>
                <input type="text" id="matricula" name="matricula" pattern="[A-Z0-9]{8,12}"><br><br>
            </div>

            <div id="divCarrera" style="display:none;">
                <label for="carrera">Carrera:</label>
                <select id="carrera" name="carrera">
                    <option value="">Seleccione una Carrera</option>
                    <?php
                    // Obtener carreras
                    $sqlCarreras = "SELECT IDCarrera, NombreCarrera FROM CARRERAS";
                    $resultCarreras = $conn->query($sqlCarreras);
                    while ($rowCarrera = $resultCarreras->fetch_assoc()) {
                        echo "<option value='{$rowCarrera['IDCarrera']}'>{$rowCarrera['NombreCarrera']}</option>";
                    }
                    ?>
                </select><br><br>
            </div>

            <button type="submit">Agregar Usuario</button>
        </form>

        <h2>Usuarios Registrados</h2>

        <?php
        // Obtener usuarios con información de rol
        $sqlUsuarios = "SELECT U.IDUsuario, U.Nombre, U.Usuario, U.IDRol, R.NombreRol 
                        FROM USUARIOS U 
                        JOIN ROLES R ON U.IDRol = R.IDRol";
        $resultUsuarios = $conn->query($sqlUsuarios);

        // Arreglo para almacenar usuarios separados por rol
        $usuariosPorRol = [];
        while ($rowUsuario = $resultUsuarios->fetch_assoc()) {
            $usuariosPorRol[$rowUsuario['NombreRol']][] = $rowUsuario;
        }

        // Mostrar usuarios por rol
        foreach ($usuariosPorRol as $rol => $usuarios) {
            echo "<div class='rol-section'><h3>Rol: $rol</h3>";
            echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Acciones</th>
                </tr>";
            foreach ($usuarios as $usuario) {
                echo "<tr>
                    <td>{$usuario['IDUsuario']}</td>
                    <td>{$usuario['Nombre']}</td>
                    <td>{$usuario['Usuario']}</td>
                    <td>
                        <a href='editar_usuario.php?id={$usuario['IDUsuario']}' class='boton-editar'>Editar</a>
                        <form action='eliminar_usuario.php' method='POST' style='display:inline;'>
                            <input type='hidden' name='id' value='{$usuario['IDUsuario']}'>
                            <button type='submit' class='boton-eliminar' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este usuario?\");'>Eliminar</button>
                        </form>
                    </td>
                </tr>";
            }
            echo "</table></div>";
        }

        // Cerrar la conexión
        $conn->close();
        ?>
    </div>
</body>

</html>
