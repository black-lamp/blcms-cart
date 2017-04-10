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

    public $additional_products;

    /**
     * @var integer
     */
    public $combinationId;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['productId', 'count', 'priceId', 'combinationId'], 'integer'],
            [['attribute_value_id', 'additional_products', 'combinationId'], 'safe'],
            [['productId', 'count'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'additional_products' => \Yii::t('cart', 'Additional')
        ];
    }
}