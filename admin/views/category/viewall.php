<?php

use yii\helpers\Html;
use common\models\Category;

$this->title = "All Categories";
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$categories = Category::find()->with('categories')->where(['parent_id' => null])->all();

function categoryTree($categories)
{
    echo '<div class="category-tree"><ul>';
    foreach ($categories as $category) {
        echo '<li class="child_category" id="cat' . $category->id . '">';
        echo Html::encode($category->name);
        if ($category->categories) {
            categoryTree($category->categories);
            echo ' <ul class="subcategory-list"></ul>';
        }
        echo '</li>';
    }
    echo '</ul></div>';
}
?>

<h3>Category Tree</h3>
<div>
    <?php categoryTree($categories); ?>
</div>

<script type="text/javascript">
    <?php ob_start() ?>
    $('.child_category').click(function() {
        $(this).toggleClass('expanded');
        const subcategories = $(this).children('.subcategory-list');
        if (subcategories.length) {
            subcategories.slideToggle();

        }
    });
    $(".category-tree ul li.child_category").click(function() {
        var id = $(this).find('.child_category').attr('id');
        if ($(this).find('.child_category').hasClass('active')) {
            $(this).find('.child_category').removeClass('active');
            return false;
        } else {
            $(this).find(' .child_category').addClass('active');
            return false;
        }

    });
    <?php $js = ob_get_clean() ?> <?php $this->registerJs($js) ?>
</script>
<style>
    <?php ob_start(); ?>.category-tree {
        padding-top: 10px
    }

    ul.category-tree ul {
        display: none;
    }

    .category-tree ul {
        display: block;
        cursor: pointer;
        padding-left: 0
    }

    .category-tree ul.active {
        display: none
    }

    .child_category {
        display: block
    }

    .child_category.active {
        display: none
    }

    .child_category {
        background: #fff;
        width: 100%;
        border-radius: 3px;
        margin-bottom: 10px;
    }

    .child_category .category-tree .child_category {
        background: #FAFAFA;
    }

    .category-tree ul {
        list-style: none;
        margin-left: 0;
        padding-left: 5px;
    }



    .category-tree .child_category {
        cursor: pointer;
        padding-left: 1rem;
        margin-bottom: 0.5rem;
        padding: 10px;
    }

    .subcategory-list {
        display: none;
    }

    .child_category:before {
        content: '- ';
    }

    .child_category.expanded:before {
        content: '+ ';
    }





    <?php $css = ob_get_clean(); ?><?php $this->registerCss($css); ?>
</style>
<?php
