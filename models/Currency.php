<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

class Currency extends \app\components\BaseActiveRecord {

    const USD = 'USD';
    const EUR = 'EUR';

    static public $labels = [
        self::USD => self::USD,
        self::EUR => self::EUR,
    ];

    public static function tableName() {
        return 'currency';
    }

    public function rules() {
        return [
            [['currency', 'rate', 'date'], 'required'],
            [['rate'], 'filter', 'filter' => ['\app\components\Misc', 'priceFilter']],
            [['currency'], 'in', 'range' => array_keys(self::$labels)],
            [['currency', 'date'], 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels() {
        return [
            'currency' => 'Валюта',
            'rate' => 'Курс',
            'date' => 'Дата',
        ];
    }

    public function search() {
        $query = self::find()->orderBy(['date' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => DataHelper::PAGE_SIZE,
            ],
            'sort' => false,
        ]);
        if ($this->load(Yii::$app->request->get())) {
            if (preg_match('/^\d{4}$/', $this->date)) {
                $query->andWhere('date LIKE :date', [':date' => "{$this->date}%"]);
            } elseif (preg_match('/^(\d{2})\.(\d{4})$/', $this->date, $matches)) {
                $query->andWhere('date LIKE :date', [':date' => "{$matches[2]}-{$matches[1]}-%"]);
            } elseif (preg_match('/^\d{2}$/', $this->date)) {
                $query->andWhere('date LIKE :date', [':date' => date("Y-{$this->date}-%")]);
            } elseif (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $this->date)) {
                $query->andWhere('date LIKE :date', [':date' => date('Y-m-d', strtotime($this->date))]);
            }
            $query->andFilterWhere(['=', 'currency', $this->currency]);
        }
        return $dataProvider;
    }

    static public function import($attrs) {
        $model = self::findModel(['date' => $attrs['date'], 'currency' => $attrs['currency']]);
        $model->setAttributes($attrs, false);
        $model->save();
        return $model;
    }

    public function getDate() {
        return date('d.m.Y', strtotime($this->date));
    }

}
