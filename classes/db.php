<?php
//
set_time_limit(0);//snimaem ogranicheniya na vipolneniya skripta
//define('LANGUAGE_ID', 1);
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

require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';

//Load WP functions
require_once(__DIR__ . '/../../wp-load.php');
global $wpdb;

class Db
{
    
    public $db;
    
    public function __construct()
    {
        require_once __DIR__ . '/../../wp-config.php';
        //DB CONNECT
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($this->db->connect_errno) {
            $err = "Не удалось подключиться к MySQL: (" . $this->db->connect_errno . ") " . $this->db->connect_error;
            $this->log('construct_log', $err, true);
        }else{
           // echo "Подключение к базе прошло успешно!";
        }
        $this->db->set_charset(DB_CHARSET);
        //DB CONNECT END
        
        //$this->db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        //$this->config = new Config();
    }
    
    public function query($sql)
    {
        
        if (!$result = $this->db->query($sql)) {
            $err = "Не удалось выполнить запрос: $sql <br>";
            $err .= "Номер ошибки: " . $this->db->errno . "\n";
            $err .= "Ошибка: " . $this->db->error . "\n";
            //$this->log('query_log', $err, true);
            
            return false;
        }
        
        if ($result->num_rows > 0) {
            return $result;
        } else {
            $err = "Функция query по данным: <br> $sql <br> - mysql вернула пустой результат! <br><hr>";
            //$this->log('query_log', $err, true);
            
            return false;
        }
        
    }
    
    public function query_assoc($sql, $row_filed)
    {
        
        if (!$result = $this->db->query($sql)) {
            $err = "Не удалось выполнить запрос: $sql <br>";
            $err .= "Номер ошибки: " . $this->db->errno . "\n";
            $err .= "Ошибка: " . $this->db->error . "\n";
            //$this->log('query_assoc_log', $err, true);
            
            return false;
        }
        
        if ($result->num_rows > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row[$row_filed];
        } else {
            $err = "Функция query_assoc по данным: <br> $sql <br> $row_filed <br> - mysql вернула пустой результат! <br><hr>";
            //$this->log('query_assoc_log', $err, true);
            
            return false;
        }
        
    }
    
    public function query_insert($sql)
    {
        if (!$result = $this->db->query($sql)) {
            $err = "Не удалось выполнить запрос: (" . $this->db->errno . ") " . $this->db->error;
            $err .= "Номер ошибки: " . $this->db->errno . "\n";
            $err .= "Ошибка: " . $this->db->error . "\n";
            //$this->log('query_insert_log', $err, true);
            
            return false;
        }else{
            $err = "Запрос <br> $sql <br> - выполнен удачно! <br><hr>";
            //$this->log('query_insert_log', $err, true);
            
            return true;
        }
    
    }
    
    function query_insert_id($sql)
    {
        if (!$result = $this->db->query($sql)) {
            $err = "Не удалось выполнить запрос: (" . $this->db->errno . ") " . $this->db->error;
            $err .= "Номер ошибки: " . $this->db->errno . "\n";
            $err .= "Ошибка: " . $this->db->error . "\n";
            //$this->log('query_insert_id_log', $err, true);
            
            return false;
        }else{
            $err = "Запрос <br> $sql <br> - выполнен удачно! <br><hr>";
            //$this->log('query_insert_id_log', $err, true);
            
            return $this->db->insert_id;
            //return mysqli_insert_id($this->db);
        }
        
    }
    
    public function query_update($sql)
    {
        if (!$result = $this->db->query($sql)) {
            $err = "Не удалось выполнить запрос: (" . $this->db->errno . ") " . $this->db->error;
            $err .= "Номер ошибки: " . $this->db->errno . "\n";
            $err .= "Ошибка: " . $this->db->error . "\n";
            //$this->log('query_update_log', $err, true);
            
            return false;
        }else{
            $err = "Запрос <br> $sql <br> - выполнен удачно! <br><hr>";
            //$this->log('query_update_log', $err, true);
            
            return true;
        }
        
    }
    
    public function query_delete($sql)
    {
        if (!$result = $this->db->query($sql)) {
            $err = "Не удалось выполнить запрос: (" . $this->db->errno . ") " . $this->db->error;
            $err .= "Номер ошибки: " . $this->db->errno . "\n";
            $err .= "Ошибка: " . $this->db->error . "\n";
            //$this->log('query_delete_log', $err, true);
            
            return false;
        }else{
            $err = "Запрос <br> $sql <br> - выполнен удачно! <br><hr>";
            //$this->log('query_delete_log', $err, true);
            
            return true;
        }
        
    }
    
    public function getParseCategories($xml_id)
    {
        $parse_categories_rezult = $this->query("SELECT DISTINCT xml_category_id FROM wpi3_xml_categories WHERE xml_id='$xml_id'");
        if($parse_categories_rezult) {
            while ($parse_categories_row = $parse_categories_rezult->fetch_assoc()) {
                $parse_categories[] = $parse_categories_row['xml_category_id'];
            }
            $xml_id = null;
            $parse_categories_rezult = null;
            $parse_categories_row = null;
            unset($xml_id,$parse_categories_rezult,$parse_categories_row);
    
            //zapustim zborschik musora
           // time_nanosleep(0, 10000000);
            //zapustim zborschik musora END
            
            return $parse_categories;
        }else{
            $parse_categories_rezult = null;
            unset($parse_categories_rezult);
    
            //pauza dla sborki musora
            //time_nanosleep(0, 10000000);
            //pauza dla sborki musora
            
            return false;
        }
    }
    
    //Work with WC_REST_API
    
    public function checkAddAtributes($attributes, $woocommerce)
    {
     //CHECK ADD ATRIBUTES & TERMS
        foreach ($attributes as $attr_name => $attr_value){
            //CHECK ATTR
            $attr_name = (string) $attr_name;
            $slug = translit($attr_name);
            $attribute_id =  $this->query_assoc("SELECT attribute_id FROM wpi3_woocommerce_attribute_taxonomies WHERE attribute_label='$attr_name'", "attribute_id");
            if(!$attribute_id){
                $attribute_id = $this->query_assoc("SELECT attribute_id FROM wpi3_woocommerce_attribute_taxonomies WHERE attribute_name='$slug'", "attribute_id");
            }
            
            if(!$attribute_id){
                //ADD ATTR AND VALUE
                //Create attr
                $data_attr = [
                    'name' => (string) $attr_name,
                    'slug' => $slug,//obyazatelno 28 simbols
                    'type' => 'select',
                    'order_by' => 'id',
                    'has_archives' => true
                ];
                if(!empty($attr_name)){
                    
                    try {
                        $attr = $woocommerce->post('products/attributes', $data_attr);
                    }
                    catch(Exception $e){
                        $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
                        $err = $info . $e->getMessage();
                        ////echo $err;
                        $this->errorLog($err);
                    }
                    
                }else{
                    show_strong("Название атребута пустое 1");
                }
                
                show_strong("Создан новый атребут: ".$attr->id);
                //Create attr
    
                //Check attr value
                $attr_value = (string) $attr_value;
                $attr_value_slug = translit($attr_value);
                $attr_value_id = $this->query_assoc("SELECT term_id FROM `wpi3_terms` WHERE name='$attr_value'","term_id");
                if(!$attr_value_id){
                    $attr_value_id = $this->query_assoc("SELECT term_id FROM `wpi3_terms` WHERE slug='$attr_value_slug'","term_id");
                }
                
                if(!$attr_value_id){
                    //CREATE attr_value
                    $data_attr_value = [
                        'name' => (string) $attr_value,
                        'slug' => $attr_value_slug
                    ];
    
                    if(!empty($attr_value)){
    
                        try {
                            $attr_term = $woocommerce->post('products/attributes/'.$attr->id.'/terms', $data_attr_value);
                        }
                        catch(Exception $e){
                            $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
                            $err = $info . $e->getMessage();
                            //echo $err;
                            $this->errorLog($err);
                        }
                        
                    }else{
                        show_strong("Значение атребута пустое 1");
                    }
                    show_strong("Создано новое значение атребута с id значения: " . $attr_term->id);
                    //set russian lang for attr value
                    if($attr_term->id){
                        $this->query_insert("INSERT INTO wpi3_term_relationships (object_id, term_taxonomy_id) VALUES ($attr_term->id, 60)");
						//uk
						//$this->query_insert("INSERT INTO wpi3_term_relationships (object_id, term_taxonomy_id) VALUES ($attr_term->id, 63)");
                    }
                    //CREATE attr_value END
                }
                //ADD ATTR AND VALUE END
            
            }else{
                //ATRIBUTE ISSET CHECK ADD VALUE
                //... osibka tut
                $attr_value = (string) $attr_value;
                $attr_value_slug = translit($attr_value);
                $attr_value_id = $this->query_assoc("SELECT term_id FROM `wpi3_terms` WHERE name='$attr_value'","term_id");
                if(!$attr_value_id){
                    $attr_value_id = $this->query_assoc("SELECT term_id FROM `wpi3_terms` WHERE slug='$attr_value_slug'","term_id");
                }
                
                if (!$attr_value_id){
                    //CREATE attr_value
                    $data_attr_value = [
                        'name' => (string) $attr_value,
                        'slug' => $attr_value_slug
                    ];
                    
                    if(!empty($attr_value)){
    
                        try {
                            $attr_term = $woocommerce->post('products/attributes/'.$attribute_id.'/terms', $data_attr_value);
                        }
                        catch(Exception $e){
                            $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
                            $err = $info . $e->getMessage();
                            //echo $err;
                            $this->errorLog($err);
                        }
                        
                    }else{
                        show_strong("Значение атребута пустое 2");
                    }
                    
                    show_strong("Атрибут существует! Создано новое значение атребута с id значения: " . $attr_term->id);
                    //set russian lang for attr value
                    if($attr_term->id){
                        $this->query_insert("INSERT INTO wpi3_term_relationships (object_id, term_taxonomy_id) VALUES ($attr_term->id, 60)");
						//uk
						//$this->query_insert("INSERT INTO wpi3_term_relationships (object_id, term_taxonomy_id) VALUES ($attr_term->id, 63)");
                    }
                    //CREATE attr_value END
                }
                //ATRIBUTE ISSET CHECK ADD VALUE END
            }
            //CHECK ATTR END
        }
    //CHECK ADD ATRIBUTES & TERMS END
    
        $attributes = null;
        $woocommerce = null;
        $attribute_id = null;
        $data_attr = null;
        $attr_name = null;
        $attr_value = null;
        $attr = null;
        $data_attr_value = null;
        $attr_term = null;
        $wpi3_terms_rezult = null;
        unset($attributes,$woocommerce,$attribute_id,$data_attr,$attr_name,$attr_value,$attr,$data_attr_value,$attr_term,$wpi3_terms_rezult);
    
        //pauza dla sborki musora
        //time_nanosleep(0, 10000000);
        //pauza dla sborki musora
        
    return true;//chtonibut vernem
    }
    
