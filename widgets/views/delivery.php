<?php
use yii\bootstrap\Html;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $deliveryMethods \bl\cms\cart\models\DeliveryMethod
 * @var $form \yii\bootstrap\ActiveForm
 */

\bl\cms\cart\widgets\assets\DeliveryAsset::register($this);
?>

<div id="delivery-methods">
    <h3><?= Yii::t('cart', 'Select delivery method'); ?>:</h3>

    <div class="row">
        <ul class="col-md-4">
            <?php foreach ($deliveryMethods as $deliveryMethod) : ?>
                <li>
                    <?= Html::radio('delivery_id', true, ['value' => $deliveryMethod->id, 'label' => $deliveryMethod->translation->title]); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>



