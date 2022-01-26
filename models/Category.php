<?php

namespace app\models;

use \Yii;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use creocoder\nestedsets\NestedSetsBehavior;

/**
 * ContactForm is the model behind the contact form.
 */
class Category extends \app\components\BaseActiveRecord {

    use \app\components\T_CpuModel;
    use \app\components\T_FileAttributesMisc;

    const ROOT_ID = '1';

    public $parent;
    public $parent_id;
    public $position_id;
    public $childrenList = [];
    static protected $groupedList = [];

    public function filesConfig() {
        if ($this->parent === null) {
            $this->initPosition();
        }
        return array(
            'cover_1' => [
                'scenario' => ModelFiles::SCENARIO_IMG, 'label' => 'Обкладинка 420x560px', 'default' => '/files/images/system/empty.png', 'resize' => [
                    'medium2' => [420, 560, true, '#ffffff'],
                    'medium' => [210, 280, true, '#ffffff'],
                    'small' => [68, 68, true, '#ffffff'],
                ],
            ],
        );
    }

    public static function tableName() {
        return 'category';
    }

    public function rules() {
        return [
            [['title'], 'required'],
            [['title', 'title_alt'], 'string', 'max' => 255],
            [['title', 'title_alt'], 'safe', 'on' => 'search'],
            [['parent_id', 'position_id'], 'safe'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels() {
        return [
            'title' => 'Найменування',
            'title_alt' => 'Аліас',
            'parent_id' => 'Родительська категорія',
            'position_id' => 'Позиція',
        ];
    }

    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                #'treeAttribute' => 'root',
                'leftAttribute' => 'lft',
                'rightAttribute' => 'rgt',
                'depthAttribute' => 'level',
            ],
        ];
    }

