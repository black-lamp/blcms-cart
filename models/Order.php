<?php
namespace bl\cms\cart\models;

use bl\cms\shop\common\components\user\models\UserAddress;
use dektrium\user\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_order".
 *
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $status
 * @property integer $address_id
 * @property integer $delivery_id
 * @property string $delivery_post_office
 *
 * @property User $user
 * @property UserAddress $address
 * @property OrderStatus $orderStatus
 * @property OrderProduct[] $OrderProducts
 */

class Order extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'status', 'address_id'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['address_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserAddress::className(), 'targetAttribute' => ['address_id' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => OrderStatus::className(), 'targetAttribute' => ['status' => 'id']],
            [['delivery_id'], 'exist', 'skipOnError' => true, 'targetClass' => DeliveryMethod::className(), 'targetAttribute' => ['delivery_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'status' => Yii::t('shop', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(UserAddress::className(), ['id' => 'address_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderStatus()
    {
        return $this->hasOne(OrderStatus::className(), ['id' => 'status']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderProducts()
    {
        return $this->hasMany(OrderProduct::className(), ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeliveryMethod()
    {
        return $this->hasMany(DeliveryMethod::className(), ['delivery_id' => 'id']);
    }
}