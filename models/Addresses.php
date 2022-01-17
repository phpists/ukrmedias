<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use app\components\Auth;

class Addresses extends \app\components\BaseActiveRecord {

    public $asDefault;

    public static function tableName() {
        return 'addresses';
    }

    public function rules() {
        return [
            [['region', 'city', 'consignee', 'name'], 'string', 'max' => 45],
            [['address'], 'string', 'max' => 255],
            ['phone', 'match', 'pattern' => '/^\+38((\s*\(\d+\)\s*)?[\d\s\-]+){10,20}$/', 'message' => 'Номер телефону повинен починатися на "+38" та складатися з не менше ніж 12 цифр.'],
            ['phone', 'filter', 'filter' => ['\app\components\Misc', 'phoneFilter']],
            [['delivery_id', 'np_area_ref', 'np_city_ref', 'np_office_no'], 'default', 'value' => null],
            ['delivery_id', 'exist', 'targetClass' => 'app\\models\\DeliveryVariants', 'targetAttribute' => 'id', 'filter' => ['is_allow' => 1]],
            ['np_area_ref', 'exist', 'targetClass' => 'app\\models\\novaposhta\NP_Areas', 'targetAttribute' => 'Ref'],
            ['np_city_ref', 'exist', 'targetClass' => 'app\\models\\novaposhta\NP_CitiesNp', 'targetAttribute' => 'Ref', 'filter' => function ($query) {
                    $query->andWhere(['Area' => $this->np_area_ref]);
                }],
            ['np_office_no', 'exist', 'targetClass' => 'app\\models\\novaposhta\NP_Offices', 'targetAttribute' => 'Number', 'filter' => function ($query) {
                    $query->andWhere(['CityRef' => $this->np_city_ref]);
                }],
            [['asDefault'], 'safe'],
        ];
    }

    public function attributeLabels() {
        return [
            'region' => 'Область',
            'city' => 'Населений пункт',
            'address' => 'Адрес',
            'consignee' => 'Вантажоодержувач',
            'name' => 'Контактна особа',
            'phone' => 'Телефон',
            'delivery_id' => 'Доставка',
            'np_area_ref' => 'Область',
            'np_city_ref' => 'Населений пункт',
            'np_office_no' => 'Номер відділення',
        ];
    }

    public function findList() {
        return self::find()->where(['firm_id' => Yii::$app->user->identity->firm_id])->all();
    }

    public function saveModel() {
        $res = $this->save();
        return $res;
    }

    public function isAllowUpdate() {
        return $this->firm_id == Yii::$app->user->identity->firm_id;
    }

    public function isAllowDelete() {
        return $this->firm_id == Yii::$app->user->identity->firm_id;
    }

    public function deleteModel() {
        return is_numeric($this->delete());
    }

    public function getSummary() {
        $labels = DeliveryVariants::keyvalClient();
        $delivery = array_key_exists($this->delivery_id, $labels) ? $labels[$this->delivery_id] . ': ' : '';
        if ($this->delivery_id == DeliveryVariants::NOVA_POSHTA) {
            $region = $this->np_area_ref <> '' ? novaposhta\NP_Areas::find()->cache()->where(['Ref' => $this->np_area_ref])->one()->Description : '';
            $city = $this->np_city_ref <> '' ? novaposhta\NP_CitiesNp::find()->cache()->where(['Ref' => $this->np_city_ref])->one()->Description : '';
            return "{$delivery}{$region}, {$city}, відділення №{$this->np_office_no}, {$this->consignee}, {$this->name}, {$this->phone}";
        } else {
            return "{$delivery}{$this->region}, {$this->city}, {$this->address}, {$this->consignee}, {$this->name}, {$this->phone}";
        }
    }

    static public function keyval() {
        $data = [];
        foreach (self::find()->where(['firm_id' => Yii::$app->user->identity->firm_id])->all() as $model) {
            $data[$model->id] = $model->getSummary();
        }
        return $data;
    }

    static public function getData($id, $firm_id) {
        $model = self::findModel(['id' => $id, 'firm_id' => $firm_id]);
        if ($model->delivery_id == DeliveryVariants::NOVA_POSHTA) {
            return $model->getAttributes(['np_area_ref', 'np_city_ref', 'np_office_no', 'phone', 'name', 'consignee']);
        } else {
            return $model->getAttributes(['region', 'city', 'address', 'phone', 'name', 'consignee']);
        }
    }

}
