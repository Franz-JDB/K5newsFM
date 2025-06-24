<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Get article ID if present
$articleId = null;
if (isset($pathParts[2]) && is_numeric($pathParts[2])) {
    $articleId = (int)$pathParts[2];
}

switch ($method) {
    case 'GET':
        if ($articleId) {
            getArticle($articleId);
        } else {
            getArticles();
        }
        break;
    case 'POST':
        createArticle();
        break;
    case 'PUT':
        if ($articleId) {
            updateArticle($articleId);
        } else {
            sendError('Article ID required for update', 400);
        }
        break;
    case 'DELETE':
        if ($articleId) {
            deleteArticle($articleId);
        } else {
            sendError('Article ID required for deletion', 400);
        }
        break;
    default:
        sendError('Method not allowed', 405);
}

function getArticles() {
    global $conn;
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $category = isset($_GET['category']) ? sanitize($conn, $_GET['category']) : null;
    $status = isset($_GET['status']) ? sanitize($conn, $_GET['status']) : 'published';
    $featured = isset($_GET['featured']) ? (bool)$_GET['featured'] : null;
    $breaking = isset($_GET['breaking']) ? (bool)$_GET['breaking'] : null;
    
    $offset = ($page - 1) * $limit;
    
    $whereConditions = ["a.status = '$status'"];
    $params = [];
    
    if ($category) {
        $whereConditions[] = "c.slug = '$category'";
    }
    
    if ($featured !== null) {
        $whereConditions[] = "a.is_featured = " . ($featured ? '1' : '0');
    }
    
    if ($breaking !== null) {
        $whereConditions[] = "a.is_breaking_news = " . ($breaking ? '1' : '0');
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
            'status' => $row['status'],
            'is_featured' => (bool)$row['is_featured'],
            'is_breaking_news' => (bool)$row['is_breaking_news'],
            'view_count' => (int)$row['view_count'],
            'published_at' => $row['published_at'],
            'created_at' => $row['created_at'],
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
                 WHERE $whereClause";
    $countResult = $conn->query($countSql);
    $total = $countResult->fetch_assoc()['total'];
    
    sendResponse([
        'articles' => $articles,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $limit,
            'total' => (int)$total,
            'total_pages' => ceil($total / $limit)
        ]
    ]);
}

function getArticle($id) {
    global $conn;
    
    // Increment view count
    $conn->query("UPDATE articles SET view_count = view_count + 1 WHERE id = $id");
    
    $sql = "SELECT 
                a.*,
                c.name as category_name,
                c.slug as category_slug,
                c.color as category_color,
                au.name as author_name,
                au.bio as author_bio,
                au.profile_image as author_image,
                au.twitter_handle,
                au.linkedin_url
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN authors au ON a.author_id = au.id
            WHERE a.id = $id";
    
    $result = $conn->query($sql);
    
    if (!$result || $result->num_rows === 0) {
        sendError('Article not found', 404);
    }
    
    $article = $result->fetch_assoc();
    
    // Get tags for this article
    $tagsSql = "SELECT t.name, t.slug 
                FROM tags t 
                JOIN article_tags at ON t.id = at.tag_id 
                WHERE at.article_id = $id";
    $tagsResult = $conn->query($tagsSql);
    $tags = [];
    while ($tag = $tagsResult->fetch_assoc()) {
        $tags[] = $tag;
    }
    
    // Get related articles
    $relatedSql = "SELECT a.id, a.headline, a.slug, a.excerpt, a.featured_image, a.published_at
                   FROM articles a
                   WHERE a.category_id = {$article['category_id']} 
                   AND a.id != $id 
                   AND a.status = 'published'
                   ORDER BY a.published_at DESC
                   LIMIT 5";
    $relatedResult = $conn->query($relatedSql);
    $related = [];
    while ($rel = $relatedResult->fetch_assoc()) {
        $related[] = $rel;
    }
    
    sendResponse([
        'id' => (int)$article['id'],
        'headline' => $article['headline'],
        'slug' => $article['slug'],
        'content' => $article['content'],
        'excerpt' => $article['excerpt'],
        'featured_image' => $article['featured_image'],
        'status' => $article['status'],
        'is_featured' => (bool)$article['is_featured'],
        'is_breaking_news' => (bool)$article['is_breaking_news'],
        'view_count' => (int)$article['view_count'],
        'published_at' => $article['published_at'],
        'created_at' => $article['created_at'],
        'category' => [
            'name' => $article['category_name'],
            'slug' => $article['category_slug'],
            'color' => $article['category_color']
        ],
        'author' => [
            'name' => $article['author_name'],
            'bio' => $article['author_bio'],
            'profile_image' => $article['author_image'],
            'twitter_handle' => $article['twitter_handle'],
            'linkedin_url' => $article['linkedin_url']
        ],
        'tags' => $tags,
        'related_articles' => $related
    ]);
}

