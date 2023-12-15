<?php

namespace common\components\settings;

use common\config\includes\P;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * SettingsController implements management for settings component.
 */
class SettingsController extends Controller
{

    public $viewPath = '@common/components/settings/views';

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
                        'allow' => P::c(P::ADMINS_SETTINGS_PAGE_VIEW),
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $this->setViewPath($this->viewPath);

        if (Yii::$app->getRequest()->isPost) {
            Setting::updateSettings(Yii::$app->getRequest()->post());
        }

        $settings_categories = SettingCategory::find()
            ->with('settings')
            ->all();

        return $this->render('index', [
            'settings_categories' => $settings_categories
        ]);
    }
}
