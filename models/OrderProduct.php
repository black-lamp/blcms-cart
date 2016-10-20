<?php

namespace bl\cms\cart\models;

use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\ProductImage;
use bl\cms\shop\common\entities\ProductPrice;
use bl\imagable\helpers\FileHelper;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_order_product".
 *
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $price_id
 * @property integer $order_id
 * @property integer $count
 *
 * @property Order $order
 * @property Product $product
 * @property ProductPrice $productPrice
 */
class OrderProduct extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_order_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'order_id', 'count'], 'required'],
            [['product_id', 'order_id', 'price_id', 'count'], 'integer'],
            [['price_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductPrice::className(), 'targetAttribute' => ['price_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id' => Yii::t('shop', 'Product ID'),
            'order_id' => Yii::t('shop', 'Order ID'),
            'count' => Yii::t('shop', 'Count'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public function getPrice()
    {
        $product = $this->product;

        if (!empty($this->price_id)) {
            $product->price = ProductPrice::findOne($this->price_id)->salePrice;
        }

        return $product->price;
    }

    public function getSmallPhoto() {
        return $image = $this->getPhoto('small');
    }
    public function getThumbPhoto() {
        return $image = $this->getPhoto('thumb');
    }
    public function getBigPhoto() {
        return $image = $this->getPhoto('big');
    }

    private function getPhoto($size) {
        $image = ProductImage::findOne($this->product_id);

        if (!empty($image)) {
            $imageName = $image->file_name;

            $logo = \Yii::$app->shop_imagable->get('shop-product', $size, $imageName);

            return '/images/shop-product/' . FileHelper::getFullName($logo);
        }
        else return false;
    }
}