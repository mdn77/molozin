<?php
/**
 * Умная система интернационализации с кэшированием и ленивым переводом.
 */

$supported_langs = ['ru', 'zh', 'es', 'en', 'hi', 'ar', 'bn', 'pt', 'ja', 'pa'];

// Определение текущего языка
$lang = 'ru';
if (isset($_GET['lang']) && in_array(strtolower($_GET['lang']), $supported_langs)) {
    $lang = strtolower($_GET['lang']);
    setcookie('lang', $lang, time() + 365*24*60*60, '/');
} elseif (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $supported_langs)) {
    $lang = $_COOKIE['lang'];
} elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    if (in_array($browser_lang, $supported_langs)) {
        $lang = $browser_lang;
    }
}

// RTL Languages support
$is_rtl = in_array($lang, ['ar', 'he', 'fa', 'pa']);


// Загрузка кэша переводов в память один раз
$cache_file = __DIR__ . '/i18n_cache.json';
$global_cache = [];
if (file_exists($cache_file)) {
    $content = file_get_contents($cache_file);
    if ($content) {
        $global_cache = json_decode($content, true) ?: [];
    }
}

// Статический словарь (самые быстрые переводы)
$translates = [
    'ru' => [
        /* MENU */
        'menu_services' => 'Услуги',
        'menu_portfolio' => 'Портфолио',
        'menu_process' => 'Процесс',
        'menu_pricing' => 'Цены',
        'menu_blog' => 'Блог',
        'menu_contacts' => 'Контакты',
        'btn_consultation' => 'Консультация',

        /* HERO */
        'hero_badge' => '🇷🇺 Российская студия • 12 лет • Премиум-разработка',
        'hero_title_1' => 'Создаем программные продукты,<br>которые ',
        'hero_title_accent' => 'приводят клиентов',
        'hero_subtitle' => 'Берём на себя всю техническую рутину: от премиального дизайна до сложных баз данных под капотом. Автоматизируем ваш бизнес и выводим его в топ.',
        'btn_order' => 'Обсудить проект',
        'btn_portfolio' => 'Смотреть работы',
        'stat_projects' => 'Проектов',
        'stat_years' => 'Лет опыта',
        'stat_clients' => 'Клиентов',
        'scroll_down' => 'Листайте вниз',

        /* PORTFOLIO */
        'port_label' => 'Работы',
        'port_title' => 'Наше Портфолио',
        'port_sub' => 'Реализованные кейсы, которыми мы гордимся.',
        'port_interactive' => 'Интерактив',
        'port_btn' => 'Все работы',

        /* SERVICES */
        'serv_label' => 'Что мы делаем',
        'serv_title' => 'Наши Услуги',
        'serv_sub' => 'Комплексные решения для вашего бизнеса — от идеи до запуска и продвижения.',
        'serv_more' => 'Подробнее',
        'serv_1_title' => 'Разработка сайтов',
        'serv_1_desc' => 'Создаем современные, адаптивные и высокопроизводительные сайты любой сложности.',
        'serv_1_f1' => 'Landing Page',
        'serv_1_f2' => 'Корпоративные порталы',
        'serv_1_f3' => 'E-commerce проекты',
        'serv_2_title' => 'Редизайн и аудит',
        'serv_2_desc' => 'Вдохнем новую жизнь в ваш текущий проект, улучшим конверсию и UX.',
        'serv_2_f1' => 'Обновление дизайна',
        'serv_2_f2' => 'Улучшение юзабилити',
        'serv_2_f3' => 'Скорость работы',
        'serv_3_title' => 'SEO Продвижение',
        'serv_3_desc' => 'Выводим ваш бизнес в ТОП-10 Яндекса и Google по ключевым запросам.',
        'serv_3_f1' => 'Технический SEO-аудит',
        'serv_3_f2' => 'Сбор семантики',
        'serv_3_f3' => 'Линкбилдинг',
        'serv_4_title' => 'Контекстная реклама',
        'serv_4_desc' => 'Быстрый поток клиентов из Яндекс.Директ и Google Ads.',
        'serv_4_f1' => 'Настройка кампаний',
        'serv_4_f2' => 'Аналитика',
        'serv_4_f3' => 'Ретаргетинг',
        'serv_5_title' => 'Техподдержка',
        'serv_5_desc' => 'Гарантируем стабильную работу вашего сайта 24/7.',
        'serv_5_f1' => 'Мониторинг 24/7',
        'serv_5_f2' => 'Исправление ошибок',
        'serv_5_f3' => 'Обновление контента',
        'serv_6_title' => 'Хостинг и домены',
        'serv_6_desc' => 'Надежное размещение ваших проектов на быстрых SSD-серверах.',
        'serv_6_f1' => 'SSL-сертификаты',
        'serv_6_f2' => 'Резервное копирование',
        'serv_6_f3' => 'Почтовые домены',

        /* ECOSYSTEM */
        'eco_label' => 'Наши платформы',
        'eco_title' => 'Экосистема IT-продуктов',
        'eco_sub' => 'Мы создаём и поддерживаем собственные федеральные сервисы.',
        'eco_1_title' => 'R-70.ru',
        'eco_1_desc' => 'SaaS-платформа. Конструктор сайтов для бизнеса.',
        'eco_2_title' => '3DCorp.ru',
        'eco_2_desc' => 'Инновационный интернет магазин с калькулятором множества 3D моделей и заказом услуг',
        'eco_3_title' => 'Best70.ru',
        'eco_3_desc' => 'Аналитический хаб и справочник компаний.',
        'trust_f1' => 'NDA & безопасность',
        'trust_f2' => 'Запуск от 7 дней',
        'trust_f3' => 'Премиум-качество',
        'trust_f4' => 'Прозрачность',

        /* PROCESS */
        'proc_label' => 'Этапы',
        'proc_title' => 'Как мы работаем',
        'proc_sub' => 'Прозрачный процесс разработки с регулярной отчетностью.',
        'proc_1_title' => 'Аналитика и ТЗ',
        'proc_1_desc' => 'Изучаем нишу, конкурентов и составляем детальный план работ.',
        'proc_2_title' => 'Дизайн и Прототип',
        'proc_2_desc' => 'Создаем визуальную концепцию и структуру каждой страницы.',
        'proc_3_title' => 'Разработка',
        'proc_3_desc' => 'Верстка и программирование функциональной части проекта.',
        'proc_4_title' => 'Тестирование',
        'proc_4_desc' => 'Проверяем работу на всех устройствах и исправляем баги.',
        'proc_5_title' => 'Запуск и Поддержка',
        'proc_5_desc' => 'Перенос на хостинг и дальнейшее сопровождение продукта.',

        /* CALCULATOR */
        'calc_label' => 'Ваш проект',
        'calc_title' => 'Рассчитайте стоимость',
        'calc_sub' => 'Укажите параметры, и нейросеть мгновенно рассчитает ориентировочную смету.',
        'calc_type_title' => 'Тип проекта',
        'calc_type_1' => 'Лендинг',
        'calc_type_2' => 'Корпоративный сайт',
        'calc_type_3' => 'Интернет-магазин',
        'calc_type_4' => 'Сложный портал / SaaS',
        'calc_addons_title' => 'Дополнительные модули',
        'calc_addon_1' => 'SEO-оптимизация (ТОП Яндекс)',
        'calc_addon_2' => 'Интеграция с CRM / 1C',
        'calc_addon_3' => 'Мультиязычность (Доп. языки)',
        'calc_addon_4' => 'Премиальные 3D анимации',
        'calc_time_title' => 'Сроки выполнения',
        'calc_time_1' => 'Срочно (+50%)',
        'calc_time_2' => 'Стандарт',
        'calc_time_3' => 'Вдумчиво',
        'calc_time_4' => 'Не спеша (-10%)',
        'calc_res_title' => 'Ориентировочная стоимость',
        'calc_res_sub' => 'Точная смета формируется после детального брифа.',
        'calc_btn' => 'Зафиксировать цену',
        'calc_hint' => 'Оставьте заявку, и мы пришлем PDF-смету с разбиением по этапам в течение часа.',

        /* PRICING */
        'price_label' => 'Тарифы',
        'price_title' => 'Стоимость решений',
        'price_sub' => 'Выберите оптимальный вариант для вашего бизнеса.',
        'price_btn' => 'Выбрать тариф',
        'price_cur' => '₽',
        'price_1_title' => 'Старт',
        'price_1_val' => 'от 30 000',
        'price_1_desc' => 'Идеально для малого бизнеса и быстрого запуска.',
        'price_1_f1' => 'Уникальный дизайн',
        'price_1_f2' => 'Адаптивная верстка',
        'price_1_f3' => 'Базовая SEO-настройка',
        'price_1_f4' => 'Интеграция форм связи',
        'price_1_f5' => 'Срок: от 7 дней',
        'price_2_badge' => 'ХИТ ПРОДАЖ',
        'price_2_title' => 'Бизнес',
        'price_2_val' => 'от 80 000',
        'price_2_desc' => 'Полноценное решение для развития компании.',
        'price_2_f1' => 'Все из тарифа Старт',
        'price_2_f2' => 'Каталог товаров/услуг',
        'price_2_f3' => 'Калькулятор стоимости',
        'price_2_f4' => 'Личный кабинет',
        'price_2_f5' => 'CRM-интеграция',
        'price_2_f6' => 'Срок: от 20 дней',
        'price_3_title' => 'Ultra',
        'price_3_val' => 'от 250 000',
        'price_3_desc' => 'Умные платформы со сложной логикой (SaaS/Порталы).',
        'price_3_f1' => 'Индивидуальная архитектура',
        'price_3_f2' => 'Сложные базы данных',
        'price_3_f3' => 'AI-функционал',
        'price_3_f4' => 'HighLoad оптимизация',
        'price_3_f5' => 'Апекс-безопасность',
        'price_3_f6' => '3D/WebGL графика',
        'price_3_f7' => 'Пожизненная гарантия',

        /* CONTACTS */
        'contact_title' => 'Свяжитесь с нами',
        'contact_desc' => 'Обсудим ваш проект и подберем лучшее техническое решение.',
        'contact_direct' => 'Связаться напрямую',
        'contact_l_phones' => 'Телефоны',
        'contact_l_address' => 'Адрес студии',
        'contact_address' => 'г. Томск, ул. Нахимова, 15',
        'form_name' => 'Ваше имя',
        'form_email' => 'Электронная почта',
        'form_phone' => 'Контактный телефон',
        'form_service' => 'Какая услуга интересует?',
        'form_service_option' => 'Выберите услугу...',
        'form_message' => 'Расскажите о проекте',
        'form_btn' => 'Отправить заявку',

        /* FOOTER */
        'footer_desc' => 'Студия Molozin.ru — создаем премиальные цифровые продукты с 2014 года.',
        'footer_l1' => 'Услуги',
        'footer_l1_1' => 'Сайт-визитка',
        'footer_l1_2' => 'Интернет-магазин',
        'footer_l1_3' => 'Корпоративный портал',
        'footer_l1_4' => 'SEO Продвижение',
        'footer_l1_5' => 'Редизайн сайта',
        'footer_l2' => 'Компания',
        'footer_l2_1' => 'Портфолио',
        'footer_l2_2' => 'Этапы работы',
        'footer_l2_3' => 'Цены',
        'footer_l2_4' => 'Контакты',
        'footer_l3' => 'Продукты',
        'footer_l3_1' => 'Конструктор R-70',
        'footer_l3_2' => '3DCorp расчеты',
        'footer_l3_3' => 'Best70 справочник',
        'footer_copy' => '© 2014-2026 Molozin.ru',
        'footer_privacy' => 'Политика конфиденциальности',
        'footer_terms' => 'Соглашение',

        /* MODAL */
        'modal_title_default' => 'Название проекта',
        'modal_desc_default' => 'Индивидуальное IT-решение премиум уровня.',
        'modal_open' => 'Открыть сайт',
        'modal_desktop' => 'ПК',
        'modal_mobile' => 'Смартфон',
        'modal_close' => 'Закрыть',
        'modal_badge' => '✨ Авторский дизайн',
        'modal_what_done' => 'Что реализовано:',
        'modal_studio' => 'Студия Molozin.ru',
        'modal_studio_desc' => 'Высокие технологии & Премиум дизайн',
        'modal_loading' => 'Загрузка...',
        'blog_empty' => 'Статей пока нет, но мы уже работаем над новым материалом.',
        'blog_main_title' => 'Блог и экспертные статьи',
        'blog_main_desc' => 'Делимся опытом разработки, рассказываем про веб-технологии и аналитику.',
    ],
    'en' => [
         'hero_title_accent' => 'attract customers',
         'btn_order' => 'Order Project',
    ]
];

