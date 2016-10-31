<?php

namespace bl\cms\cart\models;

use bl\cms\cart\CartComponent;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use bl\cms\cart\models\Order;

/**
 * SearchOrder represents the model behind the search form about `bl\cms\cart\models\Order`.
 */
class SearchOrder extends Order
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
//            [['first_name', 'last_name', 'email', 'phone', 'address', 'status'], 'safe'],
            [['status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Order::find()->where(['not in','status', [OrderStatus::STATUS_INCOMPLETE]])->orderBy(['id' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
        ]);

        $query
//            ->andFilterWhere(['like', 'first_name', $this->first_name])
//            ->andFilterWhere(['like', 'last_name', $this->last_name])
//            ->andFilterWhere(['like', 'email', $this->email])
//            ->andFilterWhere(['like', 'phone', $this->phone])
//            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}