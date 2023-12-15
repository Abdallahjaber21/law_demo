<?php


namespace console\controllers;


use common\models\CauseCode;
use common\models\DamageCode;
use common\models\Manufacturer;
use common\models\ObjectCategory;
use common\models\ObjectCode;
use common\models\Problem;
use yii\console\Controller;

class DataController extends Controller
{

    public function actionFill()
    {
        $data = [
            "1"  => [
                "label"   => "Local machinerie",
                "objects" => [
                    "01" => "Over speed governor",
                    "02" => "Over speed governor contact",
                    "03" => "Brakes",
                    "04" => "Brakes coil",
                    "05" => "Power feeder",
                    "06" => "Lighting feeder",
                    "07" => "Traction motor",
                    "08" => "Ventilation motor",
                    "09" => "Motor thermal contact",
                    "10" => "Gearbox",
                    "11" => "Selector",
                    "12" => "Motor encoder",
                    "13" => "Other",
                ]
            ],
            "2"  => [
                "label"   => "Control panel relay type",
                "objects" => [
                    "01" => "Operation relay",
                    "02" => "Selection relay",
                    "03" => "Floors selector",
                    "04" => "Selector motor",
                    "05" => "Slipping relay",
                    "06" => "Timers relay",
                    "07" => "Thermal relay",
                    "08" => "Phase relay",
                    "09" => "Power main contactor",
                    "10" => "Contactor up-direction",
                    "11" => "Contactor slow-direction",
                    "12" => "Contactor slow speed",
                    "13" => "Contactor high speed",
                    "14" => "Auxiliary contacts",
                    "15" => "Contactor door opening",
                    "16" => "Contactor door cloing",
                    "17" => "Other contactor",
                    "18" => "Rectifier",
                    "19" => "Diodes",
                    "20" => "Capacitors",
                    "21" => "Resistances",
                    "22" => "Fuses diruptors",
                    "23" => "Transformer",
                    "24" => "Connection welding",
                    "25" => "Short circuit",
                    "26" => "Clock",
                    "27" => "Unkown error",
                    "28" => "Other",
                ]
            ],
            "3"  => [
                "label"   => "Control panel electronic type",
                "objects" => [
                    "01" => "Main board",
                    "02" => "Auxiliary board",
                    "03" => "Group control panel",
                    "04" => "Automatic doors board",
                    "05" => "Hydraulic board",
                    "06" => "Power supply board",
                    "07" => "Main floor parking board",
                    "08" => "Fire emergency Board",
                    "09" => "Releveling board",
                    "10" => "Controller ventilation",
                    "11" => "Thermal relay",
                    "12" => "Phase relay",
                    "13" => "Power main contactor",
                    "14" => "Contactor up-direction",
                    "15" => "Contactor down-direction",
                    "16" => "Contactor Slow speed",
                    "17" => "Contactor high speed",
                    "18" => "Auxiliary contacts",
                    "19" => "Contactor door opening",
                    "20" => "Contactor door closing",
                    "21" => "Other contactor",
                    "22" => "Rectifier",
                    "23" => "Diodes",
                    "24" => "Capacitors",
                    "25" => "Resistances",
                    "26" => "Fuses diruptions",
                    "27" => "Transformer",
                    "28" => "Connections welding",
                    "29" => "Short circuit",
                    "30" => "Clock",
                    "31" => "Unkown error",
                    "32" => "Other",
                ]
            ],
            "4"  => [
                "label"   => "Inverter VVVF",
                "objects" => [
                    "01" => "Main board",
                    "02" => "Auxiliary board",
                    "03" => "Fuse",
                    "04" => "Thyristors",
                    "05" => "Transmission error",
                    "06" => "Other",
                ]
            ],
            "5"  => [
                "label"   => "Shaft Equipement",
                "objects" => [
                    "01" => "Final limit switch up",
                    "02" => "Final limit switch down",
                    "03" => "Magnetic leveling equipement",
                    "04" => "Optical leveling equipement",
                    "05" => "Slow down switch up",
                    "06" => "Slow down switch down",
                    "07" => "Inspection final limit switch",
                    "08" => "Shaft rocker selector",
                    "09" => "Selection plates or magnets",
                    "10" => "Stopping plates or magnets",
                    "11" => "Tension sheave contact",
                    "12" => "Tension sheave ",
                    "13" => "Pit stop switch",
                    "14" => "Shaft acces door contact",
                    "15" => "Other",
                ]
            ],
            "6"  => [
                "label"   => "CAR",
                "objects" => [
                    "01" => "Emergency stop button",
                    "02" => "Reopen button",
                    "03" => "Close button",
                    "04" => "Call button",
                    "05" => "Inspection button",
                    "06" => "Indicator",
                    "07" => "Directional arrows",
                    "08" => "Lighting switch or key switch",
                    "09" => "Fan switch or key switch",
                    "10" => "Independent switch or key switch",
                    "11" => "Fireman switch or key switch",
                    "12" => "Foot step device",
                    "13" => "Lighting",
                    "14" => "Emergency lighting",
                    "15" => "Emergency exit trap contact",
                    "16" => "Safety gear contact",
                    "17" => "Stop switch car top",
                    "18" => "Overload contact",
                    "19" => "Full load contact",
                    "20" => "Mobile came",
                    "21" => "Overload ",
                    "22" => "Other",
                ]
            ],
            "7"  => [
                "label"   => "Automatic car door",
                "objects" => [
                    "01" => "Panels",
                    "02" => "Door locks",
                    "03" => "Door safety ray",
                    "04" => "Mechanical safety edge",
                    "05" => "safety curtain",
                    "06" => "Door radar",
                    "07" => "Door impact contact",
                    "08" => "wiring of safety rays,curtains",
                    "09" => "Fixed or retractable cam",
                    "10" => "Door operator",
                    "11" => "Door load detector(torque)",
                    "12" => "Final limit switch opening",
                    "13" => "Final limit switch closing",
                    "14" => "Slow down switch",
                    "15" => "Encoder",
                    "16" => "Magnetic switch",
                    "17" => "Door operator board",
                    "18" => "Motor belt",
                    "19" => "Gear belt",
                    "20" => "Motor chain",
                    "21" => "Gear chain",
                    "22" => "Motor",
                    "23" => "Gear",
                    "24" => "Deflection roping",
                    "25" => "Hanger rollers",
                    "26" => "Deflection rollers",
                    "27" => "Thrust rollers",
                    "28" => "Door guide shoes",
                    "29" => "Other"
                ]
            ],
            "8"  => [
                "label"   => "Manual landing doors",
                "objects" => [
                    "01" => "Door closer",
                    "02" => "Door shock absorber",
                    "03" => "Door locks",
                    "04" => "Mechanical locking device",
                    "05" => "Mechanical locking contact",
                    "06" => "Preliminary Door contact",
                    "07" => "Rubber stop",
                    "08" => "Hinges",
                    "09" => "Door glass",
                    "10" => "Call button",
                    "11" => "Hanger rollers",
                    "12" => "Deflection rollers",
                    "13" => "Thrust rollers",
                    "14" => "Door guide shoes",
                    "15" => "Other"
                ]
            ],
            "9"  => [
                "label"   => "Automatic landing door",
                "objects" => [
                    "01" => "Panel",
                    "02" => "Door locks",
                    "03" => "Mechanical landing device",
                    "04" => "Mechanical landing contact",
                    "05" => "Preliminary door contact",
                    "06" => "Rubber stop",
                    "07" => "Deflection roping",
                    "08" => "Hanger rollers",
                    "09" => "Deflection rollers",
                    "10" => "Thrust rollers",
                    "11" => "Door guide shoes",
                    "12" => "Call button",
                    "13" => "Other"
                ]
            ],
            "10" => [
                "label"   => "Hydraulic",
                "objects" => [
                    "01" => "Oil tank",
                    "02" => "Oil level",
                    "03" => "Low oil temperature",
                    "04" => "High oil temperature",
                    "05" => "Oil heating resistance",
                    "06" => "Thermostat",
                    "07" => "Block valves",
                    "08" => "Valves",
                    "09" => "Hydraulic (piston)",
                    "10" => "Piping",
                    "11" => "Other"
                ]
            ],
            "11" => [
                "label"   => "Escalators",
                "objects" => [
                    "01" => "Comb safety device",
                    "02" => "Drive chain safety device",
                    "03" => "Step chain safety device",
                    "04" => "Step  level safety device",
                    "05" => "Handrail guard safety device",
                    "06" => "Step level safety device",
                    "07" => "Skirt guard safety device",
                    "08" => "Handrail drop safety device",
                    "09" => "Comb",
                    "10" => "Step",
                    "11" => "Main chain",
                    "12" => "Secondary chain",
                    "13" => "Step rollers",
                    "14" => "Chain rollers",
                    "15" => "Pressure rollers",
                    "16" => "Guide of step rollers",
                    "17" => "Handrail inlet cap",
                    "18" => "Handrail drive rollers(sheave)",
                    "19" => "Handrail",
                    "20" => "Handrail tension",
                    "21" => "Other"
                ]
            ],
            "12" => [
                "label"   => "OTHER",
                "objects" => [
                    "01" => "Normal functionning on arrival",
                    "02" => "No errors established",
                    "03" => "Misuse",
                    "04" => "Electricity breakdown",
                    "05" => "Generator breakdown",
                    "06" => "Low voltage",
                    "07" => "Machine room temperature",
                    "08" => "Other",
                    "09" => "Other retrival/prob in pit",
                    "10" => "Other intervention",
                    "11" => "Phases reversal",
                ]
            ]
        ];

        echo "Deleting object categories" . PHP_EOL;
        $count = ObjectCategory::deleteAll();
        echo "{$count} categories deleted" . PHP_EOL;

        echo "Deleting remaining objects" . PHP_EOL;
        $count = ObjectCode::deleteAll();
        echo "{$count} objects codes deleted" . PHP_EOL;

        echo "Inserting new categories and codes" . PHP_EOL;
        foreach ($data as $categoryCode => $categoryData) {
            $objectCategory = new ObjectCategory();
            $objectCategory->code = strval($categoryCode);
            $objectCategory->name = $categoryData['label'];
            $objectCategory->status = ObjectCategory::STATUS_ENABLED;
            if ($objectCategory->save()) {
                echo "Category {$objectCategory->code} - {$objectCategory->name} created successfully" . PHP_EOL;
                foreach ($categoryData['objects'] as $code => $label) {
                    $objectCode = new ObjectCode();
                    $objectCode->object_category_id = $objectCategory->id;
                    $objectCode->code = strval($code);
                    $objectCode->name = $label;
                    $objectCode->status = ObjectCode::STATUS_ENABLED;
                    if ($objectCode->save()) {
                        echo "Object {$objectCode->code} - {$objectCode->name} created successfully" . PHP_EOL;
                    }
                }
            } else {
                print_r($objectCategory->getErrors());
                exit();
            }
        }
        echo "Finished creating object categories and codes" . PHP_EOL;

        echo "---------------------------------------------" . PHP_EOL . PHP_EOL;


        $causeCodesData = [
            "0"  => "Not Specified",
            "1"  => "Loosen",
            "2"  => "Unsoldered",
            "3"  => "Burned out",
            "4"  => "Dirt",
            "5"  => "Unadjusted",
            "6"  => "Noisy",
            "7"  => "Worn out",
            "8"  => "Broken",
            "9"  => "Stolen",
            "10" => "Maltreat",
            "11" => "others",
        ];
        echo "Deleting cause codes" . PHP_EOL;
        $count = CauseCode::deleteAll();
        echo "{$count} cause codes deleted" . PHP_EOL;

        echo "Inserting new cause codes" . PHP_EOL;
        foreach ($causeCodesData as $code => $label) {
            $damageCode = new CauseCode();
            $damageCode->code = (strlen($code) == 1 ? '0' : '') . $code;
            $damageCode->name = $label;
            $damageCode->status = CauseCode::STATUS_ENABLED;
            if ($damageCode->save()) {
                echo "Cause code {$damageCode->code} - {$damageCode->name} created successfully" . PHP_EOL;
            }
        }
        echo "Finished creating cause codes" . PHP_EOL;

        echo "---------------------------------------------" . PHP_EOL . PHP_EOL;


        $damageCodesData = [
            "W" => "Works",
            "I" => "Installation",
            "M" => "Maintenance",
            "O" => "Owner, customer, user",
            "C" => "Cannot be prevented",
            "L" => "Local supply",
            "U" => "Unknown",

        ];
        echo "Deleting damage codes" . PHP_EOL;
        $count = DamageCode::deleteAll();
        echo "{$count} damage codes deleted" . PHP_EOL;

        echo "Inserting new damage codes" . PHP_EOL;
        foreach ($damageCodesData as $code => $label) {
            $damageCode = new DamageCode();
            $damageCode->code = $code;
            $damageCode->name = $label;
            $damageCode->status = DamageCode::STATUS_ENABLED;
            if ($damageCode->save()) {
                echo "Damage code {$damageCode->code} - {$damageCode->name} created successfully" . PHP_EOL;
            }
        }
        echo "Finished creating damage codes" . PHP_EOL;

        echo "---------------------------------------------" . PHP_EOL . PHP_EOL;

        $problemsData = [
            Problem::TYPE_ALL             => [
                "Out of order",
            ],

        ];
        echo "Deleting Problems" . PHP_EOL;
        $count = Problem::deleteAll();
        echo "{$count} problems deleted" . PHP_EOL;

        echo "Inserting new Problems" . PHP_EOL;
        foreach ($problemsData as $problemType => $problems) {
            $i = 0;
            foreach ($problems as $index => $problemLabel) {
                $problem = new Problem();
                $problem->type = $problemType;
                $problem->code = $problemType . '' . $i;
                $problem->name = $problemLabel;
                $problem->status = Problem::STATUS_ENABLED;
                if ($problem->save()) {
                    echo "Problem {$problem->code} - {$problem->name} created successfully" . PHP_EOL;
                }
                $i++;
            }
        }
        echo "Finished creating problems" . PHP_EOL;

        echo "---------------------------------------------" . PHP_EOL . PHP_EOL;

    }

