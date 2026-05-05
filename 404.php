<?php
/**
 * Страница 404 — Файл не найден
 * Самодостаточная страница ошибки в стиле тёмного premium-дизайна сайта.
 *
 * Ключи i18n совпадают с русским текстом — функция __() использует их
 * как fallback, если перевод отсутствует в i18n.php.
 */

require_once __DIR__ . '/i18n.php';

// Заголовок 404
http_response_code(404);

// Тексты через __(); ключ = русский fallback
$t_title    = __('Страница не найдена — 404');
$t_heading  = __('Страница не найдена');
$t_desc     = __('Возможно, страница была удалена, перемещена, или вы набрали неправильный адрес. Давайте найдём то, что вам нужно.');
$t_btn      = __('Вернуться на главную');
$t_blog     = __('Блог');
$t_services = __('Услуги');
$t_portfolio = __('Портфолио');
$t_contacts = __('Контакты');
?><!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, follow">
    <title>404 — <?= htmlspecialchars($t_title) ?></title>

    <!-- Фавиконы (те же, что на всём сайте) -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <meta name="theme-color" content="#131018">

    <!-- Шрифты -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* === 404 — САМОДОСТАТОЧНЫЕ СТИЛИ В ТЁМНОМ PREMIUM-ДИЗАЙНЕ === */
        :root {
            --color-bg: hsl(260, 20%, 8%);
            --color-text: hsl(0, 0%, 95%);
            --color-text-secondary: hsl(0, 0%, 70%);
            --color-primary: hsl(260, 100%, 65%);
            --color-secondary: hsl(200, 100%, 60%);
            --font-primary: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            --font-display: "Space Grotesk", var(--font-primary);
            --radius-xl: 24px;
            --transition-base: 300ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-primary);
            background: var(--color-bg);
            color: var(--color-text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Фоновые градиентные пятна */
        .bg-glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.12;
            pointer-events: none;
            z-index: 0;
        }
        .bg-glow--purple {
            width: 600px; height: 600px;
            background: hsl(260, 100%, 65%);
            top: -200px; left: -200px;
        }
        .bg-glow--blue {
            width: 500px; height: 500px;
            background: hsl(200, 100%, 60%);
            bottom: -200px; right: -150px;
        }

        /* Контейнер */
        .error-wrapper {
            position: relative; z-index: 1;
            text-align: center;
            padding: clamp(2rem, 5vw, 4rem);
            max-width: 700px; width: 90vw;
        }

        /* Гигантский код ошибки */
        .error-code {
            font-family: var(--font-display);
            font-size: clamp(6rem, 15vw, 12rem);
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: clamp(0.5rem, 1vw, 1rem);
            animation: error-pulse 3s ease-in-out infinite;
        }

        @keyframes error-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* Заголовок */
        .error-title {
            font-family: var(--font-display);
            font-size: clamp(1.5rem, 3vw, 2.5rem);
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: 1rem;
        }

        /* Описание */
        .error-desc {
            font-size: clamp(1rem, 1.5vw, 1.2rem);
            color: var(--color-text-secondary);
            line-height: 1.7;
            margin-bottom: 2.5rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Кнопка */
        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 36px;
            font-family: var(--font-primary);
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            border: none;
            border-radius: var(--radius-xl);
            cursor: pointer;
            text-decoration: none;
            transition: transform var(--transition-base), box-shadow var(--transition-base);
            box-shadow: 0 8px 32px hsla(260, 100%, 65%, 0.3);
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 48px hsla(260, 100%, 65%, 0.5);
        }

        .btn-home:active {
            transform: translateY(-1px);
        }

        /* Стрелка */
        .btn-arrow {
            display: inline-block;
            transition: transform var(--transition-base);
        }
        .btn-home:hover .btn-arrow {
            transform: translateX(4px);
        }

        /* Ссылки-подсказки */
        .error-links {
            margin-top: 2.5rem;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.5rem;
        }

        .error-link {
            color: var(--color-text-secondary);
            text-decoration: none;
            font-size: 0.95rem;
            transition: color var(--transition-base);
            border-bottom: 1px solid transparent;
            padding-bottom: 2px;
        }

        .error-link:hover {
            color: var(--color-primary);
            border-bottom-color: var(--color-primary);
        }

        /* Декоративные частицы */
        .particle {
            position: fixed;
            width: 4px; height: 4px;
            border-radius: 50%;
            background: var(--color-primary);
            opacity: 0.4;
            pointer-events: none;
            z-index: 0;
            animation: float-up 8s linear infinite;
        }

        @keyframes float-up {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 0.6; }
            90% { opacity: 0.2; }
            100% { transform: translateY(-100vh) scale(1.5); opacity: 0; }
        }

        /* Адаптивность */
        @media (max-width: 480px) {
            .error-links {
                flex-direction: column;
                gap: 1rem;
            }
            .btn-home {
                padding: 14px 28px;
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>

    <!-- Фоновые градиентные пятна -->
    <div class="bg-glow bg-glow--purple"></div>
    <div class="bg-glow bg-glow--blue"></div>

    <!-- Декоративные частицы -->
    <div class="particle" style="left:10%; animation-delay:0s;"></div>
    <div class="particle" style="left:25%; animation-delay:2s; width:3px; height:3px;"></div>
    <div class="particle" style="left:45%; animation-delay:4s; width:5px; height:5px;"></div>
    <div class="particle" style="left:65%; animation-delay:1s;"></div>
    <div class="particle" style="left:80%; animation-delay:3s; width:3px; height:3px;"></div>
    <div class="particle" style="left:90%; animation-delay:5s; width:6px; height:6px;"></div>

    <div class="error-wrapper">
        <!-- Код ошибки -->
        <div class="error-code">404</div>

        <!-- Заголовок -->
        <h1 class="error-title"><?= htmlspecialchars($t_heading) ?></h1>

        <!-- Описание -->
        <p class="error-desc"><?= htmlspecialchars($t_desc) ?></p>

        <!-- Кнопка возврата -->
        <a href="/" class="btn-home">
            <span><?= htmlspecialchars($t_btn) ?></span>
            <span class="btn-arrow">→</span>
        </a>

        <!-- Навигационные ссылки -->
        <div class="error-links">
            <a href="/blog/" class="error-link"><?= htmlspecialchars($t_blog) ?></a>
            <a href="/#services" class="error-link"><?= htmlspecialchars($t_services) ?></a>
            <a href="/#portfolio" class="error-link"><?= htmlspecialchars($t_portfolio) ?></a>
            <a href="/#contacts" class="error-link"><?= htmlspecialchars($t_contacts) ?></a>
        </div>
    </div>

</body>
</html>
