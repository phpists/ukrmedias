<?php

namespace app\models;

use yii\helpers\Url;
use app\components\Misc;
use Yii;

class Cpu extends \app\components\BaseActiveRecord {

    public static function tableName() {
        return 'cpu';
    }

    public function rules() {
        return [
            ['visible', 'in', 'range' => array_keys(self::$valuesTrueFalse)],
            [['cpu'], 'string', 'max' => 300],
            [['meta_title', 'meta_keywords', 'meta_descr'], 'string', 'max' => 255],
            ['cpu', 'unique', 'except' => 'import'],
        ];
    }

    public function attributeLabels() {
        return [
            'cpu' => 'УРЛ',
            'meta_title' => 'Заголовок сторінки',
            'meta_keywords' => 'meta-ключові слова',
            'meta_descr' => 'meta-опис',
            'visible' => 'Відображення',
        ];
    }

    public function autoCpu($owner) {
        if ($owner instanceof Goods) {
            $this->cpu = Misc::createCpu($owner->getTitle());
            $this->cpu .= "-{$owner->code}";
        } elseif ($this->cpu == '') {
            $this->cpu = Misc::createCpu($owner->getTitle());
        }
    }

    public function saveModel($owner, $validate = false) {
        $this->id = $owner->id;
        $this->class = get_class($owner);
        $this->autoCpu($owner);
        $res = $this->save($validate);
        if ($res && $owner->hasAttribute('visible')) {
            $owner->visible = $this->visible;
            $owner->update(false, ['visible']);
        }
        return $res;
    }

    public function getUrl($params = []) {
        $params['cpu'] = $this->cpu;
        $params[0] = '/client/catalog/index';
        return Url::to(array_filter($params));
    }

    static public function findBy($cpu) {
        $query = self::find()->where(['cpu' => $cpu])->limit(1);
        if (getenv('SERVER_ADDR') !== getenv('REMOTE_ADDR')) {
            $query->andWhere(new \yii\db\Expression('CASE WHEN class="app\\models\\Goods" THEN 1 ELSE visible=1 END'));
        }
        $cpuModel = $query->one();
        if (!$cpuModel) {
            throw new \yii\web\HttpException(404);
        }
        $model = $cpuModel->findOwner();
        $model->CpuModel = $cpuModel;
        Misc::setMetaData($model->getPageTitle(), $model->getMetaKeywords(), $model->getMetaDescr());
        Misc::setMetaSocial($model->getPageTitle(), $model->getMetaDescr(), $model->getMetaSrc());
        return $model;
    }

    public function findOwner() {
        return call_user_func_array([$this->class, 'findOne'], [$this->id]);
    }

}
