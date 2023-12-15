<?php


/* @var $this \yii\web\View */

use rmrevin\yii\fontawesome\FA;

?>
<?php

yii\bootstrap\Modal::begin([
    'headerOptions' => ['id' => 'modalHeader'],
    'id' => 'ajaxModal',
    'size' => 'modal-lg',

    'header' => '<h4 id="modalHeaderTitle"></h4>',
    //keeps from closing modal with esc key or by clicking out of the modal.
    // user must click cancel or X to close
    'clientOptions' => ['keyboard' => true],
    'options' => ['tabindex' => null,],
]);
echo "<div id='modalContent'>" . FA::icon(FA::_REFRESH)->spin() . "</div>";
yii\bootstrap\Modal::end();
?>

<script>
    <?php ob_start(); ?>
    $(function () {
        //get the click of modal button to create / update item
        //we get the button by class not by ID because you can only have one id on a page and you can
        //have multiple classes therefore you can have multiple open modal buttons on a page all with or without
        //the same link.
        //we use on so the dom element can be called again if they are nested, otherwise when we load the content once it kills the dom element and wont let you load anther modal on click without a page refresh
        $(document).on('click', '.showModalButton', function () {
            $('#ajaxModal').find('#modalContent').html('<?= '<div id="modalContent" class="text-center" style="color: white;">' . FA::icon(FA::_REFRESH)->spin()->size(FA::SIZE_5X) . "</div>"?>');
            //check if the modal is open. if it's open just reload content not whole modal
            //also this allows you to nest buttons inside of modals to reload the content it is in
            //the if else are intentionally separated instead of put into a function to get the
            //button since it is using a class not an #id so there are many of them and we need
            //to ensure we get the right button and content.
            if ($('#ajaxModal').data('bs.modal').isShown) {
                $('#ajaxModal').find('#modalContent')
                    .load($(this).attr('value'));
                //dynamiclly set the header for the modal
                document.getElementById('modalHeaderTitle').innerHTML = $(this).attr('title');
            } else {
                //if modal isn't open; open it and load content
                $('#ajaxModal').modal('show')
                    .find('#modalContent')
                    .load($(this).attr('value'));
                //dynamiclly set the header for the modal
                document.getElementById('modalHeaderTitle').innerHTML = $(this).attr('title');
            }
        });
    });
    <?php $js = ob_get_clean();?>
    <?php $this->registerJs($js);?>
</script>
<style>
    <?php ob_start(); ?>
    #ajaxModal .modal-content {
        background-color: transparent;
        box-shadow: none;
    }

    #modalHeader {
        display: none;
    }

    <?php $css = ob_get_clean();?>
    <?php $this->registerCss($css);?>
</style>
