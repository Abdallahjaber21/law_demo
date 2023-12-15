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
class RelativeTimeBehavior extends Behavior
{
    public $attribute = 'created_at';

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
            if ($name == $this->attribute . '_relative') {
                return $this->getRelativeTime();
            } else {
                throw $e;
            }
        }
    }

    private function getRelativeTime()
    {
        $timeZone = new DateTimeZone(Yii::$app->timeZone);
        $dateNow = new DateTime($this->owner->{$this->attribute}, $timeZone);
        if (isset($this->owner->{$this->attribute})) {
            return $this->formatter->asRelativeTime($this->owner->{$this->attribute});
            return $this->formatter->asRelativeTime($dateNow);
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return $name == $this->attribute . '_relative';
    }

}
