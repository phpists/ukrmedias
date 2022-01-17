<?php

namespace app\models;

use yii\data\ActiveDataProvider;
use \Yii;

class Slider extends \app\components\BaseActiveRecord {

    const PLACE_MAINPAGE = 1;

    use \app\components\T_FileAttributesMisc;

    static public $placeLabels = [
        self::PLACE_MAINPAGE => 'Стартова сторінка',
    ];

    public static function tableName() {
        return 'slider';
    }

    public function filesConfig() {
        return array(
            'photo' => [
                'scenario' => ModelFiles::SCENARIO_IMG, 'label' => 'Зображення 1090x415', 'resize' => [
                    'small' => [250, 100, true, '#ffffff'],
                ],
            ],
        );
    }

    public function rules() {
        return [
            [['url'], 'required'],
            [['url'], 'string', 'max' => 1000],
            ['place_id', 'in', 'range' => array_keys(self::$placeLabels)],
            ['visible', 'boolean'],
            ['place_id', 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels() {
        return [
            'place_id' => 'Місце розміщення',
            'url' => 'УРЛ',
            'visible' => 'Відображення',
        ];
    }

    public function search() {
        $query = self::find()->orderBy(['place_id' => SORT_ASC, 'pos' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false,
        ]);
        return $dataProvider;
    }

    static public function findList($place_id) {
        return self::find()->where(['place_id' => $place_id, 'visible' => 1])->orderBy(['pos' => SORT_ASC])->all();
    }

    public function getUrl() {
        return $this->url;
    }

    public function getVisible() {
        return array_key_exists($this->visible, self::$valuesTrueFalse) ? self::$valuesTrueFalse[$this->visible] : $this->visible;
    }

    public function getPlace() {
        return self::$placeLabels[$this->place_id];
    }

    public function deleteModel() {
        return is_numeric($this->delete());
    }

    public function saveModel() {
        return $this->save();
    }

}
