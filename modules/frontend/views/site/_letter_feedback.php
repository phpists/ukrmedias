<?php //$this->beginContent('//layouts/email', array('sign' => true));                       ?>
<p>Нове повідомлення з сайту <u><?php echo Yii::$app->name; ?></u>.</p>
<br/>
<p><?php echo $model->getDate(); ?></p>
<p><?php echo $model->getMess(); ?></p>
<p>UserId=<?php echo $model->getUser(); ?></p>
<br/><br/>
<?php
//$this->endContent();
