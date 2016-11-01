<?php
use bl\cms\cart\widgets\assets\DeliveryAsset;
use yii\helpers\ArrayHelper;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $deliveryMethods \bl\cms\cart\models\DeliveryMethod[]
 * @var $form \yii\bootstrap\ActiveForm
 * @var $model \yii\base\Model
 * @var array $config
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
                    <?= $form->field($model, 'delivery_post_office')->textInput(); ?>

                    <?= \bl\cms\shop\widgets\NovaPoshta::widget([
                        'token' => 'b696152fde625f5e9b3c6a7a0318701f',
                        'language' => (\Yii::$app->language == 'ru') ? 'ru' : 'ua',
                        'formModel' => $config['addressModel'],
                        'formAttribute' => 'postoffice'
                    ]); ?>
                </div>

            </div>

        </div>
    </div>
</div>



