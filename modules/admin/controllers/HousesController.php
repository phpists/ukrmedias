<?php

namespace app\modules\admin\controllers;

use \Yii;
use app\components\AdminController;
use app\components\Misc;
use app\models\AddrRegions;
use app\models\AddrAreas;
use app\models\AddrCities;
use app\models\AddrStreets;
use app\models\AddrHouses;
use app\models\AddrFlats;
use app\models\AccessLogic;
use app\models\DataHelper;
use app\models\Devices;
use app\models\Counters;

class HousesController extends AdminController {

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        $model = new AddrHouses;
        $dp = $model->search();
        return $this->render('index', [
                    'model' => $model,
                    'dataProvider' => $dp,
        ]);
    }

    public function actionUpdate($id = null) {
        $model = $id > 0 ? AddrHouses::findModel($id) : new AddrHouses();
        if (!$model->isNewRecord && !AccessLogic::isAllowHouseAccess($model->id)) {
            return $this->redirect(['index']);
        }
        if ($model->load(Yii::$app->request->post())) {
            $t = Yii::$app->db->beginTransaction();
            $res = $model->saveModel();
            if ($res) {
                $flatsQty = AddrFlats::fill($model);
                if ($flatsQty > $model->flats_qty) {
                    Yii::$app->session->setFlash('info', "{$model->getTitle()}: кількість приміщень у довіднику адрес - {$flatsQty}. Це більше ніж зазначено в полі 'Кількість приміщень'.");
                }
            }
            $res ? $t->commit() : $t->rollBack();
            if ($res) {
                Yii::$app->session->setFlash('success', 'Інформація записана.');
                if (Yii::$app->request->post('action') === 'exit') {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            }
        }
        return $this->render('update', [
                    'model' => $model,
                    'regions' => AddrRegions::keyval(),
                    'areas' => AddrAreas::keyval($model->region_id),
                    'cities' => AddrCities::keyval($model->region_id, $model->area_id),
                    'streets' => $model->isNewRecord ? [] : AddrStreets::keyval($model->city_id),
        ]);
    }

    public function actionDelete($id) {
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $model = AddrHouses::findOne($id);
            if ($model !== null && AccessLogic::isAllowHouseAccess($model->id)) {
                $t = Yii::$app->db->beginTransaction();
                $res = $model->deleteModel();
                $res ? $t->commit() : $t->rollBack();
            }
            return;
        }
        return $this->redirect(['index']);
    }

    public function actionDeleteFlats($id) {
        $data = json_decode(Yii::$app->security->validateData($id, DataHelper::HASH_KEY), true);
        if (!$data || !isset($data['id']) || !isset($data['time']) || $data['time'] < time() - 300) {
            return $this->redirect(getenv('HTTP_REFERER'));
        }
        if (!AccessLogic::isAllowHouseAccess($data['id'])) {
            return $this->redirect(getenv('HTTP_REFERER'));
        }
        $t = Yii::$app->db->beginTransaction();
        AddrFlats::deleteAll(['house_id' => $data['id']]);
        AddrHouses::updateFlatsQtyById($data['id']);
        $t->commit();
        return $this->redirect(getenv('HTTP_REFERER'));
    }

    public function actionDeleteFlatsSafe($id) {
        $data = json_decode(Yii::$app->security->validateData($id, DataHelper::HASH_KEY), true);
        if (!$data || !isset($data['id']) || !isset($data['time']) || $data['time'] < time() - 300) {
            return $this->redirect(getenv('HTTP_REFERER'));
        }
        if (!AccessLogic::isAllowHouseAccess($data['id'])) {
            return $this->redirect(getenv('HTTP_REFERER'));
        }
        $t = Yii::$app->db->beginTransaction();
        $ids1 = Devices::find()->select('flat_id')->where(['house_id' => $data['id']])->andWhere('flat_id IS NOT NULL')->column();
        $ids2 = Counters::find()->select('flat_id')->where(['house_id' => $data['id']])->andWhere('flat_id IS NOT NULL')->column();
        $ids = AddrFlats::find()->select('id')->where(['house_id' => $data['id']])->andWhere(['not in', 'id', $ids1])->andWhere(['not in', 'id', $ids2])->column();
        AddrFlats::deleteAll(['id' => $ids]);
        AddrHouses::updateFlatsQtyById($data['id']);
        $t->commit();
        return $this->redirect(getenv('HTTP_REFERER'));
    }

}
