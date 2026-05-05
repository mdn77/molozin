<?php
/**
 * Sitemap.xml — динамическая генерация карты сайта
 * 
 * Включает: главную, SEO-лендинги, статьи блога, privacy/terms
 * Формат: XML Sitemap Protocol 0.9
 */
require_once __DIR__ . '/admin/db.php';

header('Content-Type: application/xml; charset=utf-8');

$siteUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Главная
echo "  <url>\n";
echo "    <loc>{$siteUrl}/</loc>\n";
echo "    <changefreq>weekly</changefreq>\n";
echo "    <priority>1.0</priority>\n";
echo "  </url>\n";

try {
    // SEO-лендинги (/uslugi/{slug}/)
    $seoPages = $db->query("SELECT slug FROM seo_landings ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($seoPages as $row) {
        $slug = htmlspecialchars($row['slug'], ENT_XML1, 'UTF-8');
        echo "  <url>\n";
        echo "    <loc>{$siteUrl}/uslugi/{$slug}/</loc>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.8</priority>\n";
        echo "  </url>\n";
    }

    // Блог — список (/blog/)
    echo "  <url>\n";
    echo "    <loc>{$siteUrl}/blog/</loc>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>0.9</priority>\n";
    echo "  </url>\n";

    // Статьи блога (/blog/{slug}/)
    $blogPosts = $db->query("SELECT slug, published_at FROM blog_posts WHERE is_published = 1 ORDER BY published_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($blogPosts as $row) {
        $slug = htmlspecialchars($row['slug'], ENT_XML1, 'UTF-8');
        $lastmod = date('Y-m-d', strtotime($row['published_at']));
        echo "  <url>\n";
        echo "    <loc>{$siteUrl}/blog/{$slug}/</loc>\n";
        echo "    <lastmod>{$lastmod}</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.7</priority>\n";
        echo "  </url>\n";
    }

    // Privacy и Terms
    echo "  <url>\n";
    echo "    <loc>{$siteUrl}/privacy/</loc>\n";
    echo "    <changefreq>yearly</changefreq>\n";
    echo "    <priority>0.5</priority>\n";
    echo "  </url>\n";

    echo "  <url>\n";
    echo "    <loc>{$siteUrl}/terms/</loc>\n";
    echo "    <changefreq>yearly</changefreq>\n";
    echo "    <priority>0.5</priority>\n";
    echo "  </url>\n";

} catch (PDOException $e) {
    echo "<!-- Sitemap generation error: " . htmlspecialchars($e->getMessage()) . " -->\n";
}

echo '</urlset>';
