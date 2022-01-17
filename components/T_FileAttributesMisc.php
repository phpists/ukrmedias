<?php

namespace app\components;

use app\models\ModelFiles;
use Yii;

/**
 * Use with check box list only !
 * Save the multiple check box selection.
 */
trait T_FileAttributesMisc {

    protected $files;

    public function initFiles($forDelete = false) {
        if ($this->files === null) {
            foreach ($this->filesConfig() as $attr => $data) {
                $this->files[$attr] = ModelFiles::getInstance($this, $attr, $data, $forDelete);
            }
        }
    }

    public function initInstance($modelFiles) {
        $config = $this->filesConfig();
        $this->files[$modelFiles->attr] = ModelFiles::getInstance($this, $modelFiles->attr, $config[$modelFiles->attr], true, $modelFiles);
    }

    public function getModelFiles($attr) {
        if ($this->files === null) {
            $this->initFiles();
        }
        return $this->files[$attr];
    }

    public function beforeSave($insert) {
        if ($insert) {
            $this->initFiles();
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes) {
        if ($insert) {
            foreach ($this->files as $file) {
                $file->ownerIsNew = false;
                $file->moveTmpFile($this->getPrimaryKey());
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeDelete() {
        $this->initFiles(true);
        return parent::beforeDelete();
    }

    public function afterDelete() {
        foreach ($this->files as $file) {
            if (!$file->isNewRecord) {
                $file->deleteModel();
            }
        }
        parent::afterDelete();
    }

    static public function findImages($attr, $where = null) {
        $files = ModelFiles::find()->where(['entity' => get_called_class(), 'attr' => $attr])->andWhere('pk>0')->indexBy('pk')->all();
        $query = self::find()->orderBy('pos');
        if (is_array($where)) {
            $query->andWhere($where);
        }
        $query->andWhere(['IN', 'id', array_keys($files)]);
        $data = [];
        foreach ($query->all() as $model) {
            if (isset($files[$model->id])) {
                $model->initInstance($files[$model->id]);
                $data[] = $model;
            }
        }
        return $data;
    }

}
