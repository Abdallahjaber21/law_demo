<?php


namespace api\modules\v1\controllers;


use common\components\extensions\api\ApiController;
use common\models\Article;
use yii\data\ActiveDataProvider;

class NewsController extends ApiController
{

    public function actionArticles()
    {
        $this->isGet();

        $this->serializer = [
            'class' => 'yii\rest\Serializer',
            'collectionEnvelope' => 'items',
            'linksEnvelope' => 'links',
            'metaEnvelope' => 'meta',
        ];
        return new ActiveDataProvider([
            'query' => Article::findEnabled()->orderBy(['created_at' => SORT_DESC])
        ]);
    }

    public function actionArticle($id)
    {
        $this->isGet();

        return Article::find()->where(['id' => $id])->one();
    }
}