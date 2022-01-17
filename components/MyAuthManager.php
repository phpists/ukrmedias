<?php

namespace app\components;

class MyAuthManager extends \yii\rbac\PhpManager {

    public function checkAccess($userId, $permissionName, $params = []) {
        $assignments = $this->getAssignments($userId);
        return $this->checkAccessRecursive($userId, $permissionName, $params, $assignments);
    }

    protected function checkAccessRecursive($user, $itemName, $params, $assignments) {
        if (!isset($this->items[$itemName]) || \Yii::$app->user->isGuest) {
            return false;
        }

        if ($itemName == \Yii::$app->user->identity->role_id) {
            return true;
        }

        foreach ($this->children as $parentName => $children) {
            if (isset($children[$itemName]) && $this->checkAccessRecursive($user, $parentName, $params, $assignments)) {
                return true;
            }
        }

        return false;
    }

}
