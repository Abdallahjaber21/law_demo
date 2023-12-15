<?php

namespace common\components\extensions;

use common\components\extensions\Select2;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Description of RelationColumn
 *
 * @author Tarek K. Ajaj
 */
class RelationColumn extends DataColumn {

  public $data = [];
  public $url;

  protected function renderFilterCellContent() {
    return Select2::widget([
                'model' => $this->grid->filterModel,
                'attribute' => $this->attribute,
                'data' => $this->data,
                'pluginOptions' => ['allowClear' => true],
                'options' => [
                    'placeholder' => ''
                ],
    ]);
  }

  protected function renderDataCellContent($model, $key, $index) {
    if (!empty($this->url) && count($this->url) == 2) {
      return Html::a(parent::renderDataCellContent($model, $key, $index),[$this->url[0], $this->url[1] => ArrayHelper::getValue($model, $this->attribute)]);
    }
    return parent::renderDataCellContent($model, $key, $index);
  }

}
