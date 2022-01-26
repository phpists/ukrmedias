<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class Brands extends \app\components\BaseActiveRecord
{

    use \app\components\T_CpuModel;
    use \app\components\T_FileAttributesMisc;

    public function filesConfig()
    {
        return array(
            'logo' => [
                'scenario' => ModelFiles::SCENARIO_IMG, 'label' => 'Логотип 280x280px', 'resize' => [
                    'medium' => [280, 280, true, '#ffffff'],
                    'small' => [100, 100, true, '#ffffff'],
                ],
            ],
        );
    }

    public static function tableName()
    {
        return 'brands';
    }

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 45],
            [['title'], 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Найменування',
        ];
    }

    public function search()
    {
        $query = self::find()->orderBy(['title' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false,
        ]);
        if ($this->load(Yii::$app->request->get())) {
            $query->andFilterWhere(['like', 'title', $this->title]);
        }
        return $dataProvider;
    }

    public function getTitle()
    {
        return $this->title;
    }

    static public function import($attrs)
    {
        $model = self::findModel($attrs['brand_id']);
        $model->setAttributes(['id' => $attrs['brand_id'], 'title' => $attrs['brand']], false);
        $model->getCpuModel(true);
        $model->getCpuModel()->setScenario('import');
        $res = $model->save(false);
        if ($res) {
            $model->CpuModel->visible = 1;
            $res = $model->CpuModel->saveModel($model);
        }
        return $model;
    }

    static public function changeVisibleBrandGoods($brand, $visible)
    {
        $query = new Query;

        $query->select('goods.id')
            ->from('goods')
            ->leftJoin('cpu', 'goods.brand_id = cpu.id')
            ->where(['cpu.id' => $brand['id']]);

        $goodsIds = $query->all();

        Yii::$app->db->createCommand()
            ->update('cpu', ['visible' => $visible], ['in', 'id', $goodsIds])
            ->execute();

    }

    public function saveModel()
    {
        $res = $this->validate() && $this->CpuModel->validate();
        if (!$res) {
            return $res;
        }
        $res = $this->save(false);
        if ($res) {

            $visible = $_POST['Cpu']['visible'];
            $this->CpuModel->visible = $visible ?? 1;

            self::changeVisibleBrandGoods($this, $visible);

            $res = $this->CpuModel->saveModel($this);
        }
        return $res;
    }

    public function deleteModel()
    {
        $res = is_numeric($this->delete());
        if ($res) {
            Cpu::deleteAll(['id' => $this->id, 'class' => get_called_class()]);
        }
        return $res;
    }

    static public function keyval()
    {
        return Yii::$app->db->getMasterPdo()->query('SELECT DISTINCT id,title FROM ' . self::tableName() . ' ORDER BY title')->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    static public function findList()
    {
        $ids = (new \yii\db\Query)
            ->from('brand_cats_visibility as bcv')
            ->select('bcv.brand_id')
            ->leftJoin('cpu', 'bcv.brand_id = cpu.id')
            ->where(['cpu.visible_tile' => 1])
            ->distinct()
            ->column();

        return self::find()->where(['in', 'id', $ids])->all();
    }

//    static public function keyvalByCategory($cat_id) {
//        $query = Yii::$app->db->getMasterPdo()->prepare('SELECT DISTINCT id,title FROM ' . self::tableName() . ' WHERE id IN (SELECT DISTINCT brand_id FROM goods g WHERE cat_id=:cat_id AND ' . Goods::getPublicCondition() . ') ORDER BY title');
//        $query->execute([':cat_id' => $cat_id]);
//        return $query->fetchAll(\PDO::FETCH_KEY_PAIR);
//    }

    static public function keyvalByIds($ids)
    {
        if (count($ids) === 0) {
            return [];
        }
        foreach ($ids as $i => $id) {
            $p[":p{$i}"] = $id;
        }
        $vals = implode(',', array_keys($p));
        $query = Yii::$app->db->getMasterPdo()->prepare('SELECT DISTINCT id,title FROM ' . self::tableName() . " WHERE id IN ({$vals}) ORDER BY title");
        $query->execute($p);
        return $query->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    static public function keyvalAlt($where)
    {
        $query = Yii::$app->db->getMasterPdo()->query('SELECT DISTINCT id,title FROM ' . self::tableName() . ' WHERE id IN (SELECT DISTINCT brand_id FROM goods g WHERE ' . $where . ' AND ' . Goods::getPublicCondition() . ') ORDER BY title');
        return $query->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    public function findCategories($category)
    {
        $query = (new \yii\db\Query)->select('cat_id')->from('brand_cats_visibility')->where(['brand_id' => $this->id]);
        $query->andWhere(['in', 'cat_id', $category->getIdsDown()]);
        $data = $category->findChildrenAlt($query->column());
        return $data;
    }

//    public function getCatIds() {
//        $ids = Goods::find()->distinct()->select('cat_id')->where(['brand_id' => $this->id])->column();
//        $idsUp = [];
//        foreach ($ids as $id) {
//            $category = Category::findOne($id);
//            $idsUp = array_merge($idsUp, $category->getIdsUp(), [$id]);
//        }
//        return $idsUp;
//    }
}
