<?php

use common\assets\plugins\RepeaterAsset;
use common\assets\plugins\SortableAsset;
use common\models\MaintenanceTaskGroup;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
SortableAsset::register($this);
RepeaterAsset::register($this);
?>
<!-- outer repeater -->

<?php
$form = ActiveForm::begin();
?>
<div class="repeater">
    <div data-repeater-list="outer-list">
        <div data-repeater-item class="well well-sm">

            <div class="input-group">

                <?= Html::dropDownList("equipment_type", null, (new MaintenanceTaskGroup())->equipment_type_list,[
                    'class' => 'form-control',
                    'placeholder' => 'Equipment Type']) ?>
                <span class="input-group-addon"></span>
                <?= Html::textInput("group_code", null, [
                    'class' => 'form-control',
                    'placeholder' => 'Group Code']) ?>
                <span class="input-group-addon"></span>

                <?= Html::textInput("group_name", null, [
                    'class' => 'form-control',
                    'placeholder' => 'Group Name']) ?>
                <span class="input-group-addon"></span>

                <?= Html::textInput("group_order", null, [
                    'class' => 'form-control',
                    'placeholder' => 'Group Order']) ?>
                <div class="input-group-btn">
                    <?= Html::button("Delete Group", [
                        'class' => 'btn btn-danger',
                        'data-repeater-delete' => '',
                        'type' => "button",
                        'tabIndex' => "-1"]) ?>
                </div>
            </div>
            <br/>

            <!-- innner repeater -->
            <div class="inner-repeater">


                <table data-repeater-list="inner-list"
                       class="sortable repeater-table table table-condensed table-striped">
                    <thead>
                    <tr>
                        <th style="max-width:25px"></th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Schedule</th>

                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                            <th><?= $i ?>A</th>
                            <th><?= $i ?>B</th>
                        <?php } ?>
                        <th>Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr data-repeater-item>
                        <td style="max-width:25px">
                            <button class="btn btn-default btn-xs sort-option" tabIndex="-1">
                                <i class="fa fa-sort"></i>
                            </button>
                        </td>
                        <td>
                            <?= Html::textInput("task_code", null, [
                                'class' => 'form-control',
                                'required' => '',
                                'placeholder' => 'Task Code']) ?>
                        </td>
                        <td>
                            <?= Html::textInput("task_name", null, [
                                'class' => 'form-control',
                                'required' => '',
                                'placeholder' => 'Task Name']) ?>
                        </td>
                        <th>
                            <a href="#" class="toggle-15d">15D</a>
                            <a href="#" class="toggle-1m">1M</a>
                            <a href="#" class="toggle-6m">6M</a>
                            <a href="#" class="toggle-1y">1Y</a>
                        </th>
                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                            <td>
                                <?= Html::checkbox("m_{$i}_a", true) ?>
                            </td>
                            <td>
                                <?= Html::checkbox("m_{$i}_b", false) ?>
                            </td>
                        <?php } ?>
                        <td>
                            <?= Html::button("Delete Task", ['class' => 'btn btn-warning btn-xs', 'data-repeater-delete' => '', 'type' => "button", 'tabIndex' => "-1"]) ?>
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="12">
                            <input data-repeater-create type="button" class="btn btn-info" value="Add Task"/>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
<!--    <input data-repeater-create type="button" class="btn btn-primary" value="Add Group"/>-->
</div>

<div class="form-group text-center">
    <?= Html::submitButton('Save Group', ['class' => 'btn btn-success']) ?>
</div>
<?php ActiveForm::end() ?>

<script>
    <?php ob_start(); ?>

    $(document).ready(function () {
        $('.repeater').repeater({
            // (Required if there is a nested repeater)
            // Specify the configuration of the nested repeaters.
            // Nested configuration follows the same format as the base configuration,
            // supporting options "defaultValues", "show", "hide", etc.
            // Nested repeaters additionally require a "selector" field.
            repeaters: [{
                // (Required)
                // Specify the jQuery selector for this nested repeater
                selector: '.inner-repeater',
                initEmpty: false,
                isFirstItemUndeletable: true,
                ready: function (setIndexes) {
                    jQuery(".repeater-table").on('drop', setIndexes);
                },
            }],

            initEmpty: false,
            isFirstItemUndeletable: true,
        });
    });


    jQuery(".repeater-table").sortable({
        group: 'repeater-list',
        // axis: "y",
        cursor: 'pointer',
        opacity: 0.5,
        delay: 100,
        handle: ".sort-option",

        containerSelector: 'table',
        itemPath: '> tbody',
        itemSelector: 'tr',
        placeholder: '<tr class="placeholder"/>',

        update: function (event, ui) {
            $('.repeater').repeater('setIndexes');
        },
        onDrop: function ($item, container, _super, event) {
            _super($item, container);
            jQuery(".repeater-table").trigger('drop');
        }
    });

    $(document).on("click", '.toggle-15d', function () {
        $(this).parents("tr").find("input[type='checkbox']").prop("checked", false);
        $(this).parents("tr").find("input[type='checkbox']").prop("checked", true);

        window.m6 = 0;
        window.y1 = 0;
    });
    window.ab = false;
    $(document).on("click", '.toggle-1m', function () {
        $(this).parents("tr").find("input[type='checkbox']").prop("checked", false);
        if (window.ab) {
            $(this).parents("tr").find("td:nth-child(2n+5) input[type='checkbox']").prop("checked", true);
        } else {
            $(this).parents("tr").find("td:nth-child(2n+6) input[type='checkbox']").prop("checked", true);
        }
        window.ab = !window.ab;

        window.m6 = 0;
        window.y1 = 0;
    });
    window.m6 = 0;
    $(document).on("click", '.toggle-6m', function () {
        $(this).parents("tr").find("input[type='checkbox']").prop("checked", false);
        if(m6 == 12){
            m6 = 0;
        }
        var fm6 = m6;
        var sm6 = m6 + 12;
        $(this).parents("tr").find("td:nth-child(" + (5+fm6)  + ") input[type='checkbox']").prop("checked", true);
        $(this).parents("tr").find("td:nth-child(" + (5+sm6)  +") input[type='checkbox']").prop("checked", true);
        window.m6++;
        window.y1 = 0;
    });
    window.y1 = 0;
    $(document).on("click", '.toggle-1y', function () {
        $(this).parents("tr").find("input[type='checkbox']").prop("checked", false);
        if(y1 == 24){
            y1 = 0;
        }
        $(this).parents("tr").find("td:nth-child(" + (5+y1)  + ") input[type='checkbox']").prop("checked", true);
        window.y1++;
        window.m6 = 0;
    });
    <?php $js = ob_get_clean();?>
    <?php $this->registerJs($js);?>
</script>

<style>
    <?php ob_start(); ?>
    input[type="checkbox"] {
        height: 20px;
        width: 20px;
    }

    <?php $css = ob_get_clean();?>
    <?php $this->registerCss($css);?>
</style>

<style>
    <?php ob_start(); ?>
    body.dragging, body.dragging * {
        cursor: move !important;
    }

    .dragged {
        position: absolute;
        opacity: 0.5;
        z-index: 2000;
    }

    .sortable .placeholder {
        border: medium none;
        margin: 13px 0;
        padding: 0;
        position: relative;
        border: 1px solid red;
    }

    .sortable .placeholder:before {
        position: absolute;
        content: "";
        width: 0;
        height: 0;
        margin-top: -5px;
        left: -5px;
        top: 0px;
        border: 5px solid transparent;
        border-left-color: red;
        border-right: none;
    }

    <?php $css = ob_get_clean(); ?>
    <?php $this->registerCss($css); ?>
</style>