<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;
use app\components\RuntimeFiles;

class GoodsPhotoData extends \app\components\BaseActiveRecord {

    public static function tableName() {
        return 'photo_data';
    }

    static public function import($goods, $attrs) {
        $dataModel = new self();
        $ids = [];
        foreach ($attrs as $pos => $row) {
            $ids[] = $row['id'];
            $model = self::findModel(['goods_id' => $goods->id, 'id_1s' => $row['id']]);
            $imgdata = base64_decode($row['data']);
            if ($model->hash === md5($imgdata)) {
                ModelFiles::updateAll(['pos' => $pos], ['id' => $model->id]);
                continue;
            }
            $file = RuntimeFiles::get('goods_tmp_photos', uniqid('photo') . ".{$row['ext']}");
            $size = file_put_contents($file, $imgdata);
            if ($size === false) {
                $dataModel->addError('id', "Помилка запису файлу фотографії {$row['id']}.");
                break;
            }
            $fileModel = $goods->getModelFiles('photos');
            $fileModel->setIsNewRecord(true);
            $fileModel->id = null;
            $fileModel->file = new UploadedFile([
                'name' => basename($file),
                'tempName' => $file,
                'type' => mime_content_type($file),
                'size' => $size,
                'error' => \UPLOAD_ERR_OK,
            ]);

            if (!$model->isNewRecord) {
                $prevFileModel = ModelFiles::findOne($model->id);
                $fileModel->pos = $prevFileModel->pos;
                $goods->getModelFiles('photos')->deleteModel(['id' => $model->id]);
            } else {
                $fileModel->pos = $pos;
            }
            $res = $fileModel->saveFile(false);
            if (!$res) {
                unlink($file);
                $dataModel->addErrors($fileModel->getErrors());
                break;
            }
            unlink($file);
            $model->setIsNewRecord(true);
            $model->goods_id = $goods->id;
            $model->id_1s = $row['id'];
            $model->id = $fileModel->id;
            $model->hash = md5($imgdata);
            $model->main = $row['main'];
            $model->variant_id = (string) $row['variant_id'];
            $model->save(false);
        }
        self::clean($goods, $ids);
        return $dataModel;
    }

    static protected function clean($goods, $id1s) {
        $ids = self::find()->select('id')->where(['in', 'id_1s', $id1s])->column();
        foreach ($goods->getModelFiles('photos')->findMulti(null, ['NOT IN', 'id', $ids]) as $fileModel) {
            $fileModel->deleteModel();
        }
        foreach (self::find()->where(['goods_id' => $goods->id])->andWhere(['NOT IN', 'id', $ids])->all() as $model) {
            $model->delete();
        }
    }

}
