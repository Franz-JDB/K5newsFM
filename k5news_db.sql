-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 24, 2025 at 08:29 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `k5news_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `headline` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `excerpt` text DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `is_featured` tinyint(1) DEFAULT 0,
  `is_breaking_news` tinyint(1) DEFAULT 0,
  `view_count` int(11) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `headline`, `slug`, `content`, `excerpt`, `featured_image`, `category_id`, `author_id`, `status`, `is_featured`, `is_breaking_news`, `view_count`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'Breaking: Major Economic Policy Changes Announced', 'breaking-major-economic-policy-changes-announced', '<p>The government today announced sweeping changes to the national economic policy that will affect millions of citizens across the country. The new measures include tax reforms, infrastructure investments, and support for small businesses.</p>\r\n\r\n<p>According to the official statement released this morning, the policy changes aim to stimulate economic growth while ensuring sustainable development for the next decade. Key highlights include:</p>\r\n\r\n<ul>\r\n<li>Reduction in corporate tax rates for small and medium enterprises</li>\r\n<li>Increased funding for infrastructure projects</li>\r\n<li>New incentives for renewable energy investments</li>\r\n<li>Enhanced support for local manufacturing</li>\r\n</ul>\r\n\r\n<p>Economic experts have welcomed the announcement, calling it a \"bold step forward\" that could significantly boost the country\'s economic recovery efforts.</p>\r\n\r\n<p>\"These changes demonstrate a clear vision for economic transformation,\" said Dr. Maria Santos, a leading economist at the National Economic Institute. \"The focus on sustainable growth and local business support is particularly encouraging.\"</p>\r\n\r\n<p>The implementation of these policies is expected to begin within the next three months, with full rollout completed by the end of the fiscal year.</p>', 'The government announces comprehensive economic policy reforms including tax changes, infrastructure funding, and business support measures.', 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800', 2, 2, 'published', 1, 1, 62, '2025-06-24 00:05:03', '2025-06-24 00:05:03', '2025-06-24 05:49:19'),
(2, 'Technology Sector Sees Record Growth in Q3', 'technology-sector-sees-record-growth-q3', '<p>The technology sector has reported unprecedented growth in the third quarter of this year, with major companies posting record-breaking profits and expanding their workforce significantly.</p>\r\n\r\n<p>According to the latest industry report, the sector grew by 15.3% compared to the previous quarter, marking the highest growth rate in the past five years. This surge has been attributed to several factors:</p>\r\n\r\n<ul>\r\n<li>Increased demand for digital services during the pandemic</li>\r\n<li>Rapid adoption of cloud computing solutions</li>\r\n<li>Growth in e-commerce and online platforms</li>\r\n<li>Investment in artificial intelligence and machine learning</li>\r\n</ul>\r\n\r\n<p>Leading tech companies have announced plans to hire thousands of new employees, with many offering competitive salaries and remote work options to attract top talent.</p>\r\n\r\n<p>\"The technology sector is driving innovation and creating opportunities across all industries,\" said David Chen, Technology Analyst at TechInsights. \"This growth trend is expected to continue well into next year.\"</p>\r\n\r\n<p>Small and medium-sized tech startups have also benefited from this growth, with many receiving significant funding from venture capital firms and angel investors.</p>', 'Technology companies report 15.3% growth in Q3, with record profits and massive hiring plans across the sector.', 'https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=800', 4, 4, 'published', 1, 0, 0, '2025-06-24 00:05:03', '2025-06-24 00:05:03', '2025-06-24 00:05:03'),
(3, 'Local Sports Team Wins Championship Title', 'local-sports-team-wins-championship-title', '<p>In an electrifying match that went down to the wire, our local sports team has secured the championship title for the first time in over a decade. The victory came after a nail-biting final that had fans on the edge of their seats.</p>\r\n\r\n<p>The team, led by captain Michael Rodriguez, displayed exceptional skill and determination throughout the season, finishing with an impressive record of 28 wins and only 4 losses. The championship game was a true testament to their hard work and dedication.</p>\r\n\r\n<p>\"This victory means everything to our team and our community,\" said Rodriguez during the post-game celebration. \"We\'ve worked tirelessly for this moment, and to finally achieve it is absolutely incredible.\"</p>\r\n\r\n<p>Key highlights from the championship game include:</p>\r\n\r\n<ul>\r\n<li>Outstanding defensive performance in the final quarter</li>\r\n<li>Clutch scoring from rookie player Sarah Johnson</li>\r\n<li>Strategic coaching decisions that turned the tide</li>\r\n<li>Unwavering support from the home crowd</li>\r\n</ul>\r\n\r\n<p>The victory parade is scheduled for next weekend, with thousands of fans expected to line the streets to celebrate this historic achievement.</p>\r\n\r\n<p>Coach Williams praised his team\'s resilience: \"These players never gave up, even when the odds were against them. This championship is a result of their belief in each other and their commitment to excellence.\"</p>', 'Local team secures championship victory in thrilling final match, marking their first title in over a decade.', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800', 5, 1, 'published', 0, 0, 0, '2025-06-24 00:05:03', '2025-06-24 00:05:03', '2025-06-24 00:05:03'),
(4, 'New Health Guidelines Released for Public Safety', 'new-health-guidelines-released-public-safety', '<p>The Department of Health has released updated guidelines for public safety and wellness, incorporating the latest research findings and expert recommendations. These new guidelines aim to improve community health outcomes and prevent the spread of infectious diseases.</p>\r\n\r\n<p>The comprehensive document covers various aspects of public health, including:</p>\r\n\r\n<ul>\r\n<li>Updated vaccination recommendations for all age groups</li>\r\n<li>Enhanced protocols for public spaces and gatherings</li>\r\n<li>Mental health support and resources</li>\r\n<li>Nutrition and exercise guidelines</li>\r\n<li>Preventive care recommendations</li>\r\n</ul>\r\n\r\n<p>Dr. Emily Thompson, Director of Public Health, emphasized the importance of these guidelines: \"Our goal is to provide clear, evidence-based recommendations that everyone can follow to protect themselves and their communities.\"</p>\r\n\r\n<p>The guidelines also include specific recommendations for vulnerable populations, including elderly individuals, children, and those with pre-existing health conditions.</p>\r\n\r\n<p>Local healthcare providers have been briefed on the new guidelines and are ready to implement them in their practices. Public awareness campaigns will begin next month to ensure widespread adoption of these health measures.</p>\r\n\r\n<p>\"These guidelines represent our commitment to community health and safety,\" said Health Minister Dr. Robert Chen. \"We encourage everyone to review and follow these recommendations for the benefit of our entire community.\"</p>', 'Department of Health releases comprehensive new guidelines for public safety and wellness, incorporating latest research and expert recommendations.', 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=800', 7, 3, 'published', 0, 0, 0, '2025-06-24 00:05:03', '2025-06-24 00:05:03', '2025-06-24 00:05:03'),
(5, 'Education Reform Bill Passes in Parliament', 'education-reform-bill-passes-parliament', '<p>In a landmark decision, Parliament has passed the comprehensive Education Reform Bill, which will bring significant changes to the country\'s education system. The bill received overwhelming support from both government and opposition members.</p>\r\n\r\n<p>The reform package includes several key initiatives designed to improve educational outcomes and accessibility:</p>\r\n\r\n<ul>\r\n<li>Increased funding for public schools and universities</li>\r\n<li>Implementation of new curriculum standards</li>\r\n<li>Enhanced teacher training and development programs</li>\r\n<li>Expansion of digital learning resources</li>\r\n<li>Improved support for students with special needs</li>\r\n</ul>\r\n\r\n<p>Education Minister Sarah Williams hailed the passage as \"a historic moment for our education system.\" She emphasized that the reforms will ensure every child has access to quality education regardless of their background or location.</p>\r\n\r\n<p>The bill also includes provisions for:</p>\r\n\r\n<ul>\r\n<li>Modernization of school infrastructure</li>\r\n<li>Integration of technology in classrooms</li>\r\n<li>Partnerships with private sector for skill development</li>\r\n<li>Enhanced vocational training programs</li>\r\n</ul>\r\n\r\n<p>Implementation of the reforms will begin in the next academic year, with a phased approach to ensure smooth transition. The government has allocated substantial funding to support these initiatives.</p>\r\n\r\n<p>\"This reform represents our commitment to building a stronger, more inclusive education system,\" said Prime Minister Johnson during the parliamentary session. \"Education is the foundation of our nation\'s future.\"</p>', 'Parliament passes comprehensive Education Reform Bill with overwhelming support, bringing significant changes to the education system.', 'https://images.unsplash.com/photo-1523050854058-8df90110c9e1?w=800', 8, 2, 'published', 1, 0, 0, '2025-06-24 00:05:03', '2025-06-24 00:05:03', '2025-06-24 00:05:03');

-- --------------------------------------------------------

--
-- Table structure for table `article_tags`
--

CREATE TABLE `article_tags` (
  `article_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `article_tags`
