<?php

namespace common\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Description of RandomTokenBehavior
 *
 * @author Tarek K. Ajaj
 * Jun 28, 2017 1:20:47 PM
 * 
 * RandomTokenBehavior.php
 * UTF-8
 * 
 */
class RandomTokenBehavior extends Behavior {

    /**
     * attributes to fill with random keys
     * @var array 
     */
    public $attributes;

    /**
     * length of randomly generated keys
     * @var integer
     */
    public $length = 64;

    /**
     * random token prefix
     * @var string
     */
    public $prefix = '';

    /**
     * @inheritdoc
     */
    public function events() {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert'
        ];
    }

    /**
     * before insert event handler
     */
    public function beforeInsert($event) {
        $attributes = $this->attributes;
        if (!empty($attributes)) {
            foreach ($attributes as $key => $attribute) {
                $this->owner->$attribute = $this->prefix . Yii::$app->getSecurity()->generateRandomString($this->length);
            }
        }
        return true;
    }

}
