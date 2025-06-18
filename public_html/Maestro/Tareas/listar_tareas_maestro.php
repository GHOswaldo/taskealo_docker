<?php
// Iniciar sesión al principio del archivo
session_start();

// Verificar que el usuario esté logueado como maestro
if (!isset($_SESSION['IDUsuario'])) {
    die("Acceso denegado. Por favor, inicia sesión.");
}

// Incluir archivo de conexión a la base de datos
include '../../src/conexion_lectura.php';  // Asegúrate de que la ruta sea correcta

// Obtener el ID del maestro desde la sesión
$idMaestro = $_SESSION['IDUsuario'];

?>

<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Lista de Tareas - Taskealo</title>
    <link rel="stylesheet" href="../../src/styles/stylesA.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .boton-eliminar {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .boton-eliminar:hover {
            background-color: #c82333;
        }

        .boton-editar {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .boton-editar:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <?php include '../sidebar_maestro.php'; ?> <!-- Barra lateral -->
    <div class="content">
        <h1>Lista de Tareas</h1>

        <?php
        // Conexión a la base de datos
        $conn = new mysqli('db_master', 'root', '12345', 'taskealo');
        if ($conn->connect_error) {
            die("Error de conexión: " . $conn->connect_error);
        }

        // Obtener las tareas asignadas por el maestro (basado en el ID de usuario del maestro)
        $sql = "SELECT T.IDTarea, T.Titulo, T.Descripcion, T.FechaCreacion, T.FechaLimite, 
                       M.Nombre AS NombreMateria, G.NombreGrupo 
                FROM TAREAS T
                LEFT JOIN MATERIAS M ON T.IDMateria = M.IDMateria
                LEFT JOIN GRUPOS G ON T.IDGrupo = G.IDGrupo
                WHERE T.IDAlumno = ?";  // Filtrar por IDAlumno (IDUsuario del maestro)
        
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $conn->error);
        }

        $stmt->bind_param("i", $idMaestro); // Vincular el parámetro
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Fecha de Creación</th>
                    <th>Fecha Límite</th>
                    <th>Materia</th>
                    <th>Grupo</th>
                    <th>Acciones</th>
                </tr>";
            
            // Mostrar las tareas del maestro
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['IDTarea']}</td>
                    <td>{$row['Titulo']}</td>
                    <td>{$row['Descripcion']}</td>
                    <td>{$row['FechaCreacion']}</td>
                    <td>{$row['FechaLimite']}</td>
                    <td>{$row['NombreMateria']}</td>
                    <td>{$row['NombreGrupo']}</td>
                    <td>
                        <a href='editar_tarea.php?id={$row['IDTarea']}' class='boton-editar'>Editar</a>
                        <form action='eliminar_tarea.php' method='POST' style='display:inline;'>
                            <input type='hidden' name='id' value='{$row['IDTarea']}'>
                            <button type='submit' class='boton-eliminar' onclick='return confirm(\"¿Estás seguro de que quieres eliminar esta tarea?\");'>Eliminar</button>
                        </form>
                    </td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No hay tareas registradas.</p>";
        }

        // Cerrar la conexión
        $stmt->close();
        $conn->close();
        ?>
    </div>
</body>
</html>
