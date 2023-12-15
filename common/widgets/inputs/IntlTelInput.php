<?php

namespace common\widgets\inputs;

use common\widgets\inputs\assets\IntlTelInputAsset;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

/**
 * Description of IntlTelInput
 *
 * @author Tarek K. Ajaj
 */
class IntlTelInput extends InputWidget {

    public $initialCountry = "LB";
    public $preferredCountries = ["LB"];
    public $excludeCountries = ["IL"];
    public $onlyCountries = null;
    public $allowDropdown = true;

    public function init() {
        parent::init();
        IntlTelInputAsset::register($this->view);
    }

    public function run() {
        $util = Yii::getAlias("@staticWeb/plugins/intl-tel-input/js/utils.js");
        $inputName = Html::getInputName($this->model, $this->attribute);
        $preferredCountries = Json::encode($this->preferredCountries);
        $excludeCountries = Json::encode($this->excludeCountries);
        $onlyCountries = !empty($this->onlyCountries) ? Json::encode($this->onlyCountries) : 'undefined';
        $allowDropdown = $this->allowDropdown ? 'true' : 'false';
        $js = <<<JS
         var input = $("#{$this->options['id']}");   
         
         input.intlTelInput({
            hiddenInput: '{$inputName}',
            allowDropdown: {$allowDropdown},
            initialCountry: '{$this->initialCountry}',
            preferredCountries: {$preferredCountries},
            excludeCountries: {$excludeCountries},
            onlyCountries: {$onlyCountries},
            separateDialCode: true,
            nationalMode: false,
            autoHideDialCode: true,
            utilsScript: "{$util}" // just for formatting/placeholders etc
          });
JS;
        $this->view->registerJs($js);
        $this->options['class'] = 'form-control';
        if ($this->hasModel()) {
            return Html::input("tel", "phone-input-{$this->id}", $this->model->{$this->attribute}, $this->options);
            return Html::activeInput("tel", $this->model, $this->attribute, $this->options);
        } else {
            return Html::input("tel", "phone-input-{$this->id}", $this->value, $this->options);
            return Html::input("tel", $inputName, $this->value, $this->options);
        }
    }

}
