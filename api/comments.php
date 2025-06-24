<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Get comment ID if present
$commentId = null;
if (isset($pathParts[2]) && is_numeric($pathParts[2])) {
    $commentId = (int)$pathParts[2];
}

switch ($method) {
    case 'GET':
        if ($commentId) {
            getComment($commentId);
        } else {
            getComments();
        }
        break;
    case 'POST':
        createComment();
        break;
    case 'PUT':
        if ($commentId) {
            updateComment($commentId);
        } else {
            sendError('Comment ID required for update', 400);
        }
        break;
    case 'DELETE':
        if ($commentId) {
            deleteComment($commentId);
        } else {
            sendError('Comment ID required for deletion', 400);
        }
        break;
    default:
        sendError('Method not allowed', 405);
}

function getComments() {
    global $conn;
    
    $articleId = isset($_GET['article_id']) ? (int)$_GET['article_id'] : null;
    $approved = isset($_GET['approved']) ? (bool)$_GET['approved'] : true;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    
    $offset = ($page - 1) * $limit;
    
    $whereConditions = ["c.is_approved = " . ($approved ? '1' : '0')];
    
    if ($articleId) {
        $whereConditions[] = "c.article_id = $articleId";
    }
    
    $whereClause = implode(' AND ', $whereConditions);
    
    $sql = "SELECT c.*, a.headline as article_headline, a.slug as article_slug
            FROM comments c
            LEFT JOIN articles a ON c.article_id = a.id
            WHERE $whereClause
            ORDER BY c.created_at DESC
            LIMIT $limit OFFSET $offset";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        sendError('Database error: ' . $conn->error, 500);
    }
    
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'comment' => $row['comment'],
            'is_approved' => (bool)$row['is_approved'],
            'created_at' => $row['created_at'],
            'article' => [
                'headline' => $row['article_headline'],
                'slug' => $row['article_slug']
            ]
        ];
    }
    
    // Get total count for pagination
    $countSql = "SELECT COUNT(*) as total FROM comments c WHERE $whereClause";
    $countResult = $conn->query($countSql);
    $total = $countResult->fetch_assoc()['total'];
    
    sendResponse([
        'comments' => $comments,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => (int)$total,
            'total_pages' => ceil($total / $limit)
        ]
    ]);
}

function getComment($id) {
    global $conn;
    
    $sql = "SELECT c.*, a.headline as article_headline, a.slug as article_slug
            FROM comments c
            LEFT JOIN articles a ON c.article_id = a.id
            WHERE c.id = $id";
    
    $result = $conn->query($sql);
    
    if (!$result || $result->num_rows === 0) {
        sendError('Comment not found', 404);
    }
    
    $comment = $result->fetch_assoc();
    
    sendResponse([
        'id' => (int)$comment['id'],
        'article_id' => (int)$comment['article_id'],
        'name' => $comment['name'],
        'email' => $comment['email'],
        'comment' => $comment['comment'],
        'is_approved' => (bool)$comment['is_approved'],
        'created_at' => $comment['created_at'],
        'article' => [
            'headline' => $comment['article_headline'],
            'slug' => $comment['article_slug']
        ]
    ]);
}

function createComment() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!validateRequired($input, ['article_id', 'name', 'email', 'comment'])) {
        sendError('Article ID, name, email, and comment are required');
    }
    
    $article_id = (int)$input['article_id'];
    $name = sanitize($conn, $input['name']);
    $email = sanitize($conn, $input['email']);
    $comment = sanitize($conn, $input['comment']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendError('Invalid email format');
    }
    
    // Check if article exists
    $articleCheck = $conn->query("SELECT id FROM articles WHERE id = $article_id AND status = 'published'");
    if ($articleCheck->num_rows === 0) {
        sendError('Article not found or not published');
    }
    
    $sql = "INSERT INTO comments (article_id, name, email, comment) VALUES ($article_id, '$name', '$email', '$comment')";
    
    if ($conn->query($sql)) {
        $commentId = $conn->insert_id;
        sendResponse(['message' => 'Comment submitted successfully. It will be reviewed before publication.', 'id' => $commentId], 201);
    } else {
        sendError('Failed to submit comment: ' . $conn->error, 500);
    }
}

function updateComment($id) {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $name = isset($input['name']) ? sanitize($conn, $input['name']) : '';
    $email = isset($input['email']) ? sanitize($conn, $input['email']) : '';
    $comment = isset($input['comment']) ? sanitize($conn, $input['comment']) : '';
    $is_approved = isset($input['is_approved']) ? (bool)$input['is_approved'] : null;
    
    $updateFields = [];
    
    if ($name) {
        $updateFields[] = "name = '$name'";
    }
    
    if ($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendError('Invalid email format');
        }
        $updateFields[] = "email = '$email'";
    }
    
    if ($comment) {
        $updateFields[] = "comment = '$comment'";
    }
    
    if ($is_approved !== null) {
        $updateFields[] = "is_approved = " . ($is_approved ? '1' : '0');
    }
    
    if (empty($updateFields)) {
        sendError('No fields to update');
    }
    
    $sql = "UPDATE comments SET " . implode(', ', $updateFields) . " WHERE id = $id";
    
    if ($conn->query($sql)) {
        sendResponse(['message' => 'Comment updated successfully']);
    } else {
        sendError('Failed to update comment: ' . $conn->error, 500);
    }
}

function deleteComment($id) {
    global $conn;
    
    $sql = "DELETE FROM comments WHERE id = $id";
    
    if ($conn->query($sql)) {
        sendResponse(['message' => 'Comment deleted successfully']);
    } else {
        sendError('Failed to delete comment: ' . $conn->error, 500);
    }
}
?> 