<?php
namespace bl\cms\cart\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * @author Gutsulyak Vadim <guts.vadim@gmail.com>
 */
class OrderSearch extends Order
{
    /**
     * @var integer
     */
    public $orderUid;

    /**
     * @var integer
     */
    public $sumFrom;

    /**
     * @var integer
     */
    public $sumTo;

    /**
     * @var string
     */
    public $createdFrom;

    /**
     * @var string
     */
    public $createdTo;

    public function rules()
    {
        return [
            [['orderUid', 'sumFrom', 'sumTo'], 'integer'],
            [['createdFrom', 'createdTo'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'orderUid' => Yii::t('cart', 'Order #'),
            'createdFrom' => Yii::t('cart', 'Created from'),
            'createdTo' => Yii::t('cart', 'Created to'),
            'sumFrom' => Yii::t('cart', 'Sum from'),
            'sumTo' => Yii::t('cart', 'Sum to'),
        ];
    }


    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Order::find()
            ->where(['user_id' => \Yii::$app->user->id])
            ->andWhere(['!=', 'status', OrderStatus::STATUS_INCOMPLETE]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 3,
            ],
            'sort' => [
                'defaultOrder' => ['creation_time' => SORT_DESC]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['uid' => $this->orderUid]);
        $query->andFilterWhere(['>=', 'total_cost', $this->sumFrom])
            ->andFilterWhere(['<=', 'total_cost', $this->sumTo]);
        $query->andFilterWhere(['>=', 'creation_time', $this->createdFrom])
            ->andFilterWhere(['<=', 'creation_time', $this->createdTo]);

        return $dataProvider;
    }
}