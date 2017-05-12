<?php
/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 *
 * @var $discount
 * @var $cost
 * @var $totalCost
 */
?>
<?php if ($discount > 0): ?>
    <div class="order-sum">
        <?= Yii::t('order', 'Sum') ?> -
        <strike class="sum-old" id="orderCost">
            <?= Yii::$app->formatter->asCurrency($cost) ?>
        </strike>
    </div>

    <div class="order-discount">
        <?= Yii::t('order', 'Discount') ?> -
        <span class="discount" id="orderCost">
            <?= $discount ?>%
        </span>
    </div>
<?php endif; ?>

<div class="to-pay">
    <?= Yii::t('order', 'To pay - ') ?>
    <span class="sum" id="orderTotalCost">
        <?= Yii::$app->formatter->asCurrency($totalCost) ?>
    </span>
</div>