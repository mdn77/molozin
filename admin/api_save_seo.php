<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("Unauthorized");
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['json'])) {
    $data = json_decode($_POST['json'], true);
    if (json_last_error() === JSON_ERROR_NONE && isset($data['slug'])) {
        try {
            $stmt = $db->prepare("INSERT INTO seo_landings (slug, h1, title, description, content_top, content_bottom, faq_json) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['slug'],
                $data['h1'] ?? '',
                $data['title'] ?? '',
                $data['description'] ?? '',
                $data['content_top'] ?? '',
                $data['content_bottom'] ?? '',
                isset($data['faq']) ? json_encode($data['faq']) : '[]'
            ]);
            echo "SUCCESS";
        } catch (PDOException $e) {
            echo "DB Error: " . $e->getMessage();
        }
    } else {
        echo "Invalid JSON or missing slug";
    }
} else {
    echo "No data";
}
?>
