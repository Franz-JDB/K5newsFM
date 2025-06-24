<?php
require_once 'includes/db.php';

// Get sorting parameter
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Build the query based on sorting
$orderBy = '';
switch ($sort) {
    case 'oldest':
        $orderBy = 'ORDER BY a.published_at ASC';
        break;
    case 'latest':
    default:
        $orderBy = 'ORDER BY a.published_at DESC';
        break;
}

// Get articles
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
        WHERE a.status = 'published'
        $orderBy
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);
$articles = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $articles[] = $row;
    }
}

// Get total count for pagination
$countSql = "SELECT COUNT(*) as total FROM articles a WHERE a.status = 'published'";
$countResult = $conn->query($countSql);
$total = $countResult->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);

// Get categories for sidebar
$categoriesSql = "SELECT * FROM categories ORDER BY name";
$categoriesResult = $conn->query($categoriesSql);
$categories = [];
if ($categoriesResult) {
    while ($cat = $categoriesResult->fetch_assoc()) {
        $categories[] = $cat;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latest News - K5News</title>
    <meta name="description" content="Browse all the latest news articles from K5News.">
    <link rel="stylesheet" href="css/styles.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <img src="images/k5news_logo.png" alt="K5News Logo" class="navbar-logo d-inline-block align-text-top" style="height: 38px; width: auto; max-width: 48px;">
                <span>K5News</span>
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
                        <a class="nav-link active" href="latest.php">Latest News</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#categories">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-4">
        <div class="container" id="latest">
            <!-- Sort Controls -->
            <div class="sort-controls mb-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-0">Latest News</h4>
                        <small class="text-muted">Showing <?php echo count($articles); ?> of <?php echo $total; ?> articles</small>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <span class="me-2">Sort by:</span>
                        <a href="?sort=latest<?php echo $page > 1 ? '&page=' . $page : ''; ?>" class="sort-btn <?php echo $sort === 'latest' ? 'active' : ''; ?>">
                            <i class="fas fa-clock me-1"></i>Latest
                        </a>
                        <a href="?sort=oldest<?php echo $page > 1 ? '&page=' . $page : ''; ?>" class="sort-btn <?php echo $sort === 'oldest' ? 'active' : ''; ?>">
                            <i class="fas fa-history me-1"></i>Oldest
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Articles Column -->
                <div class="col-lg-8">
                    <div class="row">
                        <?php if (empty($articles)): ?>
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-newspaper" style="font-size: 4rem; color: #ccc;"></i>
                                <h3 class="mt-3">No articles found</h3>
                                <p class="text-muted">Check back later for the latest news.</p>
                            </div>
                        </div>
                        <?php else: ?>
                        <?php foreach ($articles as $article): ?>
                        <div class="col-lg-6 col-md-6">
                            <div class="article-card">
                                <div class="article-image">
                                    <?php if ($article['featured_image']): ?>
                                        <img src="<?php echo htmlspecialchars($article['featured_image']); ?>" alt="<?php echo htmlspecialchars($article['headline']); ?>" class="w-100 h-100 object-fit-cover">
                                    <?php else: ?>
                                        <i class="fas fa-newspaper"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="article-content">
                                    <?php if ($article['is_breaking_news']): ?>
                                        <div class="breaking-news">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Breaking News
                                        </div>
                                    <?php endif; ?>
                                    
                                    <a href="article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>" class="category-badge" style="background-color: <?php echo htmlspecialchars($article['category_color']); ?>">
                                        <?php echo htmlspecialchars($article['category_name']); ?>
                                    </a>
                                    
                                    <a href="article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>" class="article-title">
                                        <?php echo htmlspecialchars($article['headline']); ?>
                                    </a>
                                    
                                    <p class="article-excerpt"><?php echo htmlspecialchars($article['excerpt']); ?></p>
                                    
                                    <div class="article-meta">
                                        <div class="author-info">
                                            <div class="author-avatar">
                                                <?php echo strtoupper(substr($article['author_name'], 0, 1)); ?>
                                            </div>
                                            <span><?php echo htmlspecialchars($article['author_name']); ?></span>
                                        </div>
                                        <span><?php echo date('M j, Y', strtotime($article['published_at'])); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Articles pagination">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?sort=<?php echo $sort; ?>&page=<?php echo $page - 1; ?>">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?sort=<?php echo $sort; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?sort=<?php echo $sort; ?>&page=<?php echo $page + 1; ?>">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sidebar">
                        <h5 class="sidebar-title">
                            <i id="categories"class="fas fa-tags me-2"></i>Categories
                        </h5>
                        <ul class="category-list">
                            <?php foreach ($categories as $category): ?>
                            <li class="category-item">
                                <a href="category.php?slug=<?php echo htmlspecialchars($category['slug']); ?>" class="category-link">
                                    <span style="color: <?php echo htmlspecialchars($category['color']); ?>; margin-right: 0.5rem;">●</span>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="sidebar mt-4">
                        <h5 class="sidebar-title">
                            <i class="fas fa-info-circle me-2"></i>About K5News
                        </h5>
                        <p class="text-muted">
                            K5News is your trusted source for the latest news, breaking stories, and in-depth coverage of today's most important events. 
                            Stay informed with our comprehensive reporting and analysis.
                        </p>
                        <div class="d-grid">
                            <a href="#contact" class="btn btn-outline-primary">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-simple bg-white border-top mt-5 py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <span class="footer-logo-text"><i class="fas fa-newspaper me-2"></i>K5News</span>
                    <span class="text-muted small ms-2">Your trusted source for the latest news and breaking stories.</span>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="index.php" class="footer-link">Home</a>
                    <a href="latest.php" class="footer-link">Latest News</a>
                    <a href="#categories" class="footer-link">Categories</a>
                    <a href="#about" class="footer-link">About</a>
                    <a href="#contact" class="footer-link">Contact</a>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-center">
                    <span class="text-muted small">&copy; 2025 K5News. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 