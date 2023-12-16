<?php

use backend\assets\AppAsset;
use common\assets\CustomAdminLteAsset;
use dmstr\web\AdminLteAsset;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

Yii::$container->set(AdminLteAsset::className(), [
    'css' => null,
]);
CustomAdminLteAsset::register($this);
dmstr\web\AdminLteAsset::register($this);
\common\assets\LoginAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title>
        <?= Html::encode($this->title) ?>
    </title>
    <?php $this->head() ?>
    <style>
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: #4a4a4a;
            background-image: url("<?= Yii::getAlias("@staticWeb/images/bg-login.jpg") ?>");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: 50% 50%;
            top: 0;
            left: 0;
            z-index: 10;
            filter: blur(3px);
        }

        body {
            margin: 0;
        }

        canvas {
            display: block;
            vertical-align: bottom;
        }

        .login-logo {
            margin: 0;
            background-color: white;
        }
    </style>
</head>

<body class="login-page">

    <?php $this->beginBody() ?>

    <div id="particles-js"></div>
    <?= common\widgets\Alert::widget() ?>

    <div class="login-box">
        <div class="login-logo">
            <img src="<?= Yii::getAlias("@staticWeb") . "/images/logo.png" ?>">
        </div>
        <?= $content ?>
        <div class="text-center">
            <?=
                common\components\extensions\LanguageSwitcher::widget([
                    'view' => common\components\extensions\LanguageSwitcher::VIEW_LINKS
                ])
                ?>
        </div>
    </div><!-- /.login-box -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": {
                    "value": 100,
                    "density": {
                        "enable": true,
                        "value_area": 800
                    }
                },
                "color": {
                    "value": "#ffffff"
                },
                "shape": {
                    "type": "circle",
                    "stroke": {
                        "width": 1,
                        "color": "#ffffff"
                    },
                    "polygon": {
                        "nb_sides": 1
                    },
                    "image": {
                        "src": "img/github.svg",
                        "width": 100,
                        "height": 100
                    }
                },
                "opacity": {
                    "value": 0.5,
                    "random": false,
                    "anim": {
                        "enable": false,
                        "speed": 1,
                        "opacity_min": 0.1,
                        "sync": false
                    }
                },
                "size": {
                    "value": 3,
                    "random": true,
                    "anim": {
                        "enable": false,
                        "speed": 40,
                        "size_min": 0.1,
                        "sync": false
                    }
                },
                "line_linked": {
                    "enable": true,
                    "distance": 150,
                    "color": "#ffffff",
                    "opacity": 0.4,
                    "width": 1
                },
                "move": {
                    "enable": true,
                    "speed": 3,
                    "direction": "none",
                    "random": false,
                    "straight": false,
                    "out_mode": "out",
                    "bounce": false,
                    "attract": {
                        "enable": false,
                        "rotateX": 600,
                        "rotateY": 1200
                    }
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": {
                    "onhover": {
                        "enable": true,
                        "mode": "grab"
                    },
                    "onclick": {
                        "enable": true,
                        "mode": "repulse"
                    },
                    "resize": true
                },
                "modes": {
                    "grab": {
                        "distance": 215,
                        "line_linked": {
                            "opacity": 1
                        }
                    },
                    "bubble": {
                        "distance": 400,
                        "size": 40,
                        "duration": 2,
                        "opacity": 8,
                        "speed": 3
                    },
                    "repulse": {
                        "distance": 200,
                        "duration": 0.4
                    },
                    "push": {
                        "particles_nb": 4
                    },
                    "remove": {
                        "particles_nb": 2
                    }
                }
            },
            "retina_detect": true
        });
    </script>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>