<?php


use common\components\extensions\Select2;
use common\models\Technician;
use common\models\TechnicianLocation;
use common\models\TechnicianSector;
use common\models\users\Admin;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $locations TechnicianLocation[] */

$this->registerJsFile("@staticWeb/js/markerwithlabel.js", [
    'depends' => [
        'dosamigos\google\maps\MapAsset'
    ]
]);
$this->title = Yii::t('app', 'Technicians Locations');
$this->params['breadcrumbs'][] = $this->title;

$allTechniciansCount = Technician::find()
    ->joinWith(['technicianSectors'])
    ->filterWhere([TechnicianSector::tableName() . '.sector_id' => Admin::activeSectorsIds()])
    ->count();
$disabledTechniciansCount = Technician::find()
    ->joinWith(['technicianSectors'])
    ->where([Technician::tableName() . '.status' => Technician::STATUS_DISABLED])
    ->andFilterWhere([TechnicianSector::tableName() . '.sector_id' => Admin::activeSectorsIds()])
    ->count();
$onlineTechniciansCount = TechnicianLocation::find()
    ->joinWith(['technician', 'technician.technicianSectors'])
    ->where([
        'AND',
        [Technician::tableName() . '.status' => Technician::STATUS_ENABLED],
        ['>=', Technician::tableName() . '.updated_at', gmdate("Y-m-d h:i:s", strtotime("-30 minutes"))]
    ])
    ->andFilterWhere([TechnicianSector::tableName() . '.sector_id' => Admin::activeSectorsIds()])
    ->count();

$tid = Yii::$app->getRequest()->get("technician_id");
$onlineTechniciansLocation = TechnicianLocation::find()
    ->joinWith(['technician', 'technician.technicianSectors'])
    ->with(['technician'])
    ->where([
        'AND',
        [Technician::tableName() . '.status' => Technician::STATUS_ENABLED],
        ['>=', Technician::tableName() . '.updated_at', gmdate("Y-m-d h:i:s", strtotime("-30 minutes"))]
    ])
    ->andFilterWhere([TechnicianSector::tableName() . '.sector_id' => Admin::activeSectorsIds()])
    ->all();
if (!empty($tid)) {
    $onlineTechniciansLocation = TechnicianLocation::find()
        ->joinWith(['technician', 'technician.technicianSectors'])
        ->where([TechnicianLocation::tableName().'.technician_id' => $tid])
        ->andFilterWhere([TechnicianSector::tableName() . '.sector_id' => Admin::activeSectorsIds()])
        ->all();
}

$offlineTechniciansCount = $allTechniciansCount - ($disabledTechniciansCount + $onlineTechniciansCount)
?>
<?php
$data = [];
if (!empty($onlineTechniciansLocation)) {
    foreach ($onlineTechniciansLocation as $key => $location) {
        if ($location->technician->status == Technician::STATUS_DISABLED) {
            continue;
        }
        $data[] = [
            'pos'          => [$location->latitude, $location->longitude],
            'profileImage' => $location->technician->image_thumb_50_url,
            'name'         => $location->technician->name,
            'id'           => $location->technician->id,
        ];
    }
}
?>
<?= Html::beginForm('', 'GET', ['id' => 'technician-filter-form']) ?>
<div class="row row-no-gutters">
    <div class="col-md-3">
        <div class="input-group">
            <?= Select2::widget([
                'id'            => 'technician_id',
                'name'          => 'technician_id',
                'value'         => Yii::$app->request->get("technician_id"),
                'data'          => Admin::techniciansKeyValList(),
                'pluginOptions' => ['allowClear' => true],
                'options'       => [
                    'placeholder' => Yii::t('app', 'Find a technician...')
                ],
            ]) ?>
            <span class="input-group-btn">
        <button typeof="submit" class="btn btn-default">Go!</button>
      </span>
        </div>
    </div>
    <div class="col-sm-4 col-md-3">
        <div class="btn-group" role="group" aria-label="..." style="width: 100%">
            <a href="<?= Url::to(['technician-location/online']) ?>" type="button" class="btn btn-flat btn-success" style="width: 85%"
               data-remote='false'
               data-toggle='modal'
               data-target='#ajaxModal'># ONLINE</a>
            <button type="button" class="btn btn-flat btn-success" style="width: 15%"><?= $onlineTechniciansCount ?></button>
        </div>
    </div>
    <div class="col-sm-4 col-md-3">
        <div class="btn-group" role="group" aria-label="..." style="width: 100%">
            <a href="<?= Url::to(['technician-location/offline']) ?>" type="button" class="btn btn-flat btn-warning" style="width: 85%"
               data-remote='false'
               data-toggle='modal'
               data-target='#ajaxModal'># OFFLINE</a>
            <button type="button" class="btn btn-flat btn-warning" style="width: 15%"><?= $offlineTechniciansCount ?></button>
        </div>
    </div>
    <div class="col-sm-4 col-md-3">
        <div class="btn-group" role="group" aria-label="..." style="width: 100%">
            <a href="<?= Url::to(['technician-location/disabled']) ?>" type="button" class="btn btn-flat btn-danger" style="width: 85%"
               data-remote='false'
               data-toggle='modal'
               data-target='#ajaxModal'># DISABLED</a>
            <button type="button" class="btn btn-flat btn-danger" style="width: 15%"><?= $disabledTechniciansCount ?></button>
        </div>
    </div>
