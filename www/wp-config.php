<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'wp');

/** Имя пользователя MySQL */
define('DB_USER', 'root');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', '');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8mb4');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ',P_t^;cY?=: dXqT0<LLHhGA)hhe<D.Nhj=_H4-j:_(+#2N4eGGzYR /!85l#P1y');
define('SECURE_AUTH_KEY',  'X2qkLsy|AZUZl,!#5TsiXwV:?~[xb}2op9m`|55bPdCbB3d*{Dx5)Aeh~$F@y5t)');
define('LOGGED_IN_KEY',    '~ it8|HsP0Ea<jruDZi6(hg^ER_PodH!8Wo_kj&n%ost0#:_H6t0Qp*6y%pN,Tq4');
define('NONCE_KEY',        'e;G( lhp6jBDcS]p2gDrqM&Gv.H/A#+ 5k(W+Fe2>>NspzU6>= nUz=/3J>M0{0.');
define('AUTH_SALT',        '/GwBP|5(b[|$nq;wPNVOSB*^l$O^1KR49;c{W3Ig=^|RrvVOzgv,jJ#^mdWB)zGG');
define('SECURE_AUTH_SALT', '$2[GKf%QxE4Q<}>.u[m$vXkSj{</~tHln#G:T) 1W1C0S@ZR@2;92xzIyb,ENw?^');
define('LOGGED_IN_SALT',   'K^lOri[6><z!f_:8z/ZB# yIwKAlYO.64#:*6-pC~/T*8.=OuP[GL:{bDqPcd (|');
define('NONCE_SALT',       'K@=L=/8PoNEH:-)%$wtJCrdk1b:oL4}(r~GL,r+<OYM4{y=B4FwnJ0]&-S[+ &*r');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', true );     // включение дебаг режима
define( 'WP_DEBUG_LOG', true ); // true - логирование ошибок в файл /wp-content/debug.log
define( 'WP_DEBUG_DISPLAY', false ); // false - отключает показ ошибок на экране
define( 'SCRIPT_DEBUG', true ); // используем полные версии JS и CSS файлов движка

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
