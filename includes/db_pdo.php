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

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function closeConnection($pdo) {
    $pdo = null;
}

// Function to safely escape strings
function sanitize($pdo, $str) {
    return htmlspecialchars(trim($str));
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