/**
 * Основная функция перевода. 
 */
function __($key) {
    global $translates, $lang, $global_cache;
    
    // 1. Статика
    if (isset($translates[$lang][$key])) {
        return $translates[$lang][$key];
    }
    
    // 2. Кэш
    if (isset($global_cache[$lang][$key])) {
        return $global_cache[$lang][$key];
    }

    $source_text = $translates['ru'][$key] ?? $key;
    if ($lang === 'ru') return $source_text;

    // 3. Асинхронный перевод
    return '<span class="js-translate" data-key="' . htmlspecialchars($key) . '" data-source="' . htmlspecialchars($source_text) . '">' . $source_text . '</span>';
}

/**
 * API для сохранения
 */
if (isset($_POST['action']) && $_POST['action'] === 'save_translate') {
    $k = $_POST['key'] ?? '';
    $v = $_POST['value'] ?? '';
    $l = $_POST['lang'] ?? '';
    if (in_array($l, $supported_langs) && $k && $v) {
        $fp = fopen($cache_file, 'c+');
        if ($fp && flock($fp, LOCK_EX)) {
            $fsize = filesize($cache_file);
            $c = [];
            if ($fsize > 0) {
                $raw = fread($fp, $fsize);
                $c = json_decode($raw, true) ?: [];
            }
            $c[$l][$k] = $v;
            ftruncate($fp, 0);
            fseek($fp, 0);
            fwrite($fp, json_encode($c, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            flock($fp, LOCK_UN);
        }
        if ($fp) fclose($fp);
    }
    exit;
}
?>
