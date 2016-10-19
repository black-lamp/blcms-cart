<?php
namespace bl\cms\cart\widgets;

use bl\cms\cart\models\DeliveryMethod;
use Yii;
use yii\base\Widget;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * This widget adds delivery methods to shop order page
 *
 * Example:
 * <?= Delivery::widget([
 * ]); ?>
 *
 */
class Delivery extends Widget
{
    public $model;

    public function init()
    {
//        TreeWidgetAsset::register($this->getView());
    }

    public function run()
    {
        $deliveryMethods = DeliveryMethod::find()->all();
        return $this->render('delivery', [
            'deliveryMethods' => $deliveryMethods,
            'model' => $this->model,
        ]);
    }

}