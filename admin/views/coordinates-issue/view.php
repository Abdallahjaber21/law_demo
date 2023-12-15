<?php

use common\widgets\dashboard\PanelBox;
use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\Map;
use dosamigos\google\maps\overlays\Marker;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CoordinatesIssue */

$this->title = "Coordinates Issue #{$model->id}";
$this->params['breadcrumbs'][] = ['label' => 'Coordinates Issues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile("@staticWeb/js/markerwithlabel.js", [
    'depends' => [
        'dosamigos\google\maps\MapAsset'
    ]
]);
?>
<div class="coordinates-issue-view">
    <div class="row">
        <div class="col-md-12">
            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode($this->title),
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY
            ]);
            ?>
            <?php
            $center = new LatLng();
            $center->setLat($model->old_latitude);
            $center->setLng($model->old_longitude);
            $map = new Map([
                'center' => $center,
                'zoom' => 2,
            ]);

            $coord = new LatLng();
            $coord->setLat($model->old_latitude);
            $coord->setLng($model->old_longitude);
            $marker = new Marker([
                'position' => $coord,
                'title' => "Old Coordinates",
                'icon' => "http://maps.google.com/mapfiles/kml/paddle/red-blank.png",
            ]);
            $map->addOverlay($marker);

            $coord = new LatLng();
            $coord->setLat($model->new_latitude);
            $coord->setLng($model->new_longitude);
            $marker = new Marker([
                'position' => $coord,
                'title' => "New Coordinates",
                'icon' => "http://maps.google.com/mapfiles/kml/paddle/grn-blank.png",
            ]);
            $map->addOverlay($marker);

            $map->width = '100%';
            $map->height = '500';
            $map->options['zoom'] = $map->getMarkersFittingZoom();
            $map->options['center'] = $map->getMarkersCenterCoordinates();
            //            $map->width = '100%';
            //            $map->height = '100%';
            ?>
            <?= $map->display() ?>
            <?php PanelBox::end() ?>
        </div>
        <?php if ($model->status == \common\models\CoordinatesIssue::STATUS_PENDING) { ?>
            <div class="col-md-3 col-md-offset-3">
                <?= Html::a(" Keep OLD coordinates", ['coordinates-issue/reject', 'id' => $model->id], [
                    'class' => 'btn btn-lg btn-danger btn-flat btn-block',
                    'data-confirm' => 'Are you sure?'
                ]) ?>
            </div>
            <div class="col-md-3">
                <?= Html::a(" Keep NEW coordinates", ['coordinates-issue/accept', 'id' => $model->id], [
                    'class' => 'btn btn-lg btn-success btn-flat btn-block',
                    'data-confirm' => 'Are you sure?'
                ]) ?>
            </div>
        <?php } ?>
    </div>
</div>