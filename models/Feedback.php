<?php

namespace app\models;

use \Yii;
use app\components\Auth;
use yii\data\ActiveDataProvider;

/**
 * ContactForm is the model behind the contact form.
 */
class Feedback extends \app\components\BaseActiveRecord {

    const SUBJECT_GENERAL = '0';
    const SUBJECT_ORDER = '1';
    const SUBJECT_CABINET = '2';
    const STATUS_NEW = 0;
    const STATUS_OK = 1;

    static public $subjectLabels = [
        self::SUBJECT_ORDER => 'заявки та замовлення',
        self::SUBJECT_CABINET => 'робота в кабінеті',
        self::SUBJECT_GENERAL => 'інше',
    ];
    static public $statusLabels = [
        self::STATUS_NEW => 'нове',
        self::STATUS_OK => 'закрито',
    ];

    public static function tableName() {
        return 'feedback';
    }

    public function rules() {
        return [
            [['name', 'email', 'phone', 'mess'], 'filter', 'filter' => 'strip_tags'],
            [['name', 'email', 'phone', 'mess'], 'trim'],
            [['subject_id', 'mess'], 'required'],
            ['subject_id', 'in', 'range' => array_keys(self::$subjectLabels)],
            ['email', 'email'],
            ['phone', 'match', 'pattern' => '/^\+38((\s*\(\d+\)\s*)?[\d\s\-]+){10,45}$/', 'message' => 'Номер телефону повинен починатися на "+38" та складатися з не менше ніж 12 цифр.'],
            ['phone', 'filter', 'filter' => ['\app\components\Misc', 'phoneFilter']],
            ['name', 'string', 'max' => 45],
            [['email', 'address'], 'string', 'max' => 255],
            ['mess', 'string', 'max' => 8192],
            ['status_id', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels() {
        return [
            'date' => 'Дата',
            'subject_id' => 'Тема',
            'name' => 'Ім`я',
            'email' => 'E-mail',
            'phone' => 'Телефон',
            'mess' => 'Повідомлення',
            'status_id' => 'Статус',
            'user_id' => 'Користувач',
            'address' => 'Клиєнт',
        ];
    }

    public function search() {
        $query = self::find();
        if (Yii::$app->user->identity->role_id == Auth::ROLE_MANAGER) {
            $query->andWhere('firm_id IN(SELECT firm_id FROM managers_firms WHERE manager_id=:manager_id)', [':manager_id' => Yii::$app->user->getId()]);
        }
        $query->orderBy = ['date' => SORT_DESC];
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => DataHelper::PAGE_SIZE,
            ],
            'sort' => [
                'attributes' => ['date'],
            ]
        ]);
        if (!($this->load(Yii::$app->request->get()))) {
            return $dataProvider;
        }

        $query
                ->andFilterWhere(['=', 'subject_id', $this->subject_id])
                ->andFilterWhere(['=', 'status_id', $this->status_id])
                ->andFilterWhere(['like', 'CONCAT(name, " ",email, " ",phone," ", address)', $this->name])
                ->andFilterWhere(['like', 'mess', $this->mess])
        ;
        return $dataProvider;
    }

    public function getSubject() {
        return array_key_exists($this->subject_id, self::$subjectLabels) ? self::$subjectLabels[$this->subject_id] : $this->subject_id;
    }

    public function getStatus() {
        return array_key_exists($this->status_id, self::$statusLabels) ? self::$statusLabels[$this->status_id] : $this->status_id;
    }

    public function isNew() {
        return $this->status_id == self::STATUS_NEW;
    }

    public function getMess() {
        return nl2br($this->mess);
    }

    public function getDate() {
        return date('d.m.Y H:i', strtotime($this->date));
    }

    public function getContacts() {
        $data = [];
        if (!empty($this->email)) {
            $data[] = $this->email;
        }
        if (!empty($this->phone)) {
            $data[] = $this->phone;
        }
        $data = $this->name . '<br/>' . implode('; ', $data);
        if (empty($this->address)) {
            return $data;
        }
        return $data . '<div class="hint-block">' . $this->address . '</div>';
    }

    public function saveModel() {
        $this->date = date('Y-m-d H:i:s');
        $this->user_id = Yii::$app->user->getId();
        $this->status_id = self::STATUS_NEW;
        return $this->save();
    }

    public function saveByClient($firm) {
        $this->date = date('Y-m-d H:i:s');
        $this->user_id = Yii::$app->user->getId();
        $this->firm_id = $firm->id;
        $this->status_id = self::STATUS_NEW;
        $this->name = Yii::$app->user->identity->getName();
        $this->phone = Yii::$app->user->identity->getPhone();
        $this->email = Yii::$app->user->identity->getEmail();
        $this->address = $firm->getTitle();
        return $this->save();
    }

    public function toggleStatus() {
        $this->status_id = !$this->status_id;
        return $this->update(false, ['status_id']);
    }

    public function isAllowDelete() {
        return $this->status_id == self::STATUS_OK;
    }

    public function isAllowControl() {
        if (Yii::$app->user->can(Auth::ROLE_SUPERADMIN)) {
            return true;
        }
        return Yii::$app->db->createCommand()
                        ->select('1')
                        ->from('feedback_to')
                        ->where('id=:id', [':id' => $this->id])
                        ->andWhere(['IN', 'firm_id', ManagersFirms::getFirmsIds(Yii::$app->user->getId())])
                        ->limit(1)
                        ->queryScalar();
    }

}
