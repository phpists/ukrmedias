<?php

namespace app\components;

class ProjectSession extends \yii\web\DbSession {

    public function writeSession($id, $data) {
        if (\Yii::$app->user->isGuest || !\Yii::$app->user->identity) {
            $this->fields['user_id'] = null;
        } else {
            $this->fields['user_id'] = \Yii::$app->user->identity->getId();
        }
        return parent::writeSession($id, $data);
    }

}
