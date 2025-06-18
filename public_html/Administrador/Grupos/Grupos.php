<?php

include '../../src/conexion_lectura.php';

// Obtener lista de maestros
$sqlMaestros = "SELECT IDUsuario, Nombre FROM USUARIOS WHERE IDRol = 2";
$resultMaestros = $conn->query($sqlMaestros);

// Obtener lista de alumnos
$sqlAlumnos = "SELECT IDUsuario, Nombre FROM USUARIOS WHERE IDRol = 1";
$resultAlumnos = $conn->query($sqlAlumnos);

// Obtener grupos existentes
$sqlGrupos = "
    SELECT G.IDGrupo, G.NombreGrupo, 
           (SELECT U.Nombre FROM USUARIOS U 
            JOIN GRUPOS_USUARIOS GU ON U.IDUsuario = GU.IDUsuario 
            WHERE GU.IDGrupo = G.IDGrupo AND U.IDRol = 2 LIMIT 1) AS NombreMaestro,
           GROUP_CONCAT(A.Nombre SEPARATOR ', ') AS Alumnos
    FROM GRUPOS G
    LEFT JOIN GRUPOS_USUARIOS GU ON G.IDGrupo = GU.IDGrupo
    LEFT JOIN USUARIOS A ON GU.IDUsuario = A.IDUsuario AND A.IDRol = 1
    GROUP BY G.IDGrupo
";
$resultGrupos = $conn->query($sqlGrupos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Grupos - Taskealo</title>
    
    <link rel="stylesheet" href="../../src/styles/stylesA.css">
    <style>
        /* Estilo para los botones */
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

        .boton-gestionar {
            background-color: #007bff; /* Azul */
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .boton-gestionar:hover {
            background-color: #0056b3; /* Azul más oscuro al pasar el ratón */
        }
    </style>
</head>
<body>
    <!-- Updated sidebar path -->
    <?php include '../sidebar_administrador.php'; ?>

    <div class="content">
        <h1>Crear Grupo</h1>
        <form action="crear_grupo.php" method="POST">
            <label for="nombre_grupo">Nombre del Grupo:</label>
            <input type="text" id="nombre_grupo" name="nombre_grupo" required><br><br>

            <label for="maestro">Seleccionar Maestro:</label>
            <select id="maestro" name="maestro" required>
                <?php
                while ($row = $resultMaestros->fetch_assoc()) {
                    echo "<option value='" . $row['IDUsuario'] . "'>" . $row['Nombre'] . "</option>";
                }
                ?>
            </select><br><br>

            <h2>Seleccionar Alumnos:</h2>
            <?php
            while ($row = $resultAlumnos->fetch_assoc()) {
                echo "<input type='checkbox' name='alumnos[]' value='" . $row['IDUsuario'] . "'> " . $row['Nombre'] . "<br>";
            }
            ?><br>

            <button type="submit">Crear Grupo</button>
        </form>

        <h2>Grupos Existentes</h2>
        <table class="tabla-grupos">
            <thead>
                <tr>
                    <th>Nombre del Grupo</th>
                    <th>Maestro</th>
                    <th>Alumnos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $resultGrupos->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['NombreGrupo'] . "</td>";
                    echo "<td>" . ($row['NombreMaestro'] ? $row['NombreMaestro'] : 'Sin maestro') . "</td>";
                    echo "<td>" . ($row['Alumnos'] ? $row['Alumnos'] : 'Sin alumnos') . "</td>";
                    echo "<td>";
                    // Botón para eliminar el grupo
                    echo "<form action='eliminar_grupo.php' method='POST' style='display:inline;'>
                        <input type='hidden' name='id_grupo' value='" . $row['IDGrupo'] . "'>
                        <button type='submit' class='boton-eliminar' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este grupo?\");'>Eliminar Grupo</button>
                    </form>";

                    // Botón para gestionar alumnos en el grupo
                    echo "<form action='gestionar_alumnos_grupo.php' method='GET' style='display:inline;'>
                        <input type='hidden' name='id_grupo' value='" . $row['IDGrupo'] . "'>
                        <button type='submit' class='boton-gestionar'>Gestionar Grupo</button>
                    </form>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
// Cerrar conexión
$conn->close();
?>