    public function search($root_id = self::ROOT_ID) {
        $query = self::find()->where(['root' => $root_id])->andWhere('level>1');
        $query->orderBy = ['lft' => SORT_ASC];
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false,
        ]);
        if ($this->load(Yii::$app->request->get())) {
            $query->andFilterWhere(['like', 'title', $this->title]);
            $query->andFilterWhere(['like', 'title_alt', $this->title_alt]);
        }
        return $dataProvider;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getAdminTitle() {
        return str_repeat('- ', $this->level - 2) . " {$this->title}";
    }

    public function getSiteTitle() {
        return $this->title_alt <> '' ? $this->title_alt : $this->title;
    }

    public function initPosition($root = self::ROOT_ID) {
        $this->parent = $this->isNewRecord ? self::findOne($root) : $this->parents(1)->one();
        $this->parent_id = $this->parent->id;
        $this->root = $root;
        $this->position_id = (int) self::find()
                        ->select('id')
                        ->where('root=:root AND level=:level', [':root' => $this->root, ':level' => $this->isNewRecord ? $this->parent->level + 1 : $this->level])
                        ->andWhere('rgt=:rgt', [':rgt' => $this->lft - 1])
                        ->scalar();
    }

    static public function import($attrs) {
        $model = self::findModel(['id_1s' => $attrs['category_id']]);
        $model->setAttributes(['id_1s' => $attrs['category_id'], 'title' => $attrs['category']], false);
        $model->getCpuModel(true);
        $model->getCpuModel()->setScenario('import');
        $model->initPosition();
        if ($model->isNewRecord) {
            $model->visible = 1;
        }
        $r = $model->saveModel();
        $model->addErrors($model->CpuModel->getErrors());
        return $model;
    }

    public function saveModel() {
        $this->parent = self::findOne($this->parent_id);
        $prev = $this->position_id > 0 ? self::findOne($this->position_id) : null;
        if ($this->isNewRecord && $prev === null) {
            $prev = $this->parent->children(1)->orderBy(['lft' => SORT_DESC])->limit(1)->one();
        }
        $res = $this->validate() && $this->CpuModel->validate();
        if (!$res) {
            return $res;
        }
        if ($this->isRoot()) {
            $this->save(false);
        } else {
            if ($prev === null) {
                $res = $this->prependTo($this->parent, false);
            } else {
                $res = $this->insertAfter($prev, false);
            }
        }
        if ($res) {
            $res = $this->CpuModel->saveModel($this);
        }
        if ($res) {
            $this->updateVisibility();
        }
        return $res;
    }

    protected function updateVisibility() {
        $ids = $this->children()->select('id')->column();
        Yii::$app->db->createCommand()->update(self::tableName(), ['visible_by_parent' => $this->visible], ['in', 'id', $ids])->execute();
    }

    public function deleteModel() {
        $root = self::findOne(self::ROOT_ID);
        foreach (self::find()->where('id_1s<>"" AND lft>:lft AND rgt<:rgt', [':lft' => $this->lft, ':rgt' => $this->rgt])->all() as $model) {
            $model->appendTo($root, false);
        }
        #if ($this->isRoot()) {
        $res = is_numeric($this->deleteWithChildren());
        #} else {
        #    $res = $this->delete();
        #}
        if ($res) {
            Cpu::deleteAll(['id' => $this->id, 'class' => get_called_class()]);
        }
        return $res;
    }

    public function getParentList() {
        $list = self::find()
                ->select(['id', new \yii\db\Expression('CONCAT(REPEAT("- ",level-1)," ", title) AS title')])
                ->where('root=:root', [':root' => $this->root])
                //->andWhere('level<' . self::MAX_LEVEL)
                ->andWhere('id<>:id', [':id' => (int) $this->id])
                ->orderBy('lft')
                ->asArray()
                ->all();
        $data = array();
        foreach ($list as $d) {
            $data[$d['id']] = $d['title'];
        }
        return $data;
    }

    public function getPositionList($id) {
        $query = self::find()
                ->select('id, title')
                ->where('root=:root AND level=:level', [':root' => $this->root, ':level' => $this->level + 1])
                ->andWhere('id<>:id', [':id' => (int) $id])
                ->orderBy('lft')
                ->asArray()
        ;
        $data = [
            0 => 'перша',
        ];
        foreach ($query->all() as $row) {
            $data[$row['id']] = "після {$row['title']}";
        }
        return $data;
    }

    public function isExternal() {
        return strlen($this->id_1s) > 0;
    }

    public function isVisible() {
        return $this->visible > 0 && $this->visible_by_parent > 0;
    }

    public function isAllowDelete() {
        return $this->isExternal() === false;
    }

    static public function createTree($list) {
        $treeViewData = array();
        $tree = &$treeViewData;
        $node = & $tree;
        $position = array();
        $lastitem = '';
        $depth = 0;

        foreach ($list as $rawItem) {
            if ($rawItem->level > $depth) {
                $position[] = & $node; //$lastitem;
                $depth = $rawItem->level;
                $node = & $node[$lastitem]['children'];
            } else {
                while ($rawItem->level < $depth) {
                    end($position);
                    $node = & $position[key($position)];
                    array_pop($position);
                    $depth = $node[key($node)]['model']->level;
                }
            }
            $node[$rawItem->id]['model'] = $rawItem;
            if (!isset($node[$rawItem->id]['children'])) {
                $node[$rawItem->id]['children'] = [];
            }
            $lastitem = $rawItem->id;
        }

        $tree = (array) array_shift($tree);
        return isset($tree['children']) ? $tree['children'] : [];
        #return $tree;
        #$tree = (array) array_shift($tree);
        #$tree = array_key_exists('children', $tree) ? $tree['children'] : array();
        #return $tree;
    }

    public function findList() {
        $key = '_catalog_tree_data';
        $data = Yii::$app->cache->get($key);
        if ($data === false) {
            $data = self::createTree($this->children(4)->andWhere('visible=1 AND visible_by_parent=1 AND visible_by_goods=1')->all());
            Yii::$app->cache->set($key, $data);
        }
        return $data;
    }

    public function findChildrenAlt($ids) {
        return $this->children(1)->andWhere(['IN', 'id', $ids])->andWhere('visible=1 AND visible_by_parent=1 AND visible_by_goods=1')->all();
    }

    public function findChildren($depth = 1) {
        $key = "_category_children{$this->id}";
        $data = Yii::$app->cache->get($key);
        if ($data === false) {
            $data = $this->children($depth)->andWhere('visible=1 AND visible_by_parent=1 AND visible_by_goods=1')->all();
            Yii::$app->cache->set($key, $data);
        }
        return $data;
    }

    public function getIdsUp($self = false) {
        $ids = $this->parents()->select('id')->asArray()->column();
        if ($self) {
            $ids[] = $this->id;
        }
        return $ids;
    }

    public function getIdsDown($self = true, $visibleOnly = true) {
        $query = $this->children()->select('id')->asArray();
        if ($visibleOnly) {
            $query->andWhere('visible=1 AND visible_by_parent=1 AND visible_by_goods=1');
        }
        $ids = $query->column();
        if ($self) {
            array_unshift($ids, $this->id);
        }
        return $ids;
    }

    public function findParents() {
        return $this->parents()->andWhere(['<>', 'id', self::ROOT_ID])->all();
    }

    public function findLevelData($list) {
        if (isset($list[$this->id])) {
            return $list[$this->id]['children'];
        }
        $id = (int) current(array_keys(array_intersect_key($list, array_flip($this->getIdsUp()))));
        return isset($list[$id]) ? $list[$id]['children'] : [];
    }

    static public function getGroupedList($whereGoods, $ids = null) {
        $key = $whereGoods . json_encode($ids);
        if (!array_key_exists($key, self::$groupedList)) {
            self::$groupedList[$key] = [];
            $levelData = null;
            $level = null;
            foreach (self::find()->where('visible=1 AND visible_by_parent=1 AND visible_by_goods=1 AND level>1')->orderBy('lft')->all() as $cat) {
                if ($cat->level == 2) {
                    if (is_array($levelData) && count($levelData['children']) > 0) {
                        self::$groupedList[$key][] = $levelData;
                    }
                    $levelData = ['title' => $cat->getSiteTitle(), 'children' => []];
                } elseif ($cat->isLeaf()) {
                    $q1 = Goods::find()->alias('g')->where(['cat_id' => $cat->id])->andWhere(Goods::getPublicCondition())->andWhere($whereGoods)->exists();
                    $q2 = $ids === null ? true : in_array($cat->id, $ids);
                    if ($q1 && $q2) {
//                        if ($level !== null && $level <> $cat->level) {
//                            $level = $cat->level;
//                            $levelData['children'][] = '';
//                        }
                        $levelData['children'][$cat->id] = $cat->getSiteTitle();
                    }
                }
                $level = $cat->level;
            }
            if (count($levelData['children']) > 0) {
                self::$groupedList[$key][] = $levelData;
            }
        }
        return self::$groupedList[$key];
    }

    static public function getDataModelColor($color)
    {
        $color = explode('/', $color);
        return isset($color[1]) ? $color[1] : $color;
    }
}
