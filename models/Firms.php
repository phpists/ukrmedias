<?php

namespace app\models;

use yii\data\ActiveDataProvider;
use app\components\Auth;
use app\components\Misc;
use \Yii;

class Firms extends \app\components\BaseActiveRecord {

    public $managers;
    public $phones;
    public $phone;
    static protected $_kv;

    public static function tableName() {
        return 'firms';
    }

    public function rules() {
        return [
            [['title'], 'filter', 'filter' => 'strip_tags'],
            [['title'], 'filter', 'filter' => 'trim'],
            [['title'], 'required'],
            [['phone'], 'required', 'on' => 'registration'],
            ['phone', 'match', 'pattern' => '/^\+38((\s*\(\d+\)\s*)?[\d\s\-]+){10,45}$/', 'message' => 'Номер телефону повинен починатися на "+38" та складатися з не менше ніж 12 цифр.', 'on' => 'registration'],
            ['phone', 'filter', 'filter' => ['\app\components\Misc', 'phoneFilter'], 'on' => 'registration'],
            ['phone', 'checkUnique', 'on' => 'registration'],
            [['title'], 'string', 'max' => 255],
            [['default_addr_id', 'default_delivery_id', 'default_payment_id'], 'default', 'value' => null],
            ['default_addr_id', 'exist', 'targetClass' => 'app\\models\\Addresses', 'targetAttribute' => 'id'],
            ['default_delivery_id', 'in', 'range' => array_keys(DeliveryVariants::keyvalClient())],
            ['default_payment_id', 'exist', 'targetClass' => 'app\\models\\PaymentVariants', 'targetAttribute' => 'id'],
            [['managers'], 'safe'],
            [['title', 'phones'], 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels() {
        return [
            'title' => 'Найменування',
            'phones' => 'Телефони',
            'phone' => 'Контактний телефон',
            'price_type_id' => 'Тип ціни',
            'delivery_ukrmedias' => 'Доставка "Укрмедіас"',
            'discount_groups' => 'Цінові групи',
            'groups' => 'Групи клієнта',
            'manager_name' => 'Основний менеджер',
            'manager_phone' => 'Телефон менеджера',
            'manager_mrt' => 'Менеджер МРТ',
            'filter_status' => 'Статус',
            'filter_assortment' => 'Ассортимент',
            'filter_tt' => 'Види ТТ',
            'filter_activity' => 'Вид діяльності',
            'filter_discipline' => 'Платіжна дисциплина',
            'default_addr_id' => 'Адреса доставки по замовчуванню',
            'default_delivery_id' => 'Доставка по замовчуванню',
            'default_payment_id' => 'Оплата по замовчуванню',
        ];
    }

    public function checkUnique() {
        if (Yii::$app->db->createCommand('SELECT 1 FROM firms_phones WHERE phone=:phone LIMIT 1', [':phone' => $this->phone])->queryScalar()) {
            $this->addError('phone', 'Вказаний номер телефону вже зареєстрований в системі. Для доступу в особитсий кабінет зверниться до Вашого адміністратору.');
        }
    }

    public function getTitle() {
        return $this->title;
    }

    public function getPhones() {
        if ($this->phones === null) {
            $this->phones = Yii::$app->db->createCommand('SELECT phone FROM firms_phones WHERE firm_id=:id', [':id' => $this->id])->queryColumn();
        }
        return $this->phones;
    }

    public function deleteModel() {
        return is_numeric($this->delete());
    }

    public function register() {
        $this->phones = [$this->phone];
        $this->save(false);
        $this->savePhones(false);
        return true;
    }

    public function saveModel($attrs) {
        $this->setAttributes($attrs, false);
        $this->phones = $attrs['phones'];
        $res = $this->save();
        if ($res) {
            $res = $this->savePhones();
        }
        if ($res) {
            $this->addNewUsers();
            FirmsGroups::saveList($this->id, $attrs['groups']);
            FirmsDiscountGroups::saveList($this->id, $attrs['discount_groups']);
        }
        return $res;
    }

    public function updateModel() {
        if ($this->id_1s <> '') {
            $this->addError('title', 'Зміна ділера можлива лише в 1С.');
            return false;
        }
        $this->phones = explode(',', $this->phone);
        $res = $this->save(true, ['title']);
        if ($res) {
            $res = $this->savePhones();
        }
        return $res;
    }

    protected function savePhones($validate = true) {
        if ($validate) {
            $validator = new \yii\validators\RegularExpressionValidator(['pattern' => '/^\+38((\s*\(\d+\)\s*)?[\d\s\-]+){10,45}$/', 'message' => 'Номер телефону повинен починатися на "+38" та складатися з не менше ніж 12 цифр.']);
            foreach ($this->phones as $i => $phone) {
                $this->phones[$i] = Misc::phoneFilter($phone);
                if (!$validator->validate($this->phones[$i], $error)) {
                    $this->addError('phones', $error);
                    continue;
                }
                if (Yii::$app->db->createCommand('SELECT 1 FROM firms_phones WHERE phone=:phone AND firm_id<>:firm_id LIMIT 1', [':firm_id' => $this->id, ':phone' => $this->phones[$i]])->queryScalar()) {
                    $this->addError('phones', "Номер телефону {$phone} вже зайнятий.");
                }
            }
            if ($this->hasErrors()) {
                return false;
            }
        }
        foreach ($this->phones as $phone) {
            Yii::$app->db->createCommand('INSERT IGNORE INTO firms_phones (firm_id,phone)VALUES(:id,:phone)', [':id' => $this->id, ':phone' => $phone])->execute();
        }
        if (count($this->phones) > 0) {
            Yii::$app->db->createCommand()->delete('firms_phones', ['AND', 'firm_id=:firm_id', ['NOT IN', 'phone', $this->phones]], [':firm_id' => $this->id])->execute();
        } else {
            Yii::$app->db->createCommand('DELETE FROM firms_phones WHERE firm_id=:id', [':id' => $this->id])->execute();
        }
        return true;
    }

    public function addNewUsers() {
        foreach ($this->getPhones() as $phone) {
            if (Users::find()->where(['phone' => $phone])->exists()) {
                continue;
            }
            $user = new Users();
            $user->phone = $phone;
            $user->register($this->id, 1);
        }
    }

    public function saveManagers() {
        if (count($this->managers) === 0) {
            ManagersFirms::unsetAllAlt($this->id);
        }
        $labels = array_intersect_key(Users::keyvalAdmins(), array_flip($this->managers));
        foreach (array_keys($labels) as $id) {
            ManagersFirms::set($id, $this->id);
        }
        return ManagersFirms::unsetAlt($this->id, array_keys($labels));
    }

    public function updateByClient() {
        return $this->save(false, ['title', 'default_addr_id', 'default_delivery_id', 'default_payment_id']);
    }

    static public function keyval() {
        if (self::$_kv === null) {
            self::$_kv = Yii::$app->db->getMasterPdo()->query('SELECT DISTINCT id,title FROM ' . self::tableName() . ' ORDER BY title')->fetchAll(\PDO::FETCH_KEY_PAIR);
        }
        return self::$_kv;
    }

    static public function keyvalAlt() {
        $data = self::keyval();
        if (Yii::$app->user->can(Auth::ROLE_ADMIN)) {
            return $data;
        }
        $ids = ManagersFirms::getFirmsIds();
        return array_intersect_key($data, array_flip($ids));
    }

    public function search() {
        $query = self::find()->orderBy(['title' => SORT_ASC]);
        switch (Yii::$app->user->identity->role_id):
            case Auth::ROLE_MANAGER:
                $query->where(['in', 'id', ManagersFirms::getFirmsIds()]);
                break;
        endswitch;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => DataHelper::PAGE_SIZE,
            ],
            'sort' => false,
        ]);
        if (!($this->load(Yii::$app->request->get()))) {
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'title', $this->title]);
        if ($this->phones <> '') {
            $query->andWhere('id IN( SELECT firm_id FROM firms_phones WHERE phone LIKE :phone )', [':phone' => "%{$this->phones}%"]);
        }
        return $dataProvider;
    }

    public function initModel() {
        $this->managers = Yii::$app->db->createCommand('SELECT manager_id FROM managers_firms WHERE firm_id=:id', [':id' => $this->id])->queryColumn();
        $this->phones = $this->getPhones();
        $this->phone = implode(', ', $this->phones);
    }

    public function isAllowUpdate() {
        if (Yii::$app->user->can(Auth::ROLE_ADMIN)) {
            return true;
        }
        return in_array($this->id, ManagersFirms::getFirmsIds(Yii::$app->user->getId()));
    }

    public function isAllowDelete() {
        return false;
//        if (Yii::$app->user->can(Auth::ROLE_ADMIN)) {
//            return true;
//        }
//        return in_array($this->id, ManagersFirms::getFirmsIds(Yii::$app->user->getId()));
    }

    public function isAllowControl() {
        if (Yii::$app->user->can(Auth::ROLE_ADMIN)) {
            return true;
        }
        return in_array($this->id, ManagersFirms::getFirmsIds(Yii::$app->user->getId()));
    }

    public function findDefaultUser() {
        return Users::find()->where('firm_id=:client_id', [':client_id' => $this->id])->orderBy(new \yii\db\Expression('FIELD(role_id,"' . Auth::ROLE_CLIENT_DEFAULT . '","' . Auth::ROLE_CLIENT_TEST . '")'))->one();
    }

    public function findDefaultAddress() {
        return Addresses::findModel(['id' => $this->default_addr_id, 'firm_id' => $this->id]);
    }

    public function getAdminDiscountGroups() {
        return implode(', ', FirmsDiscountGroups::keyval($this->id));
    }

    public function getAdminGroups() {
        return implode(', ', FirmsGroups::keyval($this->id));
    }

    public function getPriceType() {
        $key = "_price_type_{$this->price_type_id}";
        $value = Yii::$app->cache->get($key);
        if (!$value) {
            $value = $this->hasOne(PriceTypes::className(), ['id' => 'price_type_id']);
            Yii::$app->cache->set($key, $value);
        }
        return $value;
    }

    public function getPriceTypeTitle() {
        if ($this->price_type_id == '') {
            return;
        }
        return $this->priceType->title;
    }

}
