<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;

class GoodsFilter {

    const SORT_PRICE_ASC = '0';
    const SORT_PRICE_DESC = '1';
    const SORT_NOVELTY = '2';

    static public $brands = 'b';
    static public $cats = 'c';
    static public $params = 'p';
    static public $novelty = 'n';
    static public $promo = 'promo';
    static public $pageSize = 's';
    static public $sortOrder = 'o';
    static public $css = 'css';
    static public $pageSizeLabels = [
        '15' => '15',
        '30' => '30',
        '45' => '45',
    ];
    static public $sortLabels = [
        self::SORT_PRICE_ASC => 'ціна від меншої до більшої',
        self::SORT_PRICE_DESC => 'ціна від більшої до меншої',
        self::SORT_NOVELTY => 'спочатку новинки',
    ];
    //
    protected $stateKey;
    protected $state;
    protected $containerId;
    protected $_values = [];
    protected $category;
    protected $brand_id;
    protected $search;

    public function __construct($category, $brand_id = null, $q = null, $isPromo = false, $isNovelty = false) {
        $this->stateKey = "{$category->id}_{$brand_id}_{$isPromo}_{$isNovelty}";
        $this->containerId = 'js_goods_filter_result';
        $this->category = $category;
        $this->brand_id = $brand_id;
        $this->search = $q;
        if ($isPromo) {
            $this->set(self::$promo, 1);
        }
        if ($isNovelty) {
            $this->set(self::$novelty, 1);
        }
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $this->saveState();
        } else {
            $this->restoreState();
        }
    }

    static protected function getNames() {
        return [self::$brands, self::$cats, self::$params, self::$novelty, self::$pageSize, self::$sortOrder, self::$css];
    }

    public function get($name) {
        if (isset($this->_values[$name])) {
            return $this->_values[$name];
        }
        if (!isset($this->state[$this->stateKey])) {
            $this->saveState();
        }
        $params = $this->state[$this->stateKey];
        $value = array_key_exists($name, $params) ? $params[$name] : null;
        switch ($name):
            case self::$cats:
            case self::$brands:
                if ($value === null || count($value) === 0) {
                    $value = Yii::$app->request->get($name);
                }
                break;
        endswitch;
        switch ($name):
            case self::$cats:
            case self::$params:
                $value = (array) $value;
                break;
            case self::$brands:
                $value = $this->brand_id === null ? (array) $value : [$this->brand_id];
                break;
        endswitch;
        if ($value === null) {
            $value = $this->defineDefaultValue($name);
        }
        return $this->_values[$name] = $value;
    }

    public function set($name, $value) {
        $this->_values[$name] = $value;
    }

    protected function defineDefaultValue($name) {
        switch ($name):
            case self::$pageSize:
                $value = '30';
                break;
            case self::$sortOrder:
                $value = self::SORT_PRICE_ASC;
                break;
            default:
                $value = '';
        endswitch;
        return $value;
    }

    public function isChecked($name, $value) {
        $searched = $this->get($name);
        if (is_array($value)) {
            $param_id = key($value);
            if (!isset($searched[$param_id]) || !is_array($searched[$param_id])) {
                return false;
            }
            return in_array(current($value), $searched[$param_id]);
        }
        if (is_array($searched)) {
            return in_array($value, $searched);
        }
        return $value == $searched;
    }

    protected function getValues() {
        $params = array(
            self::$brands => $this->get(self::$brands),
            self::$cats => $this->get(self::$cats),
            self::$params => $this->get(self::$params),
            self::$novelty => $this->get(self::$novelty),
            self::$pageSize => $this->get(self::$pageSize),
            self::$sortOrder => $this->get(self::$sortOrder),
        );
        return $params;
    }

    /**
     * @return CActiveDataProvider
     */
    public function getQuery($price_type_id) {
        $model = new Goods();
        $model->setScenario('search');
        $query = Goods::find();
        $query->alias('g');
        $query->leftJoin('goods_params', 'goods_params.goods_id = g.id');
        $query->select("g.*, goods_params.value as model");
        $query->andWhere(['goods_params.param_id' => 'Модель']);


        if (count($this->get(self::$brands)) > 0) {
            $query->andWhere(['in', 'brand_id', $this->get(self::$brands)]);
        }
        if (count($this->get(self::$cats)) > 0) {
            $query->andWhere(['in', 'cat_id', $this->get(self::$cats)]);
        }
        foreach ($this->get(self::$params) as $param_id => $values) {
            if (count($values) === 0) {
                continue;
            }
            $pid = md5($param_id);
            $p = [":pid{$pid}" => $param_id];
            foreach ($values as $h => $value) {
                $p[":hash{$pid}_{$h}"] = $value;
            }
            $vals = implode(',', array_keys($p));
            $query->andWhere("id IN( SELECT goods_id FROM goods_params WHERE param_id=:pid{$pid} AND hash IN({$vals}) )", $p);
        }
        if ($this->get(self::$promo) <> '') {
            $query->andWhere('has_promo=1');
        } elseif ($this->get(self::$novelty) <> '') {
            $query->andWhere('has_new=1');
        } else {
            $query->andWhere(['in', 'cat_id', $this->category->getIdsDown()]);
        }
        if ($this->search <> '') {
            $query->where('article LIKE :q OR code LIKE :q OR title LIKE :qq', [':q' => "{$this->search}%", ':qq' => "%{$this->search}%"]);
        }
        switch ($this->get(self::$sortOrder)):
            case self::SORT_PRICE_ASC:
                $query->leftJoin(['gp' => 'goods_prices'], 'g.id=gp.goods_id AND gp.price_type_id=:price_type_id', [':price_type_id' => $price_type_id]);
                $query->orderBy(new \yii\db\Expression('IFNULL(gp.price,g.price) ASC'));
                break;
            case self::SORT_PRICE_DESC:
                $query->leftJoin(['gp' => 'goods_prices'], 'g.id=gp.goods_id AND gp.price_type_id=:price_type_id', [':price_type_id' => $price_type_id]);
                $query->orderBy(new \yii\db\Expression('IFNULL(gp.price,g.price) DESC'));
                break;
            case self::SORT_NOVELTY:
                $query->orderBy(['has_new' => SORT_DESC]);
                break;
            default;
        endswitch;
        $query->andWhere(Goods::getPublicCondition());
        $query->groupBy(new \yii\db\Expression('brand_id, cat_id, CASE WHEN pic<>"" THEN pic ELSE id END'));
        return $query;
    }

    public function getDataProvider($price_type_id) {

        return new ActiveDataProvider([
            'query' => $this->getQuery($price_type_id),
            'pagination' => [
                'pageSize' => $this->get(self::$pageSize),
            ],
            'sort' => false,
        ]);
    }

    public function getQueryModelItems($price_type_id)
    {
        return $this->getQuery($price_type_id);
    }

    public function saveState($data = null) {
        $this->state = (array) Yii::$app->session->get(__CLASS__);
        foreach (self::getNames() as $name) {
            if (!is_array($data) || !isset($data[$this->stateKey][$name])) {
                $v = Yii::$app->request->post($name);
            } else {
                $v = $data[$this->stateKey][$name];
            }
            switch ($name):
                case self::$brands:
                case self::$params:
                case self::$cats:
                    $value = array_filter((array) $v);
                    break;
                default :
                    $value = $v;
            endswitch;
            $this->state[$this->stateKey][$name] = $value;
        }
        if ($data === null) {
            $this->state[$this->stateKey]['_GET']['page'] = @$_GET['page'];
            $this->state[$this->stateKey]['_GET']['per-page'] = @$_GET['per-page'];
        }
        Yii::$app->session->set(__CLASS__, $this->state);
    }

    public function restoreState() {
        $this->state = (array) Yii::$app->session->get(__CLASS__);
        if (!isset($this->state[$this->stateKey]) && count($this->state) > 0) {
            $this->resetState();
            return;
        }
        if (isset($this->state[$this->stateKey]['_GET']['page'])) {
            $_GET['page'] = $this->state[$this->stateKey]['_GET']['page'];
            $_GET['per-page'] = $this->state[$this->stateKey]['_GET']['per-page'];
        }
        return $this->state;
    }

    public function resetState() {
        $this->state = [];
        $this->state[$this->stateKey] = [];
        Yii::$app->session->set(__CLASS__, $this->state);
        $_GET['page'] = null;
        $_GET['per-page'] = null;
    }

    static public function resetStateDirect() {
        Yii::$app->session->set(__CLASS__, null);
    }

    public function beginForm() {
        $id = 'js_goods_filter_form';
        echo Html::beginTag('form', [
            'id' => $id,
            'onSubmit' => 'return false;',
            'onChange' => 'applyFilter(this, this.action);',
            'autocomplete' => 'off',
        ]);
        Yii::$app->view->registerJs('
            if(typeof window.GoodsFilterInit === "function"){
                window.GoodsFilterInit();
            }
            function applyFilter(form, url) {
                if(typeof window.GoodsFilterBefore === "function"){
                    window.GoodsFilterBefore();
                }
                $.post(url, $(form).serialize(), function(resp){
                    parseResponse("' . $this->containerId  . '", resp);
                    if(typeof window.GoodsFilterAfter === "function"){
                        window.GoodsFilterAfter();
                    }
                });
            }', \yii\web\View::POS_END, $id);
        Yii::$app->view->registerJs('
            document.addEventListener("click", function(event) {
                var a=event.target.closest("#' . $this->containerId . ' li a");
                if(a!==null){
                    event.preventDefault();
                    applyFilter(a.closest("form"),a.href);
                }
            })', \yii\web\View::POS_END);
    }

    public function endForm() {
        echo Html::endTag('form');
    }

    public function beginResult() {
        echo '<div id="' . $this->containerId . '">';
    }

    public function endResult() {
        echo '<div>';
    }

    public function getCss() {
        return $this->get(self::$css);
    }

    public function checkbox($pName, $pValue) {
        if (is_array($pValue)) {
            $k = key($pValue);
            $v = current($pValue);
            $name = "{$pName}[{$k}][]";
        } else {
            $name = "{$pName}[]";
            $v = $pValue;
        }
        $id = preg_replace('/[^a-z0-9]/', '_', "{$name}-{$v}");
        echo Html::checkbox($name, $this->isChecked($pName, $pValue), ['id' => $id, 'value' => $v]);
    }

    public function getDirectUrl() {
        if ($this->get(self::$promo) == '' && $this->get(self::$novelty) == '') {
            return false;
        }
        $params[] = '/' . Yii::$app->controller->getRoute();
        foreach (self::getNames() as $name) {
            switch ($name):
                case self::$brands:
                case self::$cats:
                    $params[$name] = $this->get($name);
                    break;
            endswitch;
        }
        return urldecode(Url::toRoute($params));
    }

}
