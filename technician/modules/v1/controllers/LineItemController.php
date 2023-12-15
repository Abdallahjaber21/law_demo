<?php


namespace technician\modules\v1\controllers;


use common\components\extensions\api\ApiController;
use common\models\CauseCode;
use common\models\DamageCode;
use common\models\Manufacturer;
use common\models\ObjectCategory;

class LineItemController extends ApiController
{

    public function actionData()
    {
        $this->isGet();

        return [
            'objects' => ObjectCategory::find()->all(),
            'causes' => CauseCode::find()->all(),
            'damages' => DamageCode::find()->all(),
            'manufacturer' => Manufacturer::find()->all(),
        ];
    }

}