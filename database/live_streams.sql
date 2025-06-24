-- Live Streams table for tracking live broadcasts
USE k5news_db;

-- Live streams table
CREATE TABLE IF NOT EXISTS live_streams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    stream_url VARCHAR(500),
    platform ENUM('facebook', 'youtube', 'twitch', 'custom') DEFAULT 'facebook',
    status ENUM('scheduled', 'live', 'ended', 'cancelled') DEFAULT 'scheduled',
    scheduled_start TIMESTAMP NULL,
    actual_start TIMESTAMP NULL,
    ended_at TIMESTAMP NULL,
    current_viewers INT DEFAULT 0,
    peak_viewers INT DEFAULT 0,
    total_views INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_scheduled_start (scheduled_start),
    INDEX idx_actual_start (actual_start)
);

-- Live stream updates table for real-time updates
CREATE TABLE IF NOT EXISTS live_stream_updates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stream_id INT,
    update_type ENUM('breaking', 'update', 'alert', 'info') DEFAULT 'update',
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (stream_id) REFERENCES live_streams(id) ON DELETE CASCADE,
    INDEX idx_stream_id (stream_id),
    INDEX idx_created_at (created_at)
);

-- Live stream viewers tracking (for analytics)
CREATE TABLE IF NOT EXISTS live_stream_viewers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stream_id INT,
    viewer_count INT NOT NULL,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (stream_id) REFERENCES live_streams(id) ON DELETE CASCADE,
    INDEX idx_stream_id (stream_id),
    INDEX idx_recorded_at (recorded_at)
);

-- Insert sample live stream data
INSERT INTO live_streams (title, description, stream_url, platform, status, scheduled_start, actual_start, current_viewers, peak_viewers, total_views, is_featured) VALUES
('Breaking News Live Coverage', 'Watch our live coverage of the latest breaking news and developments as they unfold.', 
'https://www.facebook.com/plugins/video.php?height=476&href=https%3A%2F%2Fwww.facebook.com%2F100091770458025%2Fvideos%2F703787112276703%2F&show_text=true&width=267&t=0',
'facebook', 'live', 
DATE_SUB(NOW(), INTERVAL 2 HOUR), 
DATE_SUB(NOW(), INTERVAL 2 HOUR), 
1247, 2150, 15432, 1);

-- Insert sample live updates
INSERT INTO live_stream_updates (stream_id, update_type, content) VALUES
(1, 'breaking', 'Major announcement expected from government officials'),
(1, 'update', 'Emergency response teams have arrived at the scene'),
(1, 'alert', 'Traffic being redirected in downtown area'),
(1, 'info', 'Breaking news developing in the city center'),
(1, 'update', 'Press conference scheduled for 3:00 PM today');

-- Insert sample viewer data
INSERT INTO live_stream_viewers (stream_id, viewer_count) VALUES
(1, 1200),
(1, 1247),
(1, 1180),
(1, 1320),
(1, 1250); 