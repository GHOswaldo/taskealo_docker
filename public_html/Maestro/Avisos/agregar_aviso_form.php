<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Agregar Aviso - Taskealo</title>
    <link rel="stylesheet" href="../../src/styles/stylesA.css">
</head>
<body>
    <?php include '../sidebar_maestro.php'; ?> <!-- Incluir la barra lateral correctamente -->
    <div class='content'> <!-- Contenido principal -->
        <h1>Agregar Aviso</h1>
        <form action="agregar_aviso.php" method="POST">
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" required><br><br>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea><br><br>

            <label for="grupo">Seleccionar Grupo:</label>
            <select id="grupo" name="grupo" required>
                <option value="">Seleccione un Grupo</option>
                <?php
                // Incluir el archivo de conexión
                include '../../src/conexion_escritura.php';

                // Verificar si hay un error de conexión (opcional, si ya se maneja en el archivo de conexión)
                if ($conn->connect_error) {
                    die("Error de conexión: " . $conn->connect_error);
                }

                // Obtener la lista de grupos
                $sql = "SELECT IDGrupo, NombreGrupo FROM GRUPOS";
                $result = $conn->query($sql);

                // Generar las opciones del select con los grupos
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['IDGrupo'] . "'>" . $row['NombreGrupo'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No hay grupos disponibles</option>";
                }

                // Cerrar la conexión
                $conn->close();
                ?>
            </select><br><br>

            <button type="submit">Agregar Aviso</button>
        </form>
    </div>
</body>
</html>
