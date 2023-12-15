<?php

use common\components\extensions\ActionColumn;
use common\components\extensions\Select2;
use common\models\Admin;
use common\models\Assignee;
use common\models\Category;
use common\models\Division;
use common\models\Location;
use common\models\LocationEquipments;
use common\models\Log;
use common\models\RepairRequest;
use common\models\RepairRequestFiles;
use common\models\search\AssigneeSearch;
use common\models\Technician;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use common\widgets\dashboard\PanelBox;
use common\widgets\ImagesGallery;
use kartik\form\ActiveForm;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\RepairRequest */

$this->title = $model->id . " | " . $model->service_type_label;

$this->params['breadcrumbs'][] = ['label' => 'Work Orders', 'url' => ['site/works-dashboard']];
$this->params['breadcrumbs'][] = $this->title;

$chats = $model->repairRequestChats;
?>
<div class="repair-request-view">
    <div class="row">
        <div class="col-md-12">
            <?php $panel = PanelBox::begin([
                'title' => 'Work Order Files',
                'icon' => 'user',
                'color' => PanelBox::COLOR_BLUE,
                'canMinimize' => true,
            ]); ?>
            <?php $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
                'id' => 'attach-files-form',
                'action' => ['attach-files', 'id' => $model->id],
            ]);
            echo '<div style="display:none">' . $form->field($model, 'fileUpload[]')->fileInput(['multiple' => true, 'style' => 'display: none;visibility=>hidden']) . '</div>';
            echo Html::button('Attach Files', ['class' => 'btn btn-info btn-flat col-md-1', 'id' => 'attach-button']);
            ActiveForm::end();
            ?>
            <?php $repairRequestFiles = RepairRequestFiles::find()->where(['repair_request_id' => $model->id])->all();
            $fileCount = RepairRequestFiles::find()->where(['repair_request_id' => $model->id])->count();
            foreach ($repairRequestFiles as $index => $repairRequestFile) {
                $old_file = $repairRequestFile->old_file;
                if (in_array($repairRequestFile->type, ['jpeg', 'png', 'gif', 'jpg', 'svg'])) {
                    $fileIcon = FA::i(FA::_IMAGE);
                } else if ($repairRequestFile->type == 'pdf') {
                    $fileIcon = FA::i(FA::_FILE_PDF_O);
                } else {
                    $fileIcon = FA::i(FA::_FILE);
                } ?>

                <div class="col-md-3 file-container" data-file-id="<?= $repairRequestFile->id ?>">
                    <?php
                    $fileUrl = Url::to('@staticWeb/upload/repairRequestFiles/' . $repairRequestFile->new_file);

                    echo Html::a(
                        '<p>' . $fileIcon . ' ' . Html::encode($repairRequestFile->old_file) . '</p>',
                        ['repair-request/download', 'id' => $repairRequestFile->id],
                        [
                            'style' => 'text-decoration: none; color: #333; font-size: 16px;',
                        ]
                    );
                    ?>
                </div>



            <?php }
            ?>
            <?php PanelBox::end() ?>

        </div>
        <div class="col-md-6 dropdown_btn_col">
            <?php
            $panel = PanelBox::begin([
                'title' => $this->title,
                'icon' => 'eye',
                'color' => PanelBox::COLOR_GRAY,
                // 'panelClass' => ($model->status < RepairRequest::STATUS_CHECKED_IN && $model->urgent_status) ? 'box-urgent flex-box' : 'flex-box'
            ]);

            $labor_form = Html::beginForm(
                Url::to(['repair-request/set-labor-charge', 'id' => $model->id]),
                'post',
                [
                    'class' => 'flex item-center',
                    'id' => 'labor_form'
                ]
            );

            echo $labor_form;


            $panel->beginHeaderItem();

            if ($model->urgent_status == true) {
                echo "<span class='label tag-label label-danger' style='margin-right:1rem;position:relative;top:0.5rem;'>Urgent</span>";
            }
            // echo $model->getStatusTag();
            
            echo Select2::widget([
                'attribute' => 'RepairRequest[status]',
                'name' => 'RepairRequest[status]',
                'model' => 'RepairRequest',
                'data' => $model->getNextStatus(true),
                'value' => $model->status,
                'options' => ['multiple' => false, 'placeholder' => 'Select a status', 'id' => 'change_status_dropdown', 'class' => $model->getStatusDropDownColor()],
            ]);
            $panel->endHeaderItem();
            ?>


            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',

                    // 'technician_id',
                    [
                        'attribute' => 'equipment_id',
                        'value' => function ($model) {
                            if (!empty($model->equipment_id)) {
                                $equipment = $model->equipment;
                                // return $equipment->code . ' | ' . $equipment->equipment->name . ' | ' . $equipment->equipment->category->name;
                        
                                return Html::tag('test', $equipment->code . ' | ' . $equipment->equipment->name . ' | ' . $equipment->equipment->category->name, [
                                    // html-tags won't be rendered in title
                                    'title' => $model->id,
                                    'data-placement' => 'right',
                                    'data-toggle' => 'tooltip',
                                    'style' => 'white-space:pre;'
                                ]);
                            }
                        },
                        'format' => 'raw',
                        'contentOptions' => ['style' => 'font-size:25px;']
                    ],
                    [
                        'attribute' => 'division_id',
                        'value' => function ($model) {

                            if (!empty($model->division_id)) {
                                return $model->division->name;
                            }
                        },
                        'contentOptions' => ['style' => 'font-size:25px;']

                    ],
                    [
                        'attribute' => 'location_id',
                        'value' => function ($model) {

                            if (!empty($model->location_id))
                                return $model->location->name;
                        },
                        'contentOptions' => ['style' => 'font-size:20px;']
                    ],
                    // 'urgent_status',
            
                    [
                        'attribute' => 'repair_request_path',
                        'value' => function ($model) {
                            return $model->repair_request_path;
                        },
                    ],
                    // 'project_id',
                    [
                        'attribute' => 'owner_id',
                        'value' => function ($model) {

                            if (!empty($model->owner_id))
                                return $model->owner->name;
                        },
                        'contentOptions' => ['style' => 'font-size:20px;']

                    ],
                    // [
                    //     'attribute' => 'assignees',
                    //     'value' => function ($model) {
                    //         return $model->getAssigneesDetails();
                    //     },
                    //     'format' => 'raw',
                    // ],
                    [
                        'attribute' => 'team_leader_id',
                        'value' => function ($model) {

                            if (!empty($model->team_leader_id))
                                return $model->teamLeader->name;
                        },
                    ],
                    'service_note',
                    'created_at:datetime',
                    [
                        'attribute' => 'created_by',
                        'value' => function ($model) {
                            return $model->getBlamable($model->created_by);
                        }
                    ],
                    'completed_at:datetime',
                    [
                        'attribute' => 'completed_by',
                        'value' => function ($model) {
                            return $model->getBlamable($model->completed_by);
                        }
                    ],

                    [
                        'attribute' => 'labor_charge',
                        'value' => function ($model) {
                            $formattedLaborCharge = number_format($model->labor_charge, 2);
                            return '<div class="flex items-center col-md-6" style="padding:0;">' . Html::input('text', "RepairRequest[labor_charge]", $formattedLaborCharge, [
                                'class' => 'form-control', 'placeholder' => 'Labor Charge Hour', 'oninput' => 'this.value = this.value.replace(/[^0-9.]/g, "").replace(/(\..*)\./g, "$1").replace(/^(\d*\.\d{2})\d*/g, "$1");'
                            ]) . ' ' . Html::submitButton('Save', ['class' => 'btn btn-success btn-flat']) . '</div>';
                        },
                        'format' => 'raw',
                        'visible' => ($model->status == RepairRequest::STATUS_COMPLETED || $model->status == RepairRequest::STATUS_NOT_COMPLETED || $model->status == RepairRequest::STATUS_REQUEST_COMPLETION || $model->status == RepairRequest::STATUS_REQUEST_DIFFERENT_TECHNICIAN)
                    ],
                ],
            ]) ?>

            <?php
            PanelBox::end();
            echo Html::endForm();
            ?>
        </div>
        <div class="col-md-6">
            <?php
            $panel = PanelBox::begin([
                'title' => Html::encode("More Attributes"),
                'icon' => 'plus',
                'color' => PanelBox::COLOR_ORANGE,
                // 'panelClass' => ($model->status < RepairRequest::STATUS_CHECKED_IN && $model->urgent_status)  ? 'box-urgent' : ''
            ]);
            ?>

            <?php if ($model->service_type == RepairRequest::TYPE_PPM || $model->division_id == Division::DIVISION_PLANT): ?>
                <?php $panel->addButton(Yii::t('app', 'Tasks'), ['tasks', 'id' => $model->id], ['class' => 'btn-warning btn-flat']) ?>
            <?php endif; ?>



            <?php if ($model->status != RepairRequest::STATUS_COMPLETED && $model->status != RepairRequest::STATUS_CANCELLED): ?>
                <?php $panel->addButton(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn-primary btn-flat']) ?>
            <?php endif; ?>

            <?php if (count($chats) > 0): ?>
                <?php $panel->addButton(Yii::t('app', 'Chats'), ['chats', 'id' => $model->id], ['class' => 'btn-success btn-flat']) ?>
            <?php endif; ?>

            <?php
            if ($model->status < RepairRequest::STATUS_CHECKED_IN) {
                $panel->addButton(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-flat',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]);
            }

            ?>

            <?= DetailView::widget([
                'model' => $model,
                'options' => ['class' => 'table table-striped table-bordered detail-view'],
                'attributes' => [
                    // 'requested_at:datetime',
                    // 'assigned_at:datetime',
                    'scheduled_at:datetime',
                    'reported_by_name',
                    'reported_by_phone',
                    // 'informed_at:datetime',
                    'arrived_at:datetime',
                    'departed_at:datetime',
                    'updated_at:datetime',
                    [
                        'attribute' => 'updated_by',
                        'value' => function ($model) {
                            return $model->getBlamable($model->updated_by);
                        }
                    ],
                    [
                        'attribute' => 'images',
                        'value' => function ($model) {
                            if (!empty($model->gallery_id)) {
                                return ImagesGallery::widget(['gallery' => $model->gallery]);
                            }
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'technician_signature',
                        'value' => function ($model) {

                            if ($model->technician_signature)
                                return Html::img($model->technician_signature_url, ['width' => '50%']);
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'customer_signature',
                        'value' => function ($model) {
                            if ($model->customer_signature)
                                return Html::img($model->customer_signature_url, ['width' => '50%']);
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'supervisor_signature',
                        'value' => function ($model) {
                            if ($model->supervisor_signature)
                                return Html::img($model->supervisor_signature_url, ['width' => '50%']);
                        },
                        'format' => 'raw'
                    ],
                    [
                        'attribute' => 'coordinator_signature',
                        'value' => function ($model) {
                            if ($model->coordinator_signature)
                                return Html::img($model->coordinator_signature_url, ['width' => '50%']);
                        },
                        'format' => 'raw',
                        'visible' => $model->division_id == Division::DIVISION_PLANT
                    ],
                    [
                        'attribute' => 'admin_signature',
                        'value' => function ($model) {
                            if ($model->admin_signature)
                                return Html::img($model->admin_signature, ['width' => '50%']);
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'note',
                        'value' => function ($model) {
                            return $model->note;
                        },
                        'contentOptions' => ['style' => 'font-size:20px;']
                    ],
                    [
                        'attribute' => 'supervisor_note',
                        'value' => function ($model) {
                            return $model->supervisor_note;
                        },
                        'contentOptions' => ['style' => 'font-size:20px;']
                    ],
                    [
                        'attribute' => 'coordinator_note',
                        'value' => function ($model) {
                            return $model->coordinator_note;
                        },
                        'contentOptions' => ['style' => 'font-size:20px;']
                    ],
                ],
            ]) ?>

            <?php PanelBox::end() ?>
        </div>

        <div class="col-md-12">
            <?php

            $team_form = Html::beginForm(
                Url::to(['repair-request/change-team', 'id' => $model->id]),
                'post',
                [
                    'id' => 'team_form'
                ]
            );

            echo $team_form;

            $panel = PanelBox::begin([
                'title' => 'Assignees',
                'icon' => 'user',
                'color' => PanelBox::COLOR_GREEN,
                // 'panelClass' => ($model->status < RepairRequest::STATUS_CHECKED_IN && $model->urgent_status) ? 'box-urgent flex-box' : 'flex-box'
            ]);

            $panel->beginHeaderItem();

            if ($model->status != RepairRequest::STATUS_COMPLETED && $model->status != RepairRequest::STATUS_CANCELLED)
                echo Html::submitButton('Save', ['class' => 'btn btn-sm btn-success', 'data-method' => 'post']);
            $panel->endHeaderItem();

            $new_data_provider = new ArrayDataProvider([
                'allModels' => $model->getRepairTechnicians(),
                'sort' => [
                    'attributes' => ['id', 'username', 'email'],
                ],
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);

            $new_search_model = new AssigneeSearch();

            ?>

            <?= GridView::widget([
                'dataProvider' => $new_data_provider,
                'filterModel' => null,
                //$new_search_model,
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'headerOptions' => ['class' => 'checkbox-header'],
                        'checkboxOptions' => function ($technician_model, $key, $index, $column) use ($model) {
                            $assignee = @Assignee::find()->where(['repair_request_id' => $model->id, 'user_id' => $technician_model->id])->one();
                            return ['id' => 'check-' . $technician_model->id, 'name' => '[' . $technician_model->id . ']', 'class' => 'checkbox-header', 'value' => $technician_model->id, 'checked' => !empty($assignee) ? true : false];
                        },
                        'visible' => $model->status != RepairRequest::STATUS_COMPLETED && $model->status != RepairRequest::STATUS_CANCELLED
                    ],
                    'id',
                    [
                        'attribute' => 'type',
                        'value' => function ($model) {
                            return @$model->account->type0->label;
                        },
                        'contentOptions' => [
                            'style' => 'font-weight:bold;'
                        ]
                    ],
                    'name',
                    [
                        'attribute' => 'number',
                        'label' => 'Phone Number',
                        'value' => function ($model) {
                            return @$model->phone_number;
                        }
                    ],
                    [
                        'attribute' => 'profession',
                        'value' => function ($model) {
                            return @$model->profession->name;
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function ($technician_model) use ($model) {
                            $statuses = ArrayHelper::merge((new Assignee())->status_list, (new Assignee())->acceptance_status_list);
                            $assignee = @Assignee::find()->where(['repair_request_id' => $model->id, 'user_id' => $technician_model->id])->one();

                            //current status => @$statuses[$assignee->status]
                            if ($model->status == RepairRequest::STATUS_COMPLETED || $model->status == RepairRequest::STATUS_CANCELLED) {
                                return !empty($assignee->status) ? $statuses[$assignee->status] : $statuses[Assignee::STATUS_FREE];
                            } else {
                                return Select2::widget([
                                    'attribute' => "Assignee_status[{$technician_model->id}]",
                                    'name' => "Assignee_status[{$technician_model->id}]",
                                    'model' => 'Assignee',
                                    'data' => $statuses,
                                    'value' => !empty($assignee->status) ? $assignee->status : Assignee::STATUS_FREE,
                                    'options' => ['multiple' => false, 'placeholder' => 'Select a status', 'id' => $technician_model->id]
                                ]);
                            }
                        },
                        'format' => 'raw'
                    ],
                    // 'technician_id',
            
                ],
            ]); ?>

            <?php PanelBox::end() ?>

            <?php echo Html::endForm(); ?>
        </div>
        <div class="col-md-6">
            <?php

            $repair_requests_log = Log::find()->where(['repair_request_id' => $model->id, 'type' => Log::TYPE_REPAIR_REQUEST])->orderBy(['created_at' => SORT_DESC])->all();

            if (count($repair_requests_log) > 0) {
                $panel = PanelBox::begin([
                    'title' => $model->service_type_label . ' Log History',
                    // 'icon' => 'eye',
                    'color' => PanelBox::COLOR_GRAY,
                    // 'panelClass' => ($model->status < RepairRequest::STATUS_CHECKED_IN && $model->urgent_status) ? 'box-urgent flex-box' : 'flex-box'
                ]);

                echo $this->render('_timeline', [
                    'items' => $repair_requests_log
                ]);

                $panel::end();
            }
            ?>
        </div>
        <div class="col-md-6">
            <?php

            $technicians_log = Log::find()->where(['repair_request_id' => $model->id, 'type' => Log::TYPE_TECHNICIAN])->orderBy(['created_at' => SORT_DESC])->all();

            if (count($technicians_log) > 0) {
                $panel = PanelBox::begin([
                    'title' => 'Technician Log History',
                    // 'icon' => 'eye',
                    'color' => PanelBox::COLOR_GRAY,
                    // 'panelClass' => ($model->status < RepairRequest::STATUS_CHECKED_IN && $model->urgent_status) ? 'box-urgent flex-box' : 'flex-box'
                ]);

                $panel->beginHeaderItem();
                echo Html::a('Export', Url::to(['export/technician-logs', 'id' => $model->id]), ['class' => 'btn btn-success btn-sm']);
                $panel->endHeaderItem();

                echo $this->render('_timeline', [
                    'items' => $technicians_log
                ]);

                $panel::end();
            }
            ?>
        </div>
    </div>
</div>
</div>

<script>
    <?php ob_start(); ?>
    let original_select_value = $("#change_status_dropdown").find(":selected").val();
    $("#change_status_dropdown").change(function () {
        let selected_value = $(this).find(':selected').val();

        let statuses = <?= json_encode($model->status_list) ?>;

        if (selected_value) {

            if (selected_value == <?= RepairRequest::STATUS_COMPLETED ?>) {
                decision = confirm(`Are You Sure You Want To Change Work #<?= $model->id ?> Status To: ${statuses[selected_value]}`);

                if (decision) {
                    $.ajax({
                        url: "<?= Url::to(['/repair-request/change-status', 'id' => $model->id]) ?>",
                        type: "POST",
                        dataType: "json",
                        data: {
                            status: selected_value,
                        },
                        success: function (response) {
                            window.location.reload();
                        }
                    });
                } else {
                    window?.location?.reload();
                }
            } else {
                $.ajax({
                    url: "<?= Url::to(['/repair-request/change-status', 'id' => $model->id]) ?>",
                    type: "POST",
                    dataType: "json",
                    data: {
                        status: selected_value,
                    },
                    success: function (response) {
                        window.location.reload();
                    }
                });
            }
        }
    });

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]',
        html: true
    });
    document.getElementById('attach-button').addEventListener('click', function (event) {
        event.preventDefault();
        document.getElementById('<?= Html::getInputId($model, 'fileUpload[]') ?>').click();
    });

    document.getElementById('<?= Html::getInputId($model, 'fileUpload[]') ?>').addEventListener('change', function () {
        document.getElementById('attach-files-form').submit();
    });

    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>