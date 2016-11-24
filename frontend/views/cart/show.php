<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $order \bl\cms\cart\models\Order
 * @var $profile \bl\cms\cart\common\components\user\models\Profile
 * @var $user \bl\cms\cart\common\components\user\models\User
 * @var $address \bl\cms\cart\common\components\user\models\UserAddress
 * @var $productsFromDB \bl\cms\cart\models\OrderProduct
 * @var $productsFromSession \bl\cms\shop\common\entities\Product
 */

use bl\cms\cart\widgets\Delivery;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use bl\cms\shop\frontend\assets\CartAsset;

$this->title = \Yii::t('cart', 'Cart');

CartAsset::register($this);
?>

<div class="content cart col-md-12">
    <h1 class="title">
        <?= $this->context->staticPage->translation->title ?? Yii::t('cart', 'Cart') ?>
    </h1>
    <!--DESCRIPTION-->
    <div>
        <?= $this->context->staticPage->translation->text ?? '' ?>
    </div>

    <!--EMPTY CART-->
    <?php if (empty($productsFromDB) && empty($productsFromSession)) : ?>
        <p><?= \Yii::t('cart', 'Your cart is empty.'); ?></p>
        <?= Html::a(\Yii::t('cart', 'Go to shop'), Url::toRoute('/shop'), ['class' => 'btn btn-primary']); ?>

    <!--NOT EMPTY CART-->
    <?php else : ?>
        <div>
            <?= Html::a(\Yii::t('cart', 'Clear cart'), Url::toRoute('/cart/cart/clear'), ['class' => 'btn btn-primary pull-right']); ?>
        </div>
        <table class="table table-hover table-striped products-list">
            <tr>
                <th class="col-md-4 text-center"><?= Yii::t('cart', 'Title'); ?></th>
                <th class="col-md-1 text-center"><?= Yii::t('cart', 'Photo'); ?></th>
                <th class="col-md-2 text-center"><?= Yii::t('cart', 'Price'); ?></th>
                <th class="col-md-2 text-center"><?= Yii::t('cart', 'Count'); ?></th>
                <th class="col-md-1"></th>
            </tr>

            <!--PRODUCT LIST FROM DATABASE-->
            <?php if (!empty($productsFromDB)) : ?>
                <?php foreach ($productsFromDB as $orderProduct) : ?>
                    <tr>
                        <td class="text-center">
                            <?= Html::a($orderProduct->product->translation->title, Url::to(['/shop/product/show', 'id' => $orderProduct->product->id])); ?>
                        </td>
                        <td class="text-center">
                            <?= Html::img($orderProduct->smallPhoto); ?>
                        </td>
                        <td class="text-center">
                            <?php if (!empty($orderProduct->price)) : ?>
                                <?= $orderProduct->price; ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?= $orderProduct->count; ?>
                        </td>
                        <td class="text-center">
                            <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-remove']),
                                Url::to(['/cart/cart/remove', 'id' => $orderProduct->id]),
                                [
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => \Yii::t('cart', 'Remove')
                                ]); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

            <!--PRODUCT LIST FROM SESSION-->
            <?php elseif (!empty($productsFromSession)) : ?>
                <?php foreach ($productsFromSession as $product) : ?>
                    <tr>
                        <td class="text-center">
                            <?= $product->id; ?>
                        </td>
                        <td class="text-center">
                            <?= Html::a($product->translation->title, Url::to(['/shop/product/show', 'id' => $product->id])); ?>
                        </td>
                        <td class="text-center">
                            <?php if (!empty($product->price)) : ?>
                                <?= $product->price; ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?= $product->count; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>

        <?php if (\Yii::$app->user->isGuest) : ?>
            <!--MODAL WINDOWS-->

            <!--REGISTRATION-->
            <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#registerModal">
                <?= \Yii::t('cart', 'I\'m a new user'); ?>
            </button>
            <div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel"><?= Yii::t('cart', 'Registration'); ?></h4>
                        </div>

                        <?= \bl\cms\cart\widgets\Register::widget([
                        ]) ?>
                    </div>
                </div>
            </div>

            <!--LOGIN-->
            <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#loginModal">
                <?= \Yii::t('cart', 'I already have an account'); ?>
            </button>
            <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">Название модали</h4>
                        </div>
                        <div class="modal-body">

                            <?= \dektrium\user\widgets\Login::widget([
                            ]) ?>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>


    <!--ORDER FORM-->
    <?php if (!empty($order)) : ?>
        <?php $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['/cart/cart/make-order']
        ]); ?>

        <!--PERSONAL DATA-->
        <div class="row">
            <div class="personal-data col-md-6">
                <h3><?= Yii::t('cart', 'Your personal data'); ?>:</h3>

                <!--Name-->
                <?php if (!empty(Yii::$app->user->identity->profile->name)) : ?>
                    <p>
                        <b><?= Yii::t('cart', 'Name') ?>:</b> <?= Yii::$app->user->identity->profile->name; ?>
                    </p>
                <?php else : ?>
                    <?= $form->field($profile, 'name')->textInput(); ?>
                <?php endif; ?>

                <!--Surname-->
                <?php if (!empty(Yii::$app->user->identity->profile->surname)) : ?>
                    <p>
                        <b><?= Yii::t('cart', 'Surname') ?>:</b> <?= Yii::$app->user->identity->profile->surname; ?>
                    </p>
                <?php else : ?>
                    <?= $form->field($profile, 'surname')->textInput(); ?>
                <?php endif; ?>

                <!--Patronomic-->
                <?php if (!empty(Yii::$app->user->identity->profile->patronymic)) : ?>
                    <p>
                        <b><?= Yii::t('cart', 'Patronymic') ?>
                            :</b> <?= Yii::$app->user->identity->profile->patronymic; ?>
                    </p>
                <?php else : ?>
                    <?= $form->field($profile, 'patronymic')->textInput(); ?>
                <?php endif; ?>

                <!--Email-->
                <?php if (!empty(Yii::$app->user->identity->email)) : ?>
                    <p>
                        <b><?= Yii::t('cart', 'E-mail') ?>:</b> <?= Yii::$app->user->identity->email; ?>
                    </p>
                <?php else : ?>
                    <?= $form->field($user, 'email')->textInput(); ?>
                <?php endif; ?>

                <!--Phone-->
                <?php if (!empty(Yii::$app->user->identity->profile->phone)) : ?>
                    <p>
                        <b><?= Yii::t('cart', 'Phone number') ?>:</b> <?= Yii::$app->user->identity->profile->phone; ?>
                    </p>
                <?php else : ?>
                    <?= $form->field($profile, 'phone')
                        ->widget(\yii\widgets\MaskedInput::className(), ['mask' => '(999)-999-99-99']); ?>
                <?php endif; ?>

                <?= Html::a(\Yii::t('cart', 'Change personal data'), Url::toRoute('/user/settings'),
                    [
                        'class' => 'btn btn-primary'
                    ]); ?>
            </div>

            <div class="col-md-6">
                <!--PAYMENT METHOD-->
                <?php if (Yii::$app->cart->enablePayment) : ?>
                    <h3><?=Yii::t('cart', 'Payment method');?></h3>
                    <?= \bl\cms\payment\widgets\PaymentSelector::widget([
                        'form' => $form,
                        'order' => $order
                    ]); ?>
                <?php endif; ?>

                <!--DELIVERY METHOD-->
                <?= Delivery::widget(['form' => $form, 'model' => $order]); ?>
            </div>
        </div>
        <!--Address selecting-->
        <div class="address">
            <h3><?= Yii::t('cart', 'Address'); ?>:</h3>

            <?php if (!empty(\Yii::$app->user->identity->profile->userAddresses)) : ?>
                <?= $form->field($order, 'address_id')
                    ->dropDownList(ArrayHelper::map(\Yii::$app->user->identity->profile->userAddresses, 'id', function ($model) {
                        $address = (!empty($model->city)) ? $model->city . ', ' : '';
                        $address .= (!empty($model->street)) ? Yii::t('cart', 'st.') . $model->street . ', ' : '';
                        $address .= (!empty($model->house)) ? Yii::t('cart', 'hse.') . $model->house . ' - ' : '';
                        $address .= (!empty($model->apartment)) ? Yii::t('cart', 'apt.') . $model->apartment : '';
                        return $address;
                    }),
                        ['prompt' => \Yii::t('cart', 'Select address')])->label(\Yii::t('cart', 'Select address or enter it at the next fields')); ?>
            <?php endif; ?>

            <!--Address-->
            <h4><?= \Yii::t('cart', 'Address'); ?></h4>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($address, 'country')->textInput(); ?>
                    <?= $form->field($address, 'region')->textInput(); ?>
                    <?= $form->field($address, 'city')->textInput(); ?>
                    <?= $form->field($address, 'zipcode')->textInput(); ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($address, 'street')->textInput(); ?>
                    <?= $form->field($address, 'house')->textInput(); ?>
                    <?= $form->field($address, 'apartment')->textInput(); ?>
                </div>
            </div>
        </div>

        <?= Html::submitButton(Yii::t('cart', 'Make order'), [
            'class' => 'btn btn-lg btn-danger center-block'
        ]); ?>

        <?php $form::end(); ?>
    <?php endif; ?>
</div>


