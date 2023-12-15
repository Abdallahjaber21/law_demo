<?php


namespace common\widgets\inputs;


use common\widgets\inputs\assets\PinCodeAsset;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class PinCodeInput extends InputWidget
{
    public static $autoIdPrefix = 'pincode';

    public $inputs = 4;
    public $hideDigits = true;

    public function init()
    {

        parent::init();
        PinCodeAsset::register($this->view);
    }

    public function run()
    {
        $inputId = $this->getId();
        if($this->hasModel()) {
            $inputId = Html::getInputId($this->model, $this->attribute);
        }
        $this->options['id'] = $inputId;
        $hide = $this->hideDigits? "true": "false";
        $js = <<<JS
        $('#{$inputId}').pincodeInput({

          // 4 input boxes = code of 4 digits long
          inputs: {$this->inputs},        
        
          // hide digits like password input             
          hideDigits: {$hide},   
        
          // keyDown callback             
          keydown : function(e){},
        
          // callback on every input on change (keyup event)
          change: function(input,value,inputnumber){    
            //input = the input textbox DOM element
            //value = the value entered by user (or removed)
            //inputnumber = the position of the input box (in touch mode we only have 1 big input box, so always 1 is returned here)
          },
        
          // callback when all inputs are filled in (keyup event)
          complete : function(value, e, errorElement){
            // value = the entered code
            // e = last keyup event
            // errorElement = error span next to to this, fill with html 
            // e.g. : $(errorElement).html("Code not correct");
          }
          
        });
JS;
        $this->view->registerJs($js);
        if ($this->hasModel()) {
            return Html::activeInput("text", $this->model, $this->attribute, $this->options);
        } else {
            return Html::input("text", $this->name, $this->value, $this->options);
        }
    }

}