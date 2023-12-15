<?php
\Yii::$container->set(kartik\select2\Select2::class, [
    'theme' => kartik\select2\Select2::THEME_DEFAULT,
]);
\Yii::$container->set(common\components\extensions\Select2::class, [
    'theme' => kartik\select2\Select2::THEME_DEFAULT,
]);