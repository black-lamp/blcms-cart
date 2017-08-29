<?php
namespace bl\cms\cart\widgets;

use Yii;
use yii\base\Widget;
use yii\web\View;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 *
 * Example:
 * <?= OrderSum::widget([
 * ]); ?>
 *
 */
class OrderSum extends Widget
{
    public $cost;
    public $totalCost;

    public function init()
    {
        if(empty($this->cost)) {
            $this->cost = Yii::$app->cart->getCost();
        }
        if(empty($this->totalCost)) {
            $this->totalCost = Yii::$app->cart->getTotalCost();
        }
    }

    public function run()
    {
        $discount = 0;

        if($this->totalCost < $this->cost) {
            $discount = round(($this->cost - $this->totalCost) / $this->cost * 100);
        }

        $this->view->registerJs("var currentLang='" . Yii::$app->language . "';", View::POS_HEAD);
        return $this->render('order-sum', [
            'cost' => $this->cost,
            'totalCost' => $this->totalCost,
            'discount' => $discount,
        ]);
    }

}