<?php

// Database connection parameters
$host = 'db.ncugtjhbcjfuznusrncv.supabase.co';
$dbname = 'postgres';
$user = 'coralina-user';
$password = 'C0r4l1n@_2024**H';

// Connect to PostgreSQL
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");
if (!$conn) {
    die("Error in connection: " . pg_last_error($conn));
}

// Set the datestyle to match the CSV date format
pg_query($conn, "SET datestyle = 'DMY';");

// Open the CSV file
$csvFile = fopen('data/Historico-Lectuas-Pozos-Concesionados.csv', 'r');
if (!$csvFile) {
    die("Could not open the CSV file.");
}

// Skip the header row
fgetcsv($csvFile, 1000, ",", '"', "\\");

// Prepare the SQL statement
$sql = "INSERT INTO historico_lecturas_pozos (id_historico, id_pozo, id_tipo, nombre_pozo, tipo_pozo, cota, fech_ini, h, m, med_ini, fech_fin, fech_fin_h, fech_fin_m, med_fin, hor_ini_dec, hor_fin_dec, dif_tipo, dif_vol, tpo, q, prc_q, vol_promedio_dia, temp, c_e, sal, tds, nivel, nd_mar, ne, nr, nd) 
        VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16, $17, $18, $19, $20, $21, $22, $23, $24, $25, $26, $27, $28, $29, $30, $31)";

// Read each row of the CSV file
while (($row = fgetcsv($csvFile, 1000, ",", '"', "\\")) !== FALSE) {
    // Convert date fields to 'YYYY-MM-DD' format
    $row[6] = date('Y-m-d', strtotime($row[6])); // fech_ini
    $row[10] = date('Y-m-d', strtotime($row[10])); // fech_fin
    $row[18] = date('Y-m-d', strtotime($row[18])); // tpo

    // Insert the row into the database
    $result = pg_query_params($conn, $sql, $row);
    if (!$result) {
        echo "Error in query: " . pg_last_error($conn) . "\n";
    }
}

// Close the CSV file
fclose($csvFile);

// Close the database connection
pg_close($conn);

echo "Data successfully loaded into the database.\n";
?>