</div>
<?= Html::endForm() ?>
<div id="map" style="width: 100%; height: 100%;">Loading map please wait</div>
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
<script>
    <?php ob_start() ?>
    $("#technician_id").on("change", (e) => {
        $("#technician-filter-form").submit();
    });
    <?php $js = ob_get_clean() ?>
    <?php $this->registerJs($js) ?>
</script>
<script>
    <?php ob_start(); ?>
    //adapted from http://gmaps-samples-v3.googlecode.com/svn/trunk/overlayview/custommarker.html
    function CustomMarker(latlng, map, imageSrc, name, user_id) {
        this.latlng_ = latlng;
        this.imageSrc = imageSrc;
        this.name = name;
        this.user_id = user_id;
        // Once the LatLng and text are set, add the overlay to the map.  This will
        // trigger a call to panes_changed which should in turn call draw.
        this.setMap(map);
    }

    CustomMarker.prototype = new google.maps.OverlayView();

    CustomMarker.prototype.draw = function () {
        // Check if the div has been created.
        var div = this.div_;
        if (!div) {
            // Create a overlay text DIV
            div = this.div_ = document.createElement('div');
            // Create the DIV representing our CustomMarker
            div.className = "customMarker"


            var img = document.createElement("img");
            img.src = this.imageSrc;
            div.appendChild(img);

            var name = document.createElement("div");
            name.innerHTML = this.name;
            name.classList.add("badge");
            $(name).css({
                "position": "absolute",
                "top": "17px",
                "left": "56px"
            })
            div.appendChild(name);

            var me = this;
            google.maps.event.addDomListener(div, "click", function (event) {
                google.maps.event.trigger(me, "click");
                location.href = '<?= Url::to(['technician/view']) ?>?id=' + me.user_id;
            });

            // Then add the overlay to the DOM
            var panes = this.getPanes();
            panes.overlayImage.appendChild(div);
        }

        // Position the overlay
        var point = this.getProjection().fromLatLngToDivPixel(this.latlng_);
        if (point) {
            div.style.left = point.x + 'px';
            div.style.top = point.y + 'px';
        }
    };

    CustomMarker.prototype.remove = function () {
        // Check if the overlay was on the map and needs to be removed.
        if (this.div_) {
            this.div_.parentNode.removeChild(this.div_);
            this.div_ = null;
        }
    };

    CustomMarker.prototype.getPosition = function () {
        return this.latlng_;
    };

    setTimeout(function () {
        var data = <?= Json::encode($data) ?>;

        var map = new google.maps.Map(document.getElementById("map"), {
            zoom: 9,
            center: new google.maps.LatLng(33.8734299, 35.2869806),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });


        let bounds = new google.maps.LatLngBounds();
        for (var i = 0; i < data.length; i++) {
            var marker = new CustomMarker(new google.maps.LatLng(data[i].pos[0], data[i].pos[1]), map, data[i].profileImage, data[i].name, data[i].id);
            bounds.extend(marker.getPosition());
        }
        map.fitBounds(bounds);

    }, 2000)
    <?php $js = ob_get_clean();?>
    <?php $this->registerJs($js);?>
</script>
<style>
    <?php ob_start(); ?>
    .customMarker {
        position: absolute;
        cursor: pointer;
        background: #424242;
        width: 50px;
        height: 50px;
        /* -width/2 */
        margin-left: -25px;
        /* -height + arrow */
        margin-top: -55px;
        border-radius: 50%;
        padding: 0px;
    }

    .customMarker:after {
        content: "";
        position: absolute;
        bottom: -5px;
        left: 20px;
        border-width: 5px 5px 0;
        border-style: solid;
        border-color: #424242 transparent;
        display: block;
        width: 0;
    }

    .customMarker img {
        width: 45px;
        height: 45px;
        margin: 2px;
        border-radius: 50%;
    }

    <?php $css = ob_get_clean();?>
    <?php $this->registerCss($css);?>
</style>


<script>
    <?php ob_start() ?>
    // Fill modal with content from link href
    $("#ajaxModal").on("show.bs.modal", function (e) {
        $(this).find(".modal-body").html("");
        var link = $(e.relatedTarget);
        $(this).find(".modal-body").load(link.attr("href"));
    });

    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
    <?php $js = ob_get_clean() ?>
    <?php $this->registerJs($js) ?>
</script>
