<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $products \bl\cms\shop\common\entities\Product | \bl\cms\cart\models\OrderProduct
 */

use yii\bootstrap\Html;

?>

<?= Html::tag('h3', Yii::t('cart', 'Products list')); ?>

<table>
    <tr>
        <th class="slavik"><?= Yii::t('cart', 'Product title'); ?></th>
        <th><?= Yii::t('cart', 'Count'); ?></th>
        <th><?= Yii::t('cart', 'Price'); ?></th>
    </tr>
    <?php if (Yii::$app->cart->saveToDataBase === false) : ?>
        <?php foreach ($products as $product) : ?>
            <tr>
                <?= $product->translation->title; ?>
            </tr>
            <tr>
                <?= $product->count; ?>
            </tr>
            <tr>
                <?= $product->price; ?>
            </tr>
        <?php endforeach; ?>

    <?php else : ?>
        <?php foreach ($products as $orderProduct) : ?>
            <tr>
                <?= $orderProduct->product->translation->title; ?>
            </tr>
            <tr>
                <?= $orderProduct->count; ?>
            </tr>
            <tr>
                <?= $orderProduct->price; ?>
            </tr>
        <?php endforeach; ?>

    <?php endif; ?>

</table>
