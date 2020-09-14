<?php
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
    
    //show("Для товара $product_id установлены по умолчанию следующие атрибуты: ");
    //dump($default_attributes);
}