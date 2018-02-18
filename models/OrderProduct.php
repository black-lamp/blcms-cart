<?php

namespace bl\cms\cart\models;

use bl\cms\shop\common\entities\Product;
use bl\cms\shop\common\entities\Combination;
use bl\cms\shop\common\entities\ProductImage;
use bl\cms\shop\common\entities\Price;
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
 * @property integer $combination_id
 * @property integer $order_id
 * @property integer $count
 * @property float $price
 * @property float $base_price
 * @property float $sum
 * @property float $base_sum
 *
 * @property float $priceFloor
 * @property float $calculatedSum
 * @property string $img
 * @property string $title
 *
 * @property Order $order
 * @property Product $product
 * @property Combination $combination
 * @property Price $productPrice
 * @property OrderProductAdditionalProduct $orderProductAdditionalProduct
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
            [['product_id', 'order_id', 'combination_id', 'count'], 'integer'],
            [['price', 'sum', 'base_price', 'base_sum'], 'number'],
            [['price', 'sum', 'base_price', 'base_sum'], 'safe'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
            [['combination_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Combination::className(), 'targetAttribute' => ['combination_id' => 'id']],
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
            'combination_id' => Yii::t('shop', 'Combination'),
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
    public function getCombination()
    {
        return $this->hasOne(Combination::className(), ['id' => 'combination_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * @return Price
     */
    public function getPriceObj()
    {
        $product = $this->product;

        if (!empty($this->combination_id)) {
            $price = Combination::findOne($this->combination_id)->price;
        } else if (!empty($product->price)) {
            $price = $product->price;
        }

        return $price ?? null;
    }

    /**
     * @return float|int|mixed
     */
    public function getPrice()
    {
        $price = $this->getPriceObj();

        if (!empty($price)) {
            return $price->discountPrice;
        }

        return 0;
    }

    /**
     * @return float
     */
    public function getPriceFloor()
    {
        if (!empty($this->combination)) {
            return $this->combination->price->discountPriceFloor ?: 0;
        } else {
            return $this->product->price->discountPriceFloor ?: 0;
        }
    }

    /**
     * @return float
     */
    public function getCalculatedSum()
    {
        return $this->priceFloor * $this->count;
    }

    /**
     * @return string
     */
    public function getImg()
    {
        $src = '';
        if (!empty($this->combination)) {
            $src = (!empty($this->combination->images)) ?
                $this->combination->images[0]->productImage->getBig() :
                ($this->product->image ?
                    $this->product->image->getBig() : '');
        }
        else {
            if (!empty($this->product->image)) {
                $src = $this->product->image->getBig() ?: '';
            }
        }
        return $src;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $title = '';
        if(!empty($this->product)) {
            $title .= $this->product->translation->title;
            if(!empty($this->combination)) {
                $title .= ', ' . $this->combination->title;
            }
        }
        return $title;
    }

    public function getSmallPhoto()
    {
        return $this->getPhoto('small');
    }

    public function getThumbPhoto()
    {
        return $this->getPhoto('thumb');
    }

    public function getBigPhoto()
    {
        return $this->getPhoto('big');
    }

    private function getPhoto($size)
    {
        $image = ProductImage::find()->where(['product_id' => $this->product_id])->one();

        if (!empty($image)) {
            $imageName = $image->file_name;

            $logo = \Yii::$app->shop_imagable->get('shop-product', $size, $imageName);

            return '/images/shop-product/' . FileHelper::getFullName($logo);
        } else return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderProductAdditionalProducts()
    {

        return $this->hasMany(OrderProductAdditionalProduct::className(), ['order_product_id' => 'id']);
    }

    /**
     * @param $additionalProductId integer
     * @return array|bool|null|ActiveRecord
     */
    public function getOrderProductAdditionalProduct($additionalProductId)
    {

        if (!empty($additionalProductId)) {
            $orderProductAdditionalProduct = OrderProductAdditionalProduct::find()
                ->where(['order_product_id' => $this->id, 'additional_product_id' => $additionalProductId])->one();

            return $orderProductAdditionalProduct;
        }
        return false;
    }
}