    public function checkProductType($attributes, $type, $xml_id){
        //CHECK PRODUCT TYPE
        foreach ($attributes as $attr_name => $attr_value){
            $attr_group_id = $this->query_assoc("SELECT attr_group_id FROM wpi3_xml_attr_variations WHERE xml_id='$xml_id' AND attr_name='$attr_name'", "attr_group_id");
            if($attr_group_id){
                $type = 'variable';
            }
        }
    
        $attributes = null;
        $attr_name = null;
        $attr_value = null;
        $attr_group_id = null;
        unset($attributes,$attr_name,$attr_value,$attr_group_id);
    
        //pauza dla sborki musora
        //time_nanosleep(0, 10000000);
        //pauza dla sborki musora
        
        return $type;
        //CHECK PRODUCT TYPE END
    }
    
    public function formCategories($xml_id, $offer_category)
    {
        $categories = [];
        $wpi3_xml_categories_rezult = $this->query("SELECT wp_category_id FROM wpi3_xml_categories WHERE xml_id='$xml_id' AND xml_category_id='$offer_category'");
        while($wpi3_xml_categories_row = $wpi3_xml_categories_rezult->fetch_assoc()){
            $categories[] = [ 'id' => (integer) $wpi3_xml_categories_row['wp_category_id'] ];
        }
    
        $xml_id = null;
        $offer_category = null;
        $wpi3_xml_categories_rezult = null;
        $wpi3_xml_categories_row = null;
        unset($xml_id, $offer_category, $wpi3_xml_categories_rezult, $wpi3_xml_categories_row);
    
        //pauza dla sborki musora
        //time_nanosleep(0, 10000000);
        //pauza dla sborki musora
        
        return $categories;
    }
    
