<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\components;

use \Yii;
use yii\helpers\Html;
use yii\helpers\Url;

class AdminActionColumn extends \yii\grid\ActionColumn {

    /**
     * {@inheritdoc}
     */
    public function init() {
        parent::init();
        $this->initDefaultButtons();
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons() {
        $this->initDefaultButton('view', 'fa fa-eye-open');
        $this->initDefaultButton('update', 'fa fa-pencil');
        $this->initDefaultButton('delete', 'fa fa-trash');
    }

    /**
     * Initializes the default button rendering callback for single button.
     * @param string $name Button name as it's written in template
     * @param string $iconName The part of Bootstrap glyphicon class that makes it unique
     * @param array $additionalOptions Array of additional options
     * @since 2.0.11
     */
    protected function initDefaultButton($name, $iconName, $additionalOptions = []) {
        if (!isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false) {
            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $additionalOptions) {
                switch ($name) {
                    case 'view':
                        $title = Yii::t('yii', 'View');
                        break;
                    case 'update':
                        $title = Yii::t('yii', 'Update');
                        break;
                    case 'delete':
                        $title = Yii::t('yii', 'Delete');
                        break;
                    default:
                        $title = ucfirst($name);
                }
                $options = array_merge([
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                        ], $additionalOptions, $this->buttonOptions);
                $icon = Html::tag('span', '', ['class' => $iconName]);
                return Html::a($icon, $url, $options);
            };
        }
    }

}
