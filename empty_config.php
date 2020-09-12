<?php
set_time_limit(0);//snimaem ogranicheniya na vipolneniya skripta
/*
 * https://stackoverflow.com/questions/51627281/woocommerce-rest-api-operation-timed-out
 *https://searchengines.guru/showthread.php?t=443500
 */
//define('IMAGE_PATH', __DIR__ . '/../image/data/xml');
//define('IMAGE_PATH_TO_DATABASE', 'data/xml');

define('WC_API_DOMAIN', 'https://test.com');//PRODUCTION
define('WC_API_CK', '');
define('WC_API_CS', '');
define('IMAGE_PATH', __DIR__ . '/../wp-content/uploads/xml');
define('IMAGE_PATH_TO_DATABASE', 'xml');

define('WC_API_VERSION', 'wc/v3');
define('WC_API_TIMEOUT', '1800');// curl timeout seconds
define('LOG_DIR', __DIR__ . '/var');
