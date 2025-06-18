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
    <title>Roles - Taskealo</title>
    <link rel="stylesheet" href="../../src/styles/stylesA.css">
    <style>
        /* Estilo para los botones */
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
    </style>
</head>

<body>
    <?php include '../sidebar_administrador.php'; ?>
    <div class='content'>
        <h1>Roles</h1>

        <h2>Agregar Rol</h2>
        <form action="agregar_rol.php" method="POST">
            <label for="nombre_rol">Nombre del Rol:</label>
            <input type="text" id="nombre_rol" name="nombre_rol" required><br><br>
            <button type="submit">Agregar Rol</button>
        </form>

        <h2>Roles Registrados</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre del Rol</th>
                <th>Acciones</th>
            </tr>
            <?php

            include '../../src/conexion_escritura.php';

            $result = $conn->query("SELECT * FROM ROLES");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['IDRol']}</td><td>{$row['NombreRol']}</td>
                    <td>
                        <a href='editar_rol.php?id={$row['IDRol']}' class='boton-editar'>Editar</a>
                        <form action='eliminar_rol.php' method='POST' style='display:inline;'>
                            <input type='hidden' name='id' value='{$row['IDRol']}'>
                            <button type='submit' class='boton-eliminar' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este rol?\");'>Eliminar</button>
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
