<?php
    namespace App\Services;

    class Validator {
        public static function validateTask($data, $requireTitle=true){
            $errors = [];
            if (!is_array($data)) return ['payload'=>'JSON inválido o no enviado'];
            
            if ($requireTitle && (empty($data['title']) || trim($data['title'])==='')) $errors['title']='El título es obligatorio';

            if (isset($data['status']) && !in_array($data['status'], ['pendiente','completada'])) $errors['status']='Estado inválido';

            if (isset($data['title']) && mb_strlen($data['title'])>255) $errors['title']='Título demasiado largo';
            
            return $errors;
        }
    }