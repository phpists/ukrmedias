<?php

namespace app\modules\client\controllers;

use yii\helpers\Url;
use yii\helpers\Html;
use \yii\web\UploadedFile;
use \Yii;
use app\components\Misc;
use app\components\RuntimeFiles;
use app\models\ModelFiles;
use app\models\Addresses;
use app\models\ExcelViewer;
use app\models\XML_PriceUa;
use app\models\XLS_PriceUa;
use app\models\Category;
use app\models\novaposhta\NP_CitiesNp;
use app\models\novaposhta\NP_Offices;

class DataController extends \app\components\ClientController {

    public function actionModelFileDownload($id) {
        $file = ModelFiles::download($id);
        if ($file->hasErrors()) {
            Yii::$app->session->setFlash('error', Html::errorSummary($file, ['header' => '']));
            if (getenv('HTTP_REFERER') <> '') {
                $this->redirect(getenv('HTTP_REFERER'));
            } else {
                $this->redirect('index');
            }
        }
    }

    public function actionAddress($id) {
        return json_encode(Addresses::getData($id, Yii::$app->user->identity->firm_id));
    }

    public function actionUploadXlsFile() {
        $file = UploadedFile::getInstanceByName('file');
        $ext = $file->getExtension();
        if (in_array($ext, ['xls', 'xlsx'])) {
            $res = $file->saveAs(ExcelViewer::getFname());
            if ($res) {
                return json_encode(['res' => true, 'url' => Url::to(['/client/cart/view-excel'])]);
            }
        }
        return json_encode(['res' => false]);
    }

    public function actionDownloadXml($id, $ref, $download = null) {
        $file = RuntimeFiles::getUid('XML_PriceUa', 'price_ua.xml');
        if (Yii::$app->request->isAjax) {
            $filter = (array) Yii::$app->request->post('XML_PriceUa');
            $filter['price_type_id'] = Yii::$app->user->identity->getFirm()->price_type_id;
            $res = XML_PriceUa::create($file, $filter);
            if ($res) {
                return Url::to(['download-xml', 'id' => $id, 'ref' => $ref, 'download' => 1]);
            }
        } elseif ($download && is_file($file)) {
            return Misc::sendFile($file, Category::findModel($id)->title . '.xml');
        }
        return $ref;
    }

    public function actionDownloadExcel($id, $ref, $download = null) {

        $file = RuntimeFiles::getUid('XML_PriceUa', 'price_ua.xls');
        $category = Category::findModel($id);
        if (Yii::$app->request->isAjax) {
            $filter = (array) Yii::$app->request->post('XML_PriceUa');
            $filter['price_type_id'] = Yii::$app->user->identity->getFirm()->price_type_id;
            $res = XLS_PriceUa::create($file, $category->title, $filter);
            if ($res) {
                return Url::to(['download-excel', 'id' => $id, 'ref' => $ref, 'download' => 1]);
            }
        } elseif ($download && is_file($file)) {
            return Misc::sendFile($file, $category->title . '.xls');
        }
        return $ref;
    }

    public function actionDownloadExcelPhoto($id, $ref, $download = null) {

        $file = RuntimeFiles::getUid('XML_PriceUa', 'price_ua.xls');
        $category = Category::findModel($id);
        if (Yii::$app->request->isAjax) {
            $filter = (array) Yii::$app->request->post('XML_PriceUa');
            $filter['price_type_id'] = Yii::$app->user->identity->getFirm()->price_type_id;
            $res = XLS_PriceUa::createWithPhoto($file, $category->title, $filter);
            if ($res) {
                return Url::to(['download-excel', 'id' => $id, 'ref' => $ref, 'download' => 1]);
            }
        } elseif ($download && is_file($file)) {
            return Misc::sendFile($file, $category->title . '.xls');
        }
        return $ref;
    }

    public function actionCitiesOptions($id) {
        $options = ['prompt' => ''];
        return Html::renderSelectOptions('', NP_CitiesNp::keyval($id), $options);
    }

    public function actionOfficesOptions($id) {
        $data = NP_Offices::keyval($id);
        if (count($data) > 1) {
            $selected = '';
            $options = ['prompt' => ''];
        } else {
            $options = [];
            $selected = key($data);
        }
        return Html::renderSelectOptions($selected, $data, $options);
    }

}
