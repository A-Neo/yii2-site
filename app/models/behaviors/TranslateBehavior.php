<?php

namespace app\models\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

class TranslateBehavior extends Behavior
{

    public $attributes = [];

    public function events() {
        return [
            BaseActiveRecord::EVENT_BEFORE_VALIDATE => [$this, 'beforeValidate'],
            BaseActiveRecord::EVENT_AFTER_FIND      => [$this, 'afterFind'],
        ];
    }

    public function afterFind() {
        foreach($this->attributes as $attribute){
            if($this->owner->hasAttribute($attribute) && is_string($this->owner->$attribute)){
                try{
                    $this->owner->$attribute = json_decode($old = $this->owner->$attribute, true);
                }catch(\Exception $e){
                    $this->owner->$attribute = null;
                }
                if(is_null($this->owner->$attribute)){
                    $this->owner->$attribute = array_fill_keys(Yii::$app->languagesDispatcher->languages, $old);
                }
            }
        }
    }

    public function beforeValidate() {
        foreach($this->attributes as $attribute){
            if($this->owner->hasAttribute($attribute) && is_array($this->owner->$attribute)){
                try{
                    $old = json_decode($tmp = $this->owner->getOldAttribute($attribute), true);
                }catch(\Exception $e){
                    $old = [];
                }
                if(is_null($old) || !is_array($old)){
                    $old = array_fill_keys(Yii::$app->languagesDispatcher->languages, $tmp);
                }

                $this->owner->$attribute = json_encode(ArrayHelper::merge($old, $this->owner->$attribute), JSON_UNESCAPED_UNICODE);
            }
        }
    }

    public function hasProperty($name, $checkVars = true) {
        if(strpos($name, 't_') === 0 && in_array($s = substr($name, 2), $this->attributes) && $this->owner->hasAttribute($s)){
            return true;
        }
        return parent::hasProperty($name, $checkVars); // TODO: Change the autogenerated stub
    }

    public function canGetProperty($name, $checkVars = true) {
        if(strpos($name, 't_') === 0 && in_array($s = substr($name, 2), $this->attributes) && $this->owner->hasAttribute($s)){
            return true;
        }
        return parent::canGetProperty($name, $checkVars); // TODO: Change the autogenerated stub
    }

    public function __get($name) {
        if(strpos($name, 't_') === 0){
            $s = substr($name, 2);
            if(in_array($s, $this->attributes) && $this->owner->hasAttribute($s)){
                return self::tGet($this->owner->$s, Yii::$app->language);
            }
        }
        return parent::__get($name);
    }

    public static function tFilter($array, $attributes = []) {
        $lang = substr(Yii::$app->language, 0, 2);
        foreach($array as $i => $item){
            foreach($attributes as $attribute){
                $GLOBALS['xxx'] = 1;
                $array[$i][$attribute] = self::tGet($item[$attribute], $lang, true);
            }
        }
        return $array;
    }

    public static function tGet($s, $lang, $decode = false) {
        if($decode && is_string($s)){
            try{
                $s = json_decode($tmp = $s, true);
            }catch(\Exception $e){
                $s = $tmp;
            }
        }
        if(is_array($s)){
            return $s[$lang] ?? '';
        }
        return $s;
    }

}
