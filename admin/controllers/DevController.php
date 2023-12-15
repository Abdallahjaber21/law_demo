<?php


namespace admin\controllers;


use admin\models\ExcelUploadForm;
use common\config\includes\P;
use common\models\EquipmentMaintenanceBarcode;
use Yii;
use yii\base\DynamicModel;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\UploadedFile;

class DevController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [

            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => P::c(P::DEVELOPER),
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionFixBarcodesLocations()
    {
        $model = new ExcelUploadForm();
        if (Yii::$app->request->isPost) {
            $data = $model->fromPost(Yii::$app->request->post());
            foreach ($data as $index => $datum) {
                $updateAll = EquipmentMaintenanceBarcode::updateAll(
                    ['location' => $datum['update']],
                    ['location' => $datum['location']]
                );
                echo "<strong>{$datum['location']}</strong> to <strong>{$datum['update']}</strong> - {$updateAll} of {$datum['count']} rows updated <br/>";
            }
            echo "<pre>";
            print_r($data);
            exit();
        }
        return $this->render("fix-barcodes-locations", [
            'model' => $model
        ]);
    }

    public function actionAddBarcodesCodes()
    {
        $model = new ExcelUploadForm();
        if (Yii::$app->request->isPost) {
            $data = $model->fromPost(Yii::$app->request->post());
            foreach ($data as $index => $datum) {
                $updateAll = EquipmentMaintenanceBarcode::updateAll(
                    ['code' => $datum['code']],
                    ['location' => $datum['location']]
                );
                echo "set <strong>{$datum['code']}</strong> to <strong>{$datum['location']}</strong> - {$updateAll} of {$datum['count']} rows updated <br/>";
            }
            echo "<pre>";
            print_r($data);
            exit();
        }
        return $this->render("fix-barcodes-locations", [
            'model' => $model
        ]);
    }
}
