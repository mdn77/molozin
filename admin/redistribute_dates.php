<?php
/**
 * Скрипт равномерного распределения дат публикаций блога
 * Распределяет 155 постов от 2025-11-01 до 2026-05-11 (сегодня)
 * по одному посту в день с равными интервалами.
 */

require_once __DIR__ . '/db.php';

// --- НАСТРОЙКИ ---
$startDate = '2025-11-01 10:00:00';
$endDate   = '2026-05-11 10:00:00';

// --- 1. Получаем все опубликованные посты, сортируем по текущей дате ---
$stmt = $db->query(
    "SELECT id, title, published_at FROM blog_posts WHERE is_published = 1 ORDER BY published_at ASC"
);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total = count($posts);

echo "Найдено опубликованных постов: $total\n";

if ($total === 0) {
    die("Нет постов для обработки.\n");
}

// --- 2. Вычисляем интервал между постами (в секундах) ---
$startTs = strtotime($startDate);
$endTs   = strtotime($endDate);
$rangeSeconds = $endTs - $startTs;

if ($total === 1) {
    $interval = 0;
} else {
    $interval = floor($rangeSeconds / ($total - 1));
}

echo "Диапазон: $startDate → $endDate\n";
echo "Всего секунд: $rangeSeconds, интервал: $interval сек (≈ " . round($interval / 86400, 2) . " дней)\n";

// --- 3. Назначаем новые даты ---
$db->beginTransaction();
$updateStmt = $db->prepare("UPDATE blog_posts SET published_at = :new_date WHERE id = :id");

for ($i = 0; $i < $total; $i++) {
    $newTs = $startTs + ($i * $interval);
    $newDate = date('Y-m-d H:i:s', $newTs);
    
    $updateStmt->execute([
        ':new_date' => $newDate,
        ':id'       => $posts[$i]['id']
    ]);
    
    // Показываем первые 5 и последние 5 для проверки
    if ($i < 5 || $i >= $total - 5) {
        $oldDate = $posts[$i]['published_at'];
        $title = mb_substr($posts[$i]['title'], 0, 60);
        echo "[$i] ID={$posts[$i]['id']} | $oldDate → $newDate | $title\n";
    }
}

$db->commit();
echo "\n✅ Готово! Обновлено $total постов.\n";
echo "Резервная копия: db.sqlite.backup_2026-05-11\n";
