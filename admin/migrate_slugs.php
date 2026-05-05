<?php
/**
 * Миграция slug'ов блога на человекопонятные ЧПУ
 * 
 * Запуск: php admin/migrate_slugs.php
 * Или через браузер: https://molozin.ru/admin/migrate_slugs.php
 * 
 * Что делает:
 * 1. Проходит по всем записям blog_posts
 * 2. Если slug содержит "gen-" или не соответствует формату ЧПУ — генерирует новый из title
 * 3. Транслитерация кириллицы → латиница
 * 4. Сохраняет старый slug в поле old_slug (если есть) или логирует
 */

require_once __DIR__ . '/db.php';

// Таблица транслитерации кириллица → латиница
function transliterate($str) {
    $map = [
        'а' => 'a',  'б' => 'b',  'в' => 'v',  'г' => 'g',
        'д' => 'd',  'е' => 'e',  'ё' => 'yo', 'ж' => 'zh',
        'з' => 'z',  'и' => 'i',  'й' => 'j',  'к' => 'k',
        'л' => 'l',  'м' => 'm',  'н' => 'n',  'о' => 'o',
        'п' => 'p',  'р' => 'r',  'с' => 's',  'т' => 't',
        'у' => 'u',  'ф' => 'f',  'х' => 'kh', 'ц' => 'ts',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch',
        'ъ' => '',   'ы' => 'y',  'ь' => '',
        'э' => 'e',  'ю' => 'yu', 'я' => 'ya',
        'А' => 'A',  'Б' => 'B',  'В' => 'V',  'Г' => 'G',
        'Д' => 'D',  'Е' => 'E',  'Ё' => 'Yo', 'Ж' => 'Zh',
        'З' => 'Z',  'И' => 'I',  'Й' => 'J',  'К' => 'K',
        'Л' => 'L',  'М' => 'M',  'Н' => 'N',  'О' => 'O',
        'П' => 'P',  'Р' => 'R',  'С' => 'S',  'Т' => 'T',
        'У' => 'U',  'Ф' => 'F',  'Х' => 'Kh', 'Ц' => 'Ts',
        'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Shch',
        'Ъ' => '',   'Ы' => 'Y',  'Ь' => '',
        'Э' => 'E',  'Ю' => 'Yu', 'Я' => 'Ya',
    ];

    $result = strtr($str, $map);
    return $result;
}

function generateSlug($title) {
    // Транслитерация
    $slug = transliterate($title);

    // В нижний регистр
    $slug = mb_strtolower($slug, 'UTF-8');

    // Замена всех не-латинских символов на дефис
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

    // Удаление начальных и конечных дефисов
    $slug = trim($slug, '-');

    // Удаление повторяющихся дефисов
    $slug = preg_replace('/-+/', '-', $slug);

    // Обрезка до 80 символов
    if (mb_strlen($slug) > 80) {
        $slug = mb_substr($slug, 0, 80);
        // Обрезаем до последнего полного слова (до дефиса)
        $lastDash = mb_strrpos($slug, '-');
        if ($lastDash > 40) {
            $slug = mb_substr($slug, 0, $lastDash);
        }
    }

    return $slug;
}

// Проверяем, есть ли колонка old_slug, если нет — добавляем
try {
    $db->query("SELECT old_slug FROM blog_posts LIMIT 1");
} catch (Exception $e) {
    echo "Добавляю колонку old_slug в blog_posts...\n";
    $db->exec("ALTER TABLE blog_posts ADD COLUMN old_slug TEXT");
}

// Получаем все статьи
$articles = $db->query("SELECT id, title, slug FROM blog_posts")->fetchAll(PDO::FETCH_ASSOC);

$updated = 0;
$skipped = 0;
$errors = [];

echo "=== Миграция slug'ов блога ===\n";
echo "Найдено статей: " . count($articles) . "\n\n";

foreach ($articles as $article) {
    $id = $article['id'];
    $oldSlug = $article['slug'];
    $title = $article['title'];

    // Проверяем, нужна ли миграция
    // Мигрируем если: slug начинается с "gen-" или содержит хеш-подобные паттерны
    $needsMigration = false;

    if (strpos($oldSlug, 'gen-') === 0) {
        $needsMigration = true;
    } elseif (preg_match('/[а-яё]/iu', $oldSlug)) {
        // Содержит кириллицу — нужно перегенерировать
        $needsMigration = true;
    } elseif (preg_match('/^[a-z0-9-]+$/', $oldSlug) && mb_strlen($oldSlug) <= 80) {
        // Уже валидный slug — пропускаем
        $needsMigration = false;
    }

    if (!$needsMigration) {
        $skipped++;
        continue;
    }

    // Генерируем новый slug
    $newSlug = generateSlug($title);

    // Проверяем уникальность
    $checkStmt = $db->prepare("SELECT id FROM blog_posts WHERE slug = ? AND id != ?");
    $checkStmt->execute([$newSlug, $id]);
    if ($checkStmt->fetch()) {
        // Slug не уникален — добавляем суффикс
        $newSlug = $newSlug . '-' . $id;
    }

    // Обновляем запись
    try {
        $stmt = $db->prepare("UPDATE blog_posts SET slug = ?, old_slug = ? WHERE id = ?");
        $stmt->execute([$newSlug, $oldSlug, $id]);
        echo "✓ [ID:{$id}] «{$title}»\n";
        echo "  Старый: {$oldSlug}\n";
        echo "  Новый:  {$newSlug}\n\n";
        $updated++;
    } catch (Exception $e) {
        $errors[] = "✗ [ID:{$id}] Ошибка: " . $e->getMessage();
        echo "✗ [ID:{$id}] Ошибка: " . $e->getMessage() . "\n";
    }
}

echo "=== Результаты ===\n";
echo "Обновлено: {$updated}\n";
echo "Пропущено (уже ЧПУ): {$skipped}\n";
echo "Ошибок: " . count($errors) . "\n";

if ($updated > 0) {
    echo "\n✅ Миграция завершена. Slug'и обновлены.\n";
    echo "⚠️ После миграции проверьте сайт: старые URL будут делать 301 редирект на новые.\n";
}
