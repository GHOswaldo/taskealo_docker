<?php
// Iniciar sesión al principio del archivo
session_start();

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['IDUsuario'])) {
    die("Acceso denegado. Por favor, inicia sesión.");
}

// Incluir el archivo de conexión para lectura
include '../../src/conexion_lectura.php';

// Obtener el ID del usuario (maestro) desde la sesión
$idMaestro = $_SESSION['IDUsuario'];

// Preparar la consulta para obtener los avisos creados por el maestro
$sql = "SELECT AV.IDAviso, AV.Titulo, AV.Descripcion, G.NombreGrupo
        FROM AVISOS AV
        LEFT JOIN GRUPOS G ON AV.IDGrupo = G.IDGrupo
        WHERE AV.IDMaestro = ?";

$stmt = $conn->prepare($sql);

// Verificar si la preparación del statement fue exitosa
if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}

// Vincular el parámetro del ID del maestro
$stmt->bind_param("i", $idMaestro);

// Ejecutar la consulta y obtener los resultados
$stmt->execute();
$result = $stmt->get_result();

// Verificar si hubo un error en la ejecución de la consulta
if ($result === false) {
    die("Error en la consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Lista de Avisos - Taskealo</title>
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

        .boton-agregar {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
        }

        .boton-agregar:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include '../sidebar_maestro.php'; ?> <!-- Barra lateral -->
    <div class="content">
        <h1>Lista de Avisos</h1>

        <?php
        // Verificar si hay resultados
        if ($result->num_rows > 0) {
            echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Grupo</th>
                    <th>Acciones</th>
                </tr>";
            
            // Mostrar los avisos creados por el maestro
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . htmlspecialchars($row['IDAviso']) . "</td>
                    <td>" . htmlspecialchars($row['Titulo']) . "</td>
                    <td>" . htmlspecialchars($row['Descripcion']) . "</td>
                    <td>" . htmlspecialchars($row['NombreGrupo']) . "</td>
                    <td>
                        <a href='editar_aviso.php?id=" . urlencode($row['IDAviso']) . "' class='boton-editar'>Editar</a>
                        <form action='eliminar_aviso.php' method='POST' style='display:inline;'>
                            <input type='hidden' name='id' value='" . htmlspecialchars($row['IDAviso']) . "'>
                            <button type='submit' class='boton-eliminar' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este aviso?\");'>Eliminar</button>
                        </form>
                    </td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No hay avisos registrados.</p>";
        }

        // Cerrar el statement y la conexión
        $stmt->close();
        $conn->close();
        ?>
    </div>
</body>
</html>
