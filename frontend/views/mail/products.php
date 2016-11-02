<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $products \bl\cms\shop\common\entities\Product | \bl\cms\cart\models\OrderProduct
 */

use yii\bootstrap\Html;

?>

<h3 style="text-align: center;">
    <?= Yii::t('cart', 'Products list'); ?>
</h3>

<table style="width: 500px; margin: 0 auto;">
    <tr>
        <th class="slavik"><?= Yii::t('cart', 'Product title'); ?></th>
        <th><?= Yii::t('cart', 'Count'); ?></th>
        <th><?= Yii::t('cart', 'Price'); ?></th>
    </tr>
    <?php if (Yii::$app->cart->saveToDataBase === false) : ?>
        <?php foreach ($products as $product) : ?>
            <tr>
                <td>
                    <?= $product->translation->title; ?>
                </td>
                <td>
                    <?= $product->count; ?>
                </td>
                <td>
                    <?= $product->price; ?>
                </td>
            </tr>
        <?php endforeach; ?>

    <?php else : ?>
        <?php foreach ($products as $orderProduct) : ?>
            <tr>
                <td>
                    <?= $orderProduct->product->translation->title; ?>
                </td>
                <td>
                    <?= $orderProduct->count; ?>
                </td>
                <td>
                    <?= $orderProduct->price; ?>
                </td>
            </tr>
        <?php endforeach; ?>

    <?php endif; ?>

</table>