    public function getCurlHeader($url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_NOBODY => true));
    
        $header = explode("\n", curl_exec($curl));
        curl_close($curl);
    
        return $header;
    }
    
    public function formImages($images)
    {
        $images_arr = [];
        if(!empty($images)){
            foreach ($images as $src){
                $header = $this->getCurlHeader($src);
                if(strpos($header[0], '200') !== false) {
                    $images_arr[] = [ 'src' => (string) $src ];
                } else {
                    $images_arr[] = [ 'src' => 'https://sushiboss.od.ua/image/wc-600x600.png'  ];
                }
            }
        }else{
            $images_arr[] = [ 'src' => 'https://sushiboss.od.ua/image/wc-600x600.png'  ];
        }
        
        return $images_arr;
    }
    
    public function formAttributes2($attributes, $xml_id){
        foreach ($attributes as $attr_name => $attr_value){
            $attribute_id =  $this->query_assoc("SELECT attribute_id FROM wpi3_woocommerce_attribute_taxonomies WHERE attribute_label='$attr_name'", "attribute_id");
        
            $variation = false;//true/false
            //NE SRABOTAET!!!
            $attr_group_id =  $this->query_assoc("SELECT attr_group_id FROM wpi3_xml_attr_variations WHERE attr_name='$attr_name' AND xml_id='$xml_id'", "attr_group_id");
            if($attr_group_id !== false){
                $variation = true;//true/false
            }
        
            if(!is_array($attr_value)){
                $attr_value = [$attr_value];
            }
            
            if(!empty($attribute_id) and !empty($attr_name) and !empty($attr_value)){
                $attributes_arr[] = [
                    'id' => (integer) $attribute_id, //id atrebuta v wordpress
                    'name' => $attr_name, // nazva atrebuta
                    //'position' => '0',
                    'visible' => true, //bool
                    'variation' => $variation, //bool
                    'options' => $attr_value, // masssiv znacheniy atributa
                ];
            }
            
        }
    
        $attributes = null;
        $attr_name = null;
        $attr_value = null;
        $attribute_id = null;
        $variation = null;
        $attr_group_id = null;
        $variation = null;
        unset($attributes,$attr_name,$attr_value,$attribute_id,$variation,$attr_group_id,$variation);
    
        //pauza dla sborki musora
        //time_nanosleep(0, 10000000);
        //pauza dla sborki musora
        
        show_strong("Функция formAttributes2 сформировала атребуты следующим образом!!!");
        dump($attributes_arr);
    
        return $attributes_arr;
    }

    public function formAttributes($attributes, $xml_id)
    {
        foreach ($attributes as $attr_name => $attr_value){
            $attribute_id =  $this->query_assoc("SELECT attribute_id FROM wpi3_woocommerce_attribute_taxonomies WHERE attribute_label='$attr_name'", "attribute_id");
    
            $variation = false;//true/false
            $attr_group_id =  $this->query_assoc("SELECT attr_group_id FROM wpi3_xml_attr_variations WHERE attr_name='$attr_name' AND xml_id='$xml_id'", "attr_group_id");
            if($attr_group_id !== false){
                $variation = true;//true/false
            }
            
            if(!empty($attribute_id) and !empty($attr_name) and !empty($attr_value)){
                $attributes_arr[] = [
                    'id' => (integer) $attribute_id, //id atrebuta v wordpress
                    'name' => $attr_name, // nazva atrebuta
                    //'position' => '0',
                    'visible' => true, //bool
                    'variation' => $variation, //bool
                    'options' => [$attr_value], // masssiv znacheniy atributa
                ];
            }
            
            
        }
        
        return $attributes_arr;
    }
    
    public function changeProductToVariable($product_id, $woocommerce)
    {
        show("Зашли в changeProductToVariable");
        $data = [
            'type' => 'variable'
        ];
        try {
            dump($woocommerce->put('products/'.$product_id, $data));
        }
        catch(Exception $e){
            $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
            $err = $info . $e->getMessage();
            //echo $err;
            $this->errorLog($err);
        }
        
    }
    
    public function getProductType($product_id, $woocommerce)
    {
        show("Зашли в getProductType");
        $product = $this->getProduct($product_id, $woocommerce);
        
        $rezult = [
            'type' => $product->type,
            'price' => $product->regular_price,
            'sku' => $product->sku,
            'image' => [$product->images[0]->src]
        ];
        
        return $rezult;
    }
    
    public function firstAttributesForVariations($product_id, $woocommerce)
    {
        show("Зашли в firstAttributesForVariations");
        $attributes = $this->getProductAtributes($product_id, $woocommerce);
        $prepared = [];
            foreach($attributes as $key => $value_arr) {
                $prepared[$key] = $value_arr[0];
        }
        
        return $prepared;
    }
    
    
     //Proboval
    public function formAddVariations($product_id, $offer_vendor_code, $offer_price, $attributes, $images, $xml_id, $woocommerce)
    {
        show("Зашли в formAddVariations");
        
        $wpi3_xml_attr_variations_groups_rezult = $this->query("SELECT id FROM `wpi3_xml_attr_variations_groups` WHERE xml_id='$xml_id'");
        while($wpi3_xml_attr_variations_groups_row = $wpi3_xml_attr_variations_groups_rezult->fetch_assoc()){
            $check_group_id = $wpi3_xml_attr_variations_groups_row['id'];
            
            foreach($attributes as $attr_name => $attr_value){
                $attr_group_id = $this->query_assoc("SELECT attr_group_id FROM `wpi3_xml_attr_variations` WHERE attr_name='$attr_name' AND xml_id='$xml_id'","attr_group_id");
                if($attr_group_id !== false){
                    $attribute_id = $this->query_assoc("SELECT attribute_id FROM `wpi3_woocommerce_attribute_taxonomies` WHERE attribute_label='$attr_name'","attribute_id");
                    if($check_group_id==$attr_group_id){
                        $group_id_arr[] = $check_group_id;
                        
                        if(!empty($attribute_id) and !empty($attr_value)){
                            $attributes_group_arr[$check_group_id][] = [
                                    'id' => (integer) $attribute_id,
                                    'option' => (string) $attr_value
                            ];
                        }
                        
                    }
                }
            }
        }
        
        if($group_id_arr[0]){
            $group_id_arr = array_unique($group_id_arr);
            
            
            $check_sku = $this->query_assoc("SELECT post_id FROM `wpi3_postmeta` WHERE meta_key='_sku' AND meta_value='$offer_vendor_code'", "post_id");
            if($check_sku){
                $offer_vendor_code = $offer_vendor_code . '-' . rand(0,1000);
            }
            
            
            foreach ($group_id_arr as $group_id){
                $create[] = [
                    'regular_price' => (string) $offer_price,
                    'sku' => (string) $offer_vendor_code.'-'.rand(1,10000), //Unique identifier.
                    'image' => [
                        //'id' => 3497
                        'src' => (string) $images[0]
                    ],
                    'attributes' => $attributes_group_arr[$group_id]
                ];
            }
            
            $variations_data = [
                'create' => $create
            ];
            dump($variations_data);
            if( !empty($attributes_group_arr[$group_id]) and !empty($offer_price) and !empty($images[0])) {
                
                try {
                    $rezult = $woocommerce->post('products/'.$product_id.'/variations/batch', $variations_data);
                }
                catch(Exception $e){
                    $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
                    $err = $info . $e->getMessage();
                    //echo $err;
                    $this->errorLog($err);
                }
                
            }
            
            $product_id = null;
            $offer_price = null;
            $attributes = null;
            $images = null;
            $woocommerce = null;
            $wpi3_xml_attr_variations_groups_rezult = null;
            $wpi3_xml_attr_variations_groups_row = null;
            $check_group_id = null;
            $attr_name = null;
            $attr_value = null;
            $attr_group_id = null;
            $attribute_id = null;
            $group_id_arr = null;
            $attributes_group_arr = null;
            $group_id = null;
            $create = null;
            $variations_data = null;
            unset($product_id,$offer_price,$attributes,$images,$woocommerce,$wpi3_xml_attr_variations_groups_rezult,$wpi3_xml_attr_variations_groups_row,$check_group_id,$attr_name,$attr_value,$attr_group_id,$attribute_id,$group_id_arr,$attributes_group_arr,$group_id,$create,$variations_data);
            
            //pauza dla sborki musora
            // time_nanosleep(0, 10000000);
            //pauza dla sborki musora
            if(isset($rezult)){
                dump($rezult);
                return $rezult->create[0]->id;
            }else{
                return false;
            }
            
        }else{
            
            $product_id = null;
            $offer_price = null;
            $attributes = null;
            $images = null;
            $woocommerce = null;
            $wpi3_xml_attr_variations_groups_rezult = null;
            $wpi3_xml_attr_variations_groups_row = null;
            $check_group_id = null;
            $attr_name = null;
            $attr_value = null;
            $attr_group_id = null;
            $attribute_id = null;
            $group_id_arr = null;
            $attributes_group_arr = null;
            $group_id = null;
            $create = null;
            $variations_data = null;
            unset($product_id,$offer_price,$attributes,$images,$woocommerce,$wpi3_xml_attr_variations_groups_rezult,$wpi3_xml_attr_variations_groups_row,$check_group_id,$attr_name,$attr_value,$attr_group_id,$attribute_id,$group_id_arr,$attributes_group_arr,$group_id,$create,$variations_data);
            
            //pauza dla sborki musora
            //time_nanosleep(0, 10000000);
            //pauza dla sborki musora
            
            return false;
        }
        
        
    }
    
    
    
    /*
    public function formAddVariations($product_id, $offer_vendor_code, $offer_price, $attributes, $images, $xml_id, $woocommerce)
    {
    
    $this->changeProductToVariable($product_id, $woocommerce);
    
    $wpi3_xml_attr_variations_groups_rezult = $this->query("SELECT id FROM `wpi3_xml_attr_variations_groups` WHERE xml_id='$xml_id'");
    while($wpi3_xml_attr_variations_groups_row = $wpi3_xml_attr_variations_groups_rezult->fetch_assoc()){
        $check_group_id = $wpi3_xml_attr_variations_groups_row['id'];
        
        foreach($attributes as $attr_name => $attr_value){
            $attr_group_id = $this->query_assoc("SELECT attr_group_id FROM `wpi3_xml_attr_variations` WHERE attr_name='$attr_name' AND xml_id='$xml_id'","attr_group_id");
            if($attr_group_id !== false){
                $attribute_id = $this->query_assoc("SELECT attribute_id FROM `wpi3_woocommerce_attribute_taxonomies` WHERE attribute_label='$attr_name'","attribute_id");
                if($check_group_id==$attr_group_id){
                    $group_id_arr[] = $check_group_id;
                    
                    if(!empty($attribute_id) and !empty($attr_value)){
                        $attributes_group_arr[$check_group_id][] = [
                            'id' => (integer) $attribute_id,
                            'option' => (string) $attr_value
                        ];
                    }
                    
                }
            }
        }
    }
    
    if($group_id_arr[0]){
        $group_id_arr = array_unique($group_id_arr);
    
        $check_sku = $this->query_assoc("SELECT post_id FROM `wpi3_postmeta` WHERE meta_key='_sku' AND meta_value='$offer_vendor_code'", "post_id");
        if($check_sku){
            $offer_vendor_code = $offer_vendor_code . '-' . rand(0,1000);
        }
    
        foreach ($group_id_arr as $group_id){
            $create[] = [
                'regular_price' => (string) $offer_price,
                'sku' => (string) $offer_vendor_code, //Unique identifier.
                'image' => [
                    //'id' => 3497
                    'src' => (string) $images[0]
                ],
                'attributes' => $attributes_group_arr[$group_id]
            ];
        }
    
        $variations_data = [
            'create' => $create
        ];
        dump($variations_data);
        if( !empty($attributes_group_arr[$group_id]) and !empty($offer_price) and !empty($images[0])) {
    
            try {
                $rezult = $woocommerce->post('products/'.$product_id.'/variations/batch', $variations_data);
            }
            catch(Exception $e){
                $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
                $err = $info . $e->getMessage();
                //echo $err;
                $this->errorLog($err);
            }
            
        }
        
        $product_id = null;
        $offer_price = null;
        $attributes = null;
        $images = null;
        $woocommerce = null;
        $wpi3_xml_attr_variations_groups_rezult = null;
        $wpi3_xml_attr_variations_groups_row = null;
        $check_group_id = null;
        $attr_name = null;
        $attr_value = null;
        $attr_group_id = null;
        $attribute_id = null;
        $group_id_arr = null;
        $attributes_group_arr = null;
        $group_id = null;
        $create = null;
        $variations_data = null;
        unset($product_id,$offer_price,$attributes,$images,$woocommerce,$wpi3_xml_attr_variations_groups_rezult,$wpi3_xml_attr_variations_groups_row,$check_group_id,$attr_name,$attr_value,$attr_group_id,$attribute_id,$group_id_arr,$attributes_group_arr,$group_id,$create,$variations_data);
    
        //pauza dla sborki musora
       // time_nanosleep(0, 10000000);
        //pauza dla sborki musora
        if(isset($rezult)){
            dump($rezult);
            return $rezult->create[0]->id;
        }else{
            return false;
        }
        
    }else{
    
        $product_id = null;
        $offer_price = null;
        $attributes = null;
        $images = null;
        $woocommerce = null;
        $wpi3_xml_attr_variations_groups_rezult = null;
        $wpi3_xml_attr_variations_groups_row = null;
        $check_group_id = null;
        $attr_name = null;
        $attr_value = null;
        $attr_group_id = null;
        $attribute_id = null;
        $group_id_arr = null;
        $attributes_group_arr = null;
        $group_id = null;
        $create = null;
        $variations_data = null;
        unset($product_id,$offer_price,$attributes,$images,$woocommerce,$wpi3_xml_attr_variations_groups_rezult,$wpi3_xml_attr_variations_groups_row,$check_group_id,$attr_name,$attr_value,$attr_group_id,$attribute_id,$group_id_arr,$attributes_group_arr,$group_id,$create,$variations_data);
    
        //pauza dla sborki musora
        //time_nanosleep(0, 10000000);
        //pauza dla sborki musora
        
        return false;
    }
    
    
    }
    */
    
    
    public function createShortDescription($offer_description)
    {
        $offer_description = strip_tags($offer_description);
        
        $short_description = stristr($offer_description, '. ', TRUE);
        if(!$short_description){
            $short_description = stristr($offer_description, '.&nbsp;', TRUE);
        }
        
        if(!$short_description){
            $short_description = $offer_description;
        }else{
            $short_description = $short_description.'.';
        }
        
        return $short_description;
    }
    
    
