<?php

namespace common\components\extensions;

use common\components\extensions\Select2;
use yii\grid\DataColumn;

/**
 * Description of RelationColumn
 *
 * @author Tarek K. Ajaj
 */
class CurrencyColumn extends DataColumn {

    public $currencyAttr = "currency";
    public $currency = "USD";

    public function getDataCellValue($model, $key, $index) {
        if(!empty($model->{$this->currencyAttr})) {
            return \Yii::$app->formatter->asCurrency($model->{$this->attribute}, $model->{$this->currencyAttr});
        }
        return \Yii::$app->formatter->asCurrency($model->{$this->attribute}, $this->currency);
    }

}
