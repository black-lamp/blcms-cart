<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $model \bl\cms\cart\models\Order
 * @var $model->orderStatus \bl\cms\cart\models\OrderStatus
 * @var $address \bl\cms\cart\common\components\user\models\UserAddress
 */

use yii\bootstrap\Html;
?>

<?php if (!empty($model->orderStatus->translation->title)) : ?>
    <?= Html::tag('h1', Yii::t('cart', 'Your order') . ' #' . $model->uid . Yii::t('cart', ' is ') .
        mb_strtolower($model->orderStatus->translation->title)); ?>
<?php endif; ?>

<?php if ($model->orderStatus->id == \bl\cms\cart\models\OrderStatus::STATUS_SENT) : ?>
<p>
    <?php if (!empty($model->delivery_post_office)) : ?>
        <?= Yii::t('cart', 'You can pick up it ') . Yii::t('cart', 'at the post office ') . '"' . $model->delivery_post_office .
        '" (' . $model->deliveryMethod->translation->title . ')';?>
    <?php elseif (!empty($model->address_id)) : ?>
        <p><?= Html::tag('strong', Yii::t('cart', 'Country')) . ': ' . $address->country; ?></p>
        <p><?= Html::tag('strong', Yii::t('cart', 'Region')) . ': ' . $address->region; ?></p>
        <p><?= Html::tag('strong', Yii::t('cart', 'City')) . ': ' . $address->city; ?></p>
        <p><?= Html::tag('strong', Yii::t('cart', 'House')) . ': ' . $address->house; ?></p>
        <p><?= Html::tag('strong', Yii::t('cart', 'Apartment')) . ': ' . $address->apartment; ?></p>
        <p><?= Html::tag('strong', Yii::t('cart', 'Zip')) . ': ' . $address->zipcode; ?></p>
    <?php endif; ?>

</p>
<?php else: ?>
    <?php if (!empty($model->orderStatus->translation->description)) : ?>
        <?= $model->orderStatus->translation->description; ?>
    <?php endif; ?>
<?php endif; ?>