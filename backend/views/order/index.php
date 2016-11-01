<?php
/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @var $this yii\web\View
 * @var $searchModel bl\cms\cart\models\SearchOrder
 * @var $dataProvider yii\data\ActiveDataProvider
 */

use bl\cms\cart\models\OrderStatus;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h5>
            <i class="glyphicon glyphicon-list">
            </i>
            <?= \Yii::t('shop', 'Order list'); ?>
        </h5>
    </div>

    <?= GridView::widget([
        'filterRowOptions' => ['class' => ''],
        'options' => [
            'class' => 'project-list'
        ],
        'layout'=>"{items}\n{pager}\n{summary}",

        'tableOptions' => [
            'id' => 'my-grid',
            'class' => 'table table-hover table-striped table-bordered'
        ],

        'summary' =>  Html::tag('p',
            \Yii::t('cart', 'Showing items from') . ' {begin} ' . \Yii::t('cart', 'to') . ' {end} ' .
            \Yii::t('cart', 'by') . ' {totalCount}',
            ['class' => 'text-center']),
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            /*ORDER NUMBER*/
            [
                'attribute' => 'uid',
                'value' => function($model) {
                    return Html::a($model->uid, Url::toRoute(['view', 'id' => $model->id]));
                },
                'format' => 'html',
                'headerOptions' => ['class' => 'text-center col-md-1'],
                'contentOptions' => ['class' => 'text-center project-title'],
            ],

            /*Customer*/
            [
                'headerOptions' => ['class' => 'text-center col-md-2'],
                'value' => function ($model) {

                    $customer = null;
                    if (!empty($model->user->profile->name) || !empty($model->user->profile->surname)) {
                        $customer = Html::a(
                            $model->user->profile->name . ' ' . $model->user->profile->surname,
                            Url::toRoute(['view', 'id' => $model->id])
                        );
                    }
                    if (!empty($model->address)) {
                        $customer .= Html::tag('p', $model->address->city . ', ' . $model->address->country);
                    }
                    return $customer;
                },
                'label' => Yii::t('shop', 'Customer'),
                'format' => 'html',
                'contentOptions' => ['class' => 'text-center project-title'],
            ],

            /*DATE*/
            [
                'headerOptions' => ['class' => 'text-center col-md-2'],
                'value' => function ($model) {
                    return $model->creation_time;
                },
                'label' => Yii::t('cart', 'Date'),
                'format' => 'html',
                'contentOptions' => ['class' => 'text-center'],
            ],
            /*NUMBER OF ITEMS*/
            [
                'label' => Yii::t('cart', 'Number of items'),
                'headerOptions' => ['class' => 'text-center col-md-1'],
                'value' => function($model) {
                    return count($model->orderProducts);
                },
                'contentOptions' => ['class' => 'text-center'],
            ],

            /*SUM*/
            [
                'headerOptions' => ['class' => 'text-center col-md-1'],
                'value' => function ($model) {
                    $orderProducts = $model->orderProducts;
                    $sum = 0;
                    foreach ($orderProducts as $orderProduct) {
                        $sum += $orderProduct->count * $orderProduct->price;
                    }
                    return Yii::$app->formatter->asCurrency($sum);
                },
                'label' => Yii::t('cart', 'Sum'),
                'format' => 'html',
                'contentOptions' => ['class' => 'text-center'],
            ],

            /*DELIVERY METHOD*/
            [
                'label' => Yii::t('cart', 'Delivery method'),
                'headerOptions' => ['class' => 'text-center col-md-1'],
                'value' => function ($model) {
                    return $model->deliveryMethod->translation->title;
                },
                'contentOptions' => ['class' => 'text-center'],
            ],

            /*STATUS*/
            [
                'headerOptions' => ['class' => 'text-center col-md-2'],
                'attribute' => 'status',
                'filter' => ArrayHelper::map(OrderStatus::find()->all(), 'id', function($model) {
                    return $model->translation->title;
                }),

                'value' => function ($model) {
                    $status = $model->orderStatus->translation->title;
                    return Html::a($status, Url::toRoute(['view', 'id' => $model->id]),
                        [
                            'class' => 'btn btn-default btn-xs',
                            'style' => 'color: #f0f0f0; background-color:' . $model->orderStatus->color
                        ]);
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
            ],

            /*ACTIONS*/
            [
                'headerOptions' => ['class' => 'text-center col-md-2'],
                'attribute' => \Yii::t('shop', 'Manage'),

                'value' => function ($model) {
                    return Html::a('<span class="fa fa-eye"></span>', Url::toRoute(['view', 'id' => $model->id]),
                        ['title' => Yii::t('shop', 'Status and details'), 'class' => 'btn btn-primary pjax m-r-md']) .
                    Html::a('<span class="glyphicon glyphicon-remove"></span>', Url::toRoute(['delete', 'id' => $model->id]),
                        ['title' => Yii::t('shop', 'Delete'), 'data-method' => 'post', 'class' => 'btn btn-danger pjax']);
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
            ]
        ],
    ]); ?>
</div>