<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $products
 * @var $profile \bl\cms\cart\common\components\user\models\Profile
 * @var $order \bl\cms\cart\models\Order
 * @var $address \bl\cms\cart\common\components\user\models\UserAddress
 */

?>
<h1><?=Yii::t('cart', 'Your order is accepted.'); ?></h1>
<p><?= Yii::t('cart', 'Hallo') . ", $profile->name $profile->surname";?></p>
<p><?=Yii::t('cart', 'Our managers will contact you as soon as possible'); ?></p>


