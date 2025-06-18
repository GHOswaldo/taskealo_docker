<?php
session_start();
if (!isset($_SESSION['IDUsuario']) || $_SESSION['Rol'] != 3) {
    header("Location: ../../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materias - Taskealo</title>
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
    </style>
</head>
<body>
    <?php include '../sidebar_administrador.php'; ?>
    <div class='content'>
        <h1>Materias</h1>

        <h2>Agregar Materia</h2>
        <form action="agregar_materia.php" method="POST">
            <label for="materia">Nombre de la Materia:</label>
            <input type="text" id="materia" name="materia" required><br><br>
            <button type="submit">Agregar Materia</button>
        </form>

        <h2>Materias Registradas</h2>
        <table>
            <tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr>
            <?php
            
            include '../../src/conexion_lectura.php';

            $result = $conn->query("SELECT * FROM MATERIAS");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['IDMateria']}</td><td>{$row['Nombre']}</td>
                    <td>
                        <a href='editar_materia.php?id={$row['IDMateria']}' class='boton-editar'>Editar</a>
                        <form action='eliminar_materia.php' method='POST' style='display:inline;'>
                            <input type='hidden' name='id' value='{$row['IDMateria']}'>
                            <button type='submit' class='boton-eliminar' onclick='return confirm(\"¿Estás seguro de que quieres eliminar esta materia?\");'>Eliminar</button>
                        </form>
                    </td>
                </tr>";
            }
            $conn->close();
            ?>
        </table>
    </div>
</body>
</html>
