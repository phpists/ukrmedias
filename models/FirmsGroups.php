<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\components\BaseActiveRecord;
use app\components\Misc;

class FirmsGroups extends BaseActiveRecord {

    public static function tableName() {
        return 'firms_groups';
    }

    static public function saveList($firm_id, $data) {
        $ids = [];
        foreach ($data as $row) {
            $ids[] = $row['id'];
            Yii::$app->db->createCommand('INSERT INTO ' . self::tableName() . ' (id,group_id,title)VALUES(:id,:group_id,:title) ON DUPLICATE KEY UPDATE title=:title', [':id' => $firm_id, ':group_id' => $row['id'], ':title' => $row['title']])->execute();
        }
        if (count($ids) > 0) {
            Yii::$app->db->createCommand()->delete(self::tableName(), ['AND', 'id=:firm_id', ['NOT IN', 'group_id', $ids]], [':firm_id' => $firm_id])->execute();
        } else {
            Yii::$app->db->createCommand('DELETE FROM ' . self::tableName() . ' WHERE id=:id', [':id' => $firm_id])->execute();
        }
    }

    static public function keyval($firm_id) {
        $query = Yii::$app->db->getMasterPdo()->prepare('SELECT group_id, title FROM ' . self::tableName() . ' WHERE id=:id ORDER BY title');
        $query->execute([':id' => $firm_id]);
        return $query->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

}
