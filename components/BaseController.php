<?php

namespace app\components;

use yii\web\Controller;

class BaseController extends Controller {

    public function afterAction($action, $result) {
        UsersActions::add();
        return parent::afterAction($action, $result);
    }

}
