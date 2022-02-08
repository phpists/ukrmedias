<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\components\Auth;
use app\components\Misc;

class PreOrders extends \app\components\BaseActiveRecord {

    const STATUS_NEW = '0';
    const STATUS_REJECTED = '1';
    const STATUS_PROCEED = '2';
    const STATUS_DRAFT = '255';

    static public $statusLabels = [
        self::STATUS_DRAFT => 'чернетка',
        self::STATUS_NEW => 'на розгляді',
        self::STATUS_REJECTED => 'відхилена',
        self::STATUS_PROCEED => 'опрацьована',
    ];
    public $address_id;
    static protected $draft_id;

    public static function tableName() {
        return 'preorders';
    }

    public function rules() {
        return [
            [['payment_id', 'delivery_id'], 'required'],
            [['user_id', 'manager_id'], 'default', 'value' => null],
            [['address', 'client_note'], 'string', 'max' => 1000],
            [['region', 'city', 'consignee', 'name'], 'string', 'max' => 45],
            ['phone', 'match', 'pattern' => '/^\+38((\s*\(\d+\)\s*)?[\d\s\-]+){10,20}$/', 'message' => 'Номер телефону повинен починатися на "+38" та складатися з не менше ніж 12 цифр.'],
            ['phone', 'filter', 'filter' => ['\app\components\Misc', 'phoneFilter']],
            ['address_id', 'safe'],
            ['firm_id', 'exist', 'targetClass' => 'app\\models\\Firms', 'targetAttribute' => 'id'],
            ['delivery_id', 'exist', 'targetClass' => 'app\\models\\DeliveryVariants', 'targetAttribute' => 'id', 'filter' => ['is_allow' => 1]],
            ['payment_id', 'exist', 'targetClass' => 'app\\models\\PaymentVariants', 'targetAttribute' => 'id'],
            [['delivery_id'], function ($attr) {
                    if ($this->delivery_id == DeliveryVariants::NOVA_POSHTA) {
                        foreach (['np_area_ref', 'np_city_ref', 'np_office_no', 'consignee', 'name', 'phone'] as $attr) {
                            if ($this->{$attr} == '') {
                                $this->addError($attr, "Не заполнено поле {$this->getAttributeLabel($attr)}.");
                            }
                        }
                        return;
                    }
                    if ($this->delivery_id <> DeliveryVariants::SELF) {
                        foreach (['region', 'city', 'consignee', 'name', 'address', 'phone'] as $attr) {
                            if ($this->{$attr} == '') {
                                $this->addError($attr, "Не заполнено поле {$this->getAttributeLabel($attr)}.");
                            }
                        }
                        return;
                    }
                }, 'skipOnEmpty' => false],
            array('np_area_ref', 'exist', 'targetClass' => '\app\models\novaposhta\NP_Areas', 'targetAttribute' => 'Ref'),
            ['np_city_ref', 'exist', 'targetClass' => 'app\\models\\novaposhta\NP_CitiesNp', 'targetAttribute' => 'Ref', 'filter' => function ($query) {
                    $query->andWhere(['Area' => $this->np_area_ref]);
                }],
            ['np_office_no', 'exist', 'targetClass' => 'app\\models\\novaposhta\NP_Offices', 'targetAttribute' => 'Number', 'filter' => function ($query) {
                    $query->andWhere(['CityRef' => $this->np_city_ref]);
                }],
            [['firm_id', 'id', 'status_id'], 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'Номер',
            'address' => 'Адреса',
            'address_id' => 'Мої адреси',
            'client_note' => 'Коментар замовника',
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
            'manager_id' => 'Співробітник',
            'payment_title' => 'Оплата',
            'delivery_title' => 'Доставка',
            'np_area_ref' => 'Область',
            'np_city_ref' => 'Населений пункт',
            'np_office_no' => 'Номер відділення',
        ];
    }

    public function searchByManager() {
        $query = self::find()
                ->andWhere('status_id<>:sts_id OR manager_id=:manager_id', [':sts_id' => self::STATUS_DRAFT, ':manager_id' => Yii::$app->user->getId()])
                ->orderBy(['id' => SORT_DESC])
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
            $query->andFilterWhere(['like', 'id', $this->id]);
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
                ->andWhere('status_id<>:sts_id', [':sts_id' => self::STATUS_DRAFT])
                ->orderBy(['id' => SORT_DESC])
        ;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => DataHelper::PAGE_SIZE,
            ],
            'sort' => false,
        ]);
        if ($this->load(Yii::$app->request->get())) {
            $query->andFilterWhere(['like', 'id', $this->id]);
            $query->andFilterWhere(['=', 'status_id', $this->status_id]);
        }
        return $dataProvider;
    }

    protected function initModel() {
        $firm = Firms::findOne($this->firm_id);
        $this->price_type_id = $firm->price_type_id;
        $this->payment_id = $firm->default_payment_id;
        $this->delivery_id = (string) $firm->default_delivery_id;
        $this->address_id = $firm->default_addr_id;
    }

    static public function createDraft($firm_id, $save = true) {
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
        $model->initModel();
        if ($save) {
            $model->saveModel(false);
        }
        return $model;
    }

    static protected function getDraftId() {
        if (self::$draft_id === null) {
            if (Auth::isManager()) {
                self::$draft_id = self::find()->select('id')->where(['firm_id' => Yii::$app->user->identity->firm_id, 'status_id' => self::STATUS_DRAFT])->andWhere('user_id IS NULL')->scalar();
            } else {
                self::$draft_id = self::find()->select('id')->where(['firm_id' => Yii::$app->user->identity->firm_id, 'status_id' => self::STATUS_DRAFT])->andWhere('manager_id IS NULL')->scalar();
            }
        }
        return self::$draft_id;
    }

    static public function getGoodsQty() {
        return PreOrdersDetails::getGoodsQty(self::getDraftId());
    }

    static public function getCartSummary() {
        $summary = PreOrdersDetails::calcOrderSummary(self::getDraftId());
        $summary['qty'] = self::getGoodsQty();
        $txt = Misc::wordingGoods($summary['qty']);
        $summary['info'] = "{$summary['qty']} {$txt}, {$summary['volume']} куб.м";
        return $summary;
    }

    public function validateStock() {
        foreach ($this->getDetails() as $dataModel) {
//            $goods = Goods::find()->where(['id' => $dataModel->goods_id])->exists();
//            if (!$goods) {
//                $this->addError('id', "Товар \"<i>{$dataModel->getAdminTitle(' ')}</i>\" недоступний для замовлення.");
//                PreOrdersRuntimeData::add($dataModel->goods_id, true);
//                continue;
//            }
            $variant = $dataModel->getVariant();
            if ($variant->isNewRecord || $dataModel->qty > $variant->getStockQty()) {
                if ($variant->getStockQty() > 0) {
                    $this->addError('id', "Максимальна кількість товару \"<i>{$dataModel->getAdminTitle(' / ')}</i>\" для замовлення: {$variant->getStockQty()}.");
                } else {
                    $this->addError('id', "Товар \"<i>{$dataModel->getAdminTitle(' ')}</i>\" недоступний для замовлення.");
                }
                PreOrdersRuntimeData::add($dataModel->variant_id, true);
                continue;
            }
        }
        return $this->hasErrors() === false;
    }

    public function saveModel($validate = true) {
        $this->date = date('Y-m-d');
        $this->delivery_title = (string) DeliveryVariants::findModel($this->delivery_id)->getTitle();
        $this->payment_title = (string) PaymentVariants::findModel($this->payment_id)->getTitle();
        if (Auth::isClient()) {
            $this->user_id = Yii::$app->user->getId();
        } elseif (Auth::isManager()) {
            $this->manager_id = Yii::$app->user->getId();
        }
        if ($this->delivery_id == DeliveryVariants::SELF) {
            $this->setAttributes(['region' => '', 'city' => '', 'consignee' => '', 'name' => '', 'address' => '', 'phone' => '', 'np_area_ref' => null, 'np_city_ref' => null, 'np_office_no' => null]);
        } elseif ($this->address_id <> '') {
            foreach (Addresses::getData($this->address_id, $this->firm_id) as $attr => $value) {
                if ($this->{$attr} == '') {
                    $this->{$attr} = $value;
                }
            }
        }
//        if ($this->status_id <> self::STATUS_DRAFT) {
//            if (OrdersDetails::find()->where(['doc_id' => $this->id])->exists()) {
//                $this->addError('id', 'Заявка порожня.');
//                return false;
//            }
//        }
        $res = $this->save($validate);
        return $res;
    }

    public function saveDetails($goodsData, $skipByStock = true) {
        foreach ($goodsData as $data) {
            $goods = $data['goods_id'] instanceof Goods ? $data['goods_id'] : Goods::findOne($data['goods_id']);
            $variant = $data['variant_id'] instanceof Variants ? $data['variant_id'] : Variants::findOne($data['variant_id']);
            if ($goods === null || $variant === null) {
                continue;
            }
            $variant->initGoodsData($goods->id);
            if ($skipByStock && $data['qty'] > $variant->getStockQty()) {
                continue;
            }
            $this->saveDetailsDirect((int) $data['qty'], $goods, $variant);
        }
        $this->updateAmount($skipByStock);
    }

    public function saveDetailsDirect($qty, $goods, $variant) {
        $details = PreOrdersDetails::findModel(['doc_id' => $this->id, 'goods_id' => $goods->id, 'variant_id' => $variant->id]);
        $res = true;
        if ($qty > 0) {
            $res = $details->saveModel($this->id, $qty, $goods, $variant);
        } elseif (!$details->isNewRecord) {
            $res = is_numeric($details->delete());
        }
        return $res;
    }

    public function getFirm() {
        $key = "_firm_{$this->firm_id}";
        $value = Yii::$app->cache->get($key);
        if (!$value) {
            $value = $this->hasOne(Firms::className(), ['id' => 'firm_id'])->one();
            Yii::$app->cache->set($key, $value);
        }
        return $value;
    }

    public function getManager() {
        if (empty($this->manager_id)) {
            return;
        }
        $key = "_user_{$this->manager_id}";
        $value = Yii::$app->cache->get($key);
        if (!$value) {
            $value = $this->hasOne(Users::className(), ['id' => 'manager_id'])->one();
            Yii::$app->cache->set($key, $value);
        }
        return $value;
    }

    public function getFirmTitle() {
        return $this->firm->getTitle();
    }

    public function getManagerName() {
        return $this->manager ? $this->manager->getName() : '';
    }

    public function getNumber() {
        return $this->id;
    }

    public function getDate() {
        return date('d.m.Y', strtotime($this->date));
    }

    public function getStatus() {
        return self::$statusLabels[$this->status_id];
    }

    public function getClientNote() {
        return nl2br($this->client_note);
    }

    public function isAllowUpdate() {
        return $this->status_id == self::STATUS_DRAFT && ($this->manager_id == Yii::$app->user->getId() || $this->user_id == Yii::$app->user->getId());
    }

    public function isAllowDelete() {
        return $this->status_id == self::STATUS_DRAFT && ($this->manager_id == Yii::$app->user->getId() || $this->user_id == Yii::$app->user->getId());
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

    public function getDetails() {
        return PreOrdersDetails::search($this->id)->getModels();
    }

    public function updateAmount($checkMin) {
        $this->setAttributes(PreOrdersDetails::calcOrderSummary($this->id), false);
        if ($checkMin && $this->getToMin() > 0) {
            $this->addError('amount', "До мінімальної суми замовлення залишилось {$this->getToMin()} грн.");
            return;
        }
        $this->update(false, ['amount', 'discount']);
    }

    public function getToMin() {
        $min = Settings::get(Settings::ORDER_MIN_AMOUNT);
        return $this->amount < $min ? $min - $this->amount : 0;
    }

    public function getStatusIcon() {
        switch ($this->status_id):
            case self::STATUS_PROCEED:
                $s = '<img class="status" src="img/orders/check-green.svg" alt="">';
                break;
            case self::STATUS_NEW:
                $s = '<img class="status" src="img/orders/check-blue.svg" alt="">';
                break;
            case self::STATUS_REJECTED:
                $s = '<img class="status" src="img/orders/check-orange.svg" alt="">';
                break;
            default:
                $s = '';
        endswitch;
        return $s;
    }

    static public function repeate($clean, $details) {
        $res = Yii::transaction(function () use ($clean, $details) {
                    $cart = self::createDraft(Yii::$app->user->identity->firm_id);
                    if ($clean) {
                        PreOrdersDetails::deleteAll(['doc_id' => $cart->id]);
                    }
                    foreach ($details as $dataModel) {
                        $cart->saveDetails([$dataModel->getAttributes(['goods_id', 'variant_id', 'qty'])]);
                    }
                    $cart->updateAmount(false);
                    return true;
                });
        return $res;
    }

    static public function importExcel($data, $clean) {
        $model = self::createDraft(Yii::$app->user->identity->firm_id);
        Yii::transaction(function () use ($model, $data, $clean) {
            if ($clean) {
                PreOrdersDetails::deleteAll(['doc_id' => $model->id]);
            }
            $break = false;
            $qty = 0;
//            Yii::dump($data);
//            $model->addError('id', 'тест');
//            return false;
            foreach ($data as $row => $attrs) {
                if (empty($attrs['bar_code']) || empty($attrs['qty'])) {
                    continue;
                }
                if ((int) $attrs['qty'] === 0) {
                    continue;
                }
                $goods = null;
                $goodsVariant = (new \yii\db\Query)->from('goods_variants')->where('barcode=:bar_code', [':bar_code' => $attrs['bar_code']])->limit(1)->one();
                if ($goodsVariant) {
                    $goods = Goods::findOne($goodsVariant['goods_id']);
                    $variant = Variants::findOne($goodsVariant['variant_id']);
                }
                if ($goods === null) {
                    $model->addError('id', "Строка {$row}: товар за штрих-кодом {$attrs['bar_code']} не знайдено.");
                    continue;
                }
                $variant->initGoodsData($goods->id);
                $model->saveDetailsDirect($attrs['qty'], $goods, $variant);
                $qty++;
            }
            if ($qty === 0) {
                $model->addError('id', 'Не додано жодного товару.');
            }
            return $model->hasErrors() === false;
        });
        return $model;
    }

    public function getAddressTxt() {
        switch ($this->delivery_id):
            case DeliveryVariants::NOVA_POSHTA:
                $region = novaposhta\NP_Areas::find()->cache()->where(['Ref' => $this->np_area_ref])->one()->Description;
                $city = novaposhta\NP_CitiesNp::find()->cache()->where(['Ref' => $this->np_city_ref])->one()->Description;
                $data = "{$region}, {$city}, відділення №{$this->np_office_no}, вантажоодержувач {$this->consignee}, контактна особа {$this->name}, тел.{$this->phone}";
                break;
            case DeliveryVariants::SELF:
                $data = '';
                break;
            default:
                $data = "{$this->region}, {$this->city}, {$this->address}, вантажоодержувач {$this->consignee}, контактна особа {$this->name}, тел.{$this->phone}";
        endswitch;
        return $data;
    }

}
