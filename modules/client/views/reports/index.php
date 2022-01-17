<?php

use yii\helpers\Url;
use app\components\AppActiveForm;

\app\components\assets\DatepickerAssets::register($this);
$this->title = 'Звіти';
?>
<div class="table">
    <div class="reports">
        <?php $form = AppActiveForm::begin(['method' => 'get', 'action' => Url::to(['balance'])]); ?>
        <h4>Акт звірки</h4>
        <div class="reports_inputs">
            <div>
                <p>Від</p>
                <input id="balance_from_input" name="from" type="text" class="js_datepicker" data-default="<?php echo $balanceFrom; ?>"/>
                <label for="balance_from_input" class="pointer"><img class="icon" src="img/orders/report.svg" alt=""/></label>
            </div>
            <div>
                <p>До</p>
                <input id="balance_to_input" name="to" type="text" class="js_datepicker" data-default="<?php echo $balanceTo; ?>"/>
                <label for="balance_to_input" class="pointer"><img class="icon" src="img/orders/report.svg" alt=""/></label>
            </div>
            <button>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 2C0 0.89543 0.895431 0 2 0H22C23.1046 0 24 0.895431 24 2V22C24 23.1046 23.1046 24 22 24H2C0.89543 24 0 23.1046 0 22V2Z" fill="white"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 17C11.7348 17 11.4804 16.8946 11.2929 16.7071L8.29289 13.7071C7.90237 13.3166 7.90237 12.6834 8.29289 12.2929C8.68342 11.9024 9.31658 11.9024 9.70711 12.2929L11 13.5858L11 10C11 9.44771 11.4477 9 12 9C12.5523 9 13 9.44771 13 10L13 13.5858L14.2929 12.2929C14.6834 11.9024 15.3166 11.9024 15.7071 12.2929C16.0976 12.6834 16.0976 13.3166 15.7071 13.7071L12.7071 16.7071C12.5196 16.8946 12.2652 17 12 17Z" fill=""/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6 3C5.44772 3 5 3.44772 5 4V20C5 20.5523 5.44772 21 6 21H18C18.5523 21 19 20.5523 19 20V8.41421C19 8.149 18.8946 7.89464 18.7071 7.70711L14.2929 3.29289C14.1054 3.10536 13.851 3 13.5858 3H6ZM7.5 5C7.22386 5 7 5.22386 7 5.5V18.5C7 18.7761 7.22386 19 7.5 19H16.5C16.7761 19 17 18.7761 17 18.5V9.20711C17 9.0745 16.9473 8.94732 16.8536 8.85355L13.1464 5.14645C13.0527 5.05268 12.9255 5 12.7929 5H7.5Z" fill=""/>
                </svg>
                Отримати
            </button>
        </div>
        <?php AppActiveForm::end(); ?>
        <br/>
        <br/>
        <?php $form = AppActiveForm::begin(['method' => 'get', 'action' => Url::to(['payments'])]); ?>
        <h4>Графік платежів</h4>
        <div class="reports_inputs">
            <div>
                <p>На дату</p>
                <input id="payments_from_input" name="from" type="text" class="js_datepicker" data-default="<?php echo $paymentsFrom; ?>"/>
                <label for="payments_from_input" class="pointer"><img class="icon" src="img/orders/report.svg" alt=""/></label>
            </div>
            <button>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 2C0 0.89543 0.895431 0 2 0H22C23.1046 0 24 0.895431 24 2V22C24 23.1046 23.1046 24 22 24H2C0.89543 24 0 23.1046 0 22V2Z" fill="white"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 17C11.7348 17 11.4804 16.8946 11.2929 16.7071L8.29289 13.7071C7.90237 13.3166 7.90237 12.6834 8.29289 12.2929C8.68342 11.9024 9.31658 11.9024 9.70711 12.2929L11 13.5858L11 10C11 9.44771 11.4477 9 12 9C12.5523 9 13 9.44771 13 10L13 13.5858L14.2929 12.2929C14.6834 11.9024 15.3166 11.9024 15.7071 12.2929C16.0976 12.6834 16.0976 13.3166 15.7071 13.7071L12.7071 16.7071C12.5196 16.8946 12.2652 17 12 17Z" fill=""/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6 3C5.44772 3 5 3.44772 5 4V20C5 20.5523 5.44772 21 6 21H18C18.5523 21 19 20.5523 19 20V8.41421C19 8.149 18.8946 7.89464 18.7071 7.70711L14.2929 3.29289C14.1054 3.10536 13.851 3 13.5858 3H6ZM7.5 5C7.22386 5 7 5.22386 7 5.5V18.5C7 18.7761 7.22386 19 7.5 19H16.5C16.7761 19 17 18.7761 17 18.5V9.20711C17 9.0745 16.9473 8.94732 16.8536 8.85355L13.1464 5.14645C13.0527 5.05268 12.9255 5 12.7929 5H7.5Z" fill=""/>
                </svg>
                Отримати
            </button>
        </div>
        <?php AppActiveForm::end(); ?>
    </div>
</div>
