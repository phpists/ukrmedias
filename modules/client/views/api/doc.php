<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use app\models\Api;
use app\models\Services;
use app\models\AddrFlats;
use app\models\DataHelper;
use app\models\Devices;
?>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">1. Загальна інформація.</div>
        <div class="text-normal-size text-dark">
            1.1. Доступ до API здійснюється через веб-служби на базі архитектури REST.<br/>
            Параметри по замовчуванню (якщо не вказано окремо):<br/>
            - HTTP-код при відповіді на запит: 200;<br/>
            - формат даних: JSON;<br/>
            - кодування utf-8;<br/>
            - тип MIME: application/json<br/>
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">2. Безпека.</div>
        <div class="text-normal-size text-dark">
            Верифікація даних здійснюється через цифровий підпис.<br/>
            Приватний ключ <code>pkey</code> встановлюється в особистому кабінеті.<br/>
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">2.1. Цифровий підпис.</div>
        <div class="text-normal-size text-dark">
            В кожному запиті (якщо не вказано окремо) обов'язково повинні передаватися параметри <code>id</code>, <code>data</code>, <code>time</code> та <code>signature</code>.<br/>
            Параметр <code>id</code> - ідентификатор користувача;<br/>
            Параметр <code>data</code> - дані, що підписуються, у вигляді json-строки (описані окремо для кожного запиту).<br/>
            Параметр <code>time</code> - час підпису в форматі YYYY-MM-DD HH:MM:SS.<br/>
            Параметр <code>signature</code> - підпис, є результатом хеш-функції sha256 від строки "<code>id</code> <code>pkey</code> <code>data</code> <code>time</code>".<br/>
            Дані, для яких не підтверджується цифровий підпис або час підпису відрізняється більш ніж на 5 хвилин, не повинні потрапляти в обробку.
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">2.2. Узагальнений приклад GET-запиту:</div>
        <div class="text-normal-size text-dark">
            Дані, що передаються: <code>{ "data_1" : "text", "data_2" : 123.4567 }</code><br/>
            <?php
            $data = ['data_1' => 'text', 'data_2' => 123.4567];
            $url = Api::createTestUrl('/api/system/test', $data);
            ?>
            <a href="<?php echo $url; ?>" target="_api_test"><?php echo $url; ?></a><br/>
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">3. Обмін даними.</div>
        <div class="text-normal-size text-dark">
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">3.1. Системні запити.</div>
        <div class="text-normal-size text-dark">
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">3.1.1. Тестування взаємодії з API або отримання часу на сервері.</div>
        <div class="text-normal-size text-dark">
            Шлях: <a href="<?php echo Api::createTestUrl('/api/system/test'); ?>" target="_api_test">/api/system/test</a><br/>
            Метод запиту: GET<br/><br/>
            Відповідь:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>your_method</td><td>VARCHAR</td><td>Метод запиту</td></tr>
                    <tr><td>your_id</td><td>VARCHAR</td><td>Ідентифікатор користувача</td></tr>
                    <tr><td>your_data</td><td>VARCHAR</td><td>Дані</td></tr>
                    <tr><td>your_time</td><td>VARCHAR</td><td>Час підпису</td></tr>
                    <tr><td>your_signature</td><td>VARCHAR</td><td>Цифровий підпис</td></tr>
                    <tr><td>server_time</td><td>DATETIME</td><td>Час на сервері</td></tr>
                    <tr><td>test_pkey</td><td>VARCHAR</td><td>Тестовий приватний ключ: <b><?php echo Api::TEST_PKEY; ?></b></td></tr>
                    <tr><td>calc_signature</td><td>CHAR(64)</td><td>Обчислений цифровий підпис</td></tr>
                    <tr><td>time_check</td><td>BOOL</td><td>Результат перевірки часу</td></tr>
                    <tr><td>signature_check</td><td>BOOL</td><td>Результат перевірки підпису</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">3.2. Робота з адресами.</div>
        <div class="text-normal-size text-dark">
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">3.2.1. Отримання довіднику адрес.</div>
        <div class="text-normal-size text-dark">
            Шлях: <a href="<?php echo Api::createDemoUrl('/api/address/index'); ?>" target="_api_test">/api/address/index</a><br/>
            Метод запиту: GET<br/><br/>
            У параметрі data рекомендується вказувати випадкове число, але дозволяється порожня строка.<br/>
            У відповіді в полі data буде перелік доступних для обслуговування адрес. Кожен елемент переліку включає:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>id</td><td>UNSIGNED INTEGER</td><td>Ідентифікатор будинку</td></tr>
                    <tr><td>region_id</td><td>UNSIGNED INTEGER | NULL</td><td>Ідентифікатор області. NULL – для обласного центру</td></tr>
                    <tr><td>region</td><td>VARCHAR(45)</td><td>Найменування області</td></tr>
                    <tr><td>area_id</td><td>UNSIGNED INTEGER | NULL</td><td>Ідентифікатор району. NULL – для районного центру</td></tr>
                    <tr><td>area</td><td>VARCHAR(45)</td><td>Найменування району</td></tr>
                    <tr><td>city_id</td><td>UNSIGNED INTEGER</td><td>Ідентифікатор населеного пункту</td></tr>
                    <tr><td>city</td><td>VARCHAR(45)</td><td>Найменування населеного пункту</td></tr>
                    <tr><td>street_id</td><td>UNSIGNED INTEGER</td><td>Ідентифікатор вулиці</td></tr>
                    <tr><td>street</td><td>VARCHAR(45)</td><td>Найменування вулиці</td></tr>
                    <tr><td>post_index</td><td>CHAR(5)</td><td>Поштовий індекс</td></tr>
                    <tr><td>no</td><td>VARCHAR(6)</td><td>Номер будинку</td></tr>
                    <tr><td>flats_qty</td><td>UNSIGNED INTEGER</td><td>Кількість приміщень в будинку</td></tr>
                    <tr><td>floors</td><td>UNSIGNED INTEGER | NULL</td><td>Кількість етажів</td></tr>
                    <tr><td>square</td><td>UNSIGNED DECIMAL(12,2) | NULL</td><td>Загальна площа будинку</td></tr>
                    <tr><td>heat_square</td><td>UNSIGNED DECIMAL(12,2) | NULL</td><td>Опалювальна площа</td></tr>
                    <tr><td>common_square</td><td>UNSIGNED DECIMAL(12,2) | NULL</td><td>Опалювальна площа</td></tr>
                    <tr><td>flats</td><td>-</td><td>Перелік приміщень будинку.</td></tr>
                </tbody>
            </table>
            Опис елементів переліку приміщень будинку:
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>id</td><td>UNSIGNED INTEGER</td><td>Ідентифікатор приміщення</td></tr>
                    <tr><td>no</td><td>VARCHAR(45)</td><td>Найменування</td></tr>
                    <tr><td>square</td><td>UNSIGNED DECIMAL(8,2) | NULL</td><td>Площа</td></tr>
                    <tr><td>place_k</td><td>UNSIGNED DECIMAL(15,2)</td><td>Коефіціієнт розташування</td></tr>
                    <tr><td>heat_metering</td><td>UNSIGNED TINYINT</td><td>Тип обліку теплоенергії. Можливі варіанти:<br/>
                            <?php foreach (AddrFlats::$heatMeteringLabels as $id => $label): ?>
                                <?php echo "{$id} - {$label}"; ?><br/>
                            <?php endforeach; ?></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">3.2.2. Отримання переліку будинків на обслуговуванні.</div>
        <div class="text-normal-size text-dark">
            Шлях: <a href="<?php echo Api::createDemoUrl('/api/client/houses'); ?>" target="_api_test">/api/client/houses</a><br/>
            Метод запиту: GET<br/><br/>
            У параметрі data рекомендується вказувати випадкове число, але дозволяється порожня строка.<br/>
            У відповіді в полі data буде перелік будинків. Кожен елемент переліку включає:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>id</td><td>UNSIGNED INTEGER</td><td>Ідентифікатор будинку</td></tr>
                    <tr><td>note</td><td>VARCHAR(45)</td><td>Коментар</td></tr>
                    <tr><td>services</td><td>SET</td><td>Перелік послуг, що надаються. Можливі варіанти:<br/>
                            <?php foreach (Services::$labels as $id => $label): ?>
                                <?php echo "{$id} - {$label}"; ?><br/>
                            <?php endforeach; ?>
                        </td></tr>
                    <tr><td>flats</td><td>-</td><td>Перелік приміщень будинку.</td></tr>
                </tbody>
            </table>
            Опис елементів переліку приміщень будинку:
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>id</td><td>UNSIGNED INTEGER</td><td>Ідентифікатор приміщення</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">3.2.3. Додавання або зміна адреси обслуговування.</div>
        <div class="text-normal-size text-dark">
            Шлях: /api/client/houses<br/>
            Метод запиту: POST<br/><br/>
            Даними, що передаються, є:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>id</td><td>UNSIGNED INTEGER</td><td>Ідентифікатор будинку з довіднику адрес</td></tr>
                    <tr><td>note</td><td>VARCHAR(45)</td><td>Коментар</td></tr>
                    <tr><td>services</td><td>SET</td><td>Перелік послуг, що надаються. Можливі варіанти:<br/>
                            <?php foreach (Services::$labels as $id => $label): ?>
                                <?php echo "{$id} - {$label}"; ?><br/>
                            <?php endforeach; ?>
                        </td></tr>
                    <tr><td>flats</td><td>-</td><td>Перелік ідентифікаторів приміщень будинку з довіднику адрес, які треба додати у будинок.</td></tr>
                </tbody>
            </table>
            <br/>
            Приклад даних: <code>{ "id":10, "note":"коментар", "services":[1,2], "flats":[11,12,13] }</code>
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">3.2.4. Видалення адреси обслуговування.</div>
        <div class="text-normal-size text-dark">
            Шлях: /api/client/houses<br/>
            Метод запиту: DELETE<br/><br/>
            Даними, що передаються, є:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>id</td><td>UNSIGNED INTEGER</td><td>Ідентифікатор будинку з довіднику адрес</td></tr>
                </tbody>
            </table>
            <br/>
            Приклад даних: <code>{ "id":10 }</code>
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">3.3. Робота з приладами обліку.</div>
        <div class="text-normal-size text-dark">
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">3.3.1. Отримання переліку приладів обліку.</div>
        <div class="text-normal-size text-dark">
            Шлях: <a href="<?php echo Api::createDemoUrl('/api/client/counters'); ?>" target="_api_test">/api/client/counters</a><br/>
            Метод запиту: GET<br/><br/>
            У параметрі data рекомендується вказувати випадкове число, але дозволяється порожня строка.<br/>
            У відповіді в полі data буде перелік приладів обліку. Кожен елемент переліку включає:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>id</td><td>UNSIGNED INTEGER</td><td>Ідентифікатор приладу</td></tr>
                    <tr><td>number</td><td>VARCHAR(20)</td><td>Номер приладу</td></tr>
                    <tr><td>model</td><td>VARCHAR(45)</td><td>Модель приладу</td></tr>
                    <tr><td>begin</td><td>UNSIGNED DECIMAL(15,4) | NULL</td><td>Початкові показання</td></tr>
                    <tr><td>date</td><td>DATE | NULL</td><td>Дата повірки</td></tr>
                    <tr><td>service_id</td><td>UNSIGNED TINYINT</td><td>Ідентифікатор послуги. Можливі варіанти:<br/>
                            <?php foreach (Services::$labels as $id => $label): ?>
                                <?php echo "{$id} - {$label}"; ?><br/>
                            <?php endforeach; ?></td></tr>
                    <tr><td>house_id</td><td>UNSIGNED INTEGER | NULL</td><td>Ідентифікатор будинку</td></tr>
                    <tr><td>flat_id</td><td>UNSIGNED INTEGER | NULL</td><td>Ідентифікатор приміщення</td></tr>
                    <tr><td>editable</td><td>BOOL</td><td>Можливість редагування. Редагування прилада дозволено, якщо до нього не прив'язаний пристрій для считування показань.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">3.3.2. Додавання або зміна приладу обліку.</div>
        <div class="text-normal-size text-dark">
            Шлях: /api/client/counters<br/>
            Метод запиту: POST<br/><br/>
            Редагування прилада дозволено, якщо до нього не прив'язаний пристрій для считування показань.<br/>
            Даними, що передаються, є:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>id</td><td>UNSIGNED INTEGER | NULL</td><td>Ідентифікатор приладу</td></tr>
                    <tr><td>number</td><td>VARCHAR(20)</td><td>Номер приладу</td></tr>
                    <tr><td>model</td><td>VARCHAR(45)</td><td>Модель приладу</td></tr>
                    <tr><td>begin</td><td>UNSIGNED DECIMAL(15,4) | NULL</td><td>Початкові показання</td></tr>
                    <tr><td>date</td><td>DATE | NULL</td><td>Дата повірки</td></tr>
                    <tr><td>service_id</td><td>UNSIGNED TINYINT</td><td>Ідентифікатор послуги. Можливі варіанти:<br/>
                            <?php foreach (Services::$labels as $id => $label): ?>
                                <?php echo "{$id} - {$label}"; ?><br/>
                            <?php endforeach; ?></td></tr>
                    <tr><td>house_id</td><td>UNSIGNED INTEGER | NULL</td><td>Ідентифікатор будинку</td></tr>
                    <tr><td>flat_id</td><td>UNSIGNED INTEGER | NULL</td><td>Ідентифікатор приміщення</td></tr>
                </tbody>
            </table>
            <br/>
            Приклад даних: <code>{ "id":10, "number":"112233", "model":"m123", "begin":0.0000, "date":"2020-01-01", "service_id":1, "house_id":10, "flat_id":11 }</code>
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">3.3.3. Видалення приладу обліку.</div>
        <div class="text-normal-size text-dark">
            Шлях: /api/client/counters<br/>
            Метод запиту: DELETE<br/><br/>
            Редагування прилада дозволено, якщо до нього не прив'язаний пристрій для считування показань.<br/>
            Даними, що передаються, є:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>id</td><td>UNSIGNED INTEGER</td><td>Ідентифікатор будинку з довіднику адрес</td></tr>
                </tbody>
            </table>
            <br/>
            Приклад даних: <code>{ "id":10 }</code>
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">3.4. Робота з даними про споживання.</div>
        <div class="text-normal-size text-dark">
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">3.4.1. Отримання даних про споживання за загальнобудинковими лічильниками.</div>
        <div class="text-normal-size text-dark">
            Шлях: <a href="<?php
            echo Api::createDemoUrl('/api/client/report-houses', [
                'from' => date('Y-01-01'), 'to' => date('Y-12-31'), 'service_id' => current(Api::$client->services), 'house_id' => null, 'group' => DataHelper::GROUP_MONTH
            ]);
            ?>" target="_api_test">/api/client/report-houses</a><br/>
            Метод запиту: GET<br/><br/>
            Даними, що передаються, є:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>from</td><td>DATE</td><td>Початкова дата</td></tr>
                    <tr><td>to</td><td>DATE</td><td>Кінцева дата</td></tr>
                    <tr><td>service_id</td><td>UNSIGNED TINYINT</td><td>Ідентифікатор послуги. Можливі варіанти:<br/>
                            <?php foreach (Services::$labels as $id => $label): ?>
                                <?php echo "{$id} - {$label}"; ?><br/>
                            <?php endforeach; ?></td></tr>
                    <tr><td>house_id</td><td>UNSIGNED INTEGER | NULL</td><td>Ідентифікатор будинку. Якщо вказано, то дані будуть лише по будинку.</td></tr>
                    <tr><td>group</td><td>VARCHAR</td><td>Групування даних. Можливі варіанти:<br/>
                            <?php echo DataHelper::GROUP_HOURS; ?> - по годинах<br/>
                            <?php echo DataHelper::GROUP_DAYS; ?> - по днях<br/>
                            <?php echo DataHelper::GROUP_MONTH; ?> - по місяцях<br/>
                        </td></tr>
                </tbody>
            </table>
            У відповіді в полі data буде перелік даних. Кожен елемент переліку включає:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>date</td><td>DATETIME</td><td>Дата останніх показників</td></tr>
                    <tr><td>house_id</td><td>UNSIGNED INTEGER | NULL</td><td>Ідентифікатор будинку</td></tr>
                    <tr><td>counter</td><td>VARCHAR(20)</td><td>Номер лічильника</td></tr>
                    <tr><td>device</td><td>VARCHAR(45)</td><td>Номер приладу</td></tr>
                    <tr><td>start_value</td><td>UNSIGNED DECIMAL(15,4)</td><td>Початкові показники</td></tr>
                    <tr><td>end_value</td><td>UNSIGNED DECIMAL(15,4)</td><td>Кінцеві показники</td></tr>
                    <tr><td>qty</td><td>UNSIGNED DECIMAL(15,4)</td><td>Спожито</td></tr>
                    <tr><td>unit</td><td>VARCHAR(5)</td><td>Одиниця виміру споживання. Можливі варіанти:
                            "<?php echo implode('", "', array_unique(array_merge(Services::$units, Services::$unitsAlt))); ?>"
                        </td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">3.4.2. Отримання даних про споживання за абонентськими лічильниками.</div>
        <div class="text-normal-size text-dark">
            Шлях: <a href="<?php
            echo Api::createDemoUrl('/api/client/report-flats', [
                'from' => date('Y-01-01'), 'to' => date('Y-12-31'), 'service_id' => current(Api::$client->services), 'house_id' => null, 'flat_id' => null, 'group' => DataHelper::GROUP_MONTH
            ]);
            ?>" target="_api_test">/api/client/report-flats</a><br/>
            Метод запиту: GET<br/><br/>
            Даними, що передаються, є:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>from</td><td>DATE</td><td>Початкова дата</td></tr>
                    <tr><td>to</td><td>DATE</td><td>Кінцева дата</td></tr>
                    <tr><td>service_id</td><td>UNSIGNED TINYINT</td><td>Ідентифікатор послуги. Можливі варіанти:<br/>
                            <?php foreach (Services::$labels as $id => $label): ?>
                                <?php echo "{$id} - {$label}"; ?><br/>
                            <?php endforeach; ?></td></tr>
                    <tr><td>house_id</td><td>UNSIGNED INTEGER | NULL</td><td>Ідентифікатор будинку. Якщо вказано, то дані будуть лише по будинку.</td></tr>
                    <tr><td>flat_id</td><td>UNSIGNED INTEGER | NULL</td><td>Ідентифікатор приміщення. Якщо вказано, то дані будуть лише по приміщенню.</td></tr>
                    <tr><td>group</td><td>VARCHAR</td><td>Групування даних. Можливі варіанти:<br/>
                            <?php echo DataHelper::GROUP_HOURS; ?> - по годинах<br/>
                            <?php echo DataHelper::GROUP_DAYS; ?> - по днях<br/>
                            <?php echo DataHelper::GROUP_MONTH; ?> - по місяцях<br/>
                        </td></tr>
                </tbody>
            </table>
            У відповіді в полі data буде перелік даних. Кожен елемент переліку включає:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>date</td><td>DATETIME</td><td>Дата останніх показників</td></tr>
                    <tr><td>house_id</td><td>UNSIGNED INTEGER | NULL</td><td>Ідентифікатор будинку</td></tr>
                    <tr><td>flat_id</td><td>UNSIGNED INTEGER | NULL</td><td>Ідентифікатор приміщення. Якщо вказано, то дані будуть лише по приміщенню.</td></tr>
                    <tr><td>counter</td><td>VARCHAR(20)</td><td>Номер лічильника</td></tr>
                    <tr><td>device</td><td>VARCHAR(45)</td><td>Номер приладу</td></tr>
                    <tr><td>start_value</td><td>UNSIGNED DECIMAL(15,4)</td><td>Початкові показники</td></tr>
                    <tr><td>end_value</td><td>UNSIGNED DECIMAL(15,4)</td><td>Кінцеві показники</td></tr>
                    <tr><td>qty</td><td>UNSIGNED DECIMAL(15,4)</td><td>Спожито</td></tr>
                    <tr><td>unit</td><td>VARCHAR(5)</td><td>Одиниця виміру споживання. Можливі варіанти:
                            "<?php echo implode('", "', array_unique(array_merge(Services::$units, Services::$unitsAlt))); ?>"
                        </td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">3.4.3. Отримання даних для рахунків за водопостачання.</div>
        <div class="text-normal-size text-dark">
            Шлях: <a href="<?php
            echo Api::createDemoUrl('/api/client/invoices-water', [
                'date' => date('Y-m-01', strtotime('-1 month')), 'service_id' => current(Api::$client->services), 'house_id' => null
            ]);
            ?>" target="_api_test">/api/client/invoices-water</a><br/>
            Метод запиту: GET<br/><br/>
            Даними, що передаються, є:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>date</td><td>DATE</td><td>Будь-яка дата розрахункового періоду</td></tr>
                    <tr><td>service_id</td><td>UNSIGNED TINYINT</td><td>Ідентифікатор послуги. Можливі варіанти:<br/>
                            <?php
                            foreach (Services::$labels as $id => $label):
                                if ($id == Services::ID_HEAT) {
                                    continue;
                                }
                                ?>
                                <?php echo "{$id} - {$label}"; ?><br/>
                            <?php endforeach; ?></td></tr>
                    <tr><td>house_id</td><td>UNSIGNED INTEGER | NULL</td><td>Ідентифікатор будинку.</td></tr>
                </tbody>
            </table>
            У відповіді в полі data буде перелік даних для рахунків. Кожен елемент переліку включає:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>house_id</td><td>UNSIGNED INTEGER</td><td>Ідентифікатор будинку</td></tr>
                    <tr><td>flat_id</td><td>UNSIGNED INTEGER | NULL</td><td>Ідентифікатор приміщення</td></tr>
                    <tr><td>price_main</td><td>UNSIGNED DECIMAL(15,2)</td><td>Тариф за водопостачання</td></tr>
                    <tr><td>price_drain</td><td>UNSIGNED DECIMAL(15,2)</td><td>Тариф за водовідведення</td></tr>
                    <tr><td>qty</td><td>UNSIGNED DECIMAL(15,3)</td><td>Спожито, куб.м</td></tr>
                    <tr><td>qty_add</td><td>UNSIGNED DECIMAL(15,3)</td><td>Розподіл розбалансу, куб.м</td></tr>
                    <tr><td>qty_common</td><td>UNSIGNED DECIMAL(15,3)</td><td>Витрати на загальнобудинкові потреби, куб.м</td></tr>
                    <tr><td>adjustment</td><td>DECIMAL(15,2)</td><td>Коригування, грн.</td></tr>
                    <tr><td>comment</td><td>VARCHAR(8192)</td><td>Коментар до коригування</td></tr>
                    <tr><td>details</td><td>-</td><td>Деталізація по лічильникам</td></tr>
                    <tr><td>from</td><td>DATE</td><td>Перша дата розрахункового періоду</td></tr>
                    <tr><td>to</td><td>DATE</td><td>Остання дата розрахункового періоду</td></tr>
                </tbody>
            </table>
            Кожен елемент переліку деталізації по лічильникам включає:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>counter</td><td>VARCHAR(20)</td><td>Номер лічильника</td></tr>
                    <tr><td>start_value</td><td>UNSIGNED DECIMAL(15,4)</td><td>Початкові показники, куб.м</td></tr>
                    <tr><td>end_value</td><td>UNSIGNED DECIMAL(15,4)</td><td>Кінцеві показники, куб.м</td></tr>
                    <tr><td>qty</td><td>UNSIGNED DECIMAL(15,4)</td><td>Спожито, куб.м</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">3.4.4. Отримання даних для рахунків за теплоенергію.</div>
        <div class="text-normal-size text-dark">
            Шлях: <a href="<?php
            echo Api::createDemoUrl('/api/client/invoices-heat', [
                'date' => date('Y-m-01', strtotime('-1 month')), 'house_id' => null
            ]);
            ?>" target="_api_test">/api/client/invoices-heat</a><br/>
            Метод запиту: GET<br/><br/>
            Даними, що передаються, є:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>date</td><td>DATE</td><td>Будь-яка дата розрахункового періоду</td></tr>
                    <tr><td>house_id</td><td>UNSIGNED INTEGER | NULL</td><td>Ідентифікатор будинку.</td></tr>
                </tbody>
            </table>
            У відповіді в полі data буде перелік даних для рахунків. Кожен елемент переліку включає:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>house_id</td><td>UNSIGNED INTEGER</td><td>Ідентифікатор будинку</td></tr>
                    <tr><td>flat_id</td><td>UNSIGNED INTEGER | NULL</td><td>Ідентифікатор приміщення</td></tr>
                    <tr><td>qty</td><td>UNSIGNED DECIMAL(15,4)</td><td>Спожито абонентом, ГКал</td></tr>
                    <tr><td>qty_mzk</td><td>UNSIGNED DECIMAL(15,4)</td><td>Спожито на МЗК, ГКал</td></tr>
                    <tr><td>qty_common</td><td>UNSIGNED DECIMAL(15,4)</td><td>Спожито на внутрішньобудинкові системи, ГКал</td></tr>
                    <tr><td>qty_inc</td><td>UNSIGNED DECIMAL(15,4)</td><td>Донараховано до мінімального споживання, ГКал</td></tr>
                    <tr><td>price_main</td><td>UNSIGNED DECIMAL(15,2)</td><td>Тариф, грн./ГКал</td></tr>
                    <tr><td>amount_main</td><td>UNSIGNED DECIMAL(15,2)</td><td>Нарахування за споживання, грн.</td></tr>
                    <tr><td>amount_common</td><td>UNSIGNED DECIMAL(15,2)</td><td>Витрати на обслуговування внутрішньобудинкової системи опалення, грн.</td></tr>
                    <tr><td>amount_mzk</td><td>UNSIGNED DECIMAL(15,2)</td><td>Витрати на опалення МЗК та допоміжних приміщень, грн.</td></tr>
                    <tr><td>amount_inc</td><td>DECIMAL(15,2)</td><td>Донарахування до мінімального споживання, грн.</td></tr>
                    <tr><td>amount_add</td><td>DECIMAL(15,2)</td><td>Донарахування після зведення балансу, грн.</td></tr>
                    <tr><td>amount_dec</td><td>DECIMAL(15,2)</td><td>Перерахування, грн.</td></tr>
                    <tr><td>adjustment</td><td>DECIMAL(15,2)</td><td>Коригування, грн.</td></tr>
                    <tr><td>comment</td><td>VARCHAR(8192)</td><td>Коментар до коригування</td></tr>
                    <tr><td>details</td><td>-</td><td>Деталізація по лічильникам</td></tr>
                    <tr><td>from</td><td>DATE</td><td>Перша дата розрахункового періоду</td></tr>
                    <tr><td>to</td><td>DATE</td><td>Остання дата розрахункового періоду</td></tr>
                </tbody>
            </table>
            Кожен елемент переліку деталізації по лічильникам включає:<br/>
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>type</td><td>UNSIGNED TINYINT</td><td>Тип лічильника. Можливі варіанти:<br/>
                            <?php
                            foreach (Devices::$types as $id => $label):
                                if ($id == Devices::TYPE_MODULARIS) {
                                    continue;
                                }
                                ?>
                                <?php echo "{$id} - {$label}"; ?><br/>
                            <?php endforeach; ?>
                        </td></tr>
                    <tr><td>counter</td><td>VARCHAR(20)</td><td>Номер лічильника</td></tr>
                    <tr><td>start_value</td><td>UNSIGNED DECIMAL(15,4)</td><td>Початкові показники, Гкал або у.о.</td></tr>
                    <tr><td>end_value</td><td>UNSIGNED DECIMAL(15,4)</td><td>Кінцеві показники, Гкал або у.о.</td></tr>
                    <tr><td>qty</td><td>UNSIGNED DECIMAL(15,4)</td><td>Витрати, Гкал або у.о.</td></tr>
                    <tr><td>q_pr_roz</td><td>UNSIGNED DECIMAL(15,5) | NULL</td><td>Питомий обсяг спожитої теплової енергії на опалення визначений на 1 у.о., Гкал (лише для розподілювача)</td></tr>
                    <tr><td>ck</td><td>UNSIGNED DECIMAL(15,2)</td><td>Коригувальний коефіцієнт</td></tr>
                    <tr><td>heat_k</td><td>UNSIGNED DECIMAL(15,2) | NULL</td><td>Радіаторний коефіцієнт (лише для розподілювача)</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">4. Різне.</div>
        <div class="text-normal-size text-dark">
        </div>
    </div>
</div>
<div class="card w100 card--white card--gap card--auto">
    <div class="card--info">
        <div class="h4 text-grey2">4.1. Відповіді API.</div>
        <div class="text-normal-size text-dark">
            Всі відповіді на запити (якщо не вказано окремо) додатково до описаних даних, містять наступні поля:
            <table class="simple">
                <thead>
                    <tr><th>Найменування поля</th><th>Тип даних MySQL</th><th>Опис</th></tr>
                </thead>
                <tbody>
                    <tr><td>res</td><td>BOOL</td><td>Результат запиту: true – успішно, false – помилка. При помилці в полі "data" передаєтся опис помилки.</td></tr>
                    <tr><td>time</td><td>DATETIME</td><td>Час підпису</td></tr>
                    <tr><td>signature</td><td>CHAR(64)</td><td>Цифровий підпис</td></tr>
                </tbody>
            </table>
            <br/>
            Узагальнений приклад відповіді:<br/>
            <code>{ "res":true, "data":"{ \"data\":100.0000 }", "time":"682981200", "signature":"1ea27e66872d6b884e8d85fd8b45c3a00c0484113b79b521acbe25bb87b40aea" }</code>
        </div>
    </div>
</div>
