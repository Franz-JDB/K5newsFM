<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Get author ID if present
$authorId = null;
if (isset($pathParts[2]) && is_numeric($pathParts[2])) {
    $authorId = (int)$pathParts[2];
}

switch ($method) {
    case 'GET':
        if ($authorId) {
            getAuthor($authorId);
        } else {
            getAuthors();
        }
        break;
    case 'POST':
        createAuthor();
        break;
    case 'PUT':
        if ($authorId) {
            updateAuthor($authorId);
        } else {
            sendError('Author ID required for update', 400);
        }
        break;
    case 'DELETE':
        if ($authorId) {
            deleteAuthor($authorId);
        } else {
            sendError('Author ID required for deletion', 400);
        }
        break;
    default:
        sendError('Method not allowed', 405);
}

function getAuthors() {
    global $conn;
    
    $sql = "SELECT * FROM authors WHERE is_active = 1 ORDER BY name ASC";
    $result = $conn->query($sql);
    
    if (!$result) {
        sendError('Database error: ' . $conn->error, 500);
    }
    
    $authors = [];
    while ($row = $result->fetch_assoc()) {
        $authors[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'bio' => $row['bio'],
            'profile_image' => $row['profile_image'],
            'twitter_handle' => $row['twitter_handle'],
            'linkedin_url' => $row['linkedin_url'],
            'created_at' => $row['created_at']
        ];
    }
    
    sendResponse(['authors' => $authors]);
}

function getAuthor($id) {
    global $conn;
    
    $sql = "SELECT * FROM authors WHERE id = $id AND is_active = 1";
    $result = $conn->query($sql);
    
    if (!$result || $result->num_rows === 0) {
        sendError('Author not found', 404);
    }
    
    $author = $result->fetch_assoc();
    
    // Get articles by this author
    $articlesSql = "SELECT a.id, a.headline, a.slug, a.excerpt, a.featured_image, a.published_at, a.view_count,
                           c.name as category_name, c.slug as category_slug
                    FROM articles a
                    LEFT JOIN categories c ON a.category_id = c.id
                    WHERE a.author_id = $id AND a.status = 'published'
                    ORDER BY a.published_at DESC
                    LIMIT 10";
    $articlesResult = $conn->query($articlesSql);
    $articles = [];
    while ($article = $articlesResult->fetch_assoc()) {
        $articles[] = $article;
    }
    
    // Get article count
    $countSql = "SELECT COUNT(*) as count FROM articles WHERE author_id = $id AND status = 'published'";
    $countResult = $conn->query($countSql);
    $articleCount = $countResult->fetch_assoc()['count'];
    
    sendResponse([
        'id' => (int)$author['id'],
        'name' => $author['name'],
        'email' => $author['email'],
        'bio' => $author['bio'],
        'profile_image' => $author['profile_image'],
        'twitter_handle' => $author['twitter_handle'],
        'linkedin_url' => $author['linkedin_url'],
        'article_count' => (int)$articleCount,
        'recent_articles' => $articles,
        'created_at' => $author['created_at']
    ]);
}

function createAuthor() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!validateRequired($input, ['name'])) {
        sendError('Author name is required');
    }
    
    $name = sanitize($conn, $input['name']);
    $email = isset($input['email']) ? sanitize($conn, $input['email']) : '';
    $bio = isset($input['bio']) ? sanitize($conn, $input['bio']) : '';
    $profile_image = isset($input['profile_image']) ? sanitize($conn, $input['profile_image']) : '';
    $twitter_handle = isset($input['twitter_handle']) ? sanitize($conn, $input['twitter_handle']) : '';
    $linkedin_url = isset($input['linkedin_url']) ? sanitize($conn, $input['linkedin_url']) : '';
    
    // Check if email already exists
    if ($email) {
        $checkSql = "SELECT id FROM authors WHERE email = '$email'";
        $checkResult = $conn->query($checkSql);
        if ($checkResult->num_rows > 0) {
            sendError('Author with this email already exists');
        }
    }
    
    $sql = "INSERT INTO authors (name, email, bio, profile_image, twitter_handle, linkedin_url) 
            VALUES ('$name', '$email', '$bio', '$profile_image', '$twitter_handle', '$linkedin_url')";
    
    if ($conn->query($sql)) {
        $authorId = $conn->insert_id;
        sendResponse(['message' => 'Author created successfully', 'id' => $authorId], 201);
    } else {
        sendError('Failed to create author: ' . $conn->error, 500);
    }
}

function updateAuthor($id) {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!validateRequired($input, ['name'])) {
        sendError('Author name is required');
    }
    
    $name = sanitize($conn, $input['name']);
    $email = isset($input['email']) ? sanitize($conn, $input['email']) : '';
    $bio = isset($input['bio']) ? sanitize($conn, $input['bio']) : '';
    $profile_image = isset($input['profile_image']) ? sanitize($conn, $input['profile_image']) : '';
    $twitter_handle = isset($input['twitter_handle']) ? sanitize($conn, $input['twitter_handle']) : '';
    $linkedin_url = isset($input['linkedin_url']) ? sanitize($conn, $input['linkedin_url']) : '';
    $is_active = isset($input['is_active']) ? (bool)$input['is_active'] : true;
    
    // Check if email already exists for different author
    if ($email) {
        $checkSql = "SELECT id FROM authors WHERE email = '$email' AND id != $id";
        $checkResult = $conn->query($checkSql);
        if ($checkResult->num_rows > 0) {
            sendError('Author with this email already exists');
        }
    }
    
    $sql = "UPDATE authors SET 
            name = '$name',
            email = '$email',
            bio = '$bio',
            profile_image = '$profile_image',
            twitter_handle = '$twitter_handle',
            linkedin_url = '$linkedin_url',
            is_active = " . ($is_active ? '1' : '0') . "
            WHERE id = $id";
    
    if ($conn->query($sql)) {
        sendResponse(['message' => 'Author updated successfully']);
    } else {
        sendError('Failed to update author: ' . $conn->error, 500);
    }
}

function deleteAuthor($id) {
    global $conn;
    
    // Check if author has articles
    $checkSql = "SELECT COUNT(*) as count FROM articles WHERE author_id = $id";
    $checkResult = $conn->query($checkSql);
    $articleCount = $checkResult->fetch_assoc()['count'];
    
    if ($articleCount > 0) {
        sendError('Cannot delete author with existing articles. Please reassign or delete the articles first.');
    }
    
    $sql = "DELETE FROM authors WHERE id = $id";
    
    if ($conn->query($sql)) {
        sendResponse(['message' => 'Author deleted successfully']);
    } else {
        sendError('Failed to delete author: ' . $conn->error, 500);
    }
}
?> 