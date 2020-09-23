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
    
    if(!empty($_POST['wc_product_name'])){
        $wc_product_name = $_POST['wc_product_name'];
    }else{
        $wc_product_name = '';
    }
    
    if(!empty($_POST['wc_price'])){
        $wc_price = $_POST['wc_price'];
    }else{
        $wc_price = '';
    }
    
    if(!empty($_POST['wc_product_description'])){
        $wc_product_description = $_POST['wc_product_description'];
    }else{
        $wc_product_description = '';
    }
    
    if(!empty($_POST['wc_model'])){
        $wc_model = $_POST['wc_model'];
    }else{
        $wc_model = '';
    }
    
    if(!empty($_POST['wc_product_images'])){
        $wc_product_images = $_POST['wc_product_images'];
    }else{
        $wc_product_images = [];
    }
    
    if(!empty($_POST['wc_categories'])){
        $wc_categories = $_POST['wc_categories'];
    }else{
        $wc_categories = '';
    }
    
    if(!empty($_POST['wc_attributes'])){
        $wc_attributes = $_POST['wc_attributes'];
    }else{
        $wc_attributes = [];
    }
    
    if(!empty($_POST['wc_variations'])){
        $wc_variations = $_POST['wc_variations'];
    }else{
        $wc_variations = [];
    }
    //$db->log('wc_variations', $wc_variations);
    
    if(!empty($_POST['wc_form_variations'])){
        $wc_form_variations = $_POST['wc_form_variations'];
    }else{
        $wc_form_variations = [];
    }
    
    if(isset($_POST['wc_option_add_to_dish'])){
        $wc_option_add_to_dish = $_POST['wc_option_add_to_dish'];
    }else{
        $wc_option_add_to_dish = false;
    }
    //$db->log('wc_form_variations', $wc_form_variations);
    
    
    $rezult = $db->addOcToWcProduct(
        $wc_product_name,
        $wc_price,
        $wc_model,
        $wc_product_description,
        $wc_product_images,
        $wc_categories,
        $wc_attributes,
        $wc_variations,
        $wc_form_variations,
        $wc_option_add_to_dish,
        $woocommerce);
    echo json_encode($rezult);
}
//GET opencart data END

//$rezult = $db->getProduct($product_id, $woocommerce);

