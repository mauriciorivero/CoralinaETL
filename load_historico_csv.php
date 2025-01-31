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
        VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16, $17, $18, $19, $20, $21, $22, $23, $24, $25, $26, $27, $28, $29, $30, $31)
        ON CONFLICT (id_historico) DO NOTHING";

// Read each row of the CSV file
while (($row = fgetcsv($csvFile, 1000, ",", '"', "\\")) !== FALSE) {
    // Convert date fields to 'YYYY-MM-DD' format
    $row[6] = !empty($row[6]) ? date('Y-m-d', strtotime($row[6])) : null; // fech_ini
    $row[10] = !empty($row[10]) ? date('Y-m-d', strtotime($row[10])) : null; // fech_fin
    $row[18] = !empty($row[18]) ? date('Y-m-d', strtotime($row[18])) : null; // tpo

    // Ensure integer fields are not empty
    $row[7] = !empty($row[7]) ? intval($row[7]) : null; // h
    $row[8] = !empty($row[8]) ? intval($row[8]) : null; // m
    $row[11] = !empty($row[11]) ? intval($row[11]) : null; // fech_fin_h
    $row[12] = !empty($row[12]) ? intval($row[12]) : null; // fech_fin_m

    // Ensure real fields are not empty and remove thousand separators
    $row[9] = !empty($row[9]) ? floatval(str_replace('.', '', $row[9])) : null; // med_ini
    $row[13] = !empty($row[13]) ? floatval(str_replace('.', '', $row[13])) : null; // med_fin
    $row[14] = !empty($row[14]) ? floatval(str_replace('.', '', $row[14])) : null; // hor_ini_dec
    $row[15] = !empty($row[15]) ? floatval(str_replace('.', '', $row[15])) : null; // hor_fin_dec
    $row[17] = !empty($row[17]) ? floatval(str_replace(['.', '-'], '', $row[17])) : null; // dif_vol
    $row[20] = !empty($row[20]) ? floatval(str_replace(['.', '-'], '', $row[20])) : null; // prc_q
    $row[21] = !empty($row[21]) ? floatval(str_replace('.', '', $row[21])) : null; // vol_promedio_dia
    $row[22] = !empty($row[22]) ? floatval(str_replace('.', '', $row[22])) : null; // temp
    $row[23] = !empty($row[23]) ? floatval(str_replace('.', '', $row[23])) : null; // c_e
    $row[24] = !empty($row[24]) ? floatval(str_replace('.', '', $row[24])) : null; // sal
    $row[25] = !empty($row[25]) ? floatval(str_replace('.', '', $row[25])) : null; // tds
    $row[26] = !empty($row[26]) ? floatval(str_replace('.', '', $row[26])) : null; // nivel
    $row[27] = !empty($row[27]) ? floatval(str_replace('.', '', $row[27])) : null; // nd_mar
    $row[28] = !empty($row[28]) ? floatval(str_replace('.', '', $row[28])) : null; // ne
    $row[29] = !empty($row[29]) ? floatval(str_replace('.', '', $row[29])) : null; // nr
    $row[30] = !empty($row[30]) ? floatval(str_replace('.', '', $row[30])) : null; // nd

    // Execute the query
    $result = pg_query_params($conn, $sql, $row);
    if (!$result) {
        echo "Error in query: " . pg_last_error($conn);
    }
}

// Close the CSV file
fclose($csvFile);

// Close the database connection
pg_close($conn);

echo "Data successfully loaded into the database.\n";
?>