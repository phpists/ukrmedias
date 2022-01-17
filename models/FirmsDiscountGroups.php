<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\components\BaseActiveRecord;
use app\components\Misc;

class FirmsDiscountGroups extends BaseActiveRecord {

    public static function tableName() {
        return 'discount_groups';
    }

    static public function saveList($firm_id, $data) {
        $ids = [];
        foreach ($data as $row) {
            $ids[] = $row['id'];
            $model = self::findModel($row['id']);
            $model->id = $row['id'];
            $model->title = $row['title'];
            $model->save();
            Yii::$app->db->createCommand('INSERT INTO firms_discount_groups (firm_id,group_id,discount)VALUES(:id,:group_id,:discount) ON DUPLICATE KEY UPDATE discount=:discount', [':id' => $firm_id, ':group_id' => $row['id'], ':discount' => $row['discount']])->execute();
        }
        if (count($ids) > 0) {
            Yii::$app->db->createCommand()->delete('firms_discount_groups', ['AND', 'firm_id=:firm_id', ['NOT IN', 'group_id', $ids]], [':firm_id' => $firm_id])->execute();
        } else {
            Yii::$app->db->createCommand('DELETE FROM firms_discount_groups WHERE firm_id=:id', [':id' => $firm_id])->execute();
        }
    }

    static public function keyval($firm_id) {
        $query = Yii::$app->db->getMasterPdo()->prepare('SELECT id, title FROM ' . self::tableName() . ' WHERE id IN (SELECT group_id FROM firms_discount_groups WHERE firm_id=:id) ORDER BY title');
        $query->execute([':id' => $firm_id]);
        return $query->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

}
