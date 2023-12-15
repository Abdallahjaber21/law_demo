<?php

use common\components\extensions\Select2;
use common\models\Customer;
use common\widgets\dashboard\PanelBox;
use yeesoft\multilingual\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Fake Notification');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="line-item-index">

    <div class="row">

        <div class="col-md-4">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon'  => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?= Html::a("Service Notification", ['/fake/service-notification']) ?>
            <br />
            <?= Html::a("Contract Notification", ['/fake/contract-notification']) ?>
            <br />
            <?= Html::a("News Notification", ['/fake/news-notification']) ?>
            <br />
            <?= Html::a("Tech Notification", ['/fake/tech-notification']) ?>
            <?php PanelBox::end() ?>
        </div>
        <div class="col-md-4">
            <?php $panel = PanelBox::begin([
                'title' => $this->title,
                'icon'  => 'table',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label">Title</label>
                        <?= Html::textInput("title", "E-Maintain", ['class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label">Message</label>
                        <?= Html::textarea("message", "Hello world!", ['class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label">Customer</label>
                        <?= Select2::widget([
                            'name' => 'customer_id',
                            'data' => ArrayHelper::map(Customer::find()->all(), "id", "name")
                        ])
                        ?>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label">Type</label>
                        <?= Select2::widget([
                            'name' => 'type',
                            'data' => [
                                10 => 'Active Service',
                                20 => 'Contract Reminder',
                                30 => 'Maintenance Reminder',
                                40 => 'News & Promotion',
                            ]
                        ])
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-primary btn-flat']) ?>
            </div>
            <?php ActiveForm::end(); ?>
            <?php PanelBox::end() ?>
        </div>
    </div>
</div>