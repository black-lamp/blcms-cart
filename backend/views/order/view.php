<?php
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $model bl\cms\cart\models\Order
 * @var $orderProducts bl\cms\cart\models\OrderProduct
 * @var $statuses[] bl\cms\cart\models\OrderStatus
 */

$this->title = Yii::t('cart', 'Order details');
?>
<div class="panel panel-default">

    <div class="panel-heading">
        <h1><?= \Yii::t('shop', 'Order #') . $model->id; ?></h1>
    </div>

    <!--CHANGE STATUS-->
    <div class="panel-body">
        <h2>
            <?= Yii::t('shop', 'Order status'); ?>
        </h2>
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'status')->dropDownList(ArrayHelper::map($statuses, 'id', function($model) {
            return $model->translation->title;
        }), ['options' => [$model->status => ['selected' => true]]]); ?>
        <?= Html::submitButton(Yii::t('shop', 'Change status'), ['class' => 'btn btn-primary']); ?>
        <?= Html::a(Yii::t('shop', 'Close'), Url::toRoute('/cart/order'), ['class' => 'btn btn-danger']) ?>
        <?php $form::end(); ?>
    </div>

    <!--ORDER DETAILS-->
    <div class="panel-body">
        <h2>
            <?= Yii::t('shop', 'Order details'); ?>
        </h2>

        <p><b><?= \Yii::t('shop', 'Customer name'); ?>:</b> <?=$model->user->profile->name; ?></p>
        <p><b><?= \Yii::t('shop', 'Surname'); ?>:</b> <?=$model->user->profile->surname; ?></p>
        <p><b><?= \Yii::t('shop', 'Patronymic'); ?>:</b> <?=$model->user->profile->patronymic; ?></p>
        <p><b><?= \Yii::t('shop', 'Phone number'); ?>:</b> <?=$model->user->profile->phone; ?></p>
    </div>

    <!--DELIVERY METHOD-->
    <div class="panel-body">
        <h2>
            <?= Yii::t('shop', 'Delivery method'); ?>
        </h2>
        <p>
            <?= $model->deliveryMethod->translation->title;?>
        </p>
    </div>

    <!--PRODUCT LIST-->
    <div class="panel-body">
        <h2>
            <?= Yii::t('shop', 'Product list'); ?>
        </h2>

        <table class="table table-hover table-striped table-bordered">
            <tr>
                <th>#</th>
                <th>
                    <?= Yii::t('cart', 'SKU'); ?>
                </th>
                <th>
                    <?= Yii::t('cart', 'Product title'); ?>
                </th>
                <th>
                    <?= Yii::t('cart', 'Count'); ?>
                </th>
                <th>
                    <?= Yii::t('cart', 'Price'); ?>
                </th>
                <th>
                    <?= Yii::t('cart', 'Delete'); ?>
                </th>
            </tr>

            <?php $i = 0; foreach ($orderProducts as $orderProduct) : ?>
            <tr>
                <td><?=++$i; ?></td>
                <td>
                    <?= $orderProduct->product->sku; ?>
                </td>
                <td>
                    <?= $orderProduct->product->translation->title; ?>
                    <?= (!empty($orderProduct->priceTitle)) ?
                        Html::tag('i', '(' . $orderProduct->priceTitle . ')') : ''; ?>
                </td>
                <td>
                    <?= $orderProduct->count; ?>
                </td>
                <td>
                    <?= $orderProduct->price; ?>
                </td>
                <td>
                    <?= Html::a('<span class="glyphicon glyphicon-remove"></span>', Url::toRoute(['delete-product', 'id' => $model->id]),
                        ['title' => Yii::t('yii', 'Delete'), 'class' => 'btn btn-danger pull-right pjax']); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>