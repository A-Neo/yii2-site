<?php

/* @var $this yii\web\View */
/* @var $user app\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<?=Yii::t('site','Hello')?> <?= $user->username ?>,

<?=Yii::t('site','Follow the link below to reset your password')?>:

<?= $resetLink ?>
