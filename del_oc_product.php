<?php
set_time_limit(0);//snimaem ogranicheniya na vipolneniya skripta
ini_set("memory_limit", "256M");


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
//Для бесконечной обработки запросов укажите '0'.
//ini_set('pm.max_requests', 0);

//My classes
require_once __DIR__ . '/classes/db.php';
require_once __DIR__ . '/classes/xml.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/config.php';
//My classes END
//Перехват завершения скрипта
//register_shutdown_function('shutdown');

//Composer
require __DIR__ . '/vendor/autoload.php';
use Automattic\WooCommerce\Client;
//Composer END

$db = new Db;

/*
 * WooCommerce lib
 * https://packagist.org/packages/automattic/woocommerce
 * https://woocommerce.github.io/woocommerce-rest-api-docs/
 *
 */

//WooCommerce API CLIENT
$woocommerce = new Client(
    WC_API_DOMAIN,
    WC_API_CK,
    WC_API_CS,
    [
        'version' => WC_API_VERSION,
        'timeout' => WC_API_TIMEOUT // curl timeout
    ]
);
//WooCommerce API CLIENT END

//GET opencart data
if(!empty($_POST)){
    
    if(!empty($_POST['wc_product_id'])){
        $wc_product_id = $_POST['wc_product_id'];
        $rezult = $db->deleteProduct($wc_product_id, $woocommerce);
        echo json_encode($rezult);
    }
   
}


