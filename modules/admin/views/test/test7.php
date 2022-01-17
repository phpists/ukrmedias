<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Url;
use app\models\Services;
use app\models\CalculatorTest7;

$this->params['breadcrumbs'] = [
    $this->title = 'Тест',
];
?>
<ul class="nav nav-tabs mb-3" role="tablist">
    <?php foreach (Services::$labels as $sid => $label): ?>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo Url::toRoute(['index', 'id' => $sid]) ?>">
                <?php echo Services::getIcon($sid); ?> <?php echo $label; ?>
            </a>
        </li>
    <?php endforeach; ?>
    <li class="nav-item">
        <a class="nav-link active" href="<?php echo Url::toRoute(['test2']) ?>">
            <?php echo Services::getIcon(Services::ID_HEAT); ?> будинок на 7 квартир
        </a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="main" role="tabpanel" aria-labelledby="home-tab">
        <div class="alert alert-info">
            <small>
                Кількість поверхів:	3<br/>
                Перший період після впровадження системи:	так<br/>
                Оснащеність розподілювачами опалювальних приладів:	50-75%<br/>
                Загальна площа опалювальних приміщень:	330<br/>
                Площа приміщень з розподілювачами:	150<br/>
                Площаь приміщень без обліку:	80<br/>
                Площа приміщень з лічильником:	60<br/>
                Загальнобудинкове споживання теплоенергії за месяць, Гкал:	10<br/>
                Тариф, грн:	2000<br/>
                Сума до сплати, грн:	20000<br/>
                МЗК згідно з розрахунками (3 поверхи):	16%<br/>
                Коефіцієнт для підвищення МЗК:	1.30<br/>
                Витрати на систему отоплення (інд. теплопункт з погодним регулюванням):	15%<br/>
                Мін частка сер. питомого споживання теплоенергії на опалення:	0.5<br/>
            </small>
        </div>
        <br/>
        <table class="table table-striped table-bordered shadow">
            <colgroup>
                <col width="8%"/>
                <col width="14%"/>
                <col width="14%"/>
                <col width="12%"/>
                <col width="12%"/>
                <col width="12%"/>
                <col width="12%"/>
                <col width=""/>
            </colgroup>
            <thead>
                <tr>
                    <th>Квартира</th>
                    <th>Всього</th>
                    <th>За споживання</th>
                    <th>На ВБС</th>
                    <th>На МЗК</th>
                    <th>Донарахування</th>
                    <th>Перерахування</th>
                    <th>q <sub>пр-роз</sub></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($house->getFlats() as $flat):
                    $total += $flat->Invoice->getTotalAmount();
                    ?>
                    <tr>
                        <td><?php echo $flat->no; ?></td>
                        <td><?php echo CalculatorTest7::check($flat->Invoice->getTotalAmount(), CalculatorTest7::$result[$flat->id]['total']); ?></td>
                        <td><?php echo CalculatorTest7::check($flat->Invoice->getAmountMain(), CalculatorTest7::$result[$flat->id]['main']); ?></td>
                        <td><?php echo CalculatorTest7::check($flat->Invoice->getAmountCommon(), CalculatorTest7::$result[$flat->id]['common']); ?></td>
                        <td><?php echo CalculatorTest7::check($flat->Invoice->getAmountMZK(), CalculatorTest7::$result[$flat->id]['mzk']); ?></td>
                        <td><?php echo CalculatorTest7::check($flat->Invoice->getAmountInc(), CalculatorTest7::$result[$flat->id]['inc']); ?></td>
                        <td><?php echo CalculatorTest7::check($flat->Invoice->getAmountDec(), CalculatorTest7::$result[$flat->id]['dec']); ?></td>
                        <td><?php echo CalculatorTest7::check($flat->Invoice->q_pr_roz, CalculatorTest7::$result[$flat->id]['q_pr_roz']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td>Сума:</td>
                    <td><?php echo CalculatorTest7::check($total, CalculatorTest7::$resultTotal); ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <br/>
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#house">Будинок</a>
            </li>
            <?php foreach ($house->getFlats() as $id => $flat): ?>
                <li class="nav-item">
                    <a class="nav-link"data-toggle="tab"  href="#flat<?php echo $id; ?>"><?php echo $flat->no; ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="house" role="tabpanel" aria-labelledby="home-tab">
                <?php
                array_shift($house->Invoice->details);
                echo implode('<br/>', $house->Invoice->details);
                ?>
                <?php echo $house->Invoice->printTest($house); ?>
            </div>
            <?php foreach ($house->getFlats() as $id => $flat): ?>
                <div class="tab-pane fade show" id="flat<?php echo $id; ?>" role="tabpanel" aria-labelledby="home-tab">
                    <div class="hint-block"><?php echo implode('; ', $flat->calcVariants); ?></div>
                    <?php
                    array_shift($flat->Invoice->details);
                    echo implode('<br/>', $flat->Invoice->details);
                    ?>
                    <?php echo $flat->Invoice->printTest($house); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <br/>
    </div>
</div>
