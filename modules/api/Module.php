<?php

namespace app\modules\api;

class Module extends \yii\base\Module {

    public function init() {
        parent::init();
        \Yii::$app->request->enableCsrfValidation = false;
        $this->get('errorHandler')->errorAction = 'api/system/error';
    }

}
