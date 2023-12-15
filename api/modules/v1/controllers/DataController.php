<?php

namespace api\modules\v1\controllers;

use common\components\extensions\api\ApiController;
use common\components\settings\Setting;
use common\data\Countries;
use common\models\Problem;

/**
 * Description of DataController
 *
 * @author Tarek K. Ajaj
 */
class DataController extends ApiController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        return $behaviors;
    }

    public function actionCheckUpdate()
    {
        $this->isGet();
        $dotversion = \Yii::$app->getRequest()->get("version");
        $platform = \Yii::$app->getRequest()->get("platform");

        $version = $this->dotVersionToInt($dotversion);
        switch ($platform) {
            case "android":
                if ($version < $this->dotVersionToInt(Setting::getValue("android_version"))) {
                    return [
                        'update' => true,
                        'version' => Setting::getValue("android_version"),
                        'link' => Setting::getValue("android_store"),
                    ];
                }
            case "ios":
                if ($version < $this->dotVersionToInt(Setting::getValue("ios_version"))) {
                    return [
                        'update' => true,
                        'version' => Setting::getValue("android_version"),
                        'link' => Setting::getValue("ios_store"),
                    ];
                }
        }
        return [
            'update' => false,
        ];
    }

    private function dotVersionToInt($dotVersion = "1.0.0")
    {
        $versionParts = explode(".", $dotVersion);
        $version = (intval($versionParts[0]) * 10000) + (intval($versionParts[1]) * 100) + intval($versionParts[2]);
        return $version;
    }

    public function actionTimezones()
    {
        $this->isGet();
        return Countries::getTimeZonesList();
    }

    public function actionProblems()
    {
        $this->isGet();
        return Problem::findEnabled()->all();
    }


}
