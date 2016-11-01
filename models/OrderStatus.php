<?php
namespace bl\cms\cart\models;

use bl\multilang\behaviors\TranslationBehavior;
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

    const STATUS_INCOMPLETE = 1;
    const STATUS_CONFIRMED = 2;

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
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['status' => 'id']);
    }
}