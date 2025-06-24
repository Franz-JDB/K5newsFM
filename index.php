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

// Get featured articles
$featuredSql = "SELECT 
                    a.*,
                    c.name as category_name,
                    c.slug as category_slug,
                    c.color as category_color,
                    au.name as author_name
                FROM articles a
                LEFT JOIN categories c ON a.category_id = c.id
                LEFT JOIN authors au ON a.author_id = au.id
                WHERE a.status = 'published' AND a.is_featured = 1
                ORDER BY a.published_at DESC
                LIMIT 3";
$featuredResult = $conn->query($featuredSql);
$featuredArticles = [];
if ($featuredResult) {
    while ($row = $featuredResult->fetch_assoc()) {
        $featuredArticles[] = $row;
    }
}
?>  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K5News - Latest News and Breaking Stories</title>
    <meta name="description" content="Stay informed with the latest news, breaking stories, and in-depth coverage from K5News.">
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
                        <a class="nav-link" href="#live">Live</a>
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 text-center mb-3">
                    <img src="images/k5news_logo.png" alt="K5News Logo" class="img-fluid mx-auto d-block responsive-logo" style="max-width: 220px; height: auto;">
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center mx-auto">
                    <h1 class="hero-title">Stay Informed with K5News</h1>
                    <p class="hero-subtitle">Get the latest breaking news, in-depth analysis, and comprehensive coverage of today's most important stories.</p>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="latest.php" class="btn btn-light btn-lg">Latest News</a>
                        <a href="#featured" class="btn btn-outline-light btn-lg">Featured Stories</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest News Carousel Preview -->
    <section class="latest-carousel-section py-4" id="latest">
        <div class="container">
            <h2 class="text-center mb-4">
                <i class="fas fa-clock text-primary me-2"></i>Latest News Preview
            </h2>
            <?php
            // Get 5 most recent articles for carousel
            $carouselSql = "SELECT a.*, c.name as category_name, c.color as category_color, au.name as author_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id LEFT JOIN authors au ON a.author_id = au.id WHERE a.status = 'published' ORDER BY a.published_at DESC LIMIT 5";
            $carouselResult = $conn->query($carouselSql);
            $carouselArticles = [];
            if ($carouselResult) {
                while ($row = $carouselResult->fetch_assoc()) {
                    $carouselArticles[] = $row;
                }
            }
            ?>
            <?php if (!empty($carouselArticles)): ?>
            <div id="latestNewsCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($carouselArticles as $i => $article): ?>
                    <div class="carousel-item<?php if ($i === 0) echo ' active'; ?>">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-md-6">
                                <?php if ($article['featured_image']): ?>
                                    <img src="<?php echo htmlspecialchars($article['featured_image']); ?>" class="d-block w-100 rounded-3" alt="<?php echo htmlspecialchars($article['headline']); ?>">
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center bg-light rounded-3" style="height:220px;">
                                        <i class="fas fa-newspaper fa-3x text-secondary"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0">
                                <span class="badge" style="background:<?php echo htmlspecialchars($article['category_color']); ?>; color:#fff; font-size:0.9rem;"> <?php echo htmlspecialchars($article['category_name']); ?> </span>
                                <h3 class="mt-2 mb-2" style="font-size:1.4rem; font-weight:600;">
                                    <?php echo htmlspecialchars($article['headline']); ?>
                                </h3>
                                <p class="mb-3 text-muted" style="font-size:1.05rem;">
                                    <?php echo htmlspecialchars($article['excerpt']); ?>
                                </p>
                                <div class="mb-2 text-secondary" style="font-size:0.95rem;">
                                    <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($article['author_name']); ?>
                                    &nbsp;|&nbsp;
                                    <i class="fas fa-calendar-alt me-1"></i> <?php echo date('M j, Y', strtotime($article['published_at'])); ?>
                                </div>
                                <a href="article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>" class="btn btn-primary btn-sm">Read More</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#latestNewsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#latestNewsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
            <?php else: ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-newspaper fa-3x mb-3"></i>
                <div>No news articles available yet.</div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Live Section -->
    <section class="live-section improved-live-section" id="live">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-9">
                    <div class="live-card d-flex flex-column align-items-stretch shadow-lg rounded-4 overflow-hidden">
                        <div class="live-video-panel flex-fill d-flex flex-column justify-content-center align-items-center p-0 bg-dark position-relative" style="min-width:0;">
                            <div class="live-header text-center w-100 py-3 px-2 bg-gradient position-absolute top-0 start-0" style="z-index:2; background:rgba(0,0,0,0.5);">
                                <div class="live-indicator justify-content-center align-items-center d-flex gap-2">
                                    <span class="live-dot"></span>
                                    <h2 class="live-title mb-0" style="font-size:1.5rem; color:#fff;">LIVE NOW</h2>
                                </div>
                                <p class="live-subtitle mb-0" style="font-size:1.05rem; color:#fff;">Watch breaking news and live coverage</p>
                            </div>
                            <div class="live-video-container w-100 d-flex align-items-center justify-content-center" style="aspect-ratio:16/9; min-height:340px; height:440px; background:#111;">
                                <iframe src="https://www.facebook.com/plugins/video.php?height=480&href=https%3A%2F%2Fwww.facebook.com%2F100091770458025%2Fvideos%2F703787112276703%2F&show_text=true&width=800&t=0"
                                        width="100%"
                                        height="100%"
                                        style="border:none;overflow:hidden; border-radius: 0 0 0 18px; width:100%; height:100%; min-height:340px;"
                                        allowfullscreen="true"
                                        allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"
                                        allowFullScreen="true">
                                </iframe>
                            </div>
                            <div class="live-info w-100 py-2 px-3 bg-dark bg-opacity-75 text-center" style="z-index:2;">
                                <div class="live-stats d-flex flex-wrap gap-3 justify-content-center text-light" id="liveStats">
                                    <span class="live-stat">
                                        <i class="fas fa-eye"></i> <span id="currentViewers">Loading...</span> watching
                                    </span>
                                    <span class="live-stat">
                                        <i class="fas fa-clock"></i> <span id="streamDuration">Loading...</span>
                                    </span>
                                    <span class="live-stat">
                                        <i class="fas fa-fire"></i> <span id="totalViews">Loading...</span> views
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Articles Section -->
    <?php if (!empty($featuredArticles)): ?>
    <section class="featured-section" id="featured">
        <div class="container">
            <h2 class="text-center mb-4">
                <i class="fas fa-star text-warning me-2"></i>Featured Stories
            </h2>
            <div class="row">
                <?php foreach ($featuredArticles as $article): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="featured-card">
                        <div class="featured-image">
                            <?php if ($article['featured_image']): ?>
                                <img src="<?php echo htmlspecialchars($article['featured_image']); ?>" alt="<?php echo htmlspecialchars($article['headline']); ?>" class="w-100 h-100 object-fit-cover">
                            <?php else: ?>
                                <i class="fas fa-newspaper"></i>
                            <?php endif; ?>
                        </div>
                        <div class="article-content">
                            <div class="featured-badge">Featured</div>
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
            </div>
        </div>
    </section>
    <?php endif; ?>

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
    
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add loading animation for article cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.article-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            // Load live data
            loadLiveData();
            
            // Update live data every 30 seconds
            setInterval(loadLiveData, 30000);
        });
        
        // Function to load live data
        function loadLiveData() {
            fetch('api/live.php')
                .then(response => response.json())
                .then(data => {
                    // Update statistics
                    document.getElementById('currentViewers').textContent = formatNumber(data.current_viewers);
                    document.getElementById('streamDuration').textContent = data.stream_duration.formatted;
                    document.getElementById('totalViews').textContent = formatNumber(data.total_views);
                    
                    // Update live updates
                    const updatesContainer = document.getElementById('liveUpdates');
                    updatesContainer.innerHTML = '';
                    
                    data.live_updates.forEach(update => {
                        const updateElement = document.createElement('div');
                        updateElement.className = 'live-update-item';
                        updateElement.innerHTML = `
                            <div class="update-time">${update.time}</div>
                            <div class="update-content">${update.content}</div>
                        `;
                        updatesContainer.appendChild(updateElement);
                    });
                })
                .catch(error => {
                    console.error('Error loading live data:', error);
                    // Show fallback data
                    document.getElementById('currentViewers').textContent = '1,247';
                    document.getElementById('streamDuration').textContent = '2h 15m';
                    document.getElementById('totalViews').textContent = '15,432';
                });
        }
        
        // Function to format numbers with commas
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        // Function to get live alerts
        function getLiveAlerts() {
            alert('Live alerts feature coming soon! You will be notified of breaking news.');
        }
        
        // Function to share stream
        function shareStream() {
            if (navigator.share) {
                navigator.share({
                    title: 'K5News Live Stream',
                    text: 'Watch breaking news live on K5News',
                    url: window.location.href + '#live'
                });
            } else {
                // Fallback for browsers that don't support Web Share API
                const url = window.location.href + '#live';
                navigator.clipboard.writeText(url).then(() => {
                    alert('Stream link copied to clipboard!');
                });
            }
        }
    </script>
</body>
</html> 