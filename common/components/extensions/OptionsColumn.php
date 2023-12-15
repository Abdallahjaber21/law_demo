<?php

namespace common\components\extensions;

use common\components\extensions\Select2;
use yii\grid\DataColumn;

/**
 * Description of OptionsColumn
 *
 * @author Tarek K. Ajaj
 */
class OptionsColumn extends DataColumn
{

    protected function renderFilterCellContent()
    {
        $list = "{$this->attribute}_list";
        return Select2::widget([
            'model' => $this->grid->filterModel,
            'attribute' => $this->attribute,
            'data' => $this->grid->filterModel->{$list},
            'theme' => Select2::THEME_DEFAULT,
            'pluginOptions' => ['allowClear' => true],
            'options' => [
                'placeholder' => ''
            ],
        ]);
    }

    public function getDataCellValue($model, $key, $index)
    {
        $attr = "{$this->attribute}_label";
        return $model->{$attr};
    }
}
