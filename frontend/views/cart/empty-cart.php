<?php
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\widgets\TreeWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

use bl\cms\shop\widgets\LastViewedProducts;

/**
 * @author Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 *
 * @var \yii\web\View $this
 */

$this->title = $this->context->staticPage->translation->title ?? Yii::t('cart', 'Cart');
$this->params['breadcrumbs'][] = $this->title;

\frontend\assets\CartAsset::register($this);
?>

<div class="cart">

    <?= TreeWidget::widget([
        'className' => Category::className(),
        'upIconClass' => 'fa fa-angle-up',
        'downIconClass' => 'fa fa-angle-down'
    ]) ?>

    <article>
        <h1 class="title">
            <?= $this->title ?>
        </h1>
        <p class="h3">
            <?= Yii::t('cart', 'Your cart is empty.') ?>
        </p>
        <?= Html::a(Yii::t('cart', 'Go to catalog'), Url::toRoute('/shop'), [
            'class' => 'button go-to-shop'
        ]) ?>
        <?= LastViewedProducts::widget(['num' => 4]) ?>
    </article>
</div>
