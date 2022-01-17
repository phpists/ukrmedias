<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\components;

use Closure;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQueryInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;

class AppDataColumn extends \yii\grid\DataColumn {

    /**
     * {@inheritdoc}
     */
    protected function renderHeaderCellContent() {
        if ($this->header !== null || $this->label === null && $this->attribute === null) {
            return parent::renderHeaderCellContent();
        }

        $label = $this->getHeaderCellLabel();
        if ($this->encodeLabel) {
            $label = Html::encode($label);
        }

        if ($this->attribute !== null && $this->enableSorting && ($sort = $this->grid->dataProvider->getSort()) !== false && $sort->hasAttribute($this->attribute)) {
            $html = '<span class="sort-type">
                        <span class="sort-type__item">
                            <svg width="10" height="5" viewBox="0 0 10 5" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.22936 0.262178C4.65497 -0.0873914 5.34503 -0.0873918 5.77064 0.262178L9.67861 3.47192C10.3652 4.03582 9.87892 5 8.90797 5L1.09203 5C0.121082 5 -0.365172 4.03582 0.321395 3.47192L4.22936 0.262178Z" fill="#918DA6"></path></svg>
                        </span>
                        <span class="sort-type__item">
                            <svg width="10" height="5" viewBox="0 0 10 5" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.77064 4.73782C5.34503 5.08739 4.65497 5.08739 4.22936 4.73782L0.321394 1.52808C-0.365172 0.964181 0.121082 -6.03771e-08 1.09203 0L8.90797 4.86021e-07C9.87892 5.46398e-07 10.3652 0.964181 9.67861 1.52808L5.77064 4.73782Z" fill="#918DA6"></path></svg>
                        </span>
                    </span>';
            return $sort->link($this->attribute, array_merge($this->sortLinkOptions, ['label' => $label . $html]));
        }

        return $label;
    }

}
