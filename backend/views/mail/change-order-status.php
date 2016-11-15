<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $model \bl\cms\cart\models\Order
 * @var $model->orderStatus \bl\cms\cart\models\OrderStatus
 */

use yii\bootstrap\Html;
?>

<?= Html::tag('h1', Yii::t('cart', 'Your order status is changed to') . ' "' . $model->orderStatus->translation->title . '"'); ?>

<?= $model->orderStatus->translation->description; ?>
