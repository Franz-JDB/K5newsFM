<?php
require_once 'includes/db.php';

// Get article slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    header('Location: index.php');
    exit();
}

// Get article details
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
        WHERE a.slug = '" . $conn->real_escape_string($slug) . "' AND a.status = 'published'";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    header('Location: index.php');
    exit();
}

$article = $result->fetch_assoc();

// Increment view count
$conn->query("UPDATE articles SET view_count = view_count + 1 WHERE id = " . $article['id']);

// Get tags for this article
$tagsSql = "SELECT t.name, t.slug 
            FROM tags t 
            JOIN article_tags at ON t.id = at.tag_id 
            WHERE at.article_id = " . $article['id'];
$tagsResult = $conn->query($tagsSql);
$tags = [];
while ($tag = $tagsResult->fetch_assoc()) {
    $tags[] = $tag;
}

// Get related articles
$relatedSql = "SELECT a.id, a.headline, a.slug, a.excerpt, a.featured_image, a.published_at
               FROM articles a
               WHERE a.category_id = {$article['category_id']} 
               AND a.id != {$article['id']} 
               AND a.status = 'published'
               ORDER BY a.published_at DESC
               LIMIT 3";
$relatedResult = $conn->query($relatedSql);
$relatedArticles = [];
while ($rel = $relatedResult->fetch_assoc()) {
    $relatedArticles[] = $rel;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['headline']); ?> - K5News</title>
    <meta name="description" content="<?php echo htmlspecialchars($article['excerpt']); ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color:rgb(251, 27, 6);
            --secondary-color: #34a853;
            --accent-color: #1a73e8;
            --text-dark: #202124;
            --text-light: #5f6368;
            --bg-light: #f8f9fa;
            --border-color: #dadce0;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background-color: #fff;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color),rgb(246, 5, 5));
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .nav-link:hover {
            color: white !important;
        }
        
        .article-header {
            background: white;
            padding: 3rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .article-title {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }
        
        .article-meta {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .meta-item i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        
        .category-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }
        
        .breaking-news {
            background: var(--accent-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .article-content {
            background: white;
            padding: 3rem 0;
        }
        
        .article-body {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text-dark);
        }
        
        .article-body p {
            margin-bottom: 1.5rem;
        }
        
        .article-body h2, .article-body h3 {
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }
        
        .featured-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        
        .author-section {
            background: var(--bg-light);
            padding: 2rem;
            border-radius: 15px;
            margin: 2rem 0;
        }
        
        .author-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .tags-section {
            margin: 2rem 0;
        }
        
        .tag {
            background: var(--bg-light);
            color: var(--text-dark);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            text-decoration: none;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .tag:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .related-articles {
            background: var(--bg-light);
            padding: 3rem 0;
        }
        
        .related-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }
        
        .related-card:hover {
            transform: translateY(-5px);
        }
        
        .related-image {
            height: 200px;
            background: linear-gradient(45deg, #f0f0f0, #e0e0e0);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 3rem;
        }
        
        .sidebar {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 2rem;
        }
        
        .sidebar-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--text-dark);
        }
        
        @media (max-width: 768px) {
            .article-title {
                font-size: 2rem;
            }
            
            .article-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-newspaper me-2"></i>K5News
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#categories">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Article Header -->
    <section class="article-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <?php if ($article['is_breaking_news']): ?>
                        <div class="breaking-news">
                            <i class="fas fa-exclamation-triangle me-1"></i>Breaking News
                        </div>
                    <?php endif; ?>
                    
                    <a href="category.php?slug=<?php echo htmlspecialchars($article['category_slug']); ?>" class="category-badge" style="background-color: <?php echo htmlspecialchars($article['category_color']); ?>">
                        <?php echo htmlspecialchars($article['category_name']); ?>
                    </a>
                    
                    <h1 class="article-title"><?php echo htmlspecialchars($article['headline']); ?></h1>
                    
                    <div class="article-meta">
                        <div class="meta-item">
                            <i class="fas fa-user"></i>
                            <span><?php echo htmlspecialchars($article['author_name']); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <span><?php echo date('F j, Y', strtotime($article['published_at'])); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-eye"></i>
                            <span><?php echo number_format($article['view_count']); ?> views</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span><?php echo date('g:i A', strtotime($article['published_at'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Article Content -->
    <section class="article-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <?php if ($article['featured_image']): ?>
                        <img src="<?php echo htmlspecialchars($article['featured_image']); ?>" alt="<?php echo htmlspecialchars($article['headline']); ?>" class="featured-image">
                    <?php endif; ?>
                    
                    <div class="article-body">
                        <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                    </div>
                    
                    <!-- Tags -->
                    <?php if (!empty($tags)): ?>
                    <div class="tags-section">
                        <h5><i class="fas fa-tags me-2"></i>Tags:</h5>
                        <?php foreach ($tags as $tag): ?>
                            <a href="tag.php?slug=<?php echo htmlspecialchars($tag['slug']); ?>" class="tag">
                                #<?php echo htmlspecialchars($tag['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Author Section -->
                    <div class="author-section">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <div class="author-avatar">
                                    <?php echo strtoupper(substr($article['author_name'], 0, 1)); ?>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <h5><?php echo htmlspecialchars($article['author_name']); ?></h5>
                                <?php if ($article['author_bio']): ?>
                                    <p class="text-muted"><?php echo htmlspecialchars($article['author_bio']); ?></p>
                                <?php endif; ?>
                                <div class="d-flex gap-3">
                                    <?php if ($article['twitter_handle']): ?>
                                        <a href="https://twitter.com/<?php echo htmlspecialchars($article['twitter_handle']); ?>" class="text-muted" target="_blank">
                                            <i class="fab fa-twitter fa-lg"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($article['linkedin_url']): ?>
                                        <a href="<?php echo htmlspecialchars($article['linkedin_url']); ?>" class="text-muted" target="_blank">
                                            <i class="fab fa-linkedin fa-lg"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sidebar">
                        <h5 class="sidebar-title">
                            <i class="fas fa-share-alt me-2"></i>Share This Article
                        </h5>
                        <div class="d-flex gap-2 mb-4">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-outline-primary" target="_blank">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($article['headline']); ?>" class="btn btn-outline-info" target="_blank">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-outline-secondary" target="_blank">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        </div>
                        
                        <h5 class="sidebar-title">
                            <i class="fas fa-newspaper me-2"></i>Article Info
                        </h5>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <strong>Published:</strong> <?php echo date('F j, Y', strtotime($article['published_at'])); ?>
                            </li>
                            <li class="mb-2">
                                <strong>Category:</strong> <?php echo htmlspecialchars($article['category_name']); ?>
                            </li>
                            <li class="mb-2">
                                <strong>Views:</strong> <?php echo number_format($article['view_count']); ?>
                            </li>
                            <?php if ($article['is_featured']): ?>
                            <li class="mb-2">
                                <strong>Status:</strong> <span class="badge bg-success">Featured</span>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Articles -->
    <?php if (!empty($relatedArticles)): ?>
    <section class="related-articles">
        <div class="container">
            <h3 class="text-center mb-4">
                <i class="fas fa-newspaper me-2"></i>Related Articles
            </h3>
            <div class="row">
                <?php foreach ($relatedArticles as $related): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="related-card">
                        <div class="related-image">
                            <?php if ($related['featured_image']): ?>
                                <img src="<?php echo htmlspecialchars($related['featured_image']); ?>" alt="<?php echo htmlspecialchars($related['headline']); ?>" class="w-100 h-100 object-fit-cover">
                            <?php else: ?>
                                <i class="fas fa-newspaper"></i>
                            <?php endif; ?>
                        </div>
                        <div class="p-3">
                            <a href="article.php?slug=<?php echo htmlspecialchars($related['slug']); ?>" class="text-decoration-none">
                                <h5 class="text-dark"><?php echo htmlspecialchars($related['headline']); ?></h5>
                            </a>
                            <p class="text-muted small"><?php echo htmlspecialchars($related['excerpt']); ?></p>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                <?php echo date('M j, Y', strtotime($related['published_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <h5><i class="fas fa-newspaper me-2"></i>K5News</h5>
                    <p class="text-muted">Your trusted source for the latest news and breaking stories.</p>
                </div>
                <div class="col-lg-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-muted text-decoration-none">Home</a></li>
                        <li><a href="index.php#categories" class="text-muted text-decoration-none">Categories</a></li>
                        <li><a href="index.php#about" class="text-muted text-decoration-none">About</a></li>
                        <li><a href="index.php#contact" class="text-muted text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5>Follow Us</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-muted"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-linkedin fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="text-muted mb-0">&copy; 2025 K5News. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 