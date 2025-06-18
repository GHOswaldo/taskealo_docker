<?php
session_start(); // Asegúrate de que las sesiones estén habilitadas

// Conexión a la base de datos
$conn = new mysqli('db_master', 'root', '12345', 'taskealo');
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener el ID del usuario logueado
$usuarioID = $_SESSION['IDUsuario']; // Verifica que este índice exista

// Consulta para obtener los grupos del usuario
$sqlGrupos = "SELECT IDGrupo FROM GRUPOS_USUARIOS WHERE IDUsuario = ?";
$stmtGrupos = $conn->prepare($sqlGrupos);
$stmtGrupos->bind_param('i', $usuarioID);
$stmtGrupos->execute();
$resultGrupos = $stmtGrupos->get_result();

$grupoIDs = [];
while ($row = $resultGrupos->fetch_assoc()) {
    $grupoIDs[] = $row['IDGrupo'];
}

// Si el usuario no está en ningún grupo, termina la ejecución
if (empty($grupoIDs)) {
    echo "<p>No tienes grupos asignados.</p>";
    exit();
}

// Convertir el array de IDGrupo a una cadena separada por comas para usar en la consulta
$grupoIDsStr = implode(',', $grupoIDs);

// Consulta para obtener las tareas del alumno logueado según su grupo
$sql = "SELECT T.IDTarea, T.Titulo, T.Descripcion, T.FechaCreacion, T.FechaLimite, M.Nombre AS NombreMateria
        FROM TAREAS T
        LEFT JOIN MATERIAS M ON T.IDMateria = M.IDMateria
        WHERE T.IDGrupo IN ($grupoIDsStr)"; // Filtrar por los grupos a los que pertenece el usuario

// Preparar la consulta
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Lista de Tareas - Taskealo</title>
    <link rel="stylesheet" href="../../src/styles/stylesA.css"> <!-- Asegúrate de que stylesA.css esté presente -->
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
    </style>
</head>
<body>
    <?php include '../sidebar_alumno.php'; ?> <!-- Incluir el sidebar para alumnos -->
    
    <div class="content">
        <h1>Lista de Tareas</h1>

        <?php
        if ($result->num_rows > 0) {
            echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Fecha de Creación</th>
                    <th>Fecha Límite</th>
                    <th>Materia</th>
                </tr>";
            
            // Mostrar todas las tareas
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['IDTarea']}</td>
                    <td>{$row['Titulo']}</td>
                    <td>{$row['Descripcion']}</td>
                    <td>{$row['FechaCreacion']}</td>
                    <td>{$row['FechaLimite']}</td>
                    <td>{$row['NombreMateria']}</td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No hay tareas registradas para tu grupo.</p>";
        }

        // Cerrar la conexión
        $stmt->close();
        $conn->close();
        ?>
    </div>
</body>
</html>