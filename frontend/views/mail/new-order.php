<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $products
 * @var $profile \bl\cms\shop\common\components\user\models\Profile
 * @var $order \bl\cms\cart\models\Order
 * @var $address \bl\cms\shop\common\components\user\models\UserAddress
 */
use yii\bootstrap\Html;
?>

<h1><?=Yii::t('cart', 'New order.'); ?></h1>
<p><?= Html::tag('strong', Yii::t('cart', 'Name')) . ': ' . $profile->name;?></p>
<p><?= Html::tag('strong', Yii::t('cart', 'Surname')) . ': ' . $profile->surname;?></p>
<p><?= Html::tag('strong', Yii::t('cart', 'Patronymic')) . ': ' . $profile->patronymic;?></p>
<br>
<p><?= Html::tag('strong', Yii::t('cart', 'Phone number')) . ': ' . $profile->phone;?></p>


<p><?=Yii::t('cart', 'Our managers will contact you as soon as possible'); ?></p>
