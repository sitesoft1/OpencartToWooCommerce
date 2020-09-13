<?php
//
set_time_limit(0);//snimaem ogranicheniya na vipolneniya skripta
//define('LANGUAGE_ID', 1);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

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
            echo "Не удалось подключиться к MySQL: (" . $this->db->connect_errno . ") " . $this->db->connect_error;
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
            echo "Не удалось выполнить запрос: $sql <br>";
            echo "Номер ошибки: " . $this->db->errno . "\n";
            echo "Ошибка: " . $this->db->error . "\n";
            return false;
        }
        
        if ($result->num_rows > 0) {
            return $result;
        } else {
            echo "Функция query по данным: <br> $sql <br> - mysql вернула пустой результат! <br><hr>";
            return false;
        }
        
    }
    
    public function query_assoc($sql, $row_filed)
    {
        
        if (!$result = $this->db->query($sql)) {
            echo "Не удалось выполнить запрос: $sql <br>";
            echo "Номер ошибки: " . $this->db->errno . "\n";
            echo "Ошибка: " . $this->db->error . "\n";
            return false;
        }
        
        if ($result->num_rows > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row[$row_filed];
        } else {
            //echo "Функция query_assoc по данным: <br> $sql <br> $row_filed <br> - mysql вернула пустой результат! <br><hr>";
            return false;
        }
        
    }
    
    public function query_insert($sql)
    {
        if (!$result = $this->db->query($sql)) {
            echo "Не удалось выполнить запрос: (" . $this->db->errno . ") " . $this->db->error;
            echo "Номер ошибки: " . $this->db->errno . "\n";
            echo "Ошибка: " . $this->db->error . "\n";
            return false;
        }else{
            echo "Запрос <br> $sql <br> - выполнен удачно! <br><hr>";
            return true;
        }
    
    }
    
    function query_insert_id($sql)
    {
        if (!$result = $this->db->query($sql)) {
            echo "Не удалось выполнить запрос: (" . $this->db->errno . ") " . $this->db->error;
            echo "Номер ошибки: " . $this->db->errno . "\n";
            echo "Ошибка: " . $this->db->error . "\n";
            return false;
        }else{
            echo "Запрос <br> $sql <br> - выполнен удачно! <br><hr>";
            return $this->db->insert_id;
            //return mysqli_insert_id($this->db);
        }
        
    }
    
    public function query_update($sql)
    {
        if (!$result = $this->db->query($sql)) {
            echo "Не удалось выполнить запрос: (" . $this->db->errno . ") " . $this->db->error;
            echo "Номер ошибки: " . $this->db->errno . "\n";
            echo "Ошибка: " . $this->db->error . "\n";
            return false;
        }else{
            echo "Запрос <br> $sql <br> - выполнен удачно! <br><hr>";
            return true;
        }
        
    }
    
    public function query_delete($sql)
    {
        if (!$result = $this->db->query($sql)) {
            echo "Не удалось выполнить запрос: (" . $this->db->errno . ") " . $this->db->error;
            echo "Номер ошибки: " . $this->db->errno . "\n";
            echo "Ошибка: " . $this->db->error . "\n";
            return false;
        }else{
            echo "Запрос <br> $sql <br> - выполнен удачно! <br><hr>";
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
                        echo $err;
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
                            echo $err;
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
                            echo $err;
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
    
    public function formImages($images)
    {
        $images_arr = [];
        foreach ($images as $src){
            $images_arr[] = [ 'src' => (string) $src ];
        }
    
        $images = null;
        $src = null;
        unset($images,$src);
    
        //pauza dla sborki musora
        //time_nanosleep(0, 10000000);
        //pauza dla sborki musora
        
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
            echo $err;
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
                    echo $err;
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
                echo $err;
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
    
    
    public function addProductFromOpenCart(
       // $category,
        $wc_product_name,
        $wc_price,
       // $vendor_code,
        $wc_product_description,
        //$images,
       // $attributes,
       // $type,
        $woocommerce)
    {
    
        $data = [
            'name' => (string) $wc_product_name,
            'type' => 'simple',
            'regular_price' => (string) $wc_price,
            'description' => (string) $wc_product_description,
            'short_description' => (string) $wc_product_description,
            'categories' => [
                [
                    'id' => 43
                ]
            ],
            'images' => [
                [
                    'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_front.jpg'
                ],
                [
                    'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_back.jpg'
                ]
            ]
        ];
        
        try {
            $product_rezult = $woocommerce->post('products', $data);
        }
        catch(Exception $e){
            $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
            $err = $info . $e->getMessage();
            echo $err;
            $this->errorLog($err);
        }
    
        $product_id = $product_rezult->id;
        if($product_id){
            return $product_id;
        }else{
            return false;
        }
        
    }
    
    
    
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
                   echo $err;
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
                echo $err;
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
                echo $err;
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
                echo $err;
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
            echo $err;
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
                echo $err;
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
           dump($woocommerce->delete('products/'.$product_id.'/variations/'.$variation_id, ['force' => true]));
       }
       catch(Exception $e){
           $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
           $err = $info . $e->getMessage();
           echo $err;
           $this->errorLog($err);
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
           echo $err;
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
           echo $err;
           $this->errorLog($err);
       }
       
       dump($rezult);
       return $rezult;
   }
    
    public function deleteProduct($product_id, $woocommerce)
    {
        
        try {
            dump($woocommerce->delete('products/'.$product_id, ['force' => true]));
        }
        catch(Exception $e){
            $info = 'В методе: ' . __METHOD__ . ' около строки: ' .  __LINE__ . ' произошла ошибка API: ';
            $err = $info . $e->getMessage();
            echo $err;
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
            echo $err;
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
            echo $err;
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
            echo $err;
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
            echo $err;
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
            echo $err;
            $this->errorLog($err);
        }
        
        show("Для товара $product_id установлены по умолчанию следующие атрибуты: ");
        dump($default_attributes);
        
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
                echo $err;
                $this->errorLog($err);
            }
            
    }
    
    
    
    
    
}