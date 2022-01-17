<?php

namespace app\components;

use Yii;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;

class AdminGrid extends \yii\grid\GridView {

    public $cols = array();
    public $summary = '<div class="text-right">{begin, number}-{end, number} / {totalCount, number}</div>';
    public $layout = "{items}\n{summary}\n\n{pager}\n";
    public $emptyText = 'немає записів';
    public $pager = [
        'linkContainerOptions' => ['class' => 'page-item'],
        'linkOptions' => ['class' => 'page-link'],
        'disabledListItemSubTagOptions' => ['class' => 'page-link'],
        'disableCurrentPageButton' => true,
    ];
    public $sortUrl;
    public $sortMode;

    public function init() {
        $this->sortMode = Yii::$app->request->get('sortmode');
        if ($this->sortMode) {
            \app\components\assets\AdminSortableAssets::register(Yii::$app->view);
            $this->filterModel = null;
            $this->dataProvider->pagination = false;
            array_pop($this->columns);
            array_unshift($this->columns, ['header' => '№', 'value' => function($model, $key, $index) {
                    return $index + 1;
                }]);
            $this->options['class'] .= ' admin-sortable-grid pointer';
            $this->options['data-sorturl'] = Url::toRoute($this->sortUrl);
        }
        parent::init();
        if ($this->sortMode) {

        }
    }

    public function run() {
        Pjax::begin([
            'id' => 'pjax-' . $this->getId(),
            'enablePushState' => false,
            'enableReplaceState' => false,
        ]);
        parent::run();
        Pjax::end();
    }

    public function renderTableHeader() {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderHeaderCell();
        }
        $cols = '<colgroup>';
        foreach ($this->cols as $w) {
            $cols .= '<col width="' . $w . '"/>';
        }
        $cols .= '</colgroup>';
        $content = Html::tag('tr', implode('', $cells), $this->headerRowOptions);
        if ($this->filterPosition === self::FILTER_POS_HEADER) {
            $content = $this->renderFilters() . $content;
        } elseif ($this->filterPosition === self::FILTER_POS_BODY) {
            $content .= $this->renderFilters();
        }

        return "{$cols}<thead>\n" . $content . "\n</thead>";
    }

    public function renderFilters() {
        if ($this->filterModel !== null) {
            $cells = [];
            $filter = false;
            foreach ($this->columns as $column) {
                if ($column instanceof \yii\grid\DataColumn && $column->filter === false) {
                    if (!isset($column->filterOptions['class'])) {
                        $column->filterOptions['class'] = '';
                    }
                    $column->filterOptions['class'] .= 'no-filter';
                } elseif ($column instanceof \yii\grid\DataColumn && $column->filter !== false) {
                    $filter = true;
                }
                $cells[] = $column->renderFilterCell();
            }
            $mobContent = $filter ? Html::tag('tr', '<td colspan="' . count($this->columns) . '"><span class="fa fa-filter"></span></td>', [
                        'class' => 'mobile-filter-btn cursor blue',
                        'onClick' => '$(this).next().slideToggle();',
                    ]) : null;
            return $mobContent . Html::tag('tr', implode('', $cells), $this->filterRowOptions);
        }

        return '';
    }

}
