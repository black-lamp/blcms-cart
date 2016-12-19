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
    public $attribute_value_id;

    public $additionalProducts;

    public function rules()
    {
        return [
            [['productId', 'count', 'priceId', ], 'integer'],
            ['attribute_value_id', 'safe'],
            [['productId', 'count'], 'required'],
        ];
    }
}