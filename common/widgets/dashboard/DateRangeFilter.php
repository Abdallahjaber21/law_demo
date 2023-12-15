<?php

namespace common\widgets\dashboard;

use common\widgets\dashboard\assets\DateRangeFilterAsset;
use Yii;
use yii\base\Widget;

/**
 * Description of DateRangeFilter
 *
 * @author Tarek K. Ajaj
 */
class DateRangeFilter extends Widget
{

    public $from;
    public $to;
    public $defaultRange = 6;
    public $auto_submit = false;
    public $maxSpan = 31;
    public $showCustomRangeLabel = false;

    public function init()
    {
        parent::init();

        $this->view->registerAssetBundle(DateRangeFilterAsset::class);
        $this->from = Yii::$app->getRequest()->get("_s");
        $this->to = Yii::$app->getRequest()->get("_e");
        $month = Yii::$app->getRequest()->get("month");
        $year = Yii::$app->getRequest()->get("year");
        if (empty($this->from)) {
            //$this->from = date("Y-m-d", strtotime("-{$this->defaultRange}days"));
            $this->from = date("Y-m-d");
        }
        if ($month != null && $year != null) {
            if ($month == date('n') && $year == date('Y')) {
                $this->from = date('Y-m-d', strtotime("$year-$month-" . date('d')));
                $this->to = date('Y-m-t', strtotime("$year-$month-01"));
            } else {

                $this->from = date('Y-m-d', strtotime("$year-$month-01"));
                $this->to = date('Y-m-t', strtotime("$year-$month-01"));
            }
        } else {
            if (empty($this->to)) {
                $this->from = date("Y-m-d");
            }

            if (empty($this->to)) {
                $this->to = date("Y-m-t");
            }
        }
    }


    public function run()
    {
        return $this->render("date-range", [
            'from'                 => $this->from,
            'to'                   => $this->to,
            'auto_submit'          => $this->auto_submit,
            'maxSpan'              => $this->maxSpan,
            'showCustomRangeLabel' => $this->showCustomRangeLabel
        ]);
    }
}
