<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

require '../admin/db.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) exit;

$ip = $_SERVER['REMOTE_ADDR'];
$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
$type = isset($data['type']) ? $data['type'] : '';

if ($type === 'ping') {
    // Обновляем визит (или создаем новый за сессию)
    $page = isset($data['page']) ? $data['page'] : '/';
    $screen = isset($data['screen']) ? $data['screen'] : '';
    $scroll = isset($data['scrollDepth']) ? intval($data['scrollDepth']) : 0;
    
    // Пытаемся найти сессию за последние 10 минут
    $stmt = $db->prepare("SELECT id FROM analytics WHERE ip = ? AND page = ? AND datetime(last_updated) >= datetime('now', '-10 minutes') ORDER BY id DESC LIMIT 1");
    $stmt->execute([$ip, $page]);
    $existing = $stmt->fetchColumn();
    
    if ($existing) {
        $db->prepare("UPDATE analytics SET scroll_depth = MAX(scroll_depth, ?), last_updated = CURRENT_TIMESTAMP WHERE id = ?")->execute([$scroll, $existing]);
    } else {
        $db->prepare("INSERT INTO analytics (page, user_agent, screen_res, ip, scroll_depth) VALUES (?, ?, ?, ?, ?)")->execute([$page, $ua, $screen, $ip, $scroll]);
    }
} elseif ($type === 'click') {
    $page = isset($data['page']) ? $data['page'] : '/';
    $el = isset($data['element']) ? $data['element'] : '';
    $x = isset($data['x']) ? intval($data['x']) : 0;
    $y = isset($data['y']) ? intval($data['y']) : 0;
    
    $db->prepare("INSERT INTO clicks (page, x, y, element) VALUES (?, ?, ?, ?)")->execute([$page, $x, $y, $el]);
}
?>
