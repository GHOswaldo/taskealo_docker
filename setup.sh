#!/bin/bash

# Paso 1: Acceder al directorio del proyecto
cd /c/taskealo || { echo "No se puede acceder al directorio"; exit 1; }

# Paso 2: Construir y levantar los contenedores
echo "Construyendo y levantando los contenedores..."
docker-compose up --build -d || { echo "Error al construir y levantar los contenedores"; exit 1; }

# Paso 3: Esperar un poco para que los contenedores se inicialicen completamente
echo "Esperando que los contenedores inicien..."
sleep 10

# Paso 4: Configurar el maestro (crear usuario 'replicator' y habilitar la replicación)
echo "Configurando el maestro..."

# Permitir que MariaDB/MySQL acepte conexiones desde cualquier host y crear el usuario replicador
docker exec -i taskealo-db_master-1 mysql -u root -p12345 <<EOF
CREATE USER 'replicator'@'%' IDENTIFIED BY '12345';
GRANT REPLICATION SLAVE ON *.* TO 'replicator'@'%';
FLUSH PRIVILEGES;
EOF

# Obtener el estado de la replicación en el maestro
echo "Obteniendo el estado de la replicación en el maestro..."
MASTER_STATUS=$(docker exec -i taskealo-db_master-1 mysql -u root -p12345 -e "SHOW MASTER STATUS;" | grep -v "File" | awk '{print $1, $2}')
MASTER_LOG_FILE=$(echo $MASTER_STATUS | awk '{print $1}')
MASTER_LOG_POS=$(echo $MASTER_STATUS | awk '{print $2}')
echo "Archivo binario maestro: $MASTER_LOG_FILE"
echo "Posición de registro binario maestro: $MASTER_LOG_POS"

# Paso 5: Obtener la IP del maestro
echo "Obteniendo la IP del maestro..."
MASTER_IP=$(docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' taskealo-db_master-1)
echo "IP del maestro: $MASTER_IP"

# Paso 6: Configurar el esclavo 1
echo "Configurando el esclavo 1..."
docker exec -i taskealo-db_slave1-1 mysql -u root -p12345 <<EOF
SET GLOBAL server_id = 2;
STOP SLAVE;
RESET SLAVE;
CHANGE MASTER TO MASTER_HOST='$MASTER_IP', MASTER_USER='replicator', MASTER_PASSWORD='12345', MASTER_LOG_FILE='$MASTER_LOG_FILE', MASTER_LOG_POS=$MASTER_LOG_POS;
START SLAVE;
EOF

# Paso 7: Configurar el esclavo 2
echo "Configurando el esclavo 2..."
docker exec -i taskealo-db_slave2-1 mysql -u root -p12345 <<EOF
SET GLOBAL server_id = 3;
STOP SLAVE;
RESET SLAVE;
CHANGE MASTER TO MASTER_HOST='$MASTER_IP', MASTER_USER='replicator', MASTER_PASSWORD='12345', MASTER_LOG_FILE='$MASTER_LOG_FILE', MASTER_LOG_POS=$MASTER_LOG_POS;
START SLAVE;
EOF

# Paso 8: Verificar el estado de la replicación en los esclavos
echo "Verificando el estado de la replicación en el esclavo 1..."
docker exec -i taskealo-db_slave1-1 mysql -u root -p12345 -e "SHOW SLAVE STATUS\G;" > slave1_status.txt
if grep -q "Slave_IO_Running: Yes" slave1_status.txt && grep -q "Slave_SQL_Running: Yes" slave1_status.txt; then
    echo "La replicación en el esclavo 1 está funcionando correctamente."
else
    echo "Error en la replicación del esclavo 1."
    cat slave1_status.txt
    exit 1
fi

echo "Verificando el estado de la replicación en el esclavo 2..."
docker exec -i taskealo-db_slave2-1 mysql -u root -p12345 -e "SHOW SLAVE STATUS\G;" > slave2_status.txt
if grep -q "Slave_IO_Running: Yes" slave2_status.txt && grep -q "Slave_SQL_Running: Yes" slave2_status.txt; then
    echo "La replicación en el esclavo 2 está funcionando correctamente."
else
    echo "Error en la replicación del esclavo 2."
    cat slave2_status.txt
    exit 1
fi

echo "Replicación configurada y verificada correctamente."
