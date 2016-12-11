<?php
namespace bl\cms\cart\models;

use bl\multilang\behaviors\TranslationBehavior;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_order_status".
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @property integer $id
 *
 * @property Order[] $orders
 */
class OrderStatus extends ActiveRecord
{

    /**
     * When user added products to cart, but the order is not completed yet.
     */
    const STATUS_INCOMPLETE = 1;
    /**
     * When order is completed already and waits for moderation.
     */
    const STATUS_CONFIRMED = 2;
    /**
     * When order has sent to user
     */
    const STATUS_SENT = 3;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'translation' => [
                'class' => TranslationBehavior::className(),
                'translationClass' => OrderStatusTranslation::className(),
                'relationColumn' => 'order_status_id'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_order_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['color'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id' => Yii::t('shop', 'Product ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['status' => 'id']);
    }
}