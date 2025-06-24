<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getSubscribers();
        break;
    case 'POST':
        subscribe();
        break;
    case 'DELETE':
        unsubscribe();
        break;
    default:
        sendError('Method not allowed', 405);
}

function getSubscribers() {
    global $conn;
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $active = isset($_GET['active']) ? (bool)$_GET['active'] : true;
    
    $offset = ($page - 1) * $limit;
    
    $whereCondition = $active ? "is_active = 1" : "1=1";
    
    $sql = "SELECT * FROM subscribers WHERE $whereCondition ORDER BY subscribed_at DESC LIMIT $limit OFFSET $offset";
    $result = $conn->query($sql);
    
    if (!$result) {
        sendError('Database error: ' . $conn->error, 500);
    }
    
    $subscribers = [];
    while ($row = $result->fetch_assoc()) {
        $subscribers[] = [
            'id' => (int)$row['id'],
            'email' => $row['email'],
            'name' => $row['name'],
            'is_active' => (bool)$row['is_active'],
            'subscribed_at' => $row['subscribed_at']
        ];
    }
    
    // Get total count for pagination
    $countSql = "SELECT COUNT(*) as total FROM subscribers WHERE $whereCondition";
    $countResult = $conn->query($countSql);
    $total = $countResult->fetch_assoc()['total'];
    
    sendResponse([
        'subscribers' => $subscribers,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => (int)$total,
            'total_pages' => ceil($total / $limit)
        ]
    ]);
}

function subscribe() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!validateRequired($input, ['email'])) {
        sendError('Email is required');
    }
    
    $email = sanitize($conn, $input['email']);
    $name = isset($input['name']) ? sanitize($conn, $input['name']) : '';
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendError('Invalid email format');
    }
    
    // Check if already subscribed
    $checkSql = "SELECT id, is_active FROM subscribers WHERE email = '$email'";
    $checkResult = $conn->query($checkSql);
    
    if ($checkResult->num_rows > 0) {
        $existing = $checkResult->fetch_assoc();
        
        if ($existing['is_active']) {
            sendError('Email is already subscribed to our newsletter');
        } else {
            // Reactivate subscription
            $updateSql = "UPDATE subscribers SET is_active = 1, name = '$name' WHERE id = {$existing['id']}";
            if ($conn->query($updateSql)) {
                sendResponse(['message' => 'Subscription reactivated successfully']);
            } else {
                sendError('Failed to reactivate subscription: ' . $conn->error, 500);
            }
        }
    }
    
    // Create new subscription
    $sql = "INSERT INTO subscribers (email, name) VALUES ('$email', '$name')";
    
    if ($conn->query($sql)) {
        $subscriberId = $conn->insert_id;
        sendResponse(['message' => 'Successfully subscribed to our newsletter', 'id' => $subscriberId], 201);
    } else {
        sendError('Failed to subscribe: ' . $conn->error, 500);
    }
}

function unsubscribe() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!validateRequired($input, ['email'])) {
        sendError('Email is required');
    }
    
    $email = sanitize($conn, $input['email']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendError('Invalid email format');
    }
    
    $sql = "UPDATE subscribers SET is_active = 0 WHERE email = '$email'";
    
    if ($conn->query($sql)) {
        if ($conn->affected_rows > 0) {
            sendResponse(['message' => 'Successfully unsubscribed from our newsletter']);
        } else {
            sendError('Email not found in our subscription list');
        }
    } else {
        sendError('Failed to unsubscribe: ' . $conn->error, 500);
    }
}
?> 