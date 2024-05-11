<?php

use yii\helpers\Url;
use yii\web\View;
use rmrevin\yii\fontawesome\FAS;
use app\models\Balance;

/* @var $this yii\web\View */
$this->title = Yii::t('account', 'Dashboard');
/**
 * @var $user \app\models\User
 */
$user = Yii::$app->user->identity;
$hasPoints = Balance::find()->where(['to_user_id' => $user->id, 'status' => Balance::STATUS_WAITING, 'comment' => ''])->exists();
?>
<?=$this->render('/tabs', ['hasPoints' => $hasPoints, 'user' => $user]);?>