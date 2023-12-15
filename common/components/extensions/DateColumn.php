<?php

namespace common\components\extensions;

use kartik\date\DatePicker;
use yii\grid\DataColumn;

/**
 * Description of DateColumn
 *
 * @author Tarek K. Ajaj
 */
class DateColumn extends DataColumn {

    protected function renderFilterCellContent() {
        return DatePicker::widget([
                    'model' => $this->grid->filterModel,
                    'attribute' => $this->attribute,
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'layout' => '{input}{remove}',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ]
        ]);
    }

}
