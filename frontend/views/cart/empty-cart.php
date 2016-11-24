<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
use bl\cms\shop\frontend\assets\CartAsset;
use yii\bootstrap\Html;
use yii\helpers\Url;

CartAsset::register($this);
?>

<h1 class="title">
    <?= $this->context->staticPage->translation->title ?? Yii::t('cart', 'Cart') ?>
</h1>
<!--DESCRIPTION-->
<div>
    <?= $this->context->staticPage->translation->text ?? '' ?>
</div>

<p class="text-center"><?= \Yii::t('cart', 'Your cart is empty.'); ?></p>

<div class="empty-cart">
    <?= Html::a(\Yii::t('cart', 'Go to shop'), Url::toRoute('/shop'), ['class' => 'btn btn-primary text-center']); ?>
    <div>
        <?= Html::img('/images/empty-cart-image.png'); ?>
    </div>
</div>
