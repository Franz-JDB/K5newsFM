<?php
require_once '../includes/db.php';

// Set headers for API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get live streaming data
function getLiveData() {
    global $conn;
    
    // Get current live stream
    $liveStreamSql = "SELECT * FROM live_streams WHERE status = 'live' ORDER BY actual_start DESC LIMIT 1";
    $liveStreamResult = $conn->query($liveStreamSql);
    
    if ($liveStreamResult && $liveStreamResult->num_rows > 0) {
        $stream = $liveStreamResult->fetch_assoc();
        
        // Calculate stream duration
        $streamStart = strtotime($stream['actual_start']);
        $currentTime = time();
        $duration = $currentTime - $streamStart;
        $hours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);
        
        // Get live updates for this stream
        $updatesSql = "SELECT update_type, content, created_at 
                       FROM live_stream_updates 
                       WHERE stream_id = {$stream['id']} 
                       ORDER BY created_at DESC 
                       LIMIT 5";
        $updatesResult = $conn->query($updatesSql);
        $liveUpdates = [];
        
        while ($update = $updatesResult->fetch_assoc()) {
            $prefix = '';
            switch ($update['update_type']) {
                case 'breaking':
                    $prefix = '<strong>Breaking:</strong> ';
                    break;
                case 'alert':
                    $prefix = '<strong>Alert:</strong> ';
                    break;
                case 'info':
                    $prefix = '<strong>Info:</strong> ';
                    break;
                default:
                    $prefix = '<strong>Update:</strong> ';
            }
            
            $liveUpdates[] = [
                'time' => date('g:i A', strtotime($update['created_at'])),
                'content' => $prefix . $update['content']
            ];
        }
        
        // Get total views across all articles (for overall site stats)
        $totalViewsSql = "SELECT SUM(view_count) as total_views FROM articles WHERE status = 'published'";
        $totalViewsResult = $conn->query($totalViewsSql);
        $totalViews = $totalViewsResult->fetch_assoc()['total_views'] ?? 0;
        
        // Simulate viewer fluctuation (in real implementation, this would come from the streaming platform API)
        $baseViewers = $stream['current_viewers'];
        $fluctuation = rand(-50, 50);
        $currentViewers = max(0, $baseViewers + $fluctuation);
        
        // Update current viewers in database
        $conn->query("UPDATE live_streams SET current_viewers = $currentViewers WHERE id = {$stream['id']}");
        
        // Track viewer count for analytics
        $conn->query("INSERT INTO live_stream_viewers (stream_id, viewer_count) VALUES ({$stream['id']}, $currentViewers)");
        
        return [
            'stream_id' => (int)$stream['id'],
            'title' => $stream['title'],
            'description' => $stream['description'],
            'stream_url' => $stream['stream_url'],
            'platform' => $stream['platform'],
            'current_viewers' => (int)$currentViewers,
            'peak_viewers' => (int)$stream['peak_viewers'],
            'total_views' => (int)$totalViews,
            'stream_duration' => [
                'hours' => $hours,
                'minutes' => $minutes,
                'formatted' => sprintf('%dh %dm', $hours, $minutes)
            ],
            'live_updates' => $liveUpdates,
            'stream_status' => $stream['status'],
            'actual_start' => $stream['actual_start'],
            'last_updated' => date('Y-m-d H:i:s')
        ];
    } else {
        // No live stream currently active, return default data
        $totalViewsSql = "SELECT SUM(view_count) as total_views FROM articles WHERE status = 'published'";
        $totalViewsResult = $conn->query($totalViewsSql);
        $totalViews = $totalViewsResult->fetch_assoc()['total_views'] ?? 0;
        
        return [
            'stream_id' => null,
            'title' => 'Breaking News Live Coverage',
            'description' => 'Watch our live coverage of the latest breaking news and developments as they unfold.',
            'stream_url' => 'https://www.facebook.com/plugins/video.php?height=476&href=https%3A%2F%2Fwww.facebook.com%2F100091770458025%2Fvideos%2F703787112276703%2F&show_text=true&width=267&t=0',
            'platform' => 'facebook',
            'current_viewers' => 1247,
            'peak_viewers' => 2150,
            'total_views' => (int)$totalViews,
            'stream_duration' => [
                'hours' => 2,
                'minutes' => 15,
                'formatted' => '2h 15m'
            ],
            'live_updates' => [
                [
                    'time' => date('g:i A'),
                    'content' => '<strong>Live:</strong> K5News live coverage is now active'
                ],
                [
                    'time' => date('g:i A', strtotime('-15 minutes')),
                    'content' => '<strong>Update:</strong> Breaking news developing in the city center'
                ],
                [
                    'time' => date('g:i A', strtotime('-30 minutes')),
                    'content' => '<strong>Alert:</strong> Emergency response teams have been deployed'
                ]
            ],
            'stream_status' => 'live',
            'actual_start' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'last_updated' => date('Y-m-d H:i:s')
        ];
    }
}

// Handle requests
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $data = getLiveData();
        echo json_encode($data, JSON_PRETTY_PRINT);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed'], JSON_PRETTY_PRINT);
        break;
}
?> 