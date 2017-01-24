<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
use bl\cms\shop\common\entities\Category;
use bl\cms\shop\widgets\TreeWidget;

$this->title = Yii::t('cart', 'Your order is accepted.');
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
        <h1><?= $this->title; ?></h1>
        <p><?= Yii::t('cart', 'Our manager will contact you as soon as possible'); ?></p>
    </article>
</div>