<?php


use common\components\Bitly;
use common\models\Location;
use common\models\LocationCode;
use yii\web\View;

/* @var $this View */
/* @var $location Location */
/* @var $locationCode LocationCode */

//https://firebase.google.com/docs/dynamic-links/create-manually
$link = 'https://fibrex.page.link';
$deepLink = "https://fibrex.e-maintain.com/?join={$locationCode->code}";
$linkParams = [
    'link' => $deepLink,//the deeplink handled by the app
    'apn'  => 'com.e_maintain.fibrex.user',//The package name of the Android app to use to open the link. The app must be connected to your project from the Overview page of the Firebase console. Required for the Dynamic Link to open an Android app.
    'ibi'  => 'com.e_maintain.fibrex.user',//The bundle ID of the iOS app to use to open the link. The app must be connected to your project from the Overview page of the Firebase console. Required for the Dynamic Link to open an iOS app.
    'isi'  => '1234',//Your app's App Store ID, used to send users to the App Store when the app isn't installed
    //
    'efr'  => 1,//If set to '1', skip the app preview page when the Dynamic Link is opened,
    //
    "st"   => 'Fibrex',//	The title to use when the Dynamic Link is shared in a social post.
    "sd"   => 'Download Fibrex App Now',//	The description to use when the Dynamic Link is shared in a social post.
    "si"   => Yii::getAlias("@staticWeb/images/logo.png"),//	The URL to an image related to this link. The image should be at least 300x200 px, and less than 300 KB.
    //
    "ofl"  => 'https://fibrex.e-maintain.com/',//if link opened in desktop
];
$params = http_build_query($linkParams);
$finalLink = "{$link}?$params";

/* @var $bitly Bitly */
$bitly = Yii::$app->bitly;
$shortlink = $bitly->shortenLink($finalLink);
$shortlink = "{$shortlink}?join={$locationCode->code}";
?>
<div class="center" style="position: absolute;bottom: 1mm; left: 0; width:100%;">
        <span class="small blue">
            Hotline for emergencies and service calls: <span class="blue">961-4-542801</span>
        </span>
</div>
<table width="100%" style="">
    <tbody>
    <tr>
        <td colspan="3">
            <table width="100%" style="">
                <tbody>
                <tr>
                    <td style="text-align: left;vertical-align: middle;">
                        <img src="<?= Yii::getAlias("@static/images/logo.png") ?>" width="45mm"/>
                    </td>
                    <td class="blue medium" style="text-align: left;vertical-align: middle; width: 35%">
                        Fibrex LLC<br/>
                        Musaffah Industrial Area<br/>
                        Musaffah – Abu Dhabi
                    </td>
                    <td class="blue medium right" style="text-align: left;vertical-align: middle; width: 20%">
                        +971-255 11 462<br/>
                        +971-255 13 331<br/>
                        www.fibrex.ae<br/>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td style="height: 80px">

        </td>
    </tr>
    <tr>
        <td colspan="3" class="border-bottom-large">
            <table style="width: 100%">
                <tbody>
                <tr>
                    <th class="large top" rowspan="3" style="width: 20mm">To:</th>
                    <th class="large"><?= $location->name ?></th>
                </tr>
                <tr>
                    <th class="large"><?= $location->address ?></th>
                </tr>
                <tr>
                    <th class="large"><?= $location->code ?></th>
                </tr>
                <tr>
                    <td colspan="2" style="height: 40px">

                    </td>
                </tr>
                <tr>
                    <th class="large" style="width: 20mm">Attn:</th>
                    <th class="large">Fibrex Users</th>
                </tr>
                <tr>
                    <td colspan="2" style="height: 15px"></td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <table style="width: 100%">
                <tbody>
                <tr>
                    <td class="top" style=" padding-top: 20mm">
                        <p class="large">
                            Dear Users,
                            <br/><br/>
                            Download the Fibrex App to connect directly with Fibrex team!
                        </p>
                        <br/><br/>
                        <div class="large">
                            <img style="margin: 0 2mm 0 7mm" src="<?= Yii::getAlias("@static/images/outline_check_black_24dp.png") ?>" width="5mm"/>
                            Quick and easy service request
                        </div>
                        <div class="large">
                            <img style="margin: 0 2mm 0 7mm" src="<?= Yii::getAlias("@static/images/outline_check_black_24dp.png") ?>" width="5mm"/>
                            Real time response feedback
                        </div>
                        <div class="large">
                            <img style="margin: 0 2mm 0 7mm" src="<?= Yii::getAlias("@static/images/outline_check_black_24dp.png") ?>" width="5mm"/>
                            Access to the latest features and news
                        </div>
                    </td>
                    <td class="top" style="padding: 10mm">
                        <img src="<?= Yii::getAlias("@static/images/invitation-card-image.jpg") ?>" width="75mm"/>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td class="large" colspan="3" align="center">
            <strong>Scan this Qr-code to downloadFibrex App and create your own account now.</strong>
            <img src="<?= (new \chillerlan\QRCode\QRCode())->render($shortlink) ?>" alt="QR Code" style="margin-top: 3mm;" width="60mm"/>
        </td>
    </tr>
    </tbody>
</table>