    public function actionUpdate1()
    {

        echo "---------------------------------------------" . PHP_EOL . PHP_EOL;

        $manufacturerData = [
            "000" => "Undefined",
            "001" => "Mitsubishi",
            "002" => "Mac-Puarsa",
            "003" => "Sematic",
            "004" => "GMV",
            "005" => "Breda",
            "006" => "AESA",
            "007" => "Boon Edam",
            "008" => "Telco",
            "009" => "Memco",
            "010" => "Savera",
            "011" => "Sassi",
            "012" => "Sicor",
            "013" => "Faymesa",
            "014" => "Faer",
            "015" => "Selcom",
            "016" => "Kleemann / Blain",
            "017" => "SKG",
            "018" => "ALJO",
            "019" => "IGV",
            "020" => "ACLA",
            "021" => "Fermator",
            "022" => "Autinor",
            "023" => "Microlift",
            "024" => "Siemens",
            "025" => "Drako",
            "026" => "Space Vertical",
            "027" => "Omron",
            "028" => "Wolfgang Schafer",
            "029" => "Telemecanique",
            "030" => "Gold Star",
            "031" => "Prudhomme",
            "032" => "Hitachi",
            "033" => "Nader Jks",
            "034" => "Micelect",
            "035" => "Fiam",
            "036" => "Kronenberg",
            "037" => "Russen Berger",
            "038" => "ELSCO",
            "039" => "Klefer S.A.",
            "040" => "Bitar Door",
            "041" => "Marton  Door",
            "042" => "Sodica",
            "043" => "Otis",
            "044" => "Cibes",
            "045" => "Rosemor",
            "046" => "Gervall Sa",
            "047" => "Kaba",
            "048" => "Henri Peignen",
            "049" => "Garufo GmbH",
            "050" => "Tung Da",
            "051" => "Hong Jiang",
            "052" => "Fonher",
            "053" => "Tianjin Guotai",
            "054" => "BST",
            "055" => "Dawson E&E",
            "056" => "Yaskawa",
            "057" => "C.O.A.M.",
            "058" => "Hisselektronik",
            "059" => "Ningbo Shenling (Anhui Tech)",
            "060" => "Emaintain",
            "061" => "Fixator",
            "062" => "Ningbo Aodepu Elev.Comp.",
            "063" => "Sunny Elevator Assembly Ltd.",
            "064" => "Hollister Whitney Elevator Corp.",
            "065" => "Goksu",
            "066" => "Liestritz",
            "067" => "Bridon Tianjin Rope LTD",
            "068" => "Bucher Hydraulics",
            "069" => "Carlos Silva",
            "070" => "NorAcon",
            "071" => "Cobianchi",
            "072" => "Torin",
            "073" => "Dalian LAT Laser",
            "074" => "Metron",
            "075" => "Wittur",
            "076" => "SFT Ningbo Beilun",
            "077" => "Changzhou Miejie Electric Appliances",
            "078" => "COMAQ",
            "079" => "STEP",
            "080" => "Pream",
            "081" => "Unite",
            "082" => "Jinan",
            "083" => "Efaflex",
            "084" => "WECO",
            "085" => "Safeline",
            "086" => "Silvelox",
            "087" => "AMP",
            "088" => "Kone",
            "089" => "Ceita",
            "090" => "Mincha Industry",
            "091" => "Delfar",
            "092" => "SJEC",
            "095" => "95",
            "096" => "Unicsis",
            "097" => "Schindler",
            "098" => "Atech",
            "099" => "DTS",
            "100" => "Shanghai Mitsubishi",
            "202" => "REN",
            "208" => "BFT",
            "209" => "Campisa",
            "210" => "ALO",
            "211" => "Beaino",
            "212" => "CMF (Makran Abdo)",
            "213" => "Daldos",
            "214" => "Dictator",
            "215" => "G.A.S.",
            "216" => "Ghassan Chemali",
            "217" => "Majelon",
            "218" => "Nuova MGT",
            "219" => "MicroTelco",
            "220" => "Other",
            "221" => "ELCO (RS)",
            "222" => "Simon Electric",
            "223" => "Tieffe",
            "224" => "Tridonics Co.",
            "999" => "Non Mitsubishi",
        ];
        echo "Deleting Manufacturer codes" . PHP_EOL;
        $count = Manufacturer::deleteAll();
        echo "{$count} manufacturer codes deleted" . PHP_EOL;

        echo "Inserting new manufacturer codes" . PHP_EOL;
        foreach ($manufacturerData as $code => $label) {
            $manufacturer = new Manufacturer();
            $manufacturer->code = $code;
            $manufacturer->name = $label;
            $manufacturer->status = CauseCode::STATUS_ENABLED;
            if ($manufacturer->save()) {
                echo "Manufacturer code {$manufacturer->code} - {$manufacturer->name} created successfully" . PHP_EOL;
            }
        }
        echo "Finished creating Manufacturer codes" . PHP_EOL;

        echo "---------------------------------------------" . PHP_EOL . PHP_EOL;
    }

}
