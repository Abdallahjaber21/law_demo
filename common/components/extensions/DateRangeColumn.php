<?php

namespace common\components\extensions;

use yii\grid\DataColumn;

/**
 * Description of DateRangeColumn
 *
 * @author Tarek K. Ajaj
 */
class DateRangeColumn extends DataColumn
{

    public $attribute_from;
    public $attribute_to;

    protected function renderFilterCellContent()
    {
        return DateRangeColumnInput::widget([
            'model'          => $this->grid->filterModel,
            'attribute_from' => $this->attribute_from,
            'attribute_to'   => $this->attribute_to,
            'auto_submit' => true
        ]);
    }

}
