<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use \Yii;
use app\models\Letters;
use app\models\Export;
use app\models\DataHelper;
use app\models\Tasks;
use app\models\novaposhta\NovaPoshta;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SystemController extends Controller {

    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionMinutely() {
        try {
            $letters = new Letters();
            $letters->send();
        } catch (\Throwable $ex) {
            Yii::dump($ex->getMessage());
        }
        try {
            Export::firms();
            Export::preorders();
        } catch (\Throwable $ex) {
            Yii::dump($ex->getMessage());
        }
        Tasks::run();
        return ExitCode::OK;
    }

    public function actionDaily() {
        Tasks::start(Tasks::REFRESH_CACHE);
        try {
            DataHelper::clean();
            DataHelper::setPromoGoods();
            DataHelper::closeOrders();
        } catch (\Throwable $ex) {
            Yii::dump($ex->getMessage());
        }
        try {
            NovaPoshta::syncCitiesBook();
            NovaPoshta::syncOfficesBook();
            NovaPoshta::syncMessages();
            NovaPoshta::syncTypes();
            NovaPoshta::syncPaymentTypes();
            NovaPoshta::syncPayerTypes();
            NovaPoshta::syncPayerTypesBack();
            NovaPoshta::syncDeliveryTypes();
            NovaPoshta::syncCargoTypes();
            NovaPoshta::syncCargoDescr();
            NovaPoshta::syncSenders();
        } catch (\Throwable $ex) {
            Yii::dump($ex->getMessage());
        }
        return ExitCode::OK;
    }

    public function actionMonthly() {
        return ExitCode::OK;
    }

}
