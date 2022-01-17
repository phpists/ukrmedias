<?php

namespace app\components;

use app\models\Cpu;
use \Yii;

trait T_CpuModel {

    public $CpuModel;

    public function getPageTitle() {
        $title = $this->getCpuModel()->meta_title;
        if ($title === '') {
            $title = $this->title;
        }
        return $title;
    }

    public function getMetaKeywords() {
        return $this->getCpuModel()->meta_keywords;
    }

    public function getMetaDescr() {
        return $this->getCpuModel()->meta_descr;
    }

    static public function findModel() {
        $args = func_get_args();
        $a = array_shift($args);
        $model = parent::findModel($a);
        $model->getCpuModel();
        return $model;
    }

    public function isSubmitted() {
        $res = $this->load(Yii::$app->request->post());
        if ($res) {
            $res = $this->getCpuModel()->load(Yii::$app->request->post());
        }
        return $res;
    }

    public function getCpuModel($refresh = false) {
        if ($this->CpuModel === null || $refresh) {
            $this->CpuModel = Cpu::findModel(['id' => $this->id, 'class' => get_called_class()]);
        }
        return $this->CpuModel;
    }

    public function getUrl($params = []) {
        return $this->getCpuModel()->getUrl($params);
    }

    public function getMetaSrc() {
        $host = Yii::$app->request->getHostInfo();
        switch (get_called_class()):
            case 'app\models\Equipment':
            case 'app\models\Projects':
                $src = $this->getModelFiles('photo')->getSrc('big');
                break;
            case 'app\models\Partners':
                $src = $this->getModelFiles('logo')->getSrc('big');
                break;
            case 'app\models\News':
            case 'app\models\Pages':
                if ($this->getModelFiles('cover_1')->isExists()) {
                    $src = $this->getModelFiles('cover_1')->getSrc('big');
                } elseif ($this->getModelFiles('cover_2')->isExists()) {
                    $src = $this->getModelFiles('cover_2')->getSrc('big');
                } elseif ($this->getModelFiles('cover_3')->isExists()) {
                    $src = $this->getModelFiles('cover_3')->getSrc('big');
                } else {
                    $src = null;
                }
                break;
            default:
                $src = null;
        endswitch;
        return $src === null ? null : "{$host}{$src}";
    }

}
