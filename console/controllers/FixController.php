<?php


namespace console\controllers;

use common\models\BarcodeScan;
use common\models\Equipment;
use common\models\Maintenance;
use common\models\Technician;
use yii\helpers\ArrayHelper;

class FixController extends \yii\console\Controller
{
    public function actionFix()
    {
        $maintenances = Maintenance::find()->where(['status' => Maintenance::STATUS_COMPLETE])
            ->andWhere(['remaining_barcodes' => 1])->all();
        foreach ($maintenances as $index => $maintenance) {
            $totalScannedBarcodes = BarcodeScan::find()
                ->where([
                    'AND',
                    ['maintenance_id' => $maintenance->id]
                ])
                ->count();
            $totalBarcodes = $maintenance->equipment->getEquipmentMaintenanceBarcodes()->count();
            $remaining = $totalBarcodes - $totalScannedBarcodes;
            if ($maintenance->remaining_barcodes > $remaining) {
                echo "{$maintenance->id} => {$maintenance->remaining_barcodes} => {$remaining}" . PHP_EOL;
                $maintenance->remaining_barcodes = $totalBarcodes - $totalScannedBarcodes;
                $maintenance->number_of_barcodes = $totalBarcodes;
                $maintenance->save(false);
            }
        }

        $count = Maintenance::updateAll(['complete_method' => Maintenance::COMPLETE_SCAN_ALL], ['remaining_barcodes' => 0]);
        echo "{$count} Updated" . PHP_EOL;
    }

    public function actionDtmp()
    {//remove technician assignment from assigned maintenances and return status to pending
        $disabledTechs = ArrayHelper::getColumn(Technician::find()
            ->select(['id'])
            ->where(['status' => Technician::STATUS_DISABLED])
            ->asArray()
            ->all(), 'id', false);

        $count = Maintenance::updateAll([
            'technician_id' => null,
            'status'        => Maintenance::STATUS_PENDING
        ], [
            'technician_id' => $disabledTechs,
            'status'        => Maintenance::STATUS_ASSIGNED
        ]);
        echo "{$count} maintenances updated" . PHP_EOL;
    }