//OCtoWC methods #########################################################
    
    public function addOcToWcProductDefaultAttributes($product_id, $woocommerce)
    {
        $product = $this->getProduct($product_id, $woocommerce);
        $attributes = $product->attributes;
        
        $default_attributes = [];
        foreach ($attributes as $attribute){
            $default_attributes[] = [
                'id' => (integer) $attribute->id,
                'name' => $attribute->name,
                'option' => $attribute->options[0]
            ];
        }
        
        /*
        $data = [
            'update' => [
                [
                    'id' => (integer) $product_id,
                    'default_attributes' => $default_attributes
                ]
            ]
        ];
        */
        $data = [
            'update' => [
                [
                    'id' => (integer) $product_id,
                    'default_attributes' => [
                    
                        [
                            'id' => 2,
                            'name' => 'Size',
                            'option' => 'Small'
                        ]
                    
                    ]
                ]
            ]
        ];
        
        try {
            $rezult = $woocommerce->post('products/batch', $data);
        }
        catch(Exception $e){
            $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
            $err = $info . $e->getMessage();
            //echo $err;
            $this->errorLog($err);
        }
        
       // show("Для товара $product_id пытаемся установить по умолчанию следующие атрибуты: ");
       // dump($default_attributes);
       // show("Результат операции:");
        //dump($data);
        //print_r($rezult);
        return $rezult;
    }
    
    
    
    public function formAddOcToWcVariations($product_id,
                                            $wc_model,
                                            $wc_price,
                                            $attributes,
                                            //$images,
                                            $woocommerce)
    {
        $c_attributes = $attributes;
        //$this->log('count_data', count($attributes), false);
        if(count($attributes)>1){
            foreach ($attributes as $attr_name => $attr_value) {
        
                $attribute_id = $this->query_assoc("SELECT attribute_id FROM `wp_woocommerce_attribute_taxonomies` WHERE attribute_label='$attr_name'", "attribute_id");
                if (!empty($attribute_id) and !empty($attr_value)) {
            
                    foreach ($attr_value as $attr_value_arr){
                        $attributes_group_arr = [];
                        $create = [];
                
                        $value = $attr_value_arr['value'];
                        $price = $attr_value_arr['price'];
                        $price_prefix = $attr_value_arr['price_prefix'];
                
                        $option_price = $wc_price;
                        if($price_prefix == '+'){
                            $option_price = $wc_price+$price;
                        }else if($price_prefix == '-'){
                            $option_price = $wc_price-$price;
                        }
                
                        $attributes_group_arr[] = [
                            'id' => (integer)$attribute_id,
                            'option' => (string)$value
                        ];
                
                        foreach ($c_attributes as $c_attr_name => $c_attr_value){
                            $c_attribute_id = $this->query_assoc("SELECT attribute_id FROM `wp_woocommerce_attribute_taxonomies` WHERE attribute_label='$c_attr_name'", "attribute_id");
                            if (!empty($c_attribute_id) and !empty($c_attr_value)) {
                                if($c_attr_name != $attr_name){
                                    foreach ($c_attr_value as $c_attr_value_arr){
                                        $c_value = $c_attr_value_arr['value'];
                                        $c_price = $c_attr_value_arr['price'];
                                        $c_price_prefix = $c_attr_value_arr['price_prefix'];
                                
                                        $c_option_price = $option_price;
                                        if($c_price_prefix == '+'){
                                            $c_option_price = $option_price+$c_price;
                                        }else if($c_price_prefix == '-'){
                                            $c_option_price = $option_price-$c_price;
                                        }
                                
                                
                                        $attributes_group_arr1 = $attributes_group_arr;
                                        $attributes_group_arr1[] = [
                                            'id' => (integer)$c_attribute_id,
                                            'option' => (string)$c_value
                                        ];
                                
                                
                                        //create option
                                        $create[] = [
                                            'regular_price' => (string)$c_option_price,
                                            'sku' => (string)$wc_model . '-' . rand(1, 10000), //Unique identifier.
                                            //'image' => [ 'src' => (string)$images[0] ],
                                            'attributes' => $attributes_group_arr1
                                        ];
                                        //create option END
                                
                                        //########################################################################
                                
                                        $variations_data = [
                                            'create' => $create
                                        ];
                                
                                        //$this->log('variations_data', $variations_data, true);
                                
                                        try {
                                            $rezult = $woocommerce->post('products/' . $product_id . '/variations/batch', $variations_data);
                                        } catch (Exception $e) {
                                            $info = 'В методе: ' . __METHOD__ . ' около строки: ' . __LINE__ . ' произошла ошибка API: ';
                                            $err = $info . $e->getMessage();
                                            //echo $err;
                                            $this->errorLog($err);
                                        }
                                
                                        //########################################################################
                                
                                    }
                                }
                            }
                        }
                
                        //create bilo tut...
                    }
            
                }
                break;//Завершим цикл так как нужно прогнать только первуюопцию во избежание дуюблей
            }
        }
        //Если нет опций для взаимосвязи
        
        else{
            foreach ($attributes as $attr_name => $attr_value) {
                $attribute_id = $this->query_assoc("SELECT attribute_id FROM `wp_woocommerce_attribute_taxonomies` WHERE attribute_label='$attr_name'", "attribute_id");
                if (!empty($attribute_id) and !empty($attr_value)) {
                    foreach ($attr_value as $attr_value_arr){
                        $attributes_group_arr = [];
                        $create = [];
                        
                        $value = $attr_value_arr['value'];
                        $price = $attr_value_arr['price'];
                        $price_prefix = $attr_value_arr['price_prefix'];
    
                        $option_price = $wc_price;
                        if($price_prefix == '+'){
                            $option_price = $wc_price+$price;
                        }else if($price_prefix == '-'){
                            $option_price = $wc_price-$price;
                        }
    
                        $attributes_group_arr[] = [
                            'id' => (integer)$attribute_id,
                            'option' => (string)$value
                        ];
    
                        //create option
                        $create[] = [
                            'regular_price' => (string)$option_price,
                            'sku' => (string)$wc_model . '-' . rand(1, 10000), //Unique identifier.
                            //'image' => [ 'src' => (string)$images[0] ],
                            'attributes' => $attributes_group_arr
                        ];
                        //create option END
    
                        //########################################################################
                        $variations_data = [
                            'create' => $create
                        ];
                        
                        try {
                            $rezult = $woocommerce->post('products/' . $product_id . '/variations/batch', $variations_data);
                        } catch (Exception $e) {
                            $info = 'В методе: ' . __METHOD__ . ' около строки: ' . __LINE__ . ' произошла ошибка API: ';
                            $err = $info . $e->getMessage();
                            //echo $err;
                            $this->errorLog($err);
                        }
                        //########################################################################
                        
                    }
                }
            }
        }
        
        
        
//... try bilo
        
        //return $rezult;
    }
    
    public function createSlug($name){
        $name = (string) $name;
        $slug = translit($name);
        return (string) $slug;
    }
    
    public function checkAttributeName($attr_name)
    {
        $attr_name = (string) $attr_name;
        $slug = $this->createSlug($attr_name);
        $attribute_id =  $this->query_assoc("SELECT attribute_id FROM wp_woocommerce_attribute_taxonomies WHERE attribute_label='$attr_name'", "attribute_id");
        if(!$attribute_id){
            $attribute_id = $this->query_assoc("SELECT attribute_id FROM wp_woocommerce_attribute_taxonomies WHERE attribute_name='$slug'", "attribute_id");
        }
        return $attribute_id;
    }
    
    public function createAttributeName($attr_name, $woocommerce)
    {
        $attr_name = (string) $attr_name;
        $slug = $this->createSlug($attr_name);
        //Create attr name
        $data_attr = [
            'name' => (string) $attr_name,
            'slug' => $slug,//obyazatelno 28 simbols
            'type' => 'select',
            'order_by' => 'id',
            'has_archives' => true
        ];
    
        if(!empty($attr_name)){
            try {
                $attr = $woocommerce->post('products/attributes', $data_attr);
            }
            catch(Exception $e){
                $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
                $err = $info . $e->getMessage();
                //echo $err;
                $this->errorLog($err);
                return false;
            }
        
        }else{
            $this->errorLog("Название атребута пустое 1");
            return false;
        }
        //$this->errorLog("Создан новый атребут: ".$attr->id);
        
        return $attr;
        //Create attr name END
    }
    
    public function checkAttributeValue($attr_value)
    {
        $attr_value = (string) $attr_value;
        $attr_value_slug = $this->createSlug($attr_value);
        $attr_value_id = $this->query_assoc("SELECT term_id FROM `wp_terms` WHERE name='$attr_value'","term_id");
        if(!$attr_value_id){
            $attr_value_id = $this->query_assoc("SELECT term_id FROM `wp_terms` WHERE slug='$attr_value_slug'","term_id");
        }
        return $attr_value_id;
    }
    
    public function createAttributeValue($attribute_id, $attr_value, $woocommerce)
    {
        $attr_value = (string) $attr_value;
        $attr_value_slug = $this->createSlug($attr_value);
        $data_attr_value = [
            'name' => (string) $attr_value,
            'slug' => $attr_value_slug
        ];
        if(!empty($attr_value)){
            try {
                $attr_term = $woocommerce->post('products/attributes/'.$attribute_id.'/terms', $data_attr_value);
            }
            catch(Exception $e){
                $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
                $err = $info . $e->getMessage();
                //echo $err;
                $this->errorLog($err);
                return false;
            }
        }else{
            //$this->errorLog("Значение атребута пустое 1");
            return false;
        }
        
        return $attr_term;
    }
   
    public function checkAddOcToWcAtributes($attributes, $woocommerce)
    {
        //CHECK ADD ATRIBUTES & TERMS
        foreach ($attributes as $attr_name => $attr_value){
            $attribute_id = $this->checkAttributeName($attr_name);
            
            //Create attribute if not isset
            if(!$attribute_id){
                
                //Create atribute name
                $attr = $this->createAttributeName($attr_name, $woocommerce);
                
                //Check add attr value
                if(!is_array($attr_value)){
                    $attr_value_id = $this->checkAttributeValue($attr_value);
                    if(!$attr_value_id){
                        $attr_term = $this->createAttributeValue($attr->id, $attr_value, $woocommerce);
                    }
                }else{
                    //переберем массив если пришел
                    foreach ($attr_value as $attr_value_string){
                        $attr_value_id = $this->checkAttributeValue($attr_value_string);
                        if(!$attr_value_id){
                            $attr_term = $this->createAttributeValue($attr->id, $attr_value_string, $woocommerce);
                        }
                    }
                    //переберем массив если пришел КОНЕЦ
                }
                //Check add attr value END
                
                
            }
            //Create attribute if not isset END
            
            //ATRIBUTE ISSET CHECK ADD VALUE
            else{
                //Check add attr value
                if(!is_array($attr_value)){
                    $attr_value_id = $this->checkAttributeValue($attr_value);
                    if(!$attr_value_id){
                        $attr_term = $this->createAttributeValue($attribute_id, $attr_value, $woocommerce);
                    }
                }else{
                    //переберем массив если пришел
                    foreach ($attr_value as $attr_value_string){
                        $attr_value_id = $this->checkAttributeValue($attr_value_string);
                        if(!$attr_value_id){
                            $attr_term = $this->createAttributeValue($attribute_id, $attr_value_string, $woocommerce);
                        }
                    }
                    //переберем массив если пришел КОНЕЦ
                }
                //Check add attr value END
            }
            //ATRIBUTE ISSET CHECK ADD VALUE END
            
        }
        //CHECK ADD ATRIBUTES & TERMS END
        
        return true;//chtonibut vernem
    }
    
    
    public function formOcToWcCategories($wc_categories)
    {
        if(!empty($wc_categories)){
            foreach ($wc_categories as $wc_category){
                $categories[] = array(
                    'id' => (int)$wc_category
                );
            }
        }else{
            $categories[] = array(
                'id' => 22//uncategorized
            );
        }
        return $categories;
    }
    
    public function formOcToWcAttributes($attributes, $variation=false)
    {
        foreach ($attributes as $attr_name => $attr_value){
            $attribute_id =  $this->query_assoc("SELECT attribute_id FROM wp_woocommerce_attribute_taxonomies WHERE attribute_label='$attr_name'", "attribute_id");
            
            /*
            if($attr_name == 'Размер'){
                $variation = true;//true/false
            }else{
                $variation = false;//true/false
            }
            */
            
            
            if(!empty($attribute_id) and !empty($attr_name) and !empty($attr_value)){
                if(!is_array($attr_value)){
                    $attr_value = [$attr_value];
                }
                    $attributes_arr[] = [
                        'id' => (integer) $attribute_id, //id atrebuta v wordpress
                        'name' => $attr_name, // nazva atrebuta
                        //'position' => '0',
                        'visible' => true, //bool
                        'variation' => $variation, //bool
                        'options' => $attr_value // masssiv znacheniy atributa
                    ];
                
            }
            
            
        }
        if(isset($attributes_arr)){
            return $attributes_arr;
        }else{
            return [];
        }
    }
    
    public function addOcToWcProduct(
    $wc_product_name,
    $wc_price,
	$wc_special_price,
    $wc_model,
    $wc_product_description,
    $wc_product_images,
    $wc_categories,
    $wc_attributes,
    $wc_variations,
    $wc_form_variations,
    $wc_option_add_to_dish,
    $status,
    $woocommerce)
{
    $wc_product_images = $this->formImages($wc_product_images);
    $categories = $this->formOcToWcCategories($wc_categories);
    
    //$status = 'draft';
    //$status = 'publish';
    
    $type = 'simple';
    if(!empty($wc_form_variations)){
        $type = 'variable';
    }
    
    //добавим атребуты и их значения в wordpress
    //if(!empty($wc_variations) and !empty($wc_attributes)){
    $attributes = array_merge($wc_variations, $wc_attributes);
    // }
    
    if(!empty($attributes)){
        $this->checkAddOcToWcAtributes($attributes, $woocommerce);
    }
    
    //Сформируем массив атрибутов для товара
    if(!empty($wc_variations)){
        $variations_arr = $this->formOcToWcAttributes($wc_variations, true);
    }
    if(!empty($wc_attributes)){
        $attributes_arr = $this->formOcToWcAttributes($wc_attributes, false);
    }
    
    
    if(!empty($variations_arr) and !empty($attributes_arr)){
        $attributes_variations_arr = array_merge($variations_arr,  $attributes_arr);
    }else if(!empty($variations_arr)){
        $attributes_variations_arr = $variations_arr;
    }else if(!empty($attributes_arr)){
        $attributes_variations_arr = $attributes_arr;
    }
    
    //Формируем массив товара
    $data = [];
    if(isset($wc_product_name) and !empty($wc_product_name)){
        $data['name'] = (string) $wc_product_name;
    }
    if(isset($type) and !empty($type)){
        $data['type'] = $type;
    }
	
    if(isset($wc_price) and !empty($wc_price)){
        $data['regular_price'] = (string) $wc_price;
    }
	
	if(isset($wc_special_price) and !empty($wc_special_price)){
        $data['sale_price'] = (string) $wc_special_price;
    }
	
    if(isset($wc_product_description) and !empty($wc_product_description)){
        $data['description'] = (string) $wc_product_description;
    }
    if(isset($wc_product_description) and !empty($wc_product_description)){
        $data['short_description'] = (string) $wc_product_description;
    }
    if(isset($wc_model) and !empty($wc_model)){
        $data['sku'] = (string) $wc_model.'-'.rand(1,100).'-'.rand(1,100000);
    }
    if(isset($categories) and !empty($categories)){
        $data['categories'] = $categories;
    }
    if(isset($wc_product_images) and !empty($wc_product_images)){
        $data['images'] = $wc_product_images;
    }
    if(isset($attributes_variations_arr) and !empty($attributes_variations_arr)){
        $data['attributes'] = $attributes_variations_arr;
    }
    if(isset($status) and !empty($status)){
        $data['status'] = $status;
    }
    //Формируем массив товара КОНЕЦ
    
    try {
        $product_rezult = $woocommerce->post('products', $data);
    }
    catch(Exception $e){
        $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
        $err = $info . $e->getMessage();
        //echo $err;
        $this->errorLog($err);
    }
    
    $product_id = $product_rezult->id;
    if($product_id){
        
        //добавим товару вариации
        if(!empty($wc_form_variations)){
            $this->formAddOcToWcVariations($product_id,
                $wc_model,
                $wc_price,
                $wc_form_variations,
                //$images,
                $woocommerce);
        }
        //добавим товару вариации КОНЕЦ
        //Зададим выбранные атребуты по умолчанию ДОРАБОТАТЬ
        //$this->addProductDefaultAttributes($product_id, $woocommerce);//пока не надо
        
        //Зададим "добавить к блюду" если это необходимо
        if($wc_option_add_to_dish){
            if(in_array(43, $wc_categories)){// 43 - категория пиццы
                $this->query_insert("INSERT INTO `wp_postmeta` (post_id, meta_key, meta_value) VALUES ('$product_id', '_product_meta_id', '1')");
            }else{
                $this->query_insert("INSERT INTO `wp_postmeta` (post_id, meta_key, meta_value) VALUES ('$product_id', '_product_meta_id', '2')");
            }
        }
        
        return $product_id;
    }else{
        return false;
    }
    
}
    
    public function updateOcToWcProduct(
        $wc_product_id,
        $wc_product_name,
        $wc_price,
		$wc_special_price,
        $wc_model,
        $wc_product_description,
        $wc_product_images,
        $wc_categories,
        $wc_attributes,
        $wc_variations,
        $wc_form_variations,
        $wc_option_add_to_dish,
        $status,
        $woocommerce)
    {
        $wc_product_images = $this->formImages($wc_product_images);
        $categories = $this->formOcToWcCategories($wc_categories);
        
        $type = 'simple';
        
        //Закоментим опции и атребуты пока...
        
        if(!empty($wc_form_variations)){
            $this->deleteProductVariations($wc_product_id, $woocommerce);
            $type = 'variable';
        }
        
        $attributes = array_merge($wc_variations, $wc_attributes);
        
        if(!empty($attributes)){
            $this->checkAddOcToWcAtributes($attributes, $woocommerce);
        }
        
        
        //Сформируем массив атрибутов для товара
        if(!empty($wc_variations)){
            $variations_arr = $this->formOcToWcAttributes($wc_variations, true);
        }
        if(!empty($wc_attributes)){
            $attributes_arr = $this->formOcToWcAttributes($wc_attributes, false);
        }
        
        
        if(!empty($variations_arr) and !empty($attributes_arr)){
            $attributes_variations_arr = array_merge($variations_arr,  $attributes_arr);
        }else if(!empty($variations_arr)){
            $attributes_variations_arr = $variations_arr;
        }else if(!empty($attributes_arr)){
            $attributes_variations_arr = $attributes_arr;
        }
        
        //Закоментим опции и атребуты пока... КОНЕЦ
        
        //Формируем массив товара
        $data = [];
        if(isset($wc_product_name) and !empty($wc_product_name)){
            $data['name'] = (string) $wc_product_name;
        }
        if(isset($type) and !empty($type)){
            $data['type'] = $type;
        }
        if(isset($wc_price) and !empty($wc_price)){
            $data['regular_price'] = (string) $wc_price;
        }
		if(isset($wc_special_price) and !empty($wc_special_price)){
			$data['sale_price'] = (string) $wc_special_price;
		}
        if(isset($wc_product_description) and !empty($wc_product_description)){
            $data['description'] = (string) $wc_product_description;
        }
        if(isset($wc_product_description) and !empty($wc_product_description)){
            $data['short_description'] = (string) $wc_product_description;
        }
        if(isset($wc_model) and !empty($wc_model)){
            $data['sku'] = (string) $wc_model.'-'.rand(1,100).'-'.rand(1,100000);
        }
        if(isset($categories) and !empty($categories)){
            $data['categories'] = $categories;
        }
        if(isset($wc_product_images) and !empty($wc_product_images)){
            $data['images'] = $wc_product_images;
        }
        if(isset($attributes_variations_arr) and !empty($attributes_variations_arr)){
            $data['attributes'] = $attributes_variations_arr;
        }
        if(isset($status) and !empty($status)){
            $data['status'] = $status;
        }
        //Формируем массив товара КОНЕЦ.
        
        try {
            $product_rezult = $woocommerce->put('products/'.$wc_product_id, $data);
            //$this->log('updateOcToWcProduct_log', $product_rezult, false);
            
        }
        catch(Exception $e){
            $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
            $err = $info . $e->getMessage();
            //echo $err;
            $this->errorLog($err);
            return false;
        }
    
        //добавим товару вариации.
        if(!empty($wc_form_variations)){
            $this->formAddOcToWcVariations($wc_product_id,
                $wc_model,
                $wc_price,
                $wc_form_variations,
                //$images,
                $woocommerce);
        }
        //добавим товару вариации КОНЕЦ
        
        //Добавить к блюду
        $this->query_delete("DELETE FROM `wp_postmeta` WHERE post_id='$wc_product_id' AND meta_key='_product_meta_id'");
        if($wc_option_add_to_dish){
            if(in_array(43, $wc_categories)){// 43 - категория пиццы
                $this->query_insert("INSERT INTO `wp_postmeta` (post_id, meta_key, meta_value) VALUES ('$wc_product_id', '_product_meta_id', '1')");
            }else{
                $this->query_insert("INSERT INTO `wp_postmeta` (post_id, meta_key, meta_value) VALUES ('$wc_product_id', '_product_meta_id', '2')");
            }
        }
        //Добавить к блюду КОНЕЦ
        
        return $product_rezult;
    }
    
    public function log($filename, $data, $append=false)
    {
        if($append){
            file_put_contents(LOG_DIR.'/'.$filename.'.txt', print_r($data, true), FILE_APPEND);
        }else{
            file_put_contents(LOG_DIR.'/'.$filename.'.txt', print_r($data, true));
        }
        
    }
    
