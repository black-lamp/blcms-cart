<?php
namespace bl\cms\cart\models;

use yii\base\Model;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */

class CartForm extends Model

{
    public $productId;
    public $count;
    public $priceId;

    public function rules()
    {
        return [
            [['productId', 'count', 'priceId'], 'integer'],
            [['productId', 'count'], 'required'],
        ];
    }
}