<?php

namespace app\components;

use yii\helpers\Html;

class FrontendGrid extends \yii\grid\GridView {

    public $dataColumnClass = '\app\components\AppDataColumn';
    public $summary = '{begin, number}-{end, number} / {totalCount, number}';
    public $layout = "{items}{summary}{pager}";
    public $emptyText = 'немає записів';
    public $cols = array();
    public $pager = [
        'linkContainerOptions' => ['class' => 'page-item'],
        'linkOptions' => ['class' => 'page-link'],
        'disabledListItemSubTagOptions' => ['class' => 'page-link'],
        'disableCurrentPageButton' => true,
    ];

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

    public function run() {
        $qty = $count = $this->dataProvider->getCount();
        if ($qty <= 0) {
            if (isset($this->options['class'])) {
                $this->options['class'] .= ' no-data';
            } else {
                $this->options['class'] = ' no-data';
            }
        }
        parent::run();
    }

}
