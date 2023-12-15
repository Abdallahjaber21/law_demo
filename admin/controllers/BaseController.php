<?php


namespace admin\controllers;


use common\components\extensions\Select2;
use common\widgets\inputs\PinCodeInput;
use yii\base\Action;

class BaseController extends \yii\web\Controller
{
    /**
     * @param Action $action
     * @return bool
     * @throws \Throwable
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        /////////////////////////////////////////////
        Select2::widget([
            'id' => 'aaa---aaa',
            'name' => 'aaa',
            'data' => [],
            'theme' => Select2::THEME_DEFAULT
        ]);
        PinCodeInput::widget([
            'name' => 'abc']);
        /////////////////////////////////////////////
        $parentBefore = parent::beforeAction($action);
        return $parentBefore;
    }
}