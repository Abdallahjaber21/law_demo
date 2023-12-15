<?php

use yii\helpers\Json;
use yii\web\View;

/* @var $id string */
/* @var $this View */
/* @var $features mixed */

$this->registerJsFile(Yii::getAlias("@staticWeb/plugins/leaflet-1.5.1/leaflet.js"));
$this->registerCssFile(Yii::getAlias("@staticWeb/plugins/leaflet-1.5.1/leaflet.css"));
$this->registerCssFile(Yii::getAlias("@staticWeb/plugins/leaflet-1.5.1/Leaflet.ExtraMarkers/css/leaflet.extra-markers.min.css"));
$this->registerJsFile(Yii::getAlias("@staticWeb/plugins/leaflet-1.5.1/Leaflet.ExtraMarkers/js/leaflet.extra-markers.js"));
$this->registerJsFile(Yii::getAlias("@staticWeb/plugins/leaflet-1.5.1/leaflet.rotatedMarker/leaflet.rotatedMarker.js"));
$this->registerCssFile(Yii::getAlias("@staticWeb/plugins/leaflet-1.5.1/Leaflet.MarkerCluster/MarkerCluster.css"));
$this->registerCssFile(Yii::getAlias("@staticWeb/plugins/leaflet-1.5.1/Leaflet.MarkerCluster/MarkerCluster.Default.css"));
$this->registerJsFile(Yii::getAlias("@staticWeb/plugins/leaflet-1.5.1/Leaflet.MarkerCluster/leaflet.markercluster.js"));

?>

<div id="map-<?= $id ?>" style="width: 100%;height: 100%;"></div>
<script>
    <?php ob_start(); ?>

    var mymap = L.map('map-<?= $id ?>').setView([0, 0], 18);
    var mapboxTiles = L.tileLayer(
        'https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiY29kZW5kb3QiLCJhIjoiY2sxOTRkaWMwMXNkcTNjcHk2bGp5dTlrNSJ9.2DpozOCucreMtwUo0QX65w'
        , {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 18,
            id: 'mapbox.streets',
        });
    var osmTiles = L.tileLayer(
        'https://a.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
            maxZoom: 18,
            id: 'openstreetmap.a'
        })

    var orsTiles = L.tileLayer(
        'https://api.openrouteservice.org/mapsurfer/{z}/{x}/{y}.png?api_key=5b3ce3597851110001cf624856b021f93d5749a790e786fc8d24774b'
        , {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
            maxZoom: 18,
            id: 'openstreetmap.b'
        }).addTo(mymap);;

    var markerColors = [
        'blue',
        'red',
        'orange-dark',
        'orange',
        'yellow',
        'blue-dark',
        'cyan',
        'purple',
        'violet',
        'pink',
        'green-dark',
        'green',
        'green-light',
        'black',
        'white',
    ];
    var currentColorIndex = 0;
    var markerShapes = ['circle', 'square', 'star', 'penta'];
    var routeColors = [
        '#1b75bb',
        '#8e1f21',
        '#d73f29',
        '#ec7d18',
        '#f3af23',
        '#183b45',
        '#27a4db',
        '#38224d',
        '#781776',
        '#b73b92',
        '#005e2c',
        '#00832c',
        '#489a29',
        '#000000',
        '#ffffff',
    ];
    var currentRouteColorIndex = 0;

    var featuresFilter = {};
    var allMarkers = [];
    var features = <?= Json::encode($features) ?>;
    $.each(features, function (index, feature) {
        if (feature['type'] == "route") {
            var routeColor = routeColors[0];
            if (feature['color'] != "random") {
                routeColor = feature['color'];
            } else {
                routeColor = routeColors[(currentRouteColorIndex++) % routeColors.length];
            }
            var routeStyle = {
                "color": routeColor,
                "weight": feature['weight'] ? feature['weight'] : 3,
                "opacity": feature['opacity'] ? feature['opacity'] : 1
            };

            var featureGeoJson = feature['route'];
            console.log(featureGeoJson);
            var featureLine = L.geoJSON(featureGeoJson, {style: routeStyle}).addTo(mymap);
            featuresFilter[feature['label']] = featureLine;
        }

        if (feature['type'] == "markers") {
            var featureMarkers = [];
            var markersGroupCluster = L.markerClusterGroup();
            var markersData = feature['markers'];
            var markerColor = markerColors[0];
            var markerShape = markerShapes[0];
            var markerAngle = 0;
            if (feature['color'] != "random" && markerColors.includes(feature['color'])) {
                markerColor = feature['color'];
            } else {
                markerColor = markerColors[(currentColorIndex++) % markerColors.length];
            }
            if (markerShapes.includes(feature['shape'])) {
                markerShape = feature['shape'];
            }
            if (feature['angle']) {
                markerAngle = feature['angle'];
            }

            var markerIcon = L.ExtraMarkers.icon({
                icon: feature['icon'],
                markerColor: markerColor,
                shape: markerShape,
                prefix: 'fa',
                iconColor: feature['iconColor'] ? feature['iconColor'] : 'white',
            });
            var i = 1;
            $.each(markersData, function (o, markerData) {
                if (feature['icon'] == "fa-number") {
                    markerIcon = L.ExtraMarkers.icon({
                        icon: feature['icon'],
                        markerColor: markerColor,
                        shape: markerShape,
                        prefix: 'fa',
                        iconColor: feature['iconColor'] ? feature['iconColor'] : 'white',
                        number: i++,
                    });
                }
                if(markerData['lat'] <= 0 || markerData['lng'] <= 0){

                }
                var marker = L.marker([markerData['lat'], markerData['lng']],
                    {
                        icon: markerIcon,
                        rotationAngle: markerAngle
                    });
                marker.bindPopup(markerData['name']);
                if (feature['cluster']) {
                    markersGroupCluster.addLayer(marker);
                }
                featureMarkers.push(marker);
                allMarkers.push(marker);
            });
            var markersGroup = L.layerGroup(featureMarkers);
            markersGroup.addTo(mymap);
            featuresFilter[feature['label']] = markersGroup;
            if (feature['cluster']) {
                featuresFilter[feature['label'] + " - Clustered"] = markersGroupCluster;
            }

        }
    })
    ;

    L.control.layers({
        "Open Street Map Tiles": osmTiles,
        "Open Route Service Tiles": orsTiles,
        "Map Box Tiles": mapboxTiles
    }, featuresFilter)
        .addTo(mymap);

    var featureGroup = new L.featureGroup(allMarkers);
    mymap.fitBounds(featureGroup.getBounds().pad(0.1));

    <?php $js = ob_get_clean();?>
    <?php $this->registerJs($js);?>
</script>