    public function actionPlantCodes()
    {
        $codes = [
            "3144"  => "3144_Plate:81912",
            "3143"  => "3143_Plate:82608",
            "3142"  => "3142_Plate:96102",
            "3141"  => "3141_Plate:90558",
            "3139"  => "3139_Plate:72719",
            "3140"  => "3140_Plate:72721",
            "3130"  => "3130_Plate:77885",
            "3126"  => "3126_Plate:74664",
            "3125"  => "3125_Plate:74669",
            "3124"  => "3124_Plate:73677",
            "3123"  => "3123_Plate:73073",
            "3120"  => "3120_Plate:74451",
            "3119"  => "3119_Plate:74443",
            "3132"  => "3132_Plate:72008",
            "3122"  => "3122_Plate:75447",
            "3121"  => "3121_Plate:75483",
            "3129"  => "3129_Plate:75430",
            "3138"  => "3138_Plate:72722",
            "3128"  => "3128_Plate:74665",
            "3137"  => "3137_Plate:72718",
            "3135"  => "3135_Plate:71958",
            "3134"  => "3134_Plate:71961",
            "3131"  => "3131_Plate:72717",
            "3133"  => "3133_Plate:71959",
            "3136"  => "3136_Plate:71960",
            "3127"  => "3127_Plate:80281",
            "3113"  => "3113_Plate:72636",
            "3118"  => "3118_Plate:72642",
            "3117"  => "3117_Plate:72638",
            "3116"  => "3116_Plate:72644",
            "3115"  => "3115_Plate:72645",
            "3114"  => "3114_Plate:72635",
            "3029"  => "3029_Plate:9387",
            "3058"  => "3058_Plate:61626",
            "3068"  => "3068_Plate:94901",
            "3075"  => "3075_Plate:65416",
            "3028"  => "3028_Plate:42916",
            "3064"  => "3064_Plate:94992",
            "3060"  => "3060_Plate:94624",
            "3059"  => "3059_Plate:93543",
            "3062"  => "3062_Plate:58334",
            "3070"  => "3070_Plate:94949",
            "3063"  => "3063_Plate:16731",
            "3067"  => "3067_Plate:94918",
            "3076"  => "3076_Plate:83809",
            "3042"  => "3042_Plate:94625",
            "3027"  => "3027_Plate:63703",
            "3026"  => "3026_Plate:76836",
            "3072"  => "3072_Plate:94904",
            "3018"  => "3018_Plate:40752",
            "3020"  => "3020_Plate:8799",
            "3103"  => "3103_Plate:79831",
            "3017"  => "3017_Plate:8914",
            "3104"  => "3104_Plate:79832",
            "3019"  => "3019_Plate:40743",
            "3102"  => "3102_Plate:79830",
            "3025"  => "3025_Plate:74791",
            "3040"  => "3040_Plate:87434",
            "3021"  => "3021_Plate:46721",
            "3105"  => "3105_Plate:79828",
            "3043"  => "3043_Plate:52051",
            "3016"  => "3016_Plate:90348",
            "4638"  => "4638_Plate:48420",
            "4549"  => "4549_Plate:66943",
            "4653"  => "4653_Plate:69823",
            "3099"  => "3099_Plate:75568",
            "3073"  => "3073_Plate:53081",
            "3069"  => "3069_Plate:66406",
            "3010"  => "3010_Plate:84906",
            "3012"  => "3012_Plate:8083",
            "3093"  => "3093_Plate:1572-2",
            "3013"  => "3013_Plate:71953",
            "3011"  => "3011_Plate:74935",
            "3014"  => "3014_Plate:34464",
            "3008"  => "3008_Plate:7018",
            "3009"  => "3009_Plate:7017",
            "3005"  => "3005_Plate:73880",
            "3047"  => "3047_Plate:64319",
            "3039"  => "3039_Plate:54662",
            "3004"  => "3004_Plate:9290",
            "3002"  => "3002_Plate:1572-1",
            "3048"  => "3048_Plate:28963",
            "3041"  => "3041_Plate:56014",
            "44587" => "44587_Plate:44587",
            "44592" => "44592_Plate:44592",
            "44591" => "44591_Plate:44591",
            "44590" => "44590_Plate:44590",
            "44589" => "44589_Plate:44589",
            "92253" => "92253_Plate:92253",
            "91883" => "91883_Plate:91883",
            "91104" => "91104_Plate:91104",
            "90975" => "90975_Plate:90975",
            "90666" => "90666_Plate:90666",
            "90775" => "90775_Plate:90775",
            "41284" => "41284_Plate:41284",
            "68521" => "68521_Plate:68521",
            "68499" => "68499_Plate:68499",
            "79307" => "79307_Plate:79307",
            "89623" => "89623_Plate:89623",
            "81421" => "81421_Plate:81421",
            "58584" => "58584_Plate:58584",
            "47897" => "47897_Plate:47897",
            "48193" => "48193_Plate:48193",
            "30252" => "30252_Plate:30252",
            "80337" => "80337_Plate:80337",
            "74362" => "74362_Plate:74362",
            "72355" => "72355_Plate:72355",
            "88263" => "88263_Plate:88263",
            "88927" => "88927_Plate:88927",
            "35809" => "35809_Plate:35809",
            "35949" => "35949_Plate:35949",
            "97306" => "97306_Plate:97306",
            "64459" => "64459_Plate:64459",
            "98154" => "98154_Plate:98154",
            "90467" => "90467_Plate:90467",
            "41576" => "41576_Plate:41576",
            "77739" => "77739_Plate:77739",
            "85556" => "85556_Plate:85556",
            "81325" => "81325_Plate:81325",
            "78799" => "78799_Plate:78799",
            "76998" => "76998_Plate:76998",
            "77895" => "77895_Plate:77895",
            "75313" => "75313_Plate:75313",
            "80147" => "80147_Plate:80147",
            "98377" => "98377_Plate:98377",
            "55012" => "55012_Plate:55012",
            "91405" => "91405_Plate:91405",
            "36533" => "36533_Plate:36533",
            "75629" => "75629_Plate:75629",
            "90908" => "90908_Plate:90908",
            "16362" => "16362_Plate:16362",
            "39098" => "39098_Plate:39098",
            "87380" => "87380_Plate:87380",
            "84627" => "84627_Plate:84627",
            "76685" => "76685_Plate:76685",
            "52443" => "52443_Plate:52443",
            "89448" => "89448_Plate:89448",
            "39097" => "39097_Plate:39097",
            "26960" => "26960_Plate:26960",
            "50153" => "50153_Plate:50153",
            "54102" => "54102_Plate:54102",
            "72085" => "72085_Plate:72085",
            "95600" => "95600_Plate:95600",
            "90251" => "90251_Plate:90251",
            "40033" => "40033_Plate:40033",
            "40938" => "40938_Plate:40938",
            "38499" => "38499_Plate:38499",
            "26952" => "26952_Plate:26952",
            "71376" => "71376_Plate:71376",
            "20195" => "20195_Plate:20195",
            "38662" => "38662_Plate:38662",
            "22494" => "22494_Plate:22494",
            "49745" => "49745_Plate:49745",
            "5210"  => "5210_Plate:5210",
            "97312" => "97312_Plate:97312",
            "97421" => "97421_Plate:97421",
            "75843" => "75843_Plate:75843",
            "20482" => "20482_Plate:20482",
            "11187" => "11187_Plate:11187",
            "5215"  => "5215_Plate:5215",
            "38297" => "38297_Plate:38297",
            "2979"  => "2979_Plate:2979",
            "10921" => "10921_Plate:10921",
            "14273" => "14273_Plate:14273",
            "7551"  => "7551_Plate:7551",
            "10977" => "10977_Plate:10977",
            "94737" => "94737_Plate:94737",
            "55837" => "55837_Plate:55837",
            "69442" => "69442_Plate:69442",
            "41572" => "41572_Plate:41572",
            "29347" => "29347_Plate:29347",
            "95640" => "95640_Plate:95640",
            "69120" => "69120_Plate:69120",
            "53317" => "53317_Plate:53317",
            "69533" => "69533_Plate:69533",
            "46247" => "46247_Plate:46247",
            "97221" => "97221_Plate:97221",
            "72165" => "72165_Plate:72165",
            "79160" => "79160_Plate:79160",
            "29319" => "29319_Plate:29319",
            "33961" => "33961_Plate:33961",
            "28698" => "28698_Plate:28698",
            "75698" => "75698_Plate:75698",
            "92421" => "92421_Plate:92421",
            "10358" => "10358_Plate:10358",
            "88505" => "88505_Plate:88505",
            "57361" => "57361_Plate:57361",
            "15486" => "15486_Plate:15486",
            "15480" => "15480_Plate:15480",
            "15487" => "15487_Plate:15487",
            "50610" => "50610_Plate:50610",
            "53965" => "53965_Plate:53965",
            "15479" => "15479_Plate:15479",
            "68274" => "68274_Plate:68274",
            "52264" => "52264_Plate:52264",
            "51045" => "51045_Plate:51045",
            "50243" => "50243_Plate:50243",
            "45217" => "45217_Plate:45217",
            "16224" => "16224_Plate:16224",
            "15484" => "15484_Plate:15484",
            "15481" => "15481_Plate:15481",
            "15477" => "15477_Plate:15477",
            "10533" => "10533_Plate:10533",
            "36562" => "36562_Plate:36562",
            "15961" => "15961_Plate:15961",
            "52591" => "52591_Plate:52591",
            "53329" => "53329_Plate:53329",
            "47182" => "47182_Plate:47182",
            "15485" => "15485_Plate:15485",
            "80137" => "80137_Plate:80137",
            "59310" => "59310_Plate:59310",
            "37431" => "37431_Plate:37431",
            "94809" => "94809_Plate:94809",
            "62248" => "62248_Plate:62248",
            "29931" => "29931_Plate:29931",
            "87074" => "87074_Plate:87074",
            "78472" => "78472_Plate:78472",
            "73533" => "73533_Plate:73533",
            "97803" => "97803_Plate:97803",
            "70152" => "70152_Plate:70152",
            "87067" => "87067_Plate:87067",
            "77931" => "77931_Plate:77931",
            "43624" => "43624_Plate:43624",
            "81042" => "81042_Plate:81042",
            "67659" => "67659_Plate:67659",
            "67538" => "67538_Plate:67538",
            "47331" => "47331_Plate:47331",
            "71525" => "71525_Plate:71525",
            "84308" => "84308_Plate:84308",
            "75645" => "75645_Plate:75645",
            "63299" => "63299_Plate:63299",
            "12635" => "12635_Plate:12635",
            "36778" => "36778_Plate:36778",
            "96157" => "96157_Plate:96157",
            "87026" => "87026_Plate:87026",
            "79140" => "79140_Plate:79140",
            "63177" => "63177_Plate:63177",
            "41192" => "41192_Plate:41192",
            "44086" => "44086_Plate:44086",
            "84120" => "84120_Plate:84120",
            "56128" => "56128_Plate:56128",
            "41833" => "41833_Plate:41833",
            "52927" => "52927_Plate:52927",
            "41197" => "41197_Plate:41197",
            "39116" => "39116_Plate:39116",
            "24975" => "24975_Plate:24975",
            "66824" => "66824_Plate:66824",
            "63371" => "63371_Plate:63371",
            "79930" => "79930_Plate:79930",
            "63171" => "63171_Plate:63171",
            "67453" => "67453_Plate:67453",
            "28891" => "28891_Plate:28891",
            "63196" => "63196_Plate:63196",
            "63167" => "63167_Plate:63167",
            "63203" => "63203_Plate:63203",
            "65802" => "65802_Plate:65802",
            "65506" => "65506_Plate:65506",
            "65587" => "65587_Plate:65587",
            "64009" => "64009_Plate:64009",
            "63660" => "63660_Plate:63660",
            "56294" => "56294_Plate:56294",
            "49645" => "49645_Plate:49645",
            "80032" => "80032_Plate:80032",
            "84881" => "84881_Plate:84881",
            "69488" => "69488_Plate:69488",
            "74986" => "74986_Plate:74986",
            "88704" => "88704_Plate:88704",
            "50770" => "50770_Plate:50770",
            "78242" => "78242_Plate:78242",
            "9382"  => "9382_Plate:9382",
            "9381"  => "9381_Plate:9381",
            "9377"  => "9377_Plate:9377",
            "88402" => "88402_Plate:88402",
            "37463" => "37463_Plate:37463",
            "33379" => "33379_Plate:33379",
            "13468" => "13468_Plate:13468",
            "94626" => "94626_Plate:94626",
            "99221" => "99221_Plate:99221",
            "99211" => "99211_Plate:99211",
            "77613" => "77613_Plate:77613",
            "77605" => "77605_Plate:77605",
            "67214" => "67214_Plate:67214",
            "64992" => "64992_Plate:64992",
            "61850" => "61850_Plate:61850",
            "61847" => "61847_Plate:61847",
            "54458" => "54458_Plate:54458",
            "90373" => "90373_Plate:90373",
            "52385" => "52385_Plate:52385",
            "49438" => "49438_Plate:49438",
            "76313" => "76313_Plate:76313",
            "99610" => "99610_Plate:99610",
            "93156" => "93156_Plate:93156",
            "20934" => "20934_Plate:20934",
            "13456" => "13456_Plate:13456",
            "9385"  => "9385_Plate:9385",
            "9380"  => "9380_Plate:9380",
            "9375"  => "9375_Plate:9375",
            "9373"  => "9373_Plate:9373",
            "94181" => "94181_Plate:94181",
            "67901" => "67901_Plate:67901",
            "53006" => "53006_Plate:53006",
            "47671" => "47671_Plate:47671",
            "81868" => "81868_Plate:81868",
            "67605" => "67605_Plate:67605",
            "15482" => "15482_Plate:15482",
            "63669" => "63669_Plate:63669",
            "63651" => "63651_Plate:63651",
            "89213" => "89213_Plate:89213",
        ];
        foreach ($codes as $oldCode => $newCode) {
            echo "Updating {$oldCode} -> {$newCode}" . PHP_EOL;
            Equipment::updateAll(['code' => $newCode], ['code' => "".$oldCode]);
        }
    }
}
