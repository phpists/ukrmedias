<?php

namespace app\modules\admin;

class Module extends \yii\base\Module {

    public function init() {
        parent::init();
//        \Yii::configure($this, [
//            'components' => [
//                'errorHandler' => [
//                    'class' => \yii\web\ErrorHandler::className(),
//                     'errorAction' => 'admin/site/error',
//                ]
//            ],
//        ]);

        $this->get('errorHandler')->errorAction = 'admin/site/error';
    }

}
