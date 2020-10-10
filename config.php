<?php
set_time_limit(0);//snimaem ogranicheniya na vipolneniya skripta
//Погасим ошибки
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

/*
//выводить все ошибки
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
*/
/*
 * https://stackoverflow.com/questions/51627281/woocommerce-rest-api-operation-timed-out
 *https://searchengines.guru/showthread.php?t=443500
 */
//define('IMAGE_PATH', __DIR__ . '/../image/data/xml');
//define('IMAGE_PATH_TO_DATABASE', 'data/xml');

define('WC_API_DOMAIN', 'http://test.sushisetboss.com');//PRODUCTION
define('WC_API_CK', 'ck_5cab96ef0652aa1b744d07a094320fe1bc9b4e5f');
define('WC_API_CS', 'cs_4ea0e9d3df2cc60a1a92f00aa3f29347aa804fb3');
define('IMAGE_PATH', __DIR__ . '/../wp-content/uploads/xml');
define('IMAGE_PATH_TO_DATABASE', 'xml');

define('WC_API_VERSION', 'wc/v3');
define('WC_API_TIMEOUT', '1800');// curl timeout seconds
define('LOG_DIR', __DIR__ . '/var');
