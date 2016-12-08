<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $count integer
 */
use yii\helpers\Html;
\bl\cms\cart\widgets\assets\OrderCounterAsset::register($this);
?>

<?= Html::tag('span',
    $count,
    ['id' => 'order-counter', 'style' => ($count) ? 'background-color: red' : 'background-color: #079276']
);?>
