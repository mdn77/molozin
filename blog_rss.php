<?php
/**
 * RSS 2.0 фид блога
 * 
 * Доступ: /blog/rss.xml (через .htaccess RewriteRule)
 * Содержит последние 20 опубликованных статей
 */
require_once __DIR__ . '/admin/db.php';

header('Content-Type: application/rss+xml; charset=utf-8');

$siteUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];

// Получаем последние 20 статей
try {
    $articles = $db->query(
        "SELECT title, slug, excerpt, content, seo_desc, published_at 
         FROM blog_posts 
         WHERE is_published = 1 
         ORDER BY published_at DESC 
         LIMIT 20"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $articles = [];
}

// Экранирование для XML
function xml_escape($str) {
    return htmlspecialchars($str, ENT_XML1, 'UTF-8');
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0" 
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:content="http://purl.org/rss/1.0/modules/content/">
  <channel>
    <title>Блог Molozin.ru — Веб-разработка и продвижение</title>
    <link><?= xml_escape($siteUrl) ?>/blog/</link>
    <description>Экспертные статьи по веб-разработке, продвижению, SaaS-инструментам и росту бизнеса от студии Molozin.ru.</description>
    <language>ru</language>
    <lastBuildDate><?= date('r') ?></lastBuildDate>
    <generator>Molozin.ru Blog Engine</generator>
    <atom:link href="<?= xml_escape($siteUrl) ?>/blog/rss.xml" rel="self" type="application/rss+xml"/>

    <?php foreach ($articles as $article): 
        $link = $siteUrl . '/blog/' . urlencode($article['slug']) . '/';
        $pubDate = date('r', strtotime($article['published_at']));
        $description = xml_escape($article['seo_desc'] ?? $article['excerpt'] ?? '');
        $content = xml_escape($article['content'] ?? '');
    ?>
    <item>
      <title><?= xml_escape($article['title']) ?></title>
      <link><?= xml_escape($link) ?></link>
      <guid isPermaLink="true"><?= xml_escape($link) ?></guid>
      <pubDate><?= $pubDate ?></pubDate>
      <description><?= $description ?></description>
      <content:encoded><![CDATA[<?= $content ?>]]></content:encoded>
    </item>
    <?php endforeach; ?>

  </channel>
</rss>
