<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use app\components\Auth;

class Orders extends \app\components\BaseActiveRecord {

    const STATUS_EMPTY = '0';
    const STATUS_REJECTED = '1';
    const STATUS_WAIT = '2';
    const STATUS_SETUP = '3';
    const STATUS_READY = '4';
    const STATUS_FINISHED = '255';

    static public $statusLabels = [
        self::STATUS_EMPTY => 'без статусу',
        self::STATUS_REJECTED => 'відхилена',
        self::STATUS_WAIT => 'очікує поставки',
        self::STATUS_SETUP => 'зборка',
        self::STATUS_READY => 'готовий до відправки',
        self::STATUS_FINISHED => 'виконаний',
    ];
    static public $filterStatusLabels = [
        self::STATUS_EMPTY => 'без статусу',
        self::STATUS_WAIT => 'очікує поставки',
        self::STATUS_SETUP => 'зборка',
        self::STATUS_READY => 'готовий до відправки',
        self::STATUS_FINISHED => 'виконаний',
    ];
    public $oldStatusTxt;

    public static function tableName() {
        return 'orders';
    }

    public function rules() {
        return [
            //[['region', 'city', 'consignee', 'name', 'address', 'phone', 'payment_id', 'delivery_id'], 'required'],
            ['delivery_id', 'default', 'value' => DeliveryVariants::UNDEFINED],
            [['firm_id', 'delivery_id', 'number', 'date', 'amount'], 'required', 'on' => 'import'],
            ['status_id', 'default', 'value' => self::STATUS_EMPTY],
            [['user_id', 'manager_id'], 'default', 'value' => null],
            [['number'], 'string', 'max' => 1000],
            [['manager_note'], 'string', 'max' => 1000],
            //[['region', 'city', 'consignee', 'name'], 'string', 'max' => 45],
            //['phone', 'match', 'pattern' => '/^\+38((\s*\(\d+\)\s*)?[\d\s\-]+){10,20}$/', 'message' => 'Номер телефону повинен починатися на "+38" та складатися з не менше ніж 12 цифр.'],
            //['phone', 'filter', 'filter' => ['\app\components\Misc', 'phoneFilter']],
            ['status_id', 'in', 'range' => array_keys(self::$statusLabels)],
            ['firm_id', 'exist', 'targetClass' => 'app\\models\\Firms', 'targetAttribute' => 'id'],
            ['delivery_id', 'exist', 'targetClass' => 'app\\models\\DeliveryVariants', 'targetAttribute' => 'id'],
            [['firm_id', 'request_id', 'number', 'status_id', 'request_id'], 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels() {
        return [
            'request_id' => 'Заявка',
            'number' => 'Номер',
            'address' => 'Адреса',
            'client_note' => 'Коментар замовника',
            'manager_note' => 'Коментар менеджера',
            'status_id' => 'Статус',
            'firm_id' => 'Клієнт',
            'date' => 'Дата',
            'amount' => 'Сума',
            'discount' => 'Знижка',
            'region' => 'Область',
            'city' => 'Населений пункт',
            'consignee' => 'Вантажоодержувач',
            'name' => 'Контактна особа',
            'phone' => 'Телефон',
            'delivery_id' => 'Доставка',
            'payment_id' => 'Оплата',
            'payment_title' => 'Оплата',
            'delivery_title' => 'Доставка',
        ];
    }

    public function searchByManager() {
        $query = self::find()
                ->orderBy(['number' => SORT_DESC])
        ;
        if (Yii::$app->user->identity->role_id == Auth::ROLE_MANAGER) {
            $query->andWhere(['in', 'firm_id', ManagersFirms::getFirmsIds()]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => DataHelper::PAGE_SIZE,
            ],
            'sort' => false,
        ]);
        if ($this->load(Yii::$app->request->get())) {
            $query->andFilterWhere(['like', 'request_id', $this->request_id]);
            $query->andFilterWhere(['like', 'number', $this->number]);
            $query->andFilterWhere(['=', 'status_id', $this->status_id]);
            if (!empty($this->firm_id)) {
                $query->andWhere('firm_id IN(SELECT id FROM firms WHERE title LIKE :title)', [':title' => "%{$this->firm_id}%"]);
            }
        }
        return $dataProvider;
    }

    public function searchByClient() {
        $query = self::find()
                ->where(['=', 'firm_id', Yii::$app->user->identity->firm_id])
                ->orderBy(['number' => SORT_DESC])
        ;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => DataHelper::PAGE_SIZE,
            ],
            'sort' => false,
        ]);
        if ($this->load(Yii::$app->request->get())) {
            $query->andFilterWhere(['like', 'request_id', $this->request_id]);
            $query->andFilterWhere(['like', 'number', $this->number]);
            $query->andFilterWhere(['=', 'status_id', $this->status_id]);
        }
        return $dataProvider;
    }

    static public function initModel($attrs) {
        $query = self::find();
        if (Auth::isManager()) {
            $query->where(['firm_id' => $firm_id, 'manager_id' => Yii::$app->user->getId(), 'status_id' => self::STATUS_DRAFT]);
        } else {
            $query->where(['firm_id' => $firm_id, 'user_id' => Yii::$app->user->getId(), 'status_id' => self::STATUS_DRAFT]);
        }
        $model = $query->one();
        if ($model === null) {
            $model = new self();
        }
        $model->firm_id = $firm_id;
        $model->status_id = self::STATUS_DRAFT;
        $model->saveModel(false);
        return $model;
    }

    static public function import($attrs, $preorder) {
        $model = self::findModel($attrs['id']);
        if (!$model->isNewRecord) {
            $model->oldStatusTxt = $model->getStatus();
        }
        $model->setScenario('import');
        if ($preorder) {
            if ($preorder->firm_id !== $attrs['firm_id']) {
                $preorder->firm_id = $attrs['firm_id'];
                $preorder->update(false, ['firm_id']);
            }
            $model->setAttributes($preorder->getAttributes(['region', 'city', 'address', 'phone', 'name', 'consignee', 'payment_id', 'payment_title', 'client_note', 'user_id', 'manager_id', 'np_area_ref', 'np_city_ref', 'np_office_no']), false);
        }
        $model->setAttributes($attrs, false);
        $model->delivery_title = DeliveryVariants::findOne($model->delivery_id)->getTitle();
        if (!$model->isNP()) {
            $model->np_city_ref = '';
            $model->np_office_no = '';
        }
        $res = $model->save();
        if ($res) {
            $model->saveDetails($attrs['details']);
            $model->updateAmount();
        }
        return $model;
    }

    protected function saveDetails($data) {
        OrdersDetails::deleteAll(['doc_id' => $this->id]);
        foreach ($data as $row) {
            $model = new OrdersDetails();
            $goods = Goods::findOne($row['id']);
            if ($goods === null) {
                $goods = new Goods();
                $goods->setAttributes($row, false);
                $goods->id = $row['variant_id'] ? $row['variant_id'] : "{$row['code']}/{$row['article']}";
            }
            $variant = Variants::findOne($row['variant_id']);
            if ($variant === null) {
                $variant = new Variants();
                $variant->color = $row['color'];
                $variant->size = $row['size'];
                $variant->id = $row['variant_id'] ? $row['variant_id'] : "{$row['color']}/{$row['size']}";
            }
            if (trim($goods->id, '/') == '') {
                $goods->id = uniqid('undefined_');
            }
            if (trim($variant->id, '/') == '') {
                $variant->id = uniqid('undefined_');
            }
            $model->saveModel($this->id, $row, $goods, $variant);
            $this->weight += $model->weight;
            $this->volume += $model->volume;
        }
    }

    public function getFirm() {
        $key = "_firm_{$this->firm_id}";
        $value = Yii::$app->cache->get($key);
        if (!$value) {
            $value = $this->hasOne(Firms::className(), ['id' => 'firm_id']);
            Yii::$app->cache->set($key, $value);
        }
        return $value;
    }

    public function getFirmTitle() {
        return $this->firm->getTitle();
    }

    public function getNumber() {
        return $this->number;
    }

    public function getDate() {
        return date('d.m.Y', strtotime($this->date));
    }

    public function getFullNum() {
        return $this->getNumber() . ' ' . $this->getDate();
    }

    public function getStatus() {
        return self::$statusLabels[$this->status_id];
    }

    public function getClientNote() {
        return nl2br($this->client_note);
    }

    public function getManagerNote() {
        return nl2br($this->manager_note);
    }

    public function isAllowUpdate() {
        return false;
    }

    public function isAllowDelete() {
        return false;
    }

    public function isAllowView() {
        if (Auth::isClient()) {
            return $this->firm_id == Yii::$app->user->identity->firm_id;
        }
        if (Yii::$app->user->identity->role_id == Auth::ROLE_MANAGER) {
            return in_array($this->firm_id, ManagersFirms::getFirmsIds());
        }
        return true;
    }

    public function isAllowDownload() {
        if (!$this->isAllowView()) {
            return false;
        }
        if (Auth::isClient()) {
            return in_array($this->status_id, [self::STATUS_READY]);
        }
        return true;
    }

    public function getDetails() {
        return OrdersDetails::search($this->id)->getModels();
    }

    public function getAdminRequestLink() {
        if ($this->request_id == '') {
            return '';
        }
        return Html::a($this->request_id, ['/admin/preorders/view', 'id' => $this->request_id], ['target' => '_blank', 'data-pjax' => 0]);
    }

    public function updateAmount() {
        $this->setAttributes(OrdersDetails::calcOrderSummary($this->id), false);
        $this->update(false, ['amount', 'discount', 'weight', 'volume']);
    }

    public function deleteModel() {
        return is_numeric($this->delete());
    }

    public function getStatusIcon() {
        switch ($this->status_id):
            case Orders::STATUS_FINISHED:
            case Orders::STATUS_READY:
                $s = '<img class="status" src="img/orders/check-green.svg" alt="">';
                break;
            case Orders::STATUS_SETUP:
                $s = '<img class="status" src="img/orders/check-blue.svg" alt="">';
                break;
            case Orders::STATUS_WAIT:
                $s = '<img class="status" src="img/orders/check-orange.svg" alt="">';
                break;
            default:
                $s = '';
        endswitch;
        return $s;
    }

    public function isAllowNP() {
        return $this->isNP() && $this->np_express_ref === '';
    }

    public function isOldNP() {
        return $this->isNP() && $this->np_express_ref <> ''; # && $this->np_date < date('Y-m-d', strtotime(' -2 day'));
    }

    public function isNP() {
        return in_array($this->delivery_id, [DeliveryVariants::NOVA_POSHTA]);
    }

    public function getNP_summary() {
        return $this->isNP() ? $this->np_express_num . ' від ' . date('d.m.Y', strtotime($this->np_date)) : '-';
    }

    public function getNP_cargo_type() {
        return 'Money';
    }

    public function getAddr() {
        if ($this->isNP() && !empty($this->np_city_ref)) {
            return novaposhta\NP_Offices::getName($this->np_city_ref, $this->np_office_no);
        } else {
            return $this->addr;
        }
    }

}
