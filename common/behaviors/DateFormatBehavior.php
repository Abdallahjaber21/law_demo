<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\behaviors;

use DateTime;
use DateTimeZone;
use Yii;
use yii\base\Behavior;
use yii\base\UnknownPropertyException;

/**
 * Description of DateFormatter
 *
 * @author Tarek K. Ajaj
 */
class DateFormatBehavior extends Behavior
{

    CONST TYPE_DATE = "date";
    CONST TYPE_DATE_TIME = "datetime";
    public $type = 'datetime';

    public $attributes = ['created_at'];
    public $format = 'medium';

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
            foreach ($this->attributes as $index => $attribute) {
                if ($name == $attribute . '_formatted') {
                    return $this->getDateFormatted($attribute);
                }
            }
            throw $e;
        }
    }

    private function getDateFormatted($attribute)
    {
        if (isset($this->owner->{$attribute})) {
            if ($this->type == self::TYPE_DATE_TIME) {

                $timeZone = new DateTimeZone(Yii::$app->timeZone);
                $dateNow = new DateTime($this->owner->{$attribute}, $timeZone);
                //$dateNow = date("Y-m-d H:i:s", strtotime($this->owner->{$attribute}));
                //return $this->formatter->asRelativeTime($this->owner->{$attribute});
                return $this->formatter->asDatetime($this->owner->{$attribute}, $this->format);
            }
            if ($this->type == self::TYPE_DATE) {
                return $this->formatter->asDate($this->owner->{$attribute}, $this->format);
            }
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        foreach ($this->attributes as $index => $attribute) {
            if ($name == $attribute . '_formatted') {
                return true;
            }
        }
        return false;
    }

}
