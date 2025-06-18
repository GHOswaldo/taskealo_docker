<?php
// Datos de conexión para la base de datos maestra y esclavas
$masterHost = 'db_master';
$slaveHosts = ['db_slave1', 'db_slave2'];  // Agregar más si tienes más esclavos
$username = 'root';
$password = '12345';
$dbname = 'Taskealo';

// Función para conectarse a la base de datos
function connectToDB($host, $username, $password, $dbname) {
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    return $conn;
}

// Procesar solicitud de inserción
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insert'])) {
    $data = $_POST['data'];  // Datos a insertar

    // Conectar a la base de datos maestra
    $conn = connectToDB($masterHost, $username, $password, $dbname);

    // Insertar datos en la base maestra
    $sql = "INSERT INTO example_table (data) VALUES ('$data')";
    if ($conn->query($sql) === TRUE) {
        echo "Datos insertados correctamente en la base maestra.";
    } else {
        echo "Error al insertar datos: " . $conn->error;
    }

    $conn->close();
}

// Procesar solicitud de consulta
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['consult'])) {
    $randomSlaveHost = $slaveHosts[array_rand($slaveHosts)];  // Seleccionar aleatoriamente un esclavo

    // Conectar a la base de datos esclava
    $conn = connectToDB($randomSlaveHost, $username, $password, $dbname);

    // Consultar datos en la base esclava
    $sql = "SELECT * FROM example_table";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Agregar el nombre del esclavo a los resultados
            $row['source_slave'] = $randomSlaveHost;  // Añadir la fuente de datos

            // Imprimir resultados
            echo "ID: " . $row['id'] . " - Data: " . $row['data'] . " - Source Slave: " . $row['source_slave'] . "<br>";
        }
    } else {
        echo "No se encontraron resultados.";
    }

    $conn->close();
}
?>
