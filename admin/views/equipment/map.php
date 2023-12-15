<?php

use common\components\extensions\MarkerWithLabel;
use common\models\Location;
use common\models\Technician;
use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\overlays\InfoWindow;
use edofre\markerclusterer\Map;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $locations array|Location[]|ActiveRecord[] */
/* @var $activeLocations array */
/* @var $activeMaintenances array */
/* @var $countUnits array */
$this->title = "Map";

$this->registerJsFile("@staticWeb/js/markerwithlabel.js", [
    'depends' => [
        'dosamigos\google\maps\MapAsset'
    ]
]);
?>
<?php if(count($locations) > 0){ ?>
<?php
$coords = [];

$center = new LatLng();
$center->setLat($locations[0]['latitude']);
$center->setLng($locations[0]['longitude']);
$map = new Map([
    'center'         => $center,
    'zoom'           => 2,
    'clusterOptions' => [
        'maxZoom' => 13
    ]
]);
$map->width = '100%';
$map->height = '100%';

$maintenanceIcon = Yii::getAlias("@staticWeb/images/map/mechanic.png");
$breakdownIcon = Yii::getAlias("@staticWeb/images/map/caution.png");
//http://maps.google.com/mapfiles/kml/paddle/ylw-blank.png
//http://maps.google.com/mapfiles/kml/paddle/grn-blank.png
//http://maps.google.com/mapfiles/kml/paddle/blu-blank.png
$normalIcon = Yii::getAlias("@staticWeb/images/map/red-circle.png");

foreach ($locations as $index => $location) {
    if(empty($location['latitude']) || empty($location['longitude'])){
        continue;
    }
    $coord = new LatLng();
    $coord->setLat($location['latitude']);
    $coord->setLng($location['longitude']);

    $markerImage = null;//$normalIcon;
    if (!empty($activeLocations[$location['id']])) {
        $markerImage = $breakdownIcon;
    }
    if (!empty($activeMaintenances[$location['id']])) {
        $markerImage = $maintenanceIcon;
    }

    $marker = new MarkerWithLabel([
        'position'     => $coord,
        'title'        => $location['name'],
        'icon'         => $markerImage,
        //'label' => $location->getLabel(),
        //'animation' => Animation::DROP,
        'labelContent' => $location['name'],
        'labelAnchor'  => new JsExpression('new google.maps.Point(-15, 25)'),
        "labelClass"   => "map-labels", // the CSS class for the label
        "labelStyle"   => new JsExpression("{opacity:0.75}")
    ]);

    $marker->attachInfoWindow(
        new InfoWindow([
            'content' =>
                DetailView::widget([
                    'model'      => $location,
                    'attributes' => [
                        'id',
                        'code',
                        'name',
                        'address',
                        '# units' => [
                            'label' => '# of units',
                            'value' => @$countUnits[$location['id']]['c']
                        ],
                    ],
                ])
                . (!empty($activeMaintenances[$location['id']]) ?
                    Html::a(Yii::t("app", 'Active Maintenance'), ['maintenance/progress', 'MaintenanceSearch[location_search]' => $location['code']], [
                        'class'  => 'btn btn-block btn-flat btn-xs bg-yellow',
                        'target' => '_blank'
                    ]) : '')
                . (!empty($activeLocations[$location['id']]) ?
                    Html::a(Yii::t("app", 'Active Repair/Works'), ['repair-request/index', 'RepairRequestSearch[location_id]' => $location['code']], [
                        'class'  => 'btn btn-block btn-flat btn-xs bg-red',
                        'target' => '_blank'
                    ]) : '')
                .
                Html::a(Yii::t("app", 'View'), ['location/view', 'id' => $location['id']], [
                    'class'  => 'btn btn-block btn-flat btn-xs bg-blue',
                    'target' => '_blank'
                ])
        ])
    );
    $map->addOverlay($marker);
}

$map->width = '500';
$map->height = '500';
$map->options['zoom'] = $map->getMarkersFittingZoom();
$map->options['center'] = $map->getMarkersCenterCoordinates();
$map->width = '100%';
$map->height = '100%';
?>

    <?= Html::beginForm('', 'GET', ['id' => 'technician-filter-form']) ?>
    <div class="row row-no-gutters">
        <div class="col-md-3">
            <div class="input-group">
                <?= \common\components\extensions\Select2::widget([
                    'id'            => 'location_id',
                    'name'          => 'location_id',
                    'value'         => Yii::$app->request->get("location_id"),
                    'data'          => \common\models\users\Admin::locationsKeyValList(),
                    'pluginOptions' => ['allowClear' => true],
                    'options'       => [
                        'placeholder' => Yii::t('app', 'Find a location...')
                    ],
                ]) ?>
                <span class="input-group-btn">
        <button typeof="submit" class="btn btn-default">Go!</button>
      </span>
            </div>
        </div>
        <div class="col-sm-4 col-md-3">
            <a href="<?= Url::to(['equipment/map', 'filter'=>"all"]) ?>" type="button" class="btn btn-flat btn-success btn-block">
                All Locations
            </a>
        </div>
        <div class="col-sm-4 col-md-3">
            <a href="<?= Url::to(['equipment/map', 'filter'=>"repair"]) ?>" type="button" class="btn btn-flat btn-warning btn-block">
                Active Repair/Work
            </a>
        </div>
        <div class="col-sm-4 col-md-3">
            <a href="<?= Url::to(['equipment/map', 'filter'=>"maintenance"]) ?>"  type="button" class="btn btn-flat btn-danger btn-block">
                Active Maintenance
            </a>
        </div>
    </div>
    <?= Html::endForm() ?>
<?= $map->display() ?>

<style type="text/css">
    <?php ob_start() ?>
    .content-header {
        display: none;
    }

    .content-wrapper {
        position: relative;
    }

    .content {
        padding: 0;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        padding-top: 50px;
    }

    .map-searchbox {
        top: 50px;
    }

    @media (max-width: 767px) {
        .fixed .content-wrapper .content {
            padding-top: 100px;
        }

        .map-searchbox {
            top: 100px;
        }
    }

    <?php $css = ob_get_clean() ?>
    <?php $this->registerCss($css) ?>
</style>
<?php } ?>
