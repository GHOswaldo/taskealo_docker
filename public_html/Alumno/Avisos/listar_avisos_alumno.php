<?php
session_start(); // Asegúrate de que se llama antes de cualquier salida

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['IDUsuario'])) {
    header("Location: ../../login.php");
    exit();
}

include '../../src/conexion_escritura.php';

// Obtener el ID del usuario logueado
$idUsuario = $_SESSION['IDUsuario']; // ID del usuario actual

// Obtener todos los ID de grupos del usuario
$sqlGrupos = "SELECT G.IDGrupo
              FROM GRUPOS_USUARIOS GU
              JOIN GRUPOS G ON GU.IDGrupo = G.IDGrupo
              WHERE GU.IDUsuario = ?";

$stmtGrupos = $conn->prepare($sqlGrupos);
$stmtGrupos->bind_param("i", $idUsuario);
$stmtGrupos->execute();
$resultGrupos = $stmtGrupos->get_result();

$grupoIDs = [];
while ($row = $resultGrupos->fetch_assoc()) {
    $grupoIDs[] = $row['IDGrupo'];
}

$stmtGrupos->close(); // Cerrar el statement

// Si el usuario no está en ningún grupo, termina la ejecución
if (empty($grupoIDs)) {
    echo "<p>No tienes grupos asignados.</p>";
    exit();
}

// Convertir el array de IDGrupo a una cadena separada por comas para usar en la consulta
$grupoIDsStr = implode(',', $grupoIDs);

// Obtener la lista de avisos de todos los grupos a los que pertenece el alumno
$sqlAvisos = "SELECT A.Titulo, A.Descripcion, G.NombreGrupo
              FROM AVISOS A
              JOIN GRUPOS G ON A.IDGrupo = G.IDGrupo
              WHERE A.IDGrupo IN ($grupoIDsStr)"; // Filtrar por todos los grupos

$stmtAvisos = $conn->prepare($sqlAvisos);
$stmtAvisos->execute();
$resultAvisos = $stmtAvisos->get_result();
?>

<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Lista de Avisos - Taskealo</title>
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
    </style>
</head>
<body>
    <?php include '../sidebar_alumno.php'; ?> 
    <div class="content">
        <h1>Lista de Avisos</h1>

        <?php
        if ($resultAvisos->num_rows > 0) {
            echo "<table>
                <tr>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Grupo</th>
                </tr>";
            
            // Mostrar todos los avisos
            while ($row = $resultAvisos->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['Titulo']}</td>
                    <td>{$row['Descripcion']}</td>
                    <td>{$row['NombreGrupo']}</td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No hay avisos registrados para tus grupos.</p>";
        }

        // Cerrar el statement y la conexión
        $stmtAvisos->close();
        $conn->close();
        ?>
    </div>
</body>
</html>
