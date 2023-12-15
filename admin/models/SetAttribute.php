<?php


namespace admin\models;


use yii\base\Action;

class SetAttribute extends Action
{
    public $model;

    public $attribute;

    public $value;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $id = \Yii::$app->request->getQueryParam("id");
        $className = $this->model;
        $model = $className::find()->where(['id'=> $id])->one();
        if(!empty($model)){
            $model->{$this->attribute} = $this->value;
            $model->save(false);
        }


        return $this->controller->redirect(['index']);
    }

}