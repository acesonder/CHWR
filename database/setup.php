<?php
require_once __DIR__ . '/../config/database.php';

// Initialize database
initializeDatabase();

echo "Database initialized successfully!\n";

// Create connection to the new database
$conn = getDBConnection();

// Read and execute schema
$schema = file_get_contents(__DIR__ . '/schema.sql');

// Split by semicolon and execute each statement
$statements = array_filter(array_map('trim', explode(';', $schema)));

foreach ($statements as $statement) {
    if (!empty($statement)) {
        if ($conn->query($statement) === TRUE) {
            echo "✓ Statement executed successfully\n";
        } else {
            echo "✗ Error: " . $conn->error . "\n";
        }
    }
}

echo "\n✓ Database setup completed!\n";
echo "Default admin credentials:\n";
echo "  Username: admin\n";
echo "  Password: admin123\n";

$conn->close();
?>
