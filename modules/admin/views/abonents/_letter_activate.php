Ви зареєстровані на сайті <a href="<?php echo Yii::$app->request->isSecureConnection ? 'https://' : 'http://'; ?><?php echo getenv('HTTP_HOST'); ?>/"><?php echo Yii::$app->name; ?></a>
<p>Для активації Вашого облікового запису перейдіть за посиланням <a href="<?php echo $url; ?>">АКТИВАЦІЯ</a>.</p>
<p>Лінк дійсний протягом <?php echo $time; ?> з моменту відправки цього листа.</p>
<br/>