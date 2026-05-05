<?php
$db_file = __DIR__ . '/db.sqlite';
$init_db = !file_exists($db_file);

try {
    $db = new PDO("sqlite:$db_file");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Creates core tables 
    $db->exec("
        CREATE TABLE IF NOT EXISTS leads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            email TEXT,
            phone TEXT,
            service TEXT,
            message TEXT,
            details TEXT,
            ip TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS analytics (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            page TEXT,
            user_agent TEXT,
            screen_res TEXT,
            ip TEXT,
            views INTEGER DEFAULT 1,
            clicks INTEGER DEFAULT 0,
            mouse_distance INTEGER DEFAULT 0,
            scroll_depth INTEGER DEFAULT 0,
            time_spent INTEGER DEFAULT 0,
            last_updated DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS clicks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            page TEXT,
            x INTEGER,
            y INTEGER,
            element TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS blog_posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT,
            slug TEXT UNIQUE,
            content TEXT,
            excerpt TEXT,
            image TEXT,
            seo_title TEXT,
            seo_desc TEXT,
            views INTEGER DEFAULT 0,
            published_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            is_published INTEGER DEFAULT 1,
            category_name TEXT,
            read_time INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS portfolio (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT,
            url TEXT,
            image_url TEXT,
            description TEXT,
            tags TEXT,
            sort_order INTEGER DEFAULT 0
        );
        CREATE TABLE IF NOT EXISTS seo_landings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            slug TEXT UNIQUE,
            h1 TEXT,
            title TEXT,
            description TEXT,
            content_top TEXT,
            content_bottom TEXT,
            faq_json TEXT,
            views INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>
