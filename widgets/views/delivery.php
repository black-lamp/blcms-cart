<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $deliveryMethods \bl\cms\cart\models\DeliveryMethod
 * @var $form \yii\bootstrap\ActiveForm
 */

use yii\bootstrap\Html;
?>

<h3><?=Yii::t('cart', 'Select delivery method'); ?>:</h3>

<?php foreach ($deliveryMethods as $deliveryMethod) : ?>
    <?= Html::radio('delivery_id', false, ['value' => $deliveryMethod->id, 'label' => $deliveryMethod->translation->title]); ?>
<?php endforeach; ?>



