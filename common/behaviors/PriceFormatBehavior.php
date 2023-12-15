<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\behaviors;

use yii\base\Behavior;
use yii\base\UnknownPropertyException;

/**
 * Description of PriceFormatBehavior
 *
 * @author Tarek K. Ajaj
 */
class PriceFormatBehavior extends Behavior
{

    public $attribute = 'price';
    public $currency = null;
    public $currencyAttr = 'currency';

    /**
     *
     * @var \yii\i18n\Formatter
     */
    public $formatter;

    public function init()
    {
        parent::init();
        if (empty($this->formatter)) {
            $this->formatter = \Yii::$app->getFormatter();
        }
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch (UnknownPropertyException $e) {
            if ($name == $this->attribute . '_formatted') {
                return $this->getPriceFomatted();
            } else {
                throw $e;
            }
        }
    }

    private function getPriceFomatted()
    {
        $isNegative = "";
        $value = isset($this->owner->{$this->attribute}) ? $this->owner->{$this->attribute} : 0;
        if ($value < 0) {
            $isNegative = "-";
            $value = $value * -1;
        }
        if (empty($this->currency)) {
            return $isNegative.$this->formatter->asCurrency($value, $this->owner->{$this->currencyAttr});
        }
        return $isNegative.$this->formatter->asCurrency($value, $this->currency);

    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return $name == $this->attribute . '_formatted';
    }

}