--

INSERT INTO `article_tags` (`article_id`, `tag_id`) VALUES
(1, 1),
(1, 6),
(1, 7),
(2, 2),
(2, 9),
(3, 3),
(3, 8),
(4, 4),
(4, 10),
(5, 5),
(5, 6),
(5, 7);

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `twitter_handle` varchar(50) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`id`, `name`, `email`, `bio`, `profile_image`, `twitter_handle`, `linkedin_url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Editorial Team', 'editor@k5news.com', 'Our dedicated editorial team', NULL, NULL, NULL, 1, '2025-06-24 00:04:33', '2025-06-24 00:04:33'),
(2, 'John Smith', 'john.smith@k5news.com', 'Senior Political Correspondent', NULL, NULL, NULL, 1, '2025-06-24 00:04:33', '2025-06-24 00:04:33'),
(3, 'Maria Garcia', 'maria.garcia@k5news.com', 'Business and Economy Reporter', NULL, NULL, NULL, 1, '2025-06-24 00:04:33', '2025-06-24 00:04:33'),
(4, 'David Chen', 'david.chen@k5news.com', 'Technology and Innovation Writer', NULL, NULL, NULL, 1, '2025-06-24 00:04:33', '2025-06-24 00:04:33');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#007bff',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `color`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Breaking News', 'breaking-news', 'Latest breaking news and urgent updates', '#dc3545', 1, '2025-06-24 00:04:33', '2025-06-24 00:04:33'),
(2, 'Politics', 'politics', 'Political news and government updates', '#007bff', 1, '2025-06-24 00:04:33', '2025-06-24 00:04:33'),
(3, 'Business', 'business', 'Business and economic news', '#28a745', 1, '2025-06-24 00:04:33', '2025-06-24 00:04:33'),
(4, 'Technology', 'technology', 'Technology and innovation news', '#6f42c1', 1, '2025-06-24 00:04:33', '2025-06-24 00:04:33'),
(5, 'Sports', 'sports', 'Sports news and updates', '#fd7e14', 1, '2025-06-24 00:04:33', '2025-06-24 00:04:33'),
(6, 'Entertainment', 'entertainment', 'Entertainment and celebrity news', '#e83e8c', 1, '2025-06-24 00:04:33', '2025-06-24 00:04:33'),
(7, 'Health', 'health', 'Health and medical news', '#20c997', 1, '2025-06-24 00:04:33', '2025-06-24 00:04:33'),
(8, 'Education', 'education', 'Education and academic news', '#17a2b8', 1, '2025-06-24 00:04:33', '2025-06-24 00:04:33'),
(9, 'Lifestyle', 'lifestyle', 'Lifestyle and culture news', '#6c757d', 1, '2025-06-24 00:04:33', '2025-06-24 00:04:33'),
(10, 'International', 'international', 'International news and world events', '#343a40', 1, '2025-06-24 00:04:33', '2025-06-24 00:04:33');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `article_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `article_id`, `name`, `email`, `comment`, `is_approved`, `created_at`) VALUES
(1, 1, 'John Smith', 'john.smith@email.com', 'This is great news! The economic reforms will definitely help small businesses like mine.', 1, '2025-06-24 00:05:03'),
(2, 1, 'Maria Garcia', 'maria.garcia@email.com', 'I hope these changes will create more job opportunities in our area.', 1, '2025-06-24 00:05:03'),
(3, 2, 'Tech Enthusiast', 'tech@email.com', 'The growth in the tech sector is amazing! This is creating so many opportunities for young professionals.', 1, '2025-06-24 00:05:03'),
(4, 3, 'Sports Fan', 'fan@email.com', 'What an incredible game! The team really deserved this victory after such a great season.', 1, '2025-06-24 00:05:03'),
(5, 4, 'Health Worker', 'health@email.com', 'These guidelines are much needed. Thank you for keeping our community safe.', 1, '2025-06-24 00:05:03'),
(6, 5, 'Teacher', 'teacher@email.com', 'As an educator, I\'m excited about these reforms. They will make a real difference in our classrooms.', 1, '2025-06-24 00:05:03'),
(7, 1, 'John Smith', 'john.smith@email.com', 'This is great news! The economic reforms will definitely help small businesses like mine.', 1, '2025-06-24 00:29:07'),
(8, 1, 'Maria Garcia', 'maria.garcia@email.com', 'I hope these changes will create more job opportunities in our area.', 1, '2025-06-24 00:29:07'),
(9, 2, 'Tech Enthusiast', 'tech@email.com', 'The growth in the tech sector is amazing! This is creating so many opportunities for young professionals.', 1, '2025-06-24 00:29:07'),
(10, 3, 'Sports Fan', 'fan@email.com', 'What an incredible game! The team really deserved this victory after such a great season.', 1, '2025-06-24 00:29:07'),
(11, 4, 'Health Worker', 'health@email.com', 'These guidelines are much needed. Thank you for keeping our community safe.', 1, '2025-06-24 00:29:07'),
(12, 5, 'Teacher', 'teacher@email.com', 'As an educator, I\'m excited about these reforms. They will make a real difference in our classrooms.', 1, '2025-06-24 00:29:07');

