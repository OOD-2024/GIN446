<?php
// Database connection parameters
$host = $_ENV['DB_HOST'] ?? 'db';
$user = $_ENV['DB_USER'] ?? 'user';
$pass = $_ENV['DB_PASS'] ?? 'pass';
$dbname = $_ENV['DB_NAME'] ?? 'clinic';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    
    // Set error reporting and exception mode
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test query
    $stmt = $pdo->query("SELECT 'Connection successful!' AS result");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h1>Database Connection Test</h1>";
    echo "<p>Status: ✅ Connection Successful!</p>";
    echo "<p>Message: " . htmlspecialchars($result['result']) . "</p>";
    
    // Additional system info
    echo "<h2>System Information</h2>";
    echo "<p>PHP Version: " . phpversion() . "</p>";
    echo "<p>MySQL Server Version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);

} catch(PDOException $e) {
    // Handle connection errors
    echo "<h1>Database Connection Test</h1>";
    echo "<p>Status: ❌ Connection Failed</p>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
