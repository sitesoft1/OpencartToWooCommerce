<?php
public function formAddOcToWcVariations($product_id, $wc_model, $wc_price, $attributes, $images, $woocommerce)
{
    foreach ($attributes as $attr_name => $attr_value) {
        
        $attribute_id = $this->query_assoc("SELECT attribute_id FROM `wp_woocommerce_attribute_taxonomies` WHERE attribute_label='$attr_name'", "attribute_id");
        if (!empty($attribute_id) and !empty($attr_value)) {
            
            if(!is_array($attr_value)){
                $attributes_group_arr = [
                    'id' => (integer)$attribute_id,
                    'option' => (string)$attr_value
                ];
    
                //create option
                $create[] = [
                    'regular_price' => (string)$wc_price,
                    'sku' => (string)$wc_model . '-' . rand(1, 10000), //Unique identifier.
                    //'image' => [ 'src' => (string)$images[0] ],
                    'attributes' => [$attributes_group_arr]
                ];
                //create option END
            }
            
            else{
                foreach ($attr_value as $attr_value_string){
                    $attributes_group_arr = [
                        'id' => (integer)$attribute_id,
                        'option' => (string)$attr_value_string
                    ];
    
                    //create option
                    $create[] = [
                        'regular_price' => (string)$wc_price,
                        'sku' => (string)$wc_model . '-' . rand(1, 10000), //Unique identifier.
                        //'image' => [ 'src' => (string)$images[0] ],
                        'attributes' => [$attributes_group_arr]
                    ];
                    //create option END
                }
            }
            
            
            
            
        }
        
    }
    
    
    $variations_data = [
        'create' => $create
    ];
    
    try {
        $rezult = $woocommerce->post('products/' . $product_id . '/variations/batch', $variations_data);
    } catch (Exception $e) {
        $info = 'В методе: ' . __METHOD__ . ' около строки: ' . __LINE__ . ' произошла ошибка API: ';
        $err = $info . $e->getMessage();
        echo $err;
        $this->errorLog($err);
    }
    
    return $rezult;
}