//OCtoWC methods END ####################################################
    
    
    
    public function addProduct(
        $xml_id,
        $offer_category,
        $offer_name,
        $offer_price,
        $offer_vendor_code,
        $offer_description,
        $images,
        $attributes,
        $type,
        $woocommerce)
    {
    
        $offer_name = (string) $offer_name;
        //$slug = translit($offer_name);
        $offer_group_id = $this->createGroupIdBySku($offer_vendor_code);
        if($type=='variable'){
            if( ($offer_group_id !== false) and ($offer_group_id !== $offer_vendor_code) ){
                $offer_vendor_code = $offer_group_id;
            }else{
                $offer_vendor_code = $offer_vendor_code . '-' . rand(0,1000);
            }
        }
        /*
        $check_sku = $this->query_assoc("SELECT post_id FROM `wpi3_postmeta` WHERE meta_key='_sku' AND meta_value='$offer_vendor_code'", "post_id");
            if($check_sku){
                $offer_vendor_code = $offer_vendor_code . '-' . rand(0,1000);
            }
			*/
    
        $short_description = $this->createShortDescription($offer_description);
        $categories = $this->formCategories($xml_id, $offer_category);
        $images_arr = $this->formImages($images);
        $attributes_arr = $this->formAttributes($attributes, $xml_id);
        //$type = 'variable';//variable or simple
        
        $data = [
            'name' => (string) $offer_name, //product name
            //'slug' => 'ostap-api-product-0004', //sef uri
            //'permalink' => 'http://antre.birka.club/product/joggers-test/',//Product URL
            'type' => $type, //Product type. Options: simple, grouped, external and variable. Default is simple
            'status' => 'publish', //Product status (post status). Options: draft, pending, private and publish. Default is publish.
            'featured' => false, //Featured product. Default is false.
            'catalog_visibility' => 'visible', //Catalog visibility. Options: visible, catalog, search and hidden. Default is visible.
            'description' => (string) $offer_description,
            'short_description' => $short_description,
            'sku' => (string) $offer_vendor_code, //Unique identifier.
            'regular_price' => (string) $offer_price, //Product regular price.
            //'sale_price' => '497.00', //Product sale price.
            //'stock_quantity' => 100, //integer
            'stock_status' => 'instock', //Controls the stock status of the product. Options: instock, outofstock, onbackorder. Default is instock.
            'weight' => '', //string Product weight.
            //'dimensions' => , //object - Product dimensions. object(stdClass)#8 (3) {   ["length"]=>string(0) "" ["width"]=> string(0) "" ["height"]=> string(0) "" }
            'categories' => $categories,
            'images' => $images_arr,
            'attributes' => $attributes_arr,
            /*
            'default_attributes' => [
                [
                    'id' => 1, //id atrebuta v wordpress
                    'name' => 'Цвет',
                    'option' => 'Светло-серый', //vibrannaya po umplchaniyu opciya
                ],
                [
                    'id' => 2, //id atrebuta v wordpress
                    'name' => 'Размер',
                    'option' => 'M', //vibrannaya po umplchaniyu opciya
                ]
            ],
            */
            'meta_data' => [
                [ 'key' => '_translation_porduct_type', 'value' => 'variable', ],
                [ 'key' => 'woovina_sidebar', 'value' => '0', ],
                [ 'key' => 'woovina_second_sidebar', 'value' => '0', ],
                [ 'key' => 'woovina_disable_margins', 'value' => 'enable', ],
                [ 'key' => '"woovina_display_top_bar', 'value' => 'default', ],
                [ 'key' => 'woovina_display_header', 'value' => 'default', ],
                [ 'key' => 'woovina_center_header_left_menu', 'value' => '0', ],
                [ 'key' => 'woovina_custom_header_template', 'value' => '0', ],
                [ 'key' => 'woovina_header_custom_menu', 'value' => '0', ],
                [ 'key' => 'woovina_disable_title', 'value' => 'default', ],
                [ 'key' => 'woovina_disable_heading', 'value' => 'default', ],
                [ 'key' => 'woovina_disable_breadcrumbs', 'value' => 'default', ],
                [ 'key' => 'woovina_display_footer_widgets', 'value' => 'default', ],
                [ 'key' => '"woovina_display_footer_bottom', 'value' => 'default', ],
                [ 'key' => '_yith_wfbt_ids', 'value' => [], ]
            ]
        ];
    
        if( !empty($offer_name) and !empty($offer_vendor_code) and !empty($offer_price) and !empty($categories) and !empty($images_arr) and !empty($attributes_arr) )
        {
           $check_product = $this->query_assoc("SELECT ID FROM `wpi3_posts` WHERE post_title='$offer_name'","ID");
           if($check_product == false){
               $check_product = $this->query_assoc("SELECT post_id FROM `wpi3_postmeta` WHERE meta_value='$offer_vendor_code'","post_id");
           }
           
           if($check_product == false){
    
               try {
                   $product_rezult = $woocommerce->post('products', $data);
               }
               catch(Exception $e){
                   $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
                   $err = $info . $e->getMessage();
                   //echo $err;
                   $this->errorLog($err);
               }
               
            }else{
                show_strong("Товар с названием $offer_name уже ссуществует и имеет ID - $check_product Поэтому добавлять его не будем!");
            }
           
        }
        
        $product_id = $product_rezult->id;
        if($product_id){
            $this->query_insert("INSERT INTO wpi3_term_relationships (object_id, term_taxonomy_id) VALUES ($product_id, 59)");
			//uk
            //$this->query_insert("INSERT INTO wpi3_term_relationships (object_id, term_taxonomy_id) VALUES ($product_id, 62)");
        }
        
        $xml_id = null;
        $offer_category = null;
        $offer_name = null;
        $offer_price = null;
        $offer_vendor_code = null;
        $offer_description = null;
        $images = null;
        $attributes = null;
        $type = null;
        $woocommerce = null;
        $short_description = null;
        $categories = null;
        $images_arr = null;
        $attributes_arr = null;
        $data = null;
        $product_rezult = null;
        unset($xml_id,$offer_category,$offer_name,$offer_price,$offer_vendor_code,$offer_description,$images,$attributes,$type,$woocommerce,$short_description,$categories,$images_arr,$attributes_arr,$data,$product_rezult);
    
        //pauza dla sborki musora
       // time_nanosleep(0, 10000000);
        //pauza dla sborki musora
        
        if($product_id){
            return $product_id;
        }
        
    }
        
    public function createGroupIdBySku($offer_vendor_code)
    {
        $d1 = '-';
        if(strripos($offer_vendor_code, $d1)){
            $str_arr = explode($d1, $offer_vendor_code);
        
            if( count($str_arr) == 2){
                if( iconv_strlen($str_arr[0]) > 3){
                    return $str_arr[0];
                }
            }elseif( count($str_arr) > 2 ){
            
                array_pop($str_arr);
                return implode($d1, $str_arr);
            
            }
        }
    
        $d2 = '/';
        if(strripos($offer_vendor_code, $d2)){
            $str_arr = explode($d2, $offer_vendor_code);
        
            if( count($str_arr) == 2){
                if( iconv_strlen($str_arr[0]) > 3){
                    return $str_arr[0];
                }
            }elseif( count($str_arr) > 2 ){
            
                array_pop($str_arr);
                return implode($d2, $str_arr);
            
            }
        }

        
        $d3 = '_';
        if(strripos($offer_vendor_code, $d3)){
            $str_arr = explode($d3, $offer_vendor_code);
        
            if( count($str_arr) == 2){
                if( iconv_strlen($str_arr[0]) > 3){
                    return $str_arr[0];
                }
            }elseif( count($str_arr) > 2 ){
            
                array_pop($str_arr);
                return implode($d3, $str_arr);
            
            }
        }
    
        return false;
    }
    
    public function disableProduct($product_id, $woocommerce)
    {
        $data = [
            'status' => 'draft',
            'stock_status' => 'outofstock',
            'catalog_visibility' => 'hidden'
        ];
        if(!empty($product_id)){
    
            try {
                dump($woocommerce->put('products/'.$product_id, $data));
            }
            catch(Exception $e){
                $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
                $err = $info . $e->getMessage();
                //echo $err;
                $this->errorLog($err);
            }
            
        }
    }
    
    public function enableProduct($product_id, $woocommerce)
    {
        $data = [
            'status' => 'publish',
            'stock_status' => 'instock',
            'catalog_visibility' => 'visible'
        ];
    
        if(!empty($product_id)){
    
            try {
                dump($woocommerce->put('products/'.$product_id, $data));
            }
            catch(Exception $e){
                $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
                $err = $info . $e->getMessage();
                //echo $err;
                $this->errorLog($err);
            }
            
        }
        
    }
    
    
    public function updateProductAtributes($product_id, $attributes, $xml_id, $woocommerce){
       
        $attributes2 = $this->getProductAtributes($product_id, $woocommerce);
       $concat_attributes = $this->concatAttributes($attributes2, $attributes);
        
        $form_attributes = $this->formAttributes2($concat_attributes, $xml_id);
        
        $data = [
            'attributes' => $form_attributes
        ];
    
        if(!empty($form_attributes)){
    
            try {
                dump($woocommerce->put('products/'.$product_id, $data));
            }
            catch(Exception $e){
                $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
                $err = $info . $e->getMessage();
                //echo $err;
                $this->errorLog($err);
            }
            
        }
        
    }
    
    public function getProductAtributes($product_id, $woocommerce)
    {
    
        $attributes = [];
    
        try {
            $rez = $woocommerce->get('products/'.$product_id);
        }
        catch(Exception $e){
            $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
            $err = $info . $e->getMessage();
            //echo $err;
            $this->errorLog($err);
        }
        
        foreach($rez->attributes as $attr){
            foreach($attr->options as $option){
                $attributes[$attr->name][] = $option;
            }
        }
        
        return $attributes;
    }
    
    public function concatAttributes($attr1, $attr2){
        
        if(!empty($attr1)){
            foreach ($attr1 as $k =>$v){
                if( isset($attr2[$k]) ){
                    $v[] = $attr2[$k];
                    $attr2[$k] = $v;
                }
            }
        }
        
        return $attr2;
    }
    
    public function updateProductPrice($product_id, $price, $woocommerce)
    {
        $data = [
            'regular_price' => (float) $price
        ];
    
        if(!empty($product_id) and !empty($price)){
    
            try {
                dump($woocommerce->put('products/'.$product_id, $data));
            }
            catch(Exception $e){
                $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
                $err = $info . $e->getMessage();
                //echo $err;
                $this->errorLog($err);
            }
            
        }
        
    }
    
   public function errorLog($err){
       $time = date('H-i-s');
       $err = $time.' '.$err;
        file_put_contents( LOG_DIR . '/api_error_log.txt', $err.PHP_EOL, FILE_APPEND);
   }
   
   public function deleteProductVariation($product_id, $variation_id, $woocommerce)
   {
    
       try {
          $woocommerce->delete('products/'.$product_id.'/variations/'.$variation_id, ['force' => true]);
       }
       catch(Exception $e){
           $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
           $err = $info . $e->getMessage();
           //echo $err;
           $this->errorLog($err);
       }
       
   }
    
    public function deleteProductVariations($product_id, $woocommerce)
    {
        $variations = $this->getProductVariations($product_id, $woocommerce);
        if(!empty($variations)){
            foreach ($variations as $variation){
                $this->deleteProductVariation($product_id, $variation->id, $woocommerce);
            }
        }
    }
   
   public function updateProductVariationPrice($product_id, $variation_id, $offer_price, $woocommerce)
   {
       $data = [
           'regular_price' => (string) $offer_price
       ];
    
       try {
           dump($woocommerce->put('products/'.$product_id.'/variations/'.$variation_id, $data));
       }
       catch(Exception $e){
           $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
           $err = $info . $e->getMessage();
           //echo $err;
           $this->errorLog($err);
       }
       
   }
   
   public function getProductVariations($product_id, $woocommerce)
   {
       try {
           $rezult = $woocommerce->get('products/'.$product_id.'/variations');
       }
       catch(Exception $e){
           $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
           $err = $info . $e->getMessage();
           //echo $err;
           $this->errorLog($err);
       }
       
       //dump($rezult);
       return $rezult;
   }
    
    public function deleteProduct($product_id, $woocommerce)
    {
        
        try {
            $result = $woocommerce->delete('products/'.$product_id, ['force' => true]);
            return $result;
        }
        catch(Exception $e){
            $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
            $err = $info . $e->getMessage();
            //echo $err;
            $this->errorLog($err);
        }
        
    }
    
    public function getProduct($product_id, $woocommerce)
    {
        
        try {
            $rez = $woocommerce->get('products/'.$product_id);
        }
        catch(Exception $e){
            $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
            $err = $info . $e->getMessage();
            //echo $err;
            $this->errorLog($err);
        }
        
        return $rez;
    }
    
    
    public function deleteProductImages($product_id, $woocommerce)
    {
        try {
            $options = $this->getProductVariations($product_id, $woocommerce);
        }
        catch(Exception $e){
            $info = 'В файле ' . __FILE__ . ' в методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка: ';
            $err = $info . $e->getMessage();
            //echo $err;
            $this->errorLog($err);
        }
        
        $images_to_delete = [];
        foreach($options as $option){
            $images_to_delete[$option->image->name] = $option->image->src;
        }
        
        try {
            $product = $this->getProduct($product_id, $woocommerce);
        }
        catch(Exception $e){
            $info = 'В файле ' . __FILE__ . ' в методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка: ';
            $err = $info . $e->getMessage();
            //echo $err;
            $this->errorLog($err);
        }
        
        $images_arr = $product->images;
        foreach($images_arr as $image){
            $images_to_delete[$image->name] = $image->src;
        }
        
		foreach ($images_to_delete as $name => $src ){
			show("Удаляем медиафайл $name");
			$post_id = $this->query_assoc("SELECT `ID` FROM `wpi3_posts` WHERE `post_title`='$name' AND `post_type`='attachment'", "ID");
            $del_rez = wp_delete_attachment($post_id, true);
            if($del_rez){
                show("Медиафайл $post_id по имени $name был успешно удален");
            }else{
                echo show_strong("Не удалось удалить медиафайл $post_id по имени $name");
            }
        }
		        
        return $images_to_delete;
    }
	
	public function deleteMedia($date_start='2020-03-11', $date_end='2020-04-02')
	{
		$sql = "SELECT post_id FROM `wpi3_postmeta` WHERE post_id IN (SELECT ID FROM `wpi3_posts` WHERE DATE(post_date) BETWEEN '$date_start' AND '$date_end' AND post_type='attachment' AND post_title NOT LIKE '%favicon%') AND meta_value LIKE '%woocommerce%'";
		$wpi3_postmeta_rezult = $this->query($sql);
		$post_id_arr = [];
		while ($wpi3_postmeta_row = $wpi3_postmeta_rezult->fetch_assoc()){
			$post_id_arr[] = $wpi3_postmeta_row['post_id'];
		}
		
		$exclude_post_id_arr = [
		"6799",
		"2137",
		"2184",
		"2185",
		"2186",
		"2187",
		"2188",
		"2189",
		"2216",
		"2217",
		"2218",
		"2219",
		"2306",
		"2404",
		"2439",
		"2450",
		"2452",
		"2453",
		"2476",
		"2765",
		"12",
		"2963",
		"2975",
		"3096",
		"3133",
		"3276",
		"3289",
		"3293",
		"3299",
		"3308",
		"6799",
		"91437",
		"2035",
		"2036",
		"2037",
		"2038",
		"2039",
		"2040",
		"2041",
		"2136",
		"4161",
		"4162",
		"4224",
		"4225"
		];
		
		$post_id_arr = array_diff($post_id_arr, $exclude_post_id_arr);
		
		foreach($post_id_arr as $post_id){
			wp_delete_attachment($post_id, true);
		}
		
		
	}
        
    
    public function productCountLog($filename, $msg){
        $time = date('H-i-s');
        file_put_contents( LOG_DIR . '/'.$filename.'.txt', $time.' : '.$msg.PHP_EOL, FILE_APPEND);
    }
    
    public function getAllProducts($woocommerce){
        
        try {
            $rezult = $woocommerce->get('products');
        }
        catch(Exception $e){
            $info = 'В файле ' . __FILE__ . ' в методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка: ';
            $err = $info . $e->getMessage();
            //echo $err;
            $this->errorLog($err);
        }
        
        return $rezult;
    }
    
    public function deleteAllProducts($woocommerce)
    {
        while ( count($products = $this->getAllProducts($woocommerce))>0 ){
            foreach($products as $product){
                dump($product->id);
                $this->deleteProductImages($product->id, $woocommerce);
                $this->deleteProduct($product->id, $woocommerce);
            }
        }
    }
    
    public function addProductDefaultAttributes($product_id, $woocommerce)
    {
        $variations = $this->getProductVariations($product_id, $woocommerce);
        $attributes = $variations[0]->attributes;
        
        $default_attributes = [];
        foreach ($attributes as $attribute){
            $default_attributes[] = [
                'id' => (integer) $attribute->id,
                'name' => $attribute->name,
                'option' => $attribute->option
            ];
        }
        
        
        $data = [
            'update' => [
                [
                   'id' => (integer) $product_id,
                   'default_attributes' => $default_attributes
                ]
            ]
        ];
        
        try {
            $rezult = $woocommerce->post('products/batch', $data);
        }
        catch(Exception $e){
            $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
            $err = $info . $e->getMessage();
            //echo $err;
            $this->errorLog($err);
        }
        
        //show("Для товара $product_id установлены по умолчанию следующие атрибуты: ");
        //dump($default_attributes);
        
    }
	
	public function updateProductName($product_id, $offer_name, $woocommerce)
	{
        $data = [
            'name' => (string) $offer_name
        ];
        
            try {
                dump($woocommerce->put('products/'.$product_id, $data));
            }
            catch(Exception $e){
                $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
                $err = $info . $e->getMessage();
                //echo $err;
                $this->errorLog($err);
            }
            
    }
    
    public function updateDbPrice($product_id, $name, $price, $woocommerce)
    {
        $post_title = (string)$name;
        
        $id = $this->query_assoc("SELECT `product_id` FROM `wp_wc_product_meta_lookup` WHERE `sku`='$product_id'", "product_id");
        if(!$id){
            $id = $this->query_assoc("SELECT `ID` FROM `wp_posts` WHERE `post_type`='product' AND post_title='$post_title'", "ID");
        }
        if(!$id){
            $id = $this->query_assoc("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key`='_sku' AND meta_value='$product_id'", "post_id");
        }
    
        if($id){
    
            //$this->updateProductPrice($id, $price, $woocommerce);
            
           // $rez1 = $this->query_update("UPDATE wp_wc_product_meta_lookup SET min_price='$price' WHERE `product_id`='$id'");
            //$rez2 = $this->query_update("UPDATE wp_wc_product_meta_lookup SET max_price='$price' WHERE `product_id`='$id'");
            $rez3 = $this->query_update("UPDATE wp_postmeta SET meta_value='$price' WHERE `post_id`='$id' AND meta_key='_regular_price'");
            $rez4 = $this->query_update("UPDATE wp_postmeta SET meta_value='$price' WHERE `post_id`='$id' AND meta_key='_price'");
            if($rez3 and $rez4){
                $this->log('prices_update_log', 'id - '.$id.'=price - '.$price.PHP_EOL, true);
                return 'id - '.$id.'=price - '.$price;
            }
        }else{
            $this->log('prices_update_feil_log', 'товар - '.$product_id.'=под названием - '.$name.' не обновлен!'.PHP_EOL, true);
            return 'товар - '.$product_id.'=под названием - '.$name.' не обновлен!';
        }
        
        
        
        
    }
    
    
    
    
    
}