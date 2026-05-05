<?php
require_once __DIR__ . '/admin/db.php';

header('Content-Type: application/xml; charset=utf-8');
$siteUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

$langs = ['ru', 'en', 'zh', 'es', 'hi', 'ar', 'bn', 'pt', 'ja', 'pa'];

function addHreflangLinks($baseUrl, $langs) {
    $out = '';
    foreach ($langs as $l) {
        $out .= '    <xhtml:link rel="alternate" hreflang="' . $l . '" href="' . $baseUrl . '?lang=' . $l . '" />' . "\n";
    }
    $out .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . $baseUrl . '" />' . "\n";
    return $out;
}

$staticPages = ['/'];
foreach ($staticPages as $page) {
    echo "  <url>\n    <loc>{$siteUrl}{$page}</loc>\n" . addHreflangLinks("{$siteUrl}{$page}", $langs) . "    <changefreq>weekly</changefreq>\n    <priority>1.0</priority>\n  </url>\n";
}

try {
    $stmt = $db->query("SELECT slug FROM seo_landings ORDER BY id DESC");
    $seo_pages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($seo_pages as $row) {
        $slug = htmlspecialchars($row['slug'], ENT_XML1, 'UTF-8');
        echo "  <url>\n    <loc>{$siteUrl}/uslugi/{$slug}/</loc>\n    <changefreq>daily</changefreq>\n    <priority>0.8</priority>\n  </url>\n";
    }

    echo "  <url>\n    <loc>{$siteUrl}/blog.php</loc>\n    <changefreq>daily</changefreq>\n    <priority>0.9</priority>\n  </url>\n";

    $stmt2 = $db->query("SELECT slug FROM blog_posts WHERE is_published = 1 ORDER BY published_at DESC");
    $blog_posts = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    foreach ($blog_posts as $row) {
        $slug = htmlspecialchars($row['slug'], ENT_XML1, 'UTF-8');
        echo "  <url>\n    <loc>{$siteUrl}/blog.php?article={$slug}</loc>\n    <changefreq>monthly</changefreq>\n    <priority>0.7</priority>\n  </url>\n";
    }

} catch (PDOException $e) {
    echo "<!-- Sitemap generation error: " . htmlspecialchars($e->getMessage()) . " -->\n";
}

echo '</urlset>';
?>