-- --------------------------------------------------------

--
-- Table structure for table `live_streams`
--

CREATE TABLE `live_streams` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `stream_url` varchar(500) DEFAULT NULL,
  `platform` enum('facebook','youtube','twitch','custom') DEFAULT 'facebook',
  `status` enum('scheduled','live','ended','cancelled') DEFAULT 'scheduled',
  `scheduled_start` timestamp NULL DEFAULT NULL,
  `actual_start` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `current_viewers` int(11) DEFAULT 0,
  `peak_viewers` int(11) DEFAULT 0,
  `total_views` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `live_streams`
--

INSERT INTO `live_streams` (`id`, `title`, `description`, `stream_url`, `platform`, `status`, `scheduled_start`, `actual_start`, `ended_at`, `current_viewers`, `peak_viewers`, `total_views`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 'Breaking News Live Coverage', 'Watch our live coverage of the latest breaking news and developments as they unfold.', 'https://www.facebook.com/plugins/video.php?height=476&href=https%3A%2F%2Fwww.facebook.com%2F100091770458025%2Fvideos%2F703787112276703%2F&show_text=true&width=267&t=0', 'facebook', 'live', '2025-06-23 23:31:05', '2025-06-23 23:31:05', NULL, 1222, 2150, 15432, 1, '2025-06-24 01:31:05', '2025-06-24 05:48:17');

