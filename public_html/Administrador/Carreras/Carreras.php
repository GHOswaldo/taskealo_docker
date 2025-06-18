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
    <title>Carreras - Taskealo</title>
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
        <h1>Carreras</h1>

        <h2>Agregar Carrera</h2>
        <form action="agregar_carrera.php" method="POST">
            <label for="nombre_carrera">Nombre de la Carrera:</label>
            <input type="text" id="nombre_carrera" name="nombre_carrera" required><br><br>
            <button type="submit">Agregar Carrera</button>
        </form>

        <h2>Carreras Registradas</h2>
        <table>
            <tr><th>ID</th><th>Nombre de la Carrera</th><th>Acciones</th></tr>
            <?php
            
            include '../../src/conexion_escritura.php';

            // Consulta para obtener carreras
            $result = $conn->query("SELECT * FROM CARRERAS");

            // Verificar si la consulta fue exitosa
            if ($result === false) {
                die("Error en la consulta: " . $conn->error);
            }

            // Procesar resultados
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['IDCarrera']}</td>
                    <td>{$row['NombreCarrera']}</td>
                    <td>
                        <a href='editar_carrera.php?id={$row['IDCarrera']}' class='boton-editar'>Editar</a>
                        <form action='eliminar_carrera.php' method='POST' style='display:inline;'>
                            <input type='hidden' name='id' value='{$row['IDCarrera']}'>
                            <button type='submit' class='boton-eliminar'>Eliminar</button>
                        </form>
                    </td>
                </tr>";
            }

            // Cerrar conexión
            $conn->close();
            ?>
        </table>
    </div>
</body>
</html>
