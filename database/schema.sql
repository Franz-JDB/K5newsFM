-- K5News Database Schema
-- News Broadcast Website Database

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS k5news_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE k5news_db;

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    color VARCHAR(7) DEFAULT '#007bff',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Authors table
CREATE TABLE authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE,
    bio TEXT,
    profile_image VARCHAR(255),
    twitter_handle VARCHAR(50),
    linkedin_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Articles table
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    headline VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    category_id INT,
    author_id INT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    is_featured BOOLEAN DEFAULT FALSE,
    is_breaking_news BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_published_at (published_at),
    INDEX idx_is_featured (is_featured),
    INDEX idx_is_breaking_news (is_breaking_news)
);

-- Tags table
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Article tags relationship table
CREATE TABLE article_tags (
    article_id INT,
    tag_id INT,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- Comments table
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    comment TEXT NOT NULL,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

-- Subscribers table for newsletter
CREATE TABLE subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Site settings table
CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default categories
INSERT INTO categories (name, slug, description, color) VALUES
('Breaking News', 'breaking-news', 'Latest breaking news and urgent updates', '#dc3545'),
('Politics', 'politics', 'Political news and government updates', '#007bff'),
('Business', 'business', 'Business and economic news', '#28a745'),
('Technology', 'technology', 'Technology and innovation news', '#6f42c1'),
('Sports', 'sports', 'Sports news and updates', '#fd7e14'),
('Entertainment', 'entertainment', 'Entertainment and celebrity news', '#e83e8c'),
('Health', 'health', 'Health and medical news', '#20c997'),
('Education', 'education', 'Education and academic news', '#17a2b8'),
('Lifestyle', 'lifestyle', 'Lifestyle and culture news', '#6c757d'),
('International', 'international', 'International news and world events', '#343a40');

-- Insert default authors
INSERT INTO authors (name, email, bio) VALUES
('Editorial Team', 'editor@k5news.com', 'Our dedicated editorial team'),
('John Smith', 'john.smith@k5news.com', 'Senior Political Correspondent'),
('Maria Garcia', 'maria.garcia@k5news.com', 'Business and Economy Reporter'),
('David Chen', 'david.chen@k5news.com', 'Technology and Innovation Writer');

-- Insert default site settings
INSERT INTO site_settings (setting_key, setting_value, description) VALUES
('site_name', 'K5News', 'Website name'),
('site_description', 'Your trusted source for breaking news and in-depth coverage', 'Website description'),
('site_logo', '', 'Website logo URL'),
('contact_email', 'contact@k5news.com', 'Contact email address'),
('social_facebook', '', 'Facebook page URL'),
('social_twitter', '', 'Twitter handle'),
('social_instagram', '', 'Instagram handle'),
('analytics_code', '', 'Google Analytics tracking code'),
('posts_per_page', '10', 'Number of posts to display per page'); 