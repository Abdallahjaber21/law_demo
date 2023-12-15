<?php

use common\models\Assignee;
use common\models\VillaPpmTasks;
use common\models\VillaPpmTemplates;
use yii\helpers\Html;
use common\widgets\dashboard\PanelBox;
use common\components\extensions\Select2;
use common\components\extensions\ActionColumn;
use common\config\includes\P;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\VillaPpmTemplatesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Villa Ppm Templates';
$this->params['breadcrumbs'][] = $this->title;

$models = $dataProvider->getModels();
?>
<div class="villa-ppm-templates-index">
    <div class="row">
        <div class="col-sm-12">
            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY,
                'body' => false
            ]);

            $panel->beginHeaderItem();
            if (P::c(P::PPM_VILLA_PPM_TEMPLATES_NEW)) {
                echo Html::a('New', Url::to(['create']), ['class' => 'btn btn-md btn-primary']);
            }
            $panel->endHeaderItem();
            ?>

            <?php $panel::end(); ?>
        </div>
    </div>
    <div class="row">

        <div class="col-sm-12 templates_cards">
            <?php foreach ($models as $template) { ?>
            <a href="<?= Url::to(['update', 'id' => $template->id]) ?>">
                <div class="col-sm-3 template_card">
                    <div class="contents">
                        <div class="template_header">
                            <div class="title">
                                <?= $template->name ?>
                            </div>
                            <div class="right badge badge-warning">
                                <?= $template->sector->name ?>
                            </div>
                        </div>
                        <div class="template_body">
                            <?= $template->note ?>
                        </div>
                        <div class="template_footer">
                            <div class="left">
                                <?= $template->repeating_condition_label ?>
                            </div>
                            <div class="right badge badge-info">
                                <?= @$template->category->name . ' | ' . $template->status_label ?>
                            </div>
                        </div>
                    </div>
                    <div class="template_extra">
                        <?php if ($template->asset_id) : ?>
                        <div class="extra">
                            <div class="extra_label">Asset: </div>
                            <?= @$template->asset->code . ' | ' . @$template->asset->equipment->name ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($template->location_id) : ?>
                        <div class="extra">
                            <div class="extra_label">Location: </div>
                            <?= @$template->location->name ?>
                        </div>
                        <?php endif; ?>
                        <div class="extra">
                            <div class="extra_label">Duration: </div>
                            <?= @$template->frequency . ' Days' ?>
                        </div>
                        <div class="extra">
                            <div class="extra_label">Starting Date: </div>
                            <?= @Yii::$app->formatter->asDate($template->starting_date_time) ?>
                        </div>
                        <div class="extra">
                            <div class="extra_label">Team: </div>
                            <div class="extra_table">
                                <?= VillaPpmTemplates::getCommaSeperatedValues($template->team_members, Assignee::class, 'user_id', 'name', 'user') ?>
                            </div>
                        </div>
                        <div class="extra">
                            <div class="extra_label">Tasks: </div>
                            <?= VillaPpmTemplates::getCommaSeperatedValues($template->tasks, VillaPpmTasks::class, 'id', 'name') ?>
                        </div>
                    </div>
                </div>
            </a>
            <?php } ?>
        </div>
    </div>
</div>