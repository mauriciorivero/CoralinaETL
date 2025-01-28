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

// Open the CSV file
$csvFile = fopen('data/Tipo-Pozos-Plano.csv', 'r');
if (!$csvFile) {
    die("Could not open the CSV file.");
}

// Skip the header row
fgetcsv($csvFile, 1000, ",", '"', "\\");

// Prepare the SQL statement
$sql = "INSERT INTO tipo_pozos (id_tipo, tipo_pozo) VALUES ($1, $2)";

// Read each row of the CSV file
while (($row = fgetcsv($csvFile, 1000, ",", '"', "\\")) !== FALSE) {
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