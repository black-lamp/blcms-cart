<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $orders \bl\cms\cart\models\Order[]
 */
use yii\bootstrap\Html;
use yii\helpers\Url;

?>

<table class="table table-hover table-striped table-bordered">
    <tr>
        <th class="text-center">#</th>
        <th><?= Yii::t('cart', 'Customer'); ?></th>
        <th><?= Yii::t('cart', 'Date'); ?></th>
        <th><?= Yii::t('cart', 'Sum'); ?></th>
    </tr>
<?php foreach ($orders as $order) : ?>
    <tr>
        <td class="text-center">
            <?= Html::a(
                $order->uid,
                Url::toRoute(['/cart/view', 'id' => $order->id])
            ); ?>
        </td>
        <td>
            <?= $order->user->profile->name . ' ' . $order->user->profile->surname;?>
        </td>
        <td>
            <?= $order->creation_time; ?>
        </td>
        <td>
            <?php $sum = 0; foreach ($order->orderProducts as $product) {
                $sum += $product->count * $product->price;
            }
            ?>
            <?= Yii::$app->formatter->asCurrency($sum); ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>

