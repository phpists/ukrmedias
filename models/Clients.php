<?php

namespace app\models;

use yii\data\ActiveDataProvider;
use app\components\Auth;
use app\components\Misc;
use \Yii;

class Clients extends \app\components\BaseActiveRecord {

    public $services;
    public $managers;
    public $houseServices = [];

    public static function tableName() {
        return 'clients';
    }

    public function rules() {
        return [
            [['title', 'code', 'bank', 'iban'], 'filter', 'filter' => 'strip_tags'],
            [['title', 'code', 'bank', 'iban'], 'filter', 'filter' => 'trim'],
            [['title', 'code', 'bank', 'iban'], 'required'],
            [['title', 'code', 'bank'], 'string', 'max' => 45],
            ['iban', 'string', 'min' => 15, 'max' => 29],
            ['api_key', 'string', 'min' => 64, 'max' => 64],
            [['liqpay_id', 'liqpay_key'], 'string', 'max' => 45],
            ['balance', 'default', 'value' => 0],
            [['region_id', 'area_id', 'city_id'], 'default', 'value' => null],
            ['balance', 'number', 'min' => 0],
            [['active', 'supervisor', 'api'], 'boolean'],
            ['region_id', 'exist', 'targetClass' => 'app\\models\\AddrRegions', 'targetAttribute' => 'id', 'message' => 'Некоректне посилання на область.'],
            ['area_id', 'exist', 'targetClass' => 'app\\models\\AddrAreas', 'targetAttribute' => 'id', 'message' => 'Некоректне посилання на район.',
                'filter' => function($query) {
                    $query->andWhere(['region_id' => $this->region_id]);
                }
            ],
            ['city_id', 'exist', 'targetClass' => 'app\\models\\AddrCities', 'targetAttribute' => 'id', 'message' => 'Некоректне посилання на населений пункт.',
                'filter' => function($query) {
                    $query->andWhere(['region_id' => $this->region_id, 'area_id' => $this->area_id]);
                }
            ],
            [['services', 'managers'], 'safe'],
        ];
    }

    public function attributeLabels() {
        return [
            'title' => 'Найменування',
            'balance' => 'Баланс',
            'services' => 'Послуги',
            'services_txt' => 'Послуги',
            'code' => 'код ЄДРПОУ',
            'bank' => 'Найменування банку',
            'iban' => 'IBAN',
            'api_key' => 'Ключ API',
            'liqpay_id' => 'Liqpay: публічний ключ',
            'liqpay_key' => 'Liqpay: приватний ключ',
            'active' => 'Доступ',
            'supervisor' => 'Наглядач',
            'region_id' => 'Область',
            'area_id' => 'Район',
            'city_id' => 'Населений пункт',
            'api' => 'Доступ до API',
        ];
    }

    public function getTitle() {
        return $this->title;
    }

    public function getBalance() {
        return $this->balance;
    }

    public function getCode() {
        return $this->code;
    }

    public function getBank() {
        return $this->bank;
    }

    public function getIban() {
        return $this->iban;
    }

    public function deleteModel() {
        return $this->delete();
    }

//    static public function findModel() {
//        $args = func_get_args();
//        if (Yii::$app->user->identity->role_id === Auth::ROLE_ADMIN) {
//            $model = self::find()
//                    ->where(array_shift($args))
//                    ->andWhere(['IN', 'id', ManagersClients::getClientsIds()])
//                    ->one()
//            ;
//        } else {
//            $model = self::findOne(array_shift($args));
//        }
//        if ($model === null) {
//            $model = new self();
//        }
//        return $model;
//    }

    public function saveModel() {
        if ($this->isNewRecord) {
            $this->api_id = Misc::createKey('c');
            $this->api_key = Yii::$app->security->generateRandomString(64);
            $isNew = true;
        }
        $res = $this->save();
        if ($res) {
            $this->saveServices();
            $this->setActiveAlt();
        }
        if ($res && isset($isNew)) {
            Tasks::start(Tasks::CREATE_PERIOD_BY_CLIENT, $this->id);
        }
        return $res;
    }

    protected function saveServices() {
        if (count($this->services) === 0) {
            Services::unsetAll($this->id);
            $this->services_txt = '';
            return $this->update(['services_txt']);
        }
        $labels = array_intersect_key(Services::$labels, array_flip($this->services));
        foreach (array_keys($labels) as $id) {
            Services::set($this->id, $id);
        }
        Services::unsetOne($this->id, array_keys($labels));
        $this->services_txt = implode(', ', $labels);
        $this->update(['services_txt']);
    }