-- --------------------------------------------------------

--
-- Table structure for table `live_stream_updates`
--

CREATE TABLE `live_stream_updates` (
  `id` int(11) NOT NULL,
  `stream_id` int(11) DEFAULT NULL,
  `update_type` enum('breaking','update','alert','info') DEFAULT 'update',
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `live_stream_updates`
--

INSERT INTO `live_stream_updates` (`id`, `stream_id`, `update_type`, `content`, `created_at`) VALUES
(1, 1, 'breaking', 'Major announcement expected from government officials', '2025-06-24 01:31:06'),
(2, 1, 'update', 'Emergency response teams have arrived at the scene', '2025-06-24 01:31:06'),
(3, 1, 'alert', 'Traffic being redirected in downtown area', '2025-06-24 01:31:06'),
(4, 1, 'info', 'Breaking news developing in the city center', '2025-06-24 01:31:06'),
(5, 1, 'update', 'Press conference scheduled for 3:00 PM today', '2025-06-24 01:31:06');

-- --------------------------------------------------------

--
-- Table structure for table `live_stream_viewers`
--

CREATE TABLE `live_stream_viewers` (
  `id` int(11) NOT NULL,
  `stream_id` int(11) DEFAULT NULL,
  `viewer_count` int(11) NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'site_name', 'K5News', 'Website name', '2025-06-24 00:04:33'),
(2, 'site_description', 'Your trusted source for breaking news and in-depth coverage', 'Website description', '2025-06-24 00:04:33'),
(3, 'site_logo', '', 'Website logo URL', '2025-06-24 00:04:33'),
(4, 'contact_email', 'contact@k5news.com', 'Contact email address', '2025-06-24 00:04:33'),
(5, 'social_facebook', '', 'Facebook page URL', '2025-06-24 00:04:33'),
(6, 'social_twitter', '', 'Twitter handle', '2025-06-24 00:04:33'),
(7, 'social_instagram', '', 'Instagram handle', '2025-06-24 00:04:33'),
(8, 'analytics_code', '', 'Google Analytics tracking code', '2025-06-24 00:04:33'),
(9, 'posts_per_page', '10', 'Number of posts to display per page', '2025-06-24 00:04:33');

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Economy', 'economy', '2025-06-24 00:05:03'),
(2, 'Technology', 'technology', '2025-06-24 00:05:03'),
(3, 'Sports', 'sports', '2025-06-24 00:05:03'),
(4, 'Health', 'health', '2025-06-24 00:05:03'),
(5, 'Education', 'education', '2025-06-24 00:05:03'),
(6, 'Policy', 'policy', '2025-06-24 00:05:03'),
(7, 'Reform', 'reform', '2025-06-24 00:05:03'),
(8, 'Championship', 'championship', '2025-06-24 00:05:03'),
(9, 'Growth', 'growth', '2025-06-24 00:05:03'),
(10, 'Safety', 'safety', '2025-06-24 00:05:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_published_at` (`published_at`),
  ADD KEY `idx_is_featured` (`is_featured`),
  ADD KEY `idx_is_breaking_news` (`is_breaking_news`);

--
-- Indexes for table `article_tags`
--
ALTER TABLE `article_tags`
  ADD PRIMARY KEY (`article_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`);

--
-- Indexes for table `live_streams`
--
ALTER TABLE `live_streams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `live_stream_updates`
--
ALTER TABLE `live_stream_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stream_id` (`stream_id`);

--
-- Indexes for table `live_stream_viewers`
--
ALTER TABLE `live_stream_viewers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stream_id` (`stream_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `live_streams`
--
ALTER TABLE `live_streams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `live_stream_updates`
--
ALTER TABLE `live_stream_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `live_stream_viewers`
--
ALTER TABLE `live_stream_viewers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=235;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `article_tags`
--
ALTER TABLE `article_tags`
  ADD CONSTRAINT `article_tags_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `live_stream_updates`
--
ALTER TABLE `live_stream_updates`
  ADD CONSTRAINT `live_stream_updates_ibfk_1` FOREIGN KEY (`stream_id`) REFERENCES `live_streams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `live_stream_viewers`
--
ALTER TABLE `live_stream_viewers`
  ADD CONSTRAINT `live_stream_viewers_ibfk_1` FOREIGN KEY (`stream_id`) REFERENCES `live_streams` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
