<?php

if($_SERVER['HTTP_HOST'] === 'localhost'){
    $db_host = 'localhost';      
    $db_username = 'root';      
    $db_password = '';         
    $db_name = 'k5news_db';
}else{
    $db_host = 'localhost';      
    $db_username = 'root';      
    $db_password = '';         
    $db_name = 'k5news_db';
}

$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

function closeConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

// Function to safely escape strings
function sanitize($conn, $str) {
    return $conn->real_escape_string(htmlspecialchars(trim($str)));
}

// Function to execute queries with PDO
function executeQuery($pdo, $sql, $params = []) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $e) {
        throw new Exception('Database error: ' . $e->getMessage());
    }
}
?>