function createArticle() {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!validateRequired($input, ['headline', 'content'])) {
        sendError('Headline and content are required');
    }
    
    $headline = sanitize($conn, $input['headline']);
    $content = sanitize($conn, $input['content']);
    $excerpt = isset($input['excerpt']) ? sanitize($conn, $input['excerpt']) : '';
    $featured_image = isset($input['featured_image']) ? sanitize($conn, $input['featured_image']) : '';
    $category_id = isset($input['category_id']) ? (int)$input['category_id'] : null;
    $author_id = isset($input['author_id']) ? (int)$input['author_id'] : null;
    $status = isset($input['status']) ? sanitize($conn, $input['status']) : 'draft';
    $is_featured = isset($input['is_featured']) ? (bool)$input['is_featured'] : false;
    $is_breaking_news = isset($input['is_breaking_news']) ? (bool)$input['is_breaking_news'] : false;
    
    // Create slug from headline
    $slug = createSlug($headline);
    
    // Check if slug already exists
    $checkSql = "SELECT id FROM articles WHERE slug = '$slug'";
    $checkResult = $conn->query($checkSql);
    if ($checkResult->num_rows > 0) {
        $slug = $slug . '-' . time();
    }
    
    $sql = "INSERT INTO articles (headline, slug, content, excerpt, featured_image, category_id, author_id, status, is_featured, is_breaking_news, published_at) 
            VALUES ('$headline', '$slug', '$content', '$excerpt', '$featured_image', " . 
            ($category_id ? $category_id : 'NULL') . ", " . 
            ($author_id ? $author_id : 'NULL') . ", '$status', " . 
            ($is_featured ? '1' : '0') . ", " . 
            ($is_breaking_news ? '1' : '0') . ", " .
            ($status === 'published' ? 'NOW()' : 'NULL') . ")";
    
    if ($conn->query($sql)) {
        $articleId = $conn->insert_id;
        
        // Handle tags if provided
        if (isset($input['tags']) && is_array($input['tags'])) {
            foreach ($input['tags'] as $tagName) {
                $tagName = sanitize($conn, $tagName);
                $tagSlug = createSlug($tagName);
                
                // Insert tag if not exists
                $tagSql = "INSERT IGNORE INTO tags (name, slug) VALUES ('$tagName', '$tagSlug')";
                $conn->query($tagSql);
                
                $tagId = $conn->insert_id ?: $conn->query("SELECT id FROM tags WHERE slug = '$tagSlug'")->fetch_assoc()['id'];
                
                // Link tag to article
                $linkSql = "INSERT IGNORE INTO article_tags (article_id, tag_id) VALUES ($articleId, $tagId)";
                $conn->query($linkSql);
            }
        }
        
        sendResponse(['message' => 'Article created successfully', 'id' => $articleId], 201);
    } else {
        sendError('Failed to create article: ' . $conn->error, 500);
    }
}

function updateArticle($id) {
    global $conn;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!validateRequired($input, ['headline', 'content'])) {
        sendError('Headline and content are required');
    }
    
    $headline = sanitize($conn, $input['headline']);
    $content = sanitize($conn, $input['content']);
    $excerpt = isset($input['excerpt']) ? sanitize($conn, $input['excerpt']) : '';
    $featured_image = isset($input['featured_image']) ? sanitize($conn, $input['featured_image']) : '';
    $category_id = isset($input['category_id']) ? (int)$input['category_id'] : null;
    $author_id = isset($input['author_id']) ? (int)$input['author_id'] : null;
    $status = isset($input['status']) ? sanitize($conn, $input['status']) : 'draft';
    $is_featured = isset($input['is_featured']) ? (bool)$input['is_featured'] : false;
    $is_breaking_news = isset($input['is_breaking_news']) ? (bool)$input['is_breaking_news'] : false;
    
    // Create slug from headline
    $slug = createSlug($headline);
    
    // Check if slug already exists for different article
    $checkSql = "SELECT id FROM articles WHERE slug = '$slug' AND id != $id";
    $checkResult = $conn->query($checkSql);
    if ($checkResult->num_rows > 0) {
        $slug = $slug . '-' . time();
    }
    
    $sql = "UPDATE articles SET 
            headline = '$headline',
            slug = '$slug',
            content = '$content',
            excerpt = '$excerpt',
            featured_image = '$featured_image',
            category_id = " . ($category_id ? $category_id : 'NULL') . ",
            author_id = " . ($author_id ? $author_id : 'NULL') . ",
            status = '$status',
            is_featured = " . ($is_featured ? '1' : '0') . ",
            is_breaking_news = " . ($is_breaking_news ? '1' : '0') . ",
            published_at = " . ($status === 'published' ? 'NOW()' : 'NULL') . "
            WHERE id = $id";
    
    if ($conn->query($sql)) {
        // Handle tags if provided
        if (isset($input['tags']) && is_array($input['tags'])) {
            // Remove existing tags
            $conn->query("DELETE FROM article_tags WHERE article_id = $id");
            
            foreach ($input['tags'] as $tagName) {
                $tagName = sanitize($conn, $tagName);
                $tagSlug = createSlug($tagName);
                
                // Insert tag if not exists
                $tagSql = "INSERT IGNORE INTO tags (name, slug) VALUES ('$tagName', '$tagSlug')";
                $conn->query($tagSql);
                
                $tagId = $conn->insert_id ?: $conn->query("SELECT id FROM tags WHERE slug = '$tagSlug'")->fetch_assoc()['id'];
                
                // Link tag to article
                $linkSql = "INSERT INTO article_tags (article_id, tag_id) VALUES ($id, $tagId)";
                $conn->query($linkSql);
            }
        }
        
        sendResponse(['message' => 'Article updated successfully']);
    } else {
        sendError('Failed to update article: ' . $conn->error, 500);
    }
}

function deleteArticle($id) {
    global $conn;
    
    $sql = "DELETE FROM articles WHERE id = $id";
    
    if ($conn->query($sql)) {
        sendResponse(['message' => 'Article deleted successfully']);
    } else {
        sendError('Failed to delete article: ' . $conn->error, 500);
    }
}
?> 