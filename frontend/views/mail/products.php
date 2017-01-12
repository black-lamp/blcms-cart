<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $products \bl\cms\shop\common\entities\Product | \bl\cms\cart\models\OrderProduct
 */

use yii\bootstrap\Html;
use yii\helpers\Url;

?>

<h3 style="text-align: center;">
    <?= Yii::t('cart', 'Products list'); ?>
</h3>

<table style="width: 500px; margin: 0 auto;">
    <thead>
    <tr>
        <th>
            <?= Yii::t('cart', 'Title') ?>
        </th>
        <th>
            <?= Yii::t('cart', 'Price') ?>
        </th>
        <th>
            <?= Yii::t('cart', 'Count') ?>
        </th>
    </tr>
    </thead>
    <tbody>

    <?php if (Yii::$app->cart->saveToDataBase === false) : ?>
        <?php foreach ($products as $product): ?>
            <?php $combination = (\Yii::$app->getModule('shop')->enableCombinations && !empty($product->combinationId)) ?
                $product->getCombination($product->combinationId) : NULL; ?>
            <tr>
                <!--TITLE, COMBINATION ATTRIBUTES AND ADDITIONAL PRODUCTS-->
                <td class="product-title">
                    <!--PRODUCT TITLE-->
                    <?php if (!empty($product->translation)): ?>
                        <?php $url = Url::toRoute(['/shop/product/show', 'id' => $product->id]);
                        echo Html::a($product->translation->title, $url);
                        ?>
                    <?php endif; ?>
                    <!--COMBINATION-->
                    <?php if (!empty($combination)) : ?>
                        <?php foreach ($combination->combinationAttributes as $attribute) : ?>
                            <p>
                                <?= $attribute->productAttribute->translation->title; ?>:
                                <?= $attribute->productAttributeValue->translation->value; ?>
                            </p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <!--ADDITIONAL PRODUCTS-->
                    <?php if (!empty($product->additionalProducts)): ?>
                        <p>
                            <b><?= \Yii::t('shop', 'Additional products'); ?></b>
                        </p>
                        <ul>
                            <?php foreach ($product->additionalProducts as $additionalProduct): ?>
                                <li>
                                    <?= $additionalProduct->translation->title; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </td>

                <!--PRICE-->
                <td>
                    <?php $price = (!empty($combination)) ? $combination->price->discountPrice : $product->getDiscountPrice(); ?>
                    <?= Yii::$app->formatter->asCurrency($price ?? 0) ?>
                </td>
                <!--NUMBER-->
                <td style="text-align: center">
                    <?= $product->count; ?>
                </td>
            </tr>
        <?php endforeach; ?>

    <?php else : ?>
        <?php foreach ($products as $orderProduct): ?>
            <?php $combination = (\Yii::$app->getModule('shop')->enableCombinations && !empty($orderProduct->combination_id)) ?
                $orderProduct->combination : NULL; ?>
            <tr>
                <!--TITLE, COMBINATION ATTRIBUTES AND ADDITIONAL PRODUCTS-->
                <td>
                    <!--PRODUCT TITLE-->
                    <?php if (!empty($orderProduct->product->translation)): ?>
                        <?php $url = Url::toRoute(['/shop/product/show', 'id' => $orderProduct->product_id]);
                        echo Html::a($orderProduct->product->translation->title, $url);
                        ?>
                    <?php endif; ?>
                    <!--COMBINATION-->
                    <?php if (!empty($combination)) : ?>
                        <?php foreach ($combination->combinationAttributes as $attribute) : ?>
                            <p>
                                <?= $attribute->productAttribute->translation->title; ?>:
                                <?= $attribute->productAttributeValue->translation->value; ?>
                            </p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <!--ADDITIONAL PRODUCTS-->
                    <?php if (!empty($orderProduct->additionalProduct)): ?>
                        <p>
                            <b><?= \Yii::t('shop', 'Additional products'); ?></b>
                        </p>
                        <ul>
                            <?php foreach ($orderProduct->additionalProduct as $additionalProduct): ?>
                                <li>
                                    <?= $additionalProduct->translation->title; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </td>

                <!--PRICE-->
                <td>
                    <?php $price = (!empty($combination)) ? $combination->price->discountPrice : $orderProduct->product->getDiscountPrice(); ?>
                    <?= Yii::$app->formatter->asCurrency($price ?? 0) ?>
                </td>
                <!--NUMBER-->
                <td style="text-align: center">
                    <?= $orderProduct->count; ?>
                </td>
            </tr>
        <?php endforeach; ?>

    <?php endif; ?>
    </tbody>
</table>