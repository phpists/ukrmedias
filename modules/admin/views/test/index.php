<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Url;
use app\models\Services;
use app\models\CalculatorTest;
use app\models\CalculatorSettingsHouses;
use app\models\CalculatorSettingsFlats;

$this->params['breadcrumbs'] = [
    $this->title = 'Тест',
];
?>
<ul class="nav nav-tabs mb-3" role="tablist">
    <?php foreach (Services::$labels as $sid => $label): ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($service_id == $sid): ?>active<?php endif; ?>" href="<?php echo Url::toRoute(['index', 'id' => $sid]) ?>">
                <?php echo Services::getIcon($sid); ?> <?php echo $label; ?>
            </a>
        </li>
    <?php endforeach; ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo Url::toRoute(['test7']) ?>">
            <?php echo Services::getIcon(Services::ID_HEAT); ?> будинок на 7 квартир
        </a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="main" role="tabpanel" aria-labelledby="home-tab">
        <div class="alert alert-info">
            <small>
                Загальні умови:<br/>
                15 числа - всі прилади працездатні, але дані не потрапляють в систему.<br/>
                <?php if (in_array($service_id, [Services::ID_COOL_WATER, Services::ID_HOT_WATER])): ?>
                    Споживання кожного абонента з лічильником 10 куб.м.<br/>
                    У кожного абонента є витік води 10 куб.м.<br/>
                <?php endif; ?>
                <?php if ($service_id == Services::ID_HEAT): ?>
                    Споживання кожного абонента з лічильником 10 ГКал<br/>
                    Споживання кожного абонента з розподілювачем 10 у.о.<br/>
                    Всі абоненти "без обліку" мають претензію щодо кількості/якості послуг з теплопостачання.<br/>
                <?php endif; ?>
            </small>
        </div>
        <?php
        foreach ($dataset as $house):
            $settingsHouse = $house->getSettings(date('Y-m-01'));
            ?>
            <table class="table table-striped table-bordered shadow">
                <colgroup>
                    <col width="40%"/>
                    <col />
                </colgroup>
                <tbody>
                    <tr>
                        <td>
                            Будинок <?php echo $house->no; ?><br/>
                            <div class="hint-block"><?php echo implode('; ', $house->calcVariants); ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            array_shift($house->Invoice->details);
                            echo implode('<br/>', $house->Invoice->details);
                            ?>
                            <span class="btn btn-light btn-sm" onClick="$(this).next().slideToggle();"><i class="fa fa-print"></i></span>
                            <div style="display:none;">
                                <?php echo $house->Invoice->printTest($house); ?>
                            </div>
                        </td>
                    </tr>
                    <?php foreach ($house->getFlats() as $flat): ?>
                        <tr>
                            <td>
                                Будинок <?php echo $house->no; ?>, <?php echo $flat->no; ?><br/>
                                <div class="hint-block"><?php echo implode('; ', $flat->calcVariants); ?></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                array_shift($flat->Invoice->details);
                                echo implode('<br/>', $flat->Invoice->details);
                                ?>
                                <span class="btn btn-light btn-sm" onClick="$(this).next().slideToggle();"><i class="fa fa-print"></i></span>
                                <div style="display:none;">
                                    <?php echo $flat->Invoice->printTest($house); ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <br/>
            <br/>
        <?php endforeach; ?>
    </div>
</div>
