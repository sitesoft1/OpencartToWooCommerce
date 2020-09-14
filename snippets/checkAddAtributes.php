<?php
function checkAddAtributes($attributes, $woocommerce)
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
    
    return true;//chtonibut vernem
}