    public function saveManagers() {
        if (count($this->managers) === 0) {
            ManagersClients::unsetAllAlt($this->id);
        }
        $labels = array_intersect_key(Users::keyvalAdmins(), array_flip($this->managers));
        foreach (array_keys($labels) as $id) {
            ManagersClients::set($id, $this->id);
        }
        return ManagersClients::unsetAlt($this->id, array_keys($labels));
    }

    public function updateByClient() {
        return $this->save(true, ['title', 'code', 'bank', 'iban', 'liqpay_id', 'liqpay_key', 'api_key']);
    }

    static public function keyval() {
        return Yii::$app->db->getMasterPdo()->query('SELECT DISTINCT id,title FROM ' . self::tableName() . ' ORDER BY title')->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    static public function keyvalAlt() {
        $data = self::keyval();
        if (Yii::$app->user->can(Auth::ROLE_SUPERADMIN)) {
            return $data;
        }
        $ids = ManagersClients::getClientsIds();
        return array_intersect_key($data, array_flip($ids));
    }

    static public function keyvalToTransfer($house) {
        $data = self::keyvalAlt();
        $ids = $house->getClientIds();
        return array_diff_key($data, array_flip($ids));
    }

    public function search() {
        $query = self::find()->orderBy(['title' => SORT_ASC]);
        switch (Yii::$app->user->identity->role_id):
            case Auth::ROLE_ADMIN:
                $query->where(['in', 'id', ManagersClients::getClientsIds()]);
                break;
        endswitch;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => DataHelper::PAGE_SIZE,
            ],
            'sort' => false,
        ]);
        return $dataProvider;
    }

    public function initModel($house = null) {
        $this->managers = Yii::$app->db->createCommand('SELECT manager_id FROM managers_clients WHERE client_id=:id', [':id' => $this->id])->queryColumn();
        $this->services = Yii::$app->db->createCommand('SELECT service_id FROM clients_services WHERE client_id=:id', [':id' => $this->id])->queryColumn();
        if ($house instanceof AddrHouses) {
            $this->houseServices = $house->initClientServices($this->id);
            #Yii::dump($house->clientServices);
            #$this->services = Yii::$app->db->createCommand('SELECT service_id FROM clients_services WHERE client_id=:id', [':id' => $this->id])->queryColumn();
            #$this->services = array_intersect($this->services, $house->clientServices);
            #$this->services = $house->clientServices;
        }
    }

    public function isAllowUpdate() {
        if (Yii::$app->user->can(Auth::ROLE_SUPERADMIN)) {
            return true;
        }
        return in_array($this->id, ManagersClients::getClientsIds(Yii::$app->user->getId()));
    }

    public function isAllowDelete() {
        if (Yii::$app->user->can(Auth::ROLE_SUPERADMIN)) {
            return true;
        }
        return in_array($this->id, ManagersClients::getClientsIds(Yii::$app->user->getId()));
    }

    public function isAllowControl() {
        if (Yii::$app->user->can(Auth::ROLE_SUPERADMIN)) {
            return true;
        }
        return in_array($this->id, ManagersClients::getClientsIds(Yii::$app->user->getId()));
    }

    public function findDefaultUser() {
        return Users::find()->where('client_id=:client_id', ['client_id' => $this->id])->orderBy(new \yii\db\Expression('FIELD(role_id,"' . Auth::ROLE_CLIENT_DEFAULT . '","' . Auth::ROLE_CLIENT_TEST . '")'))->one();
    }

    static public function getId($house_id, $service_id) {
        return self::find()
                        ->select('client_id')
                        ->from('clients_houses_services')
                        ->where(['house_id' => $house_id, 'service_id' => $service_id])
                        ->scalar();
    }

    static public function checkServiceExists($client_id) {
        return Yii::$app->db->createCommand('SELECT 1 FROM clients_houses_services WHERE client_id=:client_id LIMIT 1', [':client_id' => $client_id])->queryScalar();
    }

    public function isAllowLiqpay() {
        return $this->liqpay_id !== '' && $this->liqpay_key == '';
    }

    public function setActive($value) {
        $this->active = $value;
        $this->update(false, ['balance', 'active']);
        Yii::$app->db->createCommand('UPDATE users SET client_active=:active WHERE client_id=:client_id', [':client_id' => $this->id, ':active' => $value])->execute();
    }

    protected function setActiveAlt() {
        Yii::$app->db->createCommand('UPDATE users SET client_active=:active WHERE client_id=:client_id', [':client_id' => $this->id, ':active' => $this->active])->execute();
    }

    public function isSupervisor() {
        return $this->supervisor > 0;
    }

    public function isAllowApi() {
        return $this->api > 0;
    }

    public function getApiId() {
        return "{$this->api_id}-{$this->id}";
    }

}
