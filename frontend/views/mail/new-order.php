<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $user \bl\cms\shop\common\components\user\models\User
 * @var $products
 * @var $profile \bl\cms\cart\common\components\user\models\Profile
 * @var $order \bl\cms\cart\models\Order
 * @var $address \bl\cms\cart\common\components\user\models\UserAddress
 */

use yii\bootstrap\Html;

?>

<h1 style="text-align: center;"><?= Yii::t('cart', 'New order'); ?></h1>
<p><?= Html::tag('strong', Yii::t('cart', 'Name')) . ': ' . $profile->name; ?></p>
<p><?= Html::tag('strong', Yii::t('cart', 'Surname')) . ': ' . $profile->surname; ?></p>
<p><?= Html::tag('strong', Yii::t('cart', 'Patronymic')) . ': ' . $profile->patronymic; ?></p>
<br>
<p><?= Html::tag('strong', Yii::t('cart', 'Phone number')) . ': ' . $profile->phone; ?></p>
<p><?= Html::tag('strong', Yii::t('cart', 'E-mail')) . ': ' . $user->email; ?></p>

<h3><?= Yii::t('cart', 'Delivery'); ?></h3>

<?php if (!empty($order->delivery_post_office)) : ?>
    <p><?= Html::tag('strong', Yii::t('cart', 'Delivery method')) . ': ' .
        $order->deliveryMethod->translation->title; ?>
    </p>
    <p><?= Html::tag('strong', Yii::t('cart', 'Post office')) . ': ' . $order->delivery_post_office; ?></p>
<?php else : ?>
    <p><?= Html::tag('strong', Yii::t('cart', 'Delivery method')) . ': ' .
        $order->deliveryMethod->translation->title; ?>
    </p>
    <p><?= (!empty($address->country)) ?
            Html::tag('strong', Yii::t('cart', 'Country')) . ': ' . $address->country : ''; ?></p>
    <p><?= (!empty($address->region)) ?
            Html::tag('strong', Yii::t('cart', 'Region')) . ': ' . $address->region : ''; ?></p>
    <p><?= (!empty($address->city)) ?
            Html::tag('strong', Yii::t('cart', 'City')) . ': ' . $address->city : ''; ?></p>
    <p><?= (!empty($address->house)) ?
            Html::tag('strong', Yii::t('cart', 'House')) . ': ' . $address->house : ''; ?></p>
    <p><?= (!empty($address->apartment)) ?
            Html::tag('strong', Yii::t('cart', 'Apartment')) . ': ' . $address->apartment : ''; ?></p>
    <p><?= (!empty($address->zipcode)) ?
            Html::tag('strong', Yii::t('cart', 'Zip')) . ': ' . $address->zipcode : ''; ?></p>
<?php endif; ?>

<?php if (!empty($order->paymentMethod)) : ?>
    <h3><?= Yii::t('cart', 'Payment method'); ?></h3>
    <p>
        <b><?= $order->paymentMethod->translation->title ?? ''; ?></b>
    </p>
    <p>
        <i><?= $order->paymentMethod->translation->description ?? ''; ?></i>
    </p>
<?php endif; ?>

<?= $this->render('products', ['products' => $products]); ?>


