<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */

/* @var $exception Exception */

use yii\helpers\Html;


?>
<title> В разработке </title>
<div class="site-error">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
	<h1>В разработке</h1>

    <!--<div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>-->

    <p>
	Зайдите позже и следите за новостями.
     <!--   <?= Yii::t('site', 'The above error occurred while the Web server was processing your request.') ?> -->
    </p>
    <p>
       <!-- <?= Yii::t('site', 'Please contact us if you think this is a server error. Thank you.') ?> -->
    </p>
    <?php if (YII_DEBUG): ?>
        <p><?= $exception->getFile() ?>:<?= $exception->getLine() ?></p>
        <pre><?= $exception->getTraceAsString() ?></pre>
    <?php endif ?>

</div>
