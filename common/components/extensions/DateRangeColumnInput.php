<?php

namespace common\components\extensions;

use common\widgets\dashboard\assets\DateRangeFilterAsset;
use Yii;
use yii\base\Widget;
use yii\widgets\InputWidget;

/**
 * Description of DateRangeFilter
 *
 * @author Tarek K. Ajaj
 */
class DateRangeColumnInput extends Widget
{

    public $model;
    public $attribute_from;
    public $attribute_to;

    public $defaultRange = 6;
    public $auto_submit = false;
    public $maxSpan = 10000;
    public $showCustomRangeLabel = false;
    public $allowClear = true;

    public function init()
    {
        parent::init();
        $this->view->registerAssetBundle(DateRangeFilterAsset::class);
    }

    public function run()
    {
        return $this->render("date-range", [
            'widget'=> $this
        ]);
    }

}
