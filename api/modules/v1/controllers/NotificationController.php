<?php


namespace api\modules\v1\controllers;


use common\components\extensions\api\ApiController;
use common\components\notification\Notification;
use yii\data\ActiveDataProvider;

class NotificationController extends ApiController
{
    public function actionUnread()
    {
        $this->isGet();
        $user = $this->getUser();
        $unseen = Notification::find()
            ->where([
                'AND',
                ['account_id' => $user->id],
                ['seen' => false],
            ])
            ->count();
        return [
            'count' => intval($unseen)
        ];
    }

    public function actionList()
    {
        $this->isGet();
        $user = $this->getUser();

        $dataProvider = new ActiveDataProvider([
            'query' => Notification::find()
                ->where(['account_id' => $user->id])
                ->limit(20)
                ->orderBy(['id' => SORT_DESC])
        ]);

        $this->serializer = [
            'class' => 'yii\rest\Serializer',
            'collectionEnvelope' => 'items',
            'linksEnvelope' => 'links',
            'metaEnvelope' => 'meta',
        ];
        return $dataProvider;
    }

    public function actionRead()
    {
        $this->isPost();
        $user = $this->getUser();
        $id = \Yii::$app->getRequest()->post("id");
        Notification::markRead($user->id, $id);
        return [
            'success' => true
        ];
    }

    public function actionReadAll() {
        $this->isPost();
        $user = $this->getUser();
        Notification::updateAll(['seen' => true], ['account_id' => $user->id]);
        return [
            'success' => true
        ];
    }

    public function actionClearAll() {
        $this->isPost();
        $user = $this->getUser();
        Notification::deleteAll(['account_id' => $user->id]);
        return [
            'success' => true
        ];
    }

    public function actionCreate()
    {
        $this->isGet();
        $user = $this->getUser();
        Notification::createNotification($user->id, "Hello World");
    }
}