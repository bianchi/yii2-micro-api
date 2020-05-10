<?php 

namespace api\traits;


trait SetAttributesWithPrefix
{
    /**
     *  Set attributes values coming from bodies with prefix. 
     *  E.g. company_name goes to $model->name, user_email goes to $model->email
     */ 
    public function setAttributesWithPrefix($values, $prefix, $safeOnly = true)
    {
        $attributesToSet = [];
        foreach ($values as $attribute => $value) {
            if (stripos($attribute, $prefix) !== false) {
                $attribute = str_replace($prefix, '', $attribute);
                if (in_array($attribute, $this->attributes())) {
                    $attributesToSet[$attribute] = $value;
                }
            }
        }

        $this->setAttributes($attributesToSet, $safeOnly);
    }
}
