<?php

use pigolab\locationpicker\CoordinatesPicker;
use yii\base\View;
use yii\helpers\Html;
use yii\web\JsExpression;

/* @var $this View */
/* @var $id string */
?>
<script>
window.mapInitialized = false; // Initialize as false initially
<?php ob_start(); ?>

function initializeMap(component) {
    $('#<?= $id ?>').locationpicker('autosize');
    var cb = function(event) {
        $(component).locationpicker('location', {
            latitude: event.latLng.lat(),
            longitude: event.latLng.lng()
        })
    };
    <?php if (!$view_only) : ?>
    $(component).locationpicker('subscribe', {
        event: 'click',
        callback: cb
    });
    <?php endif; ?>

    window.mapInitialized = true; // Set to true after initialization
}
<?php $js = ob_get_clean(); ?>
</script>

<?=
CoordinatesPicker::widget([
    'name' => 'locationpicker-' . rand(1000, 9999),
    'id' => $id,
    'key' => Yii::$app->params['googleMapsKey'], // require , Put your google map api key
    'options' => [
        'style' => 'width: 100%;' . $view_only ? 'height: 300px;' : 'height: 500px;', // map canvas width and height
    ],
    'enableSearchBox' => $view_only ? false : true, // Enable the search box
    'searchBoxOptions' => [
        'placeholder' => 'Select a location',
        'style' => 'width: 500px;', // Optional , default width and height defined in css coordinates-picker.css
    ],
    'mapOptions' => [
        'mapTypeId' => new JsExpression('google.maps.MapTypeId.HYBRID'),
        'mapTypeControl' => $view_only ? false : true,
    ],
    'clientOptions' => [
        'zoom' => 12,
        'enableAutocomplete' => $view_only ? false : true,
        'autocompleteOptions' => [
            //'componentRestrictions' => ['country' => 'lb']
        ],
        'location' => [
            'latitude' => !empty($latitude) ? $latitude : 0,
            'longitude' => !empty($longitude) ? $longitude : 0,
        ],
        'radius' => 0,
        'addressFormat' => 'street_number',
        'inputBinding' => [
            'latitudeInput' => new JsExpression("$('#" . $latitude_attr . "')"),
            'longitudeInput' => new JsExpression("$('#" . $longitude_attr . "')"),
            // 'locationNameInput' => new JsExpression("$('#" . $address_attr . "')"),
        ],
        'oninitialized' => new JsExpression("$js")
    ],
]);
?>