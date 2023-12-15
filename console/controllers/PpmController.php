<?php


namespace console\controllers;

use common\models\Division;
use common\models\LocationEquipments;
use yii\console\Controller;

class PpmController extends Controller
{
    public function actionCheckPpms()
    {
        $division = $this->prompt('Choose a Division: (M,P,V)[Mall,Plant,Villa]', ['required' => true]);
        $selected_division = null;

        $equipments = LocationEquipments::find()->where(['IS NOT', 'meter_value', null]);

        if (strtolower(trim($division)) == 'm') {
            $selected_division = Division::DIVISION_MALL;
        } else if (strtolower(trim($division)) == 'v') {
            $selected_division = Division::DIVISION_VILLA;
        } else if (strtolower(trim($division)) == 'p') {
            $selected_division = Division::DIVISION_PLANT;
        }

        $equipments = $equipments->andWhere(['division_id' => $selected_division])->all();

        print_r($equipments);

        foreach ($equipments as $equipment) {
            $tasks = LocationEquipments::GetPpmList($equipment->id, $selected_division);

            print_r($tasks);
            echo "\n\n\n";
        }
    }
}
