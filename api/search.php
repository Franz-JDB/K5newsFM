<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    sendError('Method not allowed', 405);
}

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? sanitize($conn, $_GET['category']) : '';
$author = isset($_GET['author']) ? sanitize($conn, $_GET['author']) : '';
$date_from = isset($_GET['date_from']) ? sanitize($conn, $_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize($conn, $_GET['date_to']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

if (empty($query) && empty($category) && empty($author) && empty($date_from) && empty($date_to)) {
    sendError('At least one search parameter is required');
}

$offset = ($page - 1) * $limit;
$whereConditions = ["a.status = 'published'"];

// Search by query (headline, content, excerpt)
if (!empty($query)) {
    $searchQuery = sanitize($conn, $query);
    $whereConditions[] = "(a.headline LIKE '%$searchQuery%' OR a.content LIKE '%$searchQuery%' OR a.excerpt LIKE '%$searchQuery%')";
}

// Filter by category
if (!empty($category)) {
    $whereConditions[] = "c.slug = '$category'";
}

// Filter by author
if (!empty($author)) {
    $whereConditions[] = "au.name LIKE '%$author%'";
}

// Filter by date range
if (!empty($date_from)) {
    $whereConditions[] = "DATE(a.published_at) >= '$date_from'";
}

if (!empty($date_to)) {
    $whereConditions[] = "DATE(a.published_at) <= '$date_to'";
}

$whereClause = implode(' AND ', $whereConditions);

$sql = "SELECT 
            a.*,
            c.name as category_name,
            c.slug as category_slug,
            c.color as category_color,
            au.name as author_name,
            au.profile_image as author_image
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        LEFT JOIN authors au ON a.author_id = au.id
        WHERE $whereClause
        ORDER BY a.is_breaking_news DESC, a.published_at DESC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

if (!$result) {
    sendError('Database error: ' . $conn->error, 500);
}

$articles = [];
while ($row = $result->fetch_assoc()) {
    $articles[] = [
        'id' => (int)$row['id'],
        'headline' => $row['headline'],
        'slug' => $row['slug'],
        'excerpt' => $row['excerpt'],
        'featured_image' => $row['featured_image'],
        'is_featured' => (bool)$row['is_featured'],
        'is_breaking_news' => (bool)$row['is_breaking_news'],
        'view_count' => (int)$row['view_count'],
        'published_at' => $row['published_at'],
        'category' => [
            'name' => $row['category_name'],
            'slug' => $row['category_slug'],
            'color' => $row['category_color']
        ],
        'author' => [
            'name' => $row['author_name'],
            'profile_image' => $row['author_image']
        ]
    ];
}

// Get total count for pagination
$countSql = "SELECT COUNT(*) as total FROM articles a 
             LEFT JOIN categories c ON a.category_id = c.id 
             LEFT JOIN authors au ON a.author_id = au.id 
             WHERE $whereClause";
$countResult = $conn->query($countSql);
$total = $countResult->fetch_assoc()['total'];

// Get search suggestions
$suggestions = [];
if (!empty($query)) {
    $suggestionSql = "SELECT DISTINCT a.headline 
                     FROM articles a 
                     WHERE a.status = 'published' 
                     AND a.headline LIKE '%$searchQuery%' 
                     LIMIT 5";
    $suggestionResult = $conn->query($suggestionSql);
    while ($suggestion = $suggestionResult->fetch_assoc()) {
        $suggestions[] = $suggestion['headline'];
    }
}

sendResponse([
    'query' => $query,
    'articles' => $articles,
    'suggestions' => $suggestions,
    'pagination' => [
        'current_page' => $page,
        'per_page' => $limit,
        'total' => (int)$total,
        'total_pages' => ceil($total / $limit)
    ],
    'filters' => [
        'category' => $category,
        'author' => $author,
        'date_from' => $date_from,
        'date_to' => $date_to
    ]
]);
?> 