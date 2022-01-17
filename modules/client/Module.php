<?php

namespace app\modules\client;

class Module extends \yii\base\Module {

    public function init() {
        parent::init();
        $this->get('errorHandler')->errorAction = 'client/site/error';
        \Yii::setAlias('@module', '@app/modules/client');
    }

}
