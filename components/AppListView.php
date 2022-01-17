<?php

namespace app\components;

use Yii;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class AppListView extends \yii\widgets\ListView {

    public $summary = '<div class="">{begin, number}-{end, number} / {totalCount, number}</div>';
    public $layout = '<div class="inform-table-wrap">{sorter}<div class="staff inform-table-list">{items}</div>{summary}{pager}</div>';
    public $emptyText = 'немає записів';
    public $headerTpl;
    public $itemOptions = [
        'tag' => false,
    ];
    public $pager = [
        'options' => ['class' => 'page_selection'],
        'linkContainerOptions' => ['class' => 'page'],
        'linkOptions' => ['class' => ''],
        'disabledListItemSubTagOptions' => ['class' => ''],
        'disableCurrentPageButton' => true,
        'activePageCssClass' => 'active',
        'nextPageCssClass' => 'arrow',
        'prevPageCssClass' => 'arrow',
        'prevPageLabel' => '<img src="img/orders/arrow-left.svg" alt="">',
        'nextPageLabel' => '<img src="img/orders/arrow-right.svg" alt="">',
    ];
    public $filterModel;
    public $headerAttrs = [];
    public $filter = [];
    protected $_sorter;

    public function run() {
        Pjax::begin([
            'id' => 'pjax-' . $this->getId(),
            'enablePushState' => false,
            'enableReplaceState' => false,
        ]);
        parent::run();
        Pjax::end();
        if (count($this->filter) > 0) {
            $this->getView()->registerJs('$(document).on("change","#pjax-' . $this->getId() . ' form", function(event) {
                    $.pjax.submit(event, "#pjax-' . $this->getId() . '",{push:false,replace:false});
                })');
        }
        $this->getView()->registerJs('$(document).on("pjax:complete", function() {})');
    }

    public function getSorter() {
        if ($this->_sorter === null) {
            $this->_sorter = $this->dataProvider->getSort();
        }
        return $this->_sorter;
    }

    public function getSortLink($attr) {
        $sorter = $this->getSorter();
        if (($direction = $sorter->getAttributeOrder($attr)) !== null) {
            $class = $direction === SORT_DESC ? 'desc' : 'asc';
            if (isset($options['class'])) {
                $options['class'] .= ' ' . $class;
            } else {
                $options['class'] = $class;
            }
        }
        $options['data-sort'] = $sorter->createSortParam($attr);
        return [
            'url' => $sorter->createUrl($attr),
            'options' => $options,
            'direction' => $direction,
        ];
    }

    public function isSortable($attr) {
        if (!$this->getSorter()) {
            return false;
        }
        return array_key_exists($attr, $this->getSorter()->attributes);
    }

//    public function renderEmpty() {
//        $options = $this->emptyTextOptions;
//        $tag = ArrayHelper::remove($options, 'tag', 'div');
//        $div = Html::tag($tag, $this->emptyText, $options);
//        if (count($this->filter) > 0) {
//            $header = $this->render('/layouts/_grid_header', ['widget' => $this, 'searchModel' => $this->searchModel]);
//            return "{$header}{$div}";
//        } else {
//            return $div;
//        }
//    }



    public function renderSorter() {
        return $this->render($this->headerTpl, ['widget' => $this, 'filterModel' => $this->filterModel]);
    }

}
