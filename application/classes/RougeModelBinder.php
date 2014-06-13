<?php

class RougeModelBinder  {
    /**
     * @var RougeModelBinder
     */
    public static $singleton = null;

    public static function changeSingleton(RougeModelBinder $s) {
        self::$singleton = $s;
    }

    public function bindModel($data, $modelType = null) {
        $data = (object)$data;

        $maybeModel = ModelCache::$singleton->get_cached_model($data, $modelType);

        if($maybeModel !== null) {
            return $maybeModel;
        }

        $modelType = null === $modelType ? 'MySQLRecord' : $modelType;

        // if the model type is just a class (there is no :: indicating a function call
        if(strpos($modelType, '::') === false) {
            if(method_exists($modelType, 'modelBinder')) {
                $model = $modelType::modelBinder($data);
            }
            else {
                $model = new $modelType();

                foreach($data as $k => $v) {
                    $model->{$k} = $v;
                }
            }
        }
        else {
            $model = call_user_func($modelType, $data); // warning: slow
        }

        ModelCache::$singleton->set_cached_model($data, $modelType, $model);

        return $model;
    }

    public function bindModelFromPrefixedMembers($object, $prefix, $modelType = null, $treatAllNullAsNull = true) {
        $data = get_object_vars($object);

        if(count(array_keys($data)) == 0) {
            return null;
        }

        $maybeModel = ModelCache::$singleton->get_cached_model($object, $modelType, $prefix);

        if($maybeModel !== null) {
            return $maybeModel;
        }

        $allNull = true;
        $new_data = array();

        foreach($data as $k => $v) {
            if($k[0] != $prefix[0] || $k[1] != '_') {
                continue;
            }

            $newKey = '';
            $keyLength = strlen($k) - 2;
            for($i = 0; $i < $keyLength; $i++) {
                $newKey .= $k[$i + 2];
            }

            $new_data[$newKey] = $v;

            if($v !== null) {
                $allNull = false;
            }
        }

        $new_data = (object)$new_data;

        if($allNull && $treatAllNullAsNull) {
            return null;
        }

        $model = $this->bindModel($new_data, $modelType);

        ModelCache::$singleton->set_cached_model($object, $modelType, $model, $prefix);

        return $model;
    }
}

if(RougeModelBinder::$singleton === null) {
    RougeModelBinder::changeSingleton(new RougeModelBinder());
}