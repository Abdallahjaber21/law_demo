<?php
use common\assets\DropZoneAsset;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "Upload Pdf";

DropZoneAsset::register($this);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

<div id="pdf-form">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <?php echo Html::beginForm(Url::to(['site/upload-pdf']), 'POST', ['class' => 'dropzone', 'id' => 'my-great-dropzone', 'enctype' => 'multipart/form-data']); ?>
            <?= Html::a("Continue", Url::to(['site/pdf-gpt']), ['class' => 'btn btn-success d-none', 'id' => 'continue_btn']) ?>

            <div class="dz-preview dz-file-preview">
                <div class="dz-details">
                    <div class="dz-filename"><span data-dz-name></span></div>
                    <div class="dz-size" data-dz-size></div>
                    <img data-dz-thumbnail />
                </div>
                <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                <div class="dz-success-mark"><span>✔</span></div>
                <div class="dz-error-mark"><span>✘</span></div>
                <div class="dz-error-message"><span data-dz-errormessage></span></div>
            </div>
            <?php echo Html::endForm(); ?>

        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    <?php ob_start(); ?>
    Dropzone.autoDiscover = false;

    if (typeof Dropzone.instances[0] !== 'undefined') {
        Dropzone.instances[0].destroy(); // Destroy the existing instance
    }

    var myDropzone = new Dropzone("#my-great-dropzone", {
        // url: "/admin/upload",
        params: {
            someparams: "test"
        },
        paramName: "file", // The name that will be used to transfer the file
        maxFilesize: 2, // MB
        addRemoveLinks: true,
        maxFiles: 1,
        init: function () {
            // Event for when a file is added
            this.on("addedfile", function (file) {
                console.log('File added:', file);
            });

            // Event for when the upload is complete
            this.on("success", function (file, response) {
                response = JSON?.parse(response);
                console.log('Upload successful. Server response:', response);


                if (response?.success) {
                    let path_url = $("#continue_btn").attr("href").replace("#", "") + "?pdf_path=" + response?.filepath;
                    console.warn('<<< path_url >>>', path_url);
                    $("#continue_btn").removeClass("d-none");
                    $("#continue_btn").attr("href", path_url);
                }
            });

            // Event for when a file is removed
            this.on("removedfile", function (file) {
                console.log('File removed:', file);

                if (this.files[0] == undefined) {
                    $("#continue_btn").addClass("d-none");
                    $("#continue_btn").attr("href", "<?= Url::to(['site/pdf-gpt']) ?>");
                }
            });

            // Additional initialization logic
            this.on("maxfilesexceeded", function () {
                if (this.files[1] != null) {
                    this.removeFile(this.files[0]);
                }
            });
        },
        accept: function (file, done) {
            console.warn('<<< File >>>', file);
            if (file?.type != "application/pdf") {
                toastr.error('Only Pdf Files Are Accepted!', 'Error', {
                    timeOut: 5000,
                    "closeButton": true,
                    "debug": false,
                    "progressBar": true,
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                });

                this.removeFile(this.files[0]);
            } else {
                done();
            }
        }
    });

    <?php $js = ob_get_clean(); ?>
    <?php $this->registerJs($js); ?>
</script>