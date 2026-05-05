<?php
// Настройки безопасности
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Разрешаем только POST запросы
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
    exit;
}

// Получаем данные
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Валидация данных
if (!$data || !isset($data['name']) || !isset($data['email']) || !isset($data['message'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Недостаточно данных']);
    exit;
}

// Очистка и валидация
$name = htmlspecialchars(trim($data['name']), ENT_QUOTES, 'UTF-8');
$email = filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL);
$phone = isset($data['phone']) ? htmlspecialchars(trim($data['phone']), ENT_QUOTES, 'UTF-8') : '';
$service = isset($data['service']) ? htmlspecialchars(trim($data['service']), ENT_QUOTES, 'UTF-8') : 'Не указано';
$message = htmlspecialchars(trim($data['message']), ENT_QUOTES, 'UTF-8');

// Проверка email
if (!$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Некорректный email адрес']);
    exit;
}

// Получаем анти-спам данные
$formTime = isset($data['formTime']) ? intval($data['formTime']) : 0;
$interactions = isset($data['interactions']) ? intval($data['interactions']) : 0;
$mouseMovements = isset($data['mouseMovements']) ? intval($data['mouseMovements']) : 0;
$touchEvents = isset($data['touchEvents']) ? intval($data['touchEvents']) : 0;
$userAgent = isset($data['userAgent']) ? htmlspecialchars($data['userAgent'], ENT_QUOTES, 'UTF-8') : '';
$screenResolution = isset($data['screenResolution']) ? htmlspecialchars($data['screenResolution'], ENT_QUOTES, 'UTF-8') : '';
$referrer = isset($data['referrer']) ? htmlspecialchars($data['referrer'], ENT_QUOTES, 'UTF-8') : '';
$formSource = isset($data['formSource']) ? htmlspecialchars($data['formSource'], ENT_QUOTES, 'UTF-8') : '';

// Флаг возможного спама
$spamFlag = isset($data['spamFlag']) ? $data['spamFlag'] : '';

// Наименования услуг
$serviceNames = [
    'website' => 'Создание сайта',
    'redesign' => 'Редизайн',
    'seo' => 'SEO-продвижение',
    'advertising' => 'Контекстная реклама',
    'support' => 'Поддержка',
    'hosting' => 'Хостинг'
];
$serviceName = isset($serviceNames[$service]) ? $serviceNames[$service] : $service;

// Формируем тему письма
$subject = $spamFlag . '[Molozin.ru] Новая заявка: ' . $serviceName;

// Формируем тело письма
$emailBody = "
=== НОВАЯ ЗАЯВКА С САЙТА MOLOZIN.RU ===

Источник: {$formSource}

--- ДАННЫЕ КЛИЕНТА ---
Имя: {$name}
Email: {$email}
Телефон: " . ($phone ?: 'Не указан') . "
Интересующая услуга: {$serviceName}

--- СООБЩЕНИЕ ---
{$message}

--- ТЕХНИЧЕСКАЯ ИНФОРМАЦИЯ ---
Время заполнения формы: " . round($formTime / 1000, 2) . " сек
Количество взаимодействий: {$interactions}
Движения мыши: {$mouseMovements}
Касания (мобильные): {$touchEvents}
User Agent: {$userAgent}
Разрешение экрана: {$screenResolution}
Источник перехода: " . ($referrer ?: 'Прямой переход') . "

--- ДАТА И ВРЕМЯ ---
" . date('d.m.Y H:i:s') . "

---
Это автоматическое сообщение с сайта Molozin.ru
";

// Адрес получателя
$to = 'mdn77@yandex.ru';

// Дополнительные заголовки
$headers = [
    'From: 3d@best70.ru',
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion(),
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: 8bit',
    'X-Priority: 1',
    'X-Form-Source: Molozin.ru Contact Form',
    'X-Client-IP: ' . $_SERVER['REMOTE_ADDR']
];

// Параметры для mail() функции
$params = '-f3d@best70.ru'; // Envelope Sender - критически важно для Reg.ru

// Отправка письма
$success = mail($to, $subject, $emailBody, implode("\r\n", $headers), $params);

// Логирование
$logEntry = sprintf(
    "[%s] %s | %s | %s | %s | Success: %s\n",
    date('Y-m-d H:i:s'),
    $_SERVER['REMOTE_ADDR'],
    $name,
    $email,
    $serviceName,
    $success ? 'YES' : 'NO'
);

file_put_contents(__DIR__ . '/logs/contact-form.log', $logEntry, FILE_APPEND);

// Запись в базу данных SQLite
try {
    require_once __DIR__ . '/admin/db.php';
    if (isset($db)) {
        $details = "Скролл: $interactions, Мышь: $mouseMovements, Тач: $touchEvents\n$userAgent\n$screenResolution\n$referrer";
        $stmt = $db->prepare("INSERT INTO leads (name, email, phone, service, message, details, ip) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $serviceName, $message, $details, $_SERVER['REMOTE_ADDR']]);
    }
} catch (Exception $e) {
    // Тихо игнорируем, если БД не доступна
}

// Уведомление в Telegram (опционально)
if ($success) {
    sendTelegramNotification($name, $email, $phone, $serviceName, $message);
}

// Ответ клиенту
if ($success) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Сообщение успешно отправлено'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка при отправке сообщения'
    ]);
}

/**
 * Отправка уведомления в Telegram
 */
function sendTelegramNotification($name, $email, $phone, $service, $message) {
    // Настройки Telegram Bot (замените на свои)
    $botToken = 'YOUR_BOT_TOKEN'; // Получите у @BotFather
    $chatId = 'YOUR_CHAT_ID'; // Получите у @userinfobot
    
    // Если токен не настроен, пропускаем
    if ($botToken === 'YOUR_BOT_TOKEN') {
        return false;
    }
    
    $telegramMessage = "🔔 <b>Новая заявка с Molozin.ru</b>\n\n";
    $telegramMessage .= "👤 <b>Имя:</b> " . htmlspecialchars($name) . "\n";
    $telegramMessage .= "📧 <b>Email:</b> " . htmlspecialchars($email) . "\n";
    $telegramMessage .= "📱 <b>Телефон:</b> " . ($phone ?: 'Не указан') . "\n";
    $telegramMessage .= "🎯 <b>Услуга:</b> " . htmlspecialchars($service) . "\n\n";
    $telegramMessage .= "💬 <b>Сообщение:</b>\n" . htmlspecialchars($message);
    
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $postData = [
        'chat_id' => $chatId,
        'text' => $telegramMessage,
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($postData),
            'timeout' => 5
        ]
    ];
    
    $context = stream_context_create($options);
    @file_get_contents($url, false, $context);
    
    return true;
}
?>
