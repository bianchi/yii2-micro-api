<?php 

namespace api\traits;


trait SetErrorsAddingPrefix
{
    /**
     *  Set errors coming from attributes without prefix to attributes with prefix
     *  E.g. Erros from $model2->name goes to $model->prefix_name
     */ 
    public function setErrorsAddingPrefix($errorsToAdd, $prefix, $attributesToIgnore = [])
    {
        $convertedErrors = [];
        // Adds user_ to the attribute errors. E.g. User->email errors goes to Account->user_email
        foreach ($errorsToAdd as $attribute => $errors) {
            if (in_array($attribute, $attributesToIgnore)) {
                continue;
            }
            
            $convertedErrors[$prefix . $attribute] = $errors;
        }

        $currentErrors = $this->getErrors();
        $newErrors = array_merge($currentErrors, $convertedErrors);

        $this->addErrors($newErrors);
    }
}
