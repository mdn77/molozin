<?php
session_start();
require 'auth.php';

if (!isset($_SESSION['admin_logged_in'])) {
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в панель управления Molozin.ru</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #0d0f1a; color: #fff; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: #1a1c29; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.5); width: 100%; max-width: 400px; text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 6px; border: 1px solid #333; background: #000; color: white; box-sizing: border-box; }
        button { background: linear-gradient(135deg, hsl(260, 100%, 65%), hsl(200, 100%, 60%)); color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Molozin.ru Admin</h2>
        <form method="POST">
            <input type="password" name="password" placeholder="Пароль" required autofocus>
            <button type="submit">Вход</button>
        </form>
    </div>
</body>
</html>
<?php
    exit;
}

require 'db.php';

$page = isset($_GET['p']) ? $_GET['p'] : 'dashboard';

// Simple Router
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель управления Molozin.ru</title>
    <style>
        :root { --bg: #0d0f1a; --surface: #1a1c29; --primary: hsl(260, 100%, 65%); --text: #fff; --text-sec: #aaa; --border: #333; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: var(--surface); border-right: 1px solid var(--border); padding: 2rem 1rem; display: flex; flex-direction: column; gap: 10px;}
        .sidebar h2 { margin-top: 0; font-size: 1.2rem; color: var(--primary); margin-bottom: 2rem;}
        .sidebar a { color: var(--text-sec); text-decoration: none; padding: 10px; border-radius: 6px; transition: 0.3s background; }
        .sidebar a:hover, .sidebar a.active { background: hsla(260, 100%, 65%, 0.1); color: var(--primary); }
        .main { flex: 1; padding: 2rem; overflow-y: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: var(--surface); border-radius: 8px; overflow: hidden; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid var(--border); }
        th { background: rgba(255,255,255,0.05); font-weight: 500; color: var(--text-sec); }
        tr:hover { background: rgba(255,255,255,0.02); }
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;}
        .card { background: var(--surface); padding: 20px; border-radius: 8px; border: 1px solid var(--border); }
        .card h3 { margin: 0 0 10px 0; color: var(--text-sec); font-size: 0.9rem;}
        .card .val { font-size: 2rem; font-weight: bold; color: var(--primary); }
        input, select, textarea { width: 100%; padding: 10px; margin: 10px 0; border-radius: 4px; border: 1px solid var(--border); background: var(--bg); color: white; box-sizing: border-box;}
        button { background: var(--primary); color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Molozin.ru CMS</h2>
        <a href="?p=dashboard" class="<?= $page == 'dashboard' ? 'active' : '' ?>">Главная / Аналитика</a>
        <a href="?p=leads" class="<?= $page == 'leads' ? 'active' : '' ?>">Заявки (Лиды)</a>
        <a href="?p=portfolio" class="<?= $page == 'portfolio' ? 'active' : '' ?>">Портфолио</a>
        <a href="?p=seo" class="<?= $page == 'seo' ? 'active' : '' ?>">SEO Услуги (Города)</a>
        <a href="?p=translations" class="<?= $page == 'translations' ? 'active' : '' ?>">Переводы (Тексты)</a>
        <a href="?p=blog" class="<?= $page == 'blog' ? 'active' : '' ?>">Блог / SEO статьи</a>
        <a href="?logout=1" style="margin-top:auto; color: #ff5555;">Выйти</a>
    </div>
    
    <div class="main">
        <?php if ($page == 'dashboard'): 
            $views = $db->query("SELECT SUM(views) FROM analytics")->fetchColumn() ?: 0;
            $clicks = $db->query("SELECT COUNT(*) FROM clicks")->fetchColumn() ?: 0;
            $leads = $db->query("SELECT COUNT(*) FROM leads")->fetchColumn() ?: 0;
            $analytics = $db->query("SELECT * FROM analytics ORDER BY last_updated DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
        ?>
            <h1>Дичайшая Аналитика 🚀</h1>
            <div class="card-grid">
                <div class="card"><h3>Всего просмотров</h3><div class="val"><?= $views ?></div></div>
                <div class="card"><h3>Крайне важных кликов</h3><div class="val"><?= $clicks ?></div></div>
                <div class="card"><h3>Заявок</h3><div class="val"><?= $leads ?></div></div>
            </div>
            
            <h2>Последние заходы</h2>
            <table>
                <tr><th>Страница</th><th>IP</th><th>Просмотры</th><th>ОС / Браузер</th><th>Разрешение</th><th>Скролл (%)</th><th>Обновлено</th></tr>
                <?php foreach ($analytics as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['page']) ?></td>
                    <td><?= htmlspecialchars($row['ip']) ?></td>
                    <td><?= $row['views'] ?></td>
                    <td style="font-size: 0.8em; max-width: 200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= htmlspecialchars($row['user_agent']) ?>"><?= htmlspecialchars($row['user_agent']) ?></td>
                    <td><?= htmlspecialchars($row['screen_res']) ?></td>
                    <td><?= $row['scroll_depth'] ?>%</td>
                    <td><?= $row['last_updated'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($page == 'leads'): 
            $leads = $db->query("SELECT * FROM leads ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
        ?>
            <h1>Заявки с сайта</h1>
            <table>
                <tr><th>Дата</th><th>Имя</th><th>Контакты</th><th>Услуга</th><th>Тех. Инфо</th></tr>
                <?php foreach ($leads as $row): ?>
                <tr>
                    <td><?= $row['created_at'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?><br><?= htmlspecialchars($row['phone']) ?></td>
                    <td><strong><?= htmlspecialchars($row['service']) ?></strong><br><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                    <td style="font-size:0.8em; color:#aaa;"><?= nl2br(htmlspecialchars($row['details'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($page == 'portfolio'): 
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
                if ($_POST['action'] === 'add') {
                    $stmt = $db->prepare("INSERT INTO portfolio (title, url, image_url, description, tags, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$_POST['title'], $_POST['url'], $_POST['image_url'], $_POST['description'], $_POST['tags'], (int)$_POST['sort_order']]);
                } elseif ($_POST['action'] === 'delete') {
                    $stmt = $db->prepare("DELETE FROM portfolio WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                }
                header("Location: ?p=portfolio");
                exit;
            }
            $portfolio = $db->query("SELECT * FROM portfolio ORDER BY sort_order ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
        ?>
            <h1>Управление Портфолио</h1>
            
            <div class="card" style="margin-bottom: 20px;">
                <h3>Добавить новый проект</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="text" name="title" placeholder="Название проекта (например, Arenda.R-70.ru)" required>
                    <input type="url" name="url" placeholder="Ссылка на проект (например, https://arenda.r-70.ru)" required>
                    <input type="url" name="image_url" placeholder="Прямая ссылка на скриншот (например, https://s0.wp.com/mshots/v1/https://arenda.r-70.ru?w=600&h=450)" required>
                    <textarea name="description" placeholder="Описание проекта" rows="3" required></textarea>
                    <input type="text" name="tags" placeholder="Теги (через запятую, например: Недвижимость, Букинг)" required>
                    <input type="number" name="sort_order" placeholder="Порядок сортировки (0, 1, 2...)" value="0">
                    <button type="submit" style="margin-top:10px;">Добавить в портфолио</button>
                </form>
            </div>

            <table>
                <tr><th>Скриншот</th><th>Название / URL</th><th>Описание / Теги</th><th>Сортировка</th><th>Действия</th></tr>
                <?php foreach ($portfolio as $row): ?>
                <tr>
                    <td><img src="<?= htmlspecialchars($row['image_url']) ?>" style="width: 120px; border-radius: 6px; border: 1px solid var(--border);"></td>
                    <td>
                        <strong><?= htmlspecialchars($row['title']) ?></strong><br>
                        <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank" style="color:var(--primary); font-size: 0.9em;"><?= htmlspecialchars($row['url']) ?></a>
                    </td>
                    <td style="font-size: 0.9em; max-width: 300px;">
                        <?= htmlspecialchars($row['description']) ?><br>
                        <span style="color:var(--primary); font-size:0.8em; margin-top:5px; display:inline-block;">Теги: <?= htmlspecialchars($row['tags']) ?></span>
                    </td>
                    <td><?= $row['sort_order'] ?></td>
                    <td>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Точно удалить?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit" style="background:#ff5555; padding:8px 12px; font-size:0.9em;">Удалить</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($portfolio)): ?>
                <tr><td colspan="5" style="text-align:center; padding: 20px;">Портфолио пусто. Добавьте первый проект!</td></tr>
                <?php endif; ?>
            </table>
        <?php elseif ($page == 'translations'): 
            require_once '../i18n.php';
            $custom_file = '../i18n_custom.json';
            $cache_file = '../i18n_cache.json';
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_translations') {
                $custom = file_exists($custom_file) ? json_decode(file_get_contents($custom_file), true) ?: [] : [];
                $cache = file_exists($cache_file) ? json_decode(file_get_contents($cache_file), true) ?: [] : [];
                
                if (isset($_POST['texts']) && is_array($_POST['texts'])) {
                    foreach ($_POST['texts'] as $key => $value) {
                        $old_value = $translates['ru'][$key] ?? '';
                        // Если значение изменили (или добавили)
                        if ($value !== $old_value && trim($value) !== '') {
                            $custom[$key] = $value;
                            // Очищаем этот ключ из кэша автопереводчика для всех языков,
                            // чтобы при следующем открытии сайта скрипт скачал новый перевод с Google
                            foreach ($cache as $l => $c) {
                                if (isset($cache[$l][$key])) unset($cache[$l][$key]);
                            }
                        }
                    }
                }
                
                // Добавление полностью нового ключа
                if (!empty($_POST['new_key']) && !empty($_POST['new_value'])) {
                    $custom[$_POST['new_key']] = $_POST['new_value'];
                }
                
                file_put_contents($custom_file, json_encode($custom, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                file_put_contents($cache_file, json_encode($cache, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                
                header("Location: ?p=translations&saved=1");
                exit;
            }
        ?>
            <h1>Управление текстами сайта</h1>
            <p style="color: var(--text-sec); margin-bottom: 20px;">Здесь вы можете изменить любой текст на сайте (на Русском языке).<br>
            Система <b>автоматически переведет его заново</b> на все языки (китайский, английский, испанский и т.д.) через Google API.</p>
            
            <?php if(isset($_GET['saved'])): ?>
                <div style="background: rgba(46, 204, 113, 0.2); border: 1px solid #2ecc71; color: #2ecc71; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    ✅ Тексты успешно сохранены и отправлены на автоперевод! Обновите главную страницу сайта, чтобы увидеть результат.
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="action" value="save_translations">
                
                <div class="card" style="margin-bottom: 30px;">
                    <h3 style="color: var(--primary);">Добавить новый блок текста (Новый ключ)</h3>
                    <div style="display:flex; gap:10px; flex-wrap: wrap;">
                        <input type="text" name="new_key" placeholder="Системное имя (например: my_super_banner)" style="flex:1; min-width: 200px;">
                        <input type="text" name="new_value" placeholder="Сам текст (на русском)" style="flex:2; min-width: 300px;">
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom: 20px;">Все тексты на сайте</h3>
                    <?php 
                        $all_ru_keys = $translates['ru'];
                        foreach ($all_ru_keys as $key => $val):
                    ?>
                        <div style="margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 15px;">
                            <label style="color: var(--primary); font-size: 0.85rem; display:block; margin-bottom: 8px; font-weight: bold;"><?= htmlspecialchars($key) ?></label>
                            <?php if(strlen($val) > 70 || strpos($val, "\n") !== false || strpos($val, "<br>") !== false): ?>
                                <textarea name="texts[<?= htmlspecialchars($key) ?>]" rows="3" style="font-family: inherit; font-size: 0.95rem; line-height: 1.4;"><?= htmlspecialchars($val) ?></textarea>
                            <?php else: ?>
                                <input type="text" name="texts[<?= htmlspecialchars($key) ?>]" value="<?= htmlspecialchars($val) ?>" style="font-family: inherit; font-size: 0.95rem;">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="position: sticky; bottom: 20px; background: var(--surface); padding: 20px; border: 1px solid var(--primary); border-radius: 8px; margin-top:30px; text-align: center; box-shadow: 0 -4px 15px rgba(0,0,0,0.5);">
                    <button type="submit" style="font-size: 1.1rem; padding: 15px 40px; text-transform: uppercase; letter-spacing: 1px;">Сохранить изменения и Автоперевести</button>
                    <p style="margin: 10px 0 0 0; color: var(--text-sec); font-size: 0.8rem;">Внимание: после сохранения все переводы на китайский, испанский и др. языки сгенерируются автоматически в течение пары секунд при первом заходе клиента!</p>
                </div>
            </form>
            
        <?php elseif ($page == 'blog'): ?>
            <h1>Блог и SEO Статьи (В разработке)</h1>
            <p>Генерация десятков статей, мета-тегов и управление картой сайта.</p>

        <?php elseif ($page == 'seo'): 
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
                if ($_POST['action'] === 'add') {
                    $stmt = $db->prepare("INSERT INTO seo_landings (slug, h1, title, description, content_top, content_bottom) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$_POST['slug'], $_POST['h1'], $_POST['title'], $_POST['description'], $_POST['content_top'], $_POST['content_bottom']]);
                } elseif ($_POST['action'] === 'delete') {
                    $stmt = $db->prepare("DELETE FROM seo_landings WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                }
                header("Location: ?p=seo");
                exit;
            }
            $seo_pages = $db->query("SELECT * FROM seo_landings ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        ?>
            <h1>Управление SEO-Лендингами</h1>
            
            <div class="card" style="margin-bottom: 20px; border-color: #0ea5e9;">
                <h3 style="color:#0ea5e9;">🤖 AI-Автоматизация (Массовая генерация 300+ страниц)</h3>
                <p style="color:var(--text-sec); font-size: 0.9em; margin-bottom: 15px;">Введите запросы (Город + Ниша) с новой строки. Нейросеть (ChatGPT/Qwen) сгенерирует идеальные продающие тексты, мета-теги и сложит их в базу.</p>
                <div>
                    <input type="text" id="ai_api_url" value="https://api.vsegpt.ru/v1/chat/completions" placeholder="API Endpoint (или ProxyAPI, OpenRouter, Qwen)">
                    <input type="password" id="ai_api_key" placeholder="Ваш API Key (Bearer Token)" value="sk-or-vv-e9efaa29b8fe1131d0573a2813102d5af26c3e2fb07f3a458772c38585a5a126">
                    <input type="text" id="ai_api_model" value="openai/gpt-4o-mini" placeholder="Модель (gpt-4o-mini, qwen-turbo и т.д.)">
                    
                    <div style="margin: 15px 0; background: hsla(200, 100%, 60%, 0.1); padding: 15px; border-radius: 8px; border: 1px solid rgba(14, 165, 233, 0.3);">
                        <h4 style="margin-top:0; color:#0ea5e9;">🚀 Режим "Автопилот" (Ниши × Города)</h4>
                        <p style="font-size: 0.8em; color: var(--text-sec); margin-bottom: 10px;">Нажмите кнопку ниже, чтобы автоматически создать 150 посадочных страниц (10 лучших ниш × 15 крупнейших городов РФ) и сразу запустить их генерацию.</p>
                        <button onclick="generateMatrix()" type="button" style="background:#1e293b; border: 1px solid #0ea5e9; color: #fff; width: 100%;">Сгенерировать Матрицу (150 страниц) и ЗАПУСТИТЬ</button>
                    </div>

                    <textarea id="ai_keywords" placeholder="Разработка сайтов в Новосибирске&#10;Купить сайт интернет-магазина для клиники в Москве" rows="4">Разработка сайтов в Новосибирске
Купить сайт интернет-магазина для клиники в Москве</textarea>
                    
                    <button onclick="startAIGen()" id="ai_btn" style="background:#0ea5e9; margin-top:10px; width: 100%;">Запустить AI-Генерацию площадок</button>
                    <div id="ai_log" style="margin-top:15px; background:#000; padding:10px; border-radius:4px; font-family:monospace; font-size:0.85em; color:#0f0; max-height:200px; overflow-y:auto; display:none;"></div>
                </div>
            </div>
            
            <script>
            function generateMatrix() {
                const cities = ['Москве', 'Санкт-Петербурге', 'Новосибирске', 'Екатеринбурге', 'Казани', 'Нижнем Новгороде', 'Красноярске', 'Челябинске', 'Уфе', 'Ростове-на-Дону', 'Краснодаре', 'Омске', 'Воронеже', 'Перми', 'Волгограде'];
                const niches = ['строительной компании', 'ремонта квартир', 'медицинского центра', 'стоматологии', 'косметологии', 'автосервиса', 'юридической компании', 'бухгалтерских услуг', 'клининговой компании', 'доставки еды'];
                
                let queries = [];
                for(let c of cities) {
                    for(let n of niches) {
                        queries.push(`Сайт для ${n} в ${c}`);
                    }
                }
                
                document.getElementById('ai_keywords').value = queries.join('\n');
                startAIGen(); // сразу запускаем
            }

            async function startAIGen() {
                const keys = document.getElementById('ai_keywords').value.split('\n').map(k => k.trim()).filter(k => k);
                const url = document.getElementById('ai_api_url').value;
                const apiKey = document.getElementById('ai_api_key').value;
                const model = document.getElementById('ai_api_model').value;
                const log = document.getElementById('ai_log');
                const btn = document.getElementById('ai_btn');
                
                if(!keys.length || !url || !apiKey) {
                    alert('Заполните API Key, URL и хотя бы 1 ключевую фразу.');
                    return;
                }
                
                btn.disabled = true;
                log.style.display = 'block';
                log.innerHTML = 'Запуск...<br>';
                
                const sysPrompt = "Опытный SEO-маркетолог и копирайтер. Создай посадочную страницу для веб-студии 'Molozin.ru'. Тон: премиальный, убедительный. ОСТРОЖНО: ВЕРНИ ТОЛЬКО И ИСКЛЮЧИТЕЛЬНО VALID JSON в формате:\n{\"slug\": \"url-alias-latin\", \"h1\": \"H1\", \"title\": \"SEO Title\", \"description\": \"SEO Description\", \"content_top\": \"Hero Subtitle\", \"content_bottom\": \"SEO Text HTML\", \"faq\": [{\"q\":\"Question?\", \"a\":\"Answer.\"}]} (3 вопроса в FAQ).";

                for(let i=0; i<keys.length; i++) {
                    const k = keys[i];
                    log.innerHTML += `[${i+1}/${keys.length}] Генерация: <b>${k}</b>... `;
                    try {
                        const response = await fetch(url, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "Authorization": "Bearer " + apiKey
                            },
                            body: JSON.stringify({
                                model: model,
                                // response_format is optional, some providers crash on it, so omit for safe compatibility, prompt is strict enough
                                messages: [
                                    {role: "system", content: sysPrompt},
                                    {role: "user", content: `Создай страницу для услуги: ${k}`}
                                ],
                                temperature: 0.7
                            })
                        });
                        
                        const json = await response.json();
                        if(json.error) {
                            log.innerHTML += `<span style="color:red">Ошибка AI: ${json.error.message}</span><br>`;
                            continue;
                        }
                        
                        let aiContent = json.choices[0].message.content;
                        aiContent = aiContent.replace(/```json/g, '').replace(/```/g, '').trim(); // clear formatting if any
                        
                        log.innerHTML += `<span style="color:yellow">Получено, сохранение... </span>`;
                        
                        const saveRes = await fetch('api_save_seo.php', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                            body: 'json=' + encodeURIComponent(aiContent)
                        });
                        
                        const saveText = await saveRes.text();
                        if(saveText.includes('SUCCESS')) {
                            log.innerHTML += `<span style="color:white">Успех!</span><br>`;
                        } else {
                            log.innerHTML += `<span style="color:red">Ошибка БД: ${saveText}</span><br>`;
                        }
                        
                    } catch (e) {
                         log.innerHTML += `<span style="color:red">Сетевая ошибка: ${e.message}</span><br>`;
                    }
                }
                
                log.innerHTML += '<b>Генерация завершена. Для просмотра страниц обновите эту страницу.</b><br>';
                btn.disabled = false;
            }
            </script>

            <table style="margin-bottom: 30px;">
                <tr><th>URL / Клик</th><th>H1 / Title</th><th>Просмотры</th><th>Доп. Текст</th><th>Действия</th></tr>
                <?php foreach ($seo_pages as $row): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($row['slug']) ?></strong><br>
                        <a href="/uslugi/<?= htmlspecialchars($row['slug']) ?>/" target="_blank" style="color:var(--primary); font-size: 0.9em;">/uslugi/<?= htmlspecialchars($row['slug']) ?>/</a>
                    </td>
                    <td style="font-size: 0.9em; max-width: 300px;">
                        <span style="color:#fff"><?= htmlspecialchars($row['h1']) ?></span><br>
                        <span style="color:var(--text-sec)"><?= htmlspecialchars($row['title']) ?></span>
                    </td>
                    <td><span style="font-size:1.5em; font-weight:bold; color:var(--primary);"><?= $row['views'] ?></span></td>
                    <td style="font-size: 0.8em; color:var(--text-sec); max-width: 200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        <?= htmlspecialchars($row['content_bottom']) ?>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Удалить эту страницу? Трафик с Яндекса пропадет!');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit" style="background:#ff5555; padding:8px 12px; font-size:0.9em;">Удалить</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($seo_pages)): ?>
                <tr><td colspan="5" style="text-align:center; padding: 20px;">Пока нет SEO-страниц. Создайте первую с помощью AI выше!</td></tr>
                <?php endif; ?>
            </table>

            <div class="card" style="margin-bottom: 20px;">
                <h3>Создать посадочную страницу вручную</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="text" name="slug" placeholder="URL-алиас (пример: sozdanie-sayta-klining-tomsk)" pattern="[a-zA-Z0-9_-]+" required>
                    <input type="text" name="h1" placeholder="Заголовок H1 (пример: Разработка сайтов для клининга в Томске)" required>
                    <input type="text" name="title" placeholder="Meta Title (пример: Заказать сайт для клининговой компании в Томске под ключ)" required>
                    <textarea name="description" placeholder="Meta Description (Сниппет для Яндекса)" rows="2" required></textarea>
                    
                    <h4 style="margin-top:15px; color:var(--text-sec);">Подзаголовок на главном экране (Hero Subtitle)</h4>
                    <textarea name="content_top" placeholder="Опишите боль клиента в этой нише и предложите решение..." rows="3"></textarea>
                    
                    <h4 style="margin-top:15px; color:var(--text-sec);">Доп. блок текста (внизу страницы для SEO-веса)</h4>
                    <textarea name="content_bottom" placeholder="Напишите SEO-текст (<h1>, <p>, <ul>...) для этой страницы" rows="4"></textarea>
                    
                    <button type="submit" style="margin-top:15px; background:linear-gradient(135deg, hsl(200, 100%, 60%), hsl(260, 100%, 65%));">Сгенерировать страницу</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
