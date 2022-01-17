<?php

namespace app\components;

use yii\filters\AccessControl;
use \Yii;

class AbonentController extends BaseController {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'roles' => [Auth::ROLE_ABONENT],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

}
