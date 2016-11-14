<?php
use bl\cms\cart\widgets\assets\DeliveryAsset;
use yii\helpers\ArrayHelper;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $deliveryMethods \bl\cms\cart\models\DeliveryMethod[]
 * @var $form \yii\bootstrap\ActiveForm
 * @var $model \yii\base\Model
 */

DeliveryAsset::register($this);
?>

<div id="delivery-methods">
    <h3><?= Yii::t('cart', 'Select delivery method'); ?>:</h3>

    <div class="row">

        <?= $form->field($model, 'delivery_id')
            ->radioList(ArrayHelper::map($deliveryMethods, 'id',
                function ($item) {
                    return $item->translation->title;
                }))
            ->label(false); ?>

        <div class="col-md-12 delivery-info">
            <div class="col-md-3">
                <img id="delivery-logo" src="" alt="">
            </div>
            <div class="col-md-9">
                <p id="delivery-title"></p>
                <p id="delivery-description"></p>

                <div class="post-office">

                    <?= \bl\cms\novaposhta\widgets\NovaPoshtaWarehouseSelector::widget([
                        'language' => (\Yii::$app->language == 'ru') ? 'ru' : 'ua',
                        'form' => $form,
                        'formModel' => $model,
                        'formAttribute' => 'postoffice'
                    ]); ?>
                </div>

            </div>

        </div>
    </div